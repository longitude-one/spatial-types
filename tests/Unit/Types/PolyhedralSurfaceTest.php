<?php

/*
 * This file is part of the LongitudeOne Spatial project.
 *
 * Copyright (c) 2026 Alexandre Tranchant <alexandre.tranchant@gmail.com>
 *
 * See LICENSE for copyright and license information.
 *
 */

declare(strict_types=1);

namespace LongitudeOne\SpatialTypes\Tests\Unit\Types;

use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\SpatialTypes\Exception\OutOfBoundsException;
use LongitudeOne\SpatialTypes\Internal\Types\AbstractPolyhedralSurfaceImmutable;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\PolyhedralSurfaceImmutable;
use LongitudeOne\SpatialTypes\Value\Coordinates;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Internal\Types\AbstractPolyhedralSurfaceImmutable
 *
 * @phpstan-type Position array{int, int, int, 3?: int}
 * @phpstan-type Patches list<list<list<Position>>>
 */
class PolyhedralSurfaceTest extends TestCase
{
    /** @return iterable<string, array{class-string<AbstractPolyhedralSurfaceImmutable>}> */
    public static function surfaces(): iterable
    {
        yield 'Dimension3z Geometry' => [PolyhedralSurfaceImmutable::class];

        yield 'Dimension3z Geography' => [\LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\PolyhedralSurfaceImmutable::class];

        yield 'Dimension4zm Geometry' => [\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\PolyhedralSurfaceImmutable::class];

        yield 'Dimension4zm Geography' => [\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\PolyhedralSurfaceImmutable::class];
    }

    /**
     * @param string $class Surface class
     *
     * @phpstan-return Patches
     */
    private static function coordinates(string $class): array
    {
        $patches = [
            [[[0, 0, 0], [2, 0, 0], [0, 2, 0], [0, 0, 0]]],
            [[[2, 0, 0], [0, 0, 0], [0, 0, 2], [2, 0, 0]]],
        ];
        if (str_contains($class, '4zm')) {
            foreach ($patches as &$patch) {
                foreach ($patch[0] as &$position) {
                    $position[] = 7;
                }
            }
        }

        return $patches;
    }

    /**
     * @param string $class Surface class
     *
     * @phpstan-param class-string<AbstractPolyhedralSurfaceImmutable> $class
     */
    #[DataProvider('surfaces')]
    public function testEmptySurface(string $class): void
    {
        $surface = new $class();
        self::assertTrue($surface->hasZ());
        self::assertSame([], $surface->getElements());
        self::assertSame([], $surface->getPatches());
        self::assertSame([], $surface->toArray());
        self::assertSame(['type' => 'PolyhedralSurface', 'coordinates' => [], 'srid' => 0], $surface->jsonSerialize());
        $copy = $surface->withSpatialReference(SpatialReference::epsg(4326));
        self::assertSame($class, new \ReflectionClass($copy)->getName());
        self::assertSame(4326, $copy->getSrid());
        self::assertSame(0, $surface->getSrid());
        $this->expectException(OutOfBoundsException::class);
        $surface->getPatch(0);
    }

    /**
     * @param string $class Surface class
     *
     * @phpstan-param class-string<AbstractPolyhedralSurfaceImmutable> $class
     */
    #[DataProvider('surfaces')]
    public function testEmptySurfaceCannotReplacePatch(string $class): void
    {
        $surface = new $class();
        $this->expectException(OutOfBoundsException::class);
        $surface->withPatch(0, self::coordinates($class)[0]);
    }

    /**
     * @param string $class Surface class
     *
     * @phpstan-param class-string<AbstractPolyhedralSurfaceImmutable> $class
     */
    #[DataProvider('surfaces')]
    public function testOpenSurfaceAndIndependentCopies(string $class): void
    {
        $coordinates = self::coordinates($class);
        $surface = new $class($coordinates, 4326);
        self::assertSame(GeometryTypeEnum::POLYHEDRALSURFACE, $surface->getType());
        self::assertSame(str_contains($class, '4zm'), $surface->hasM());
        self::assertSame($coordinates, $surface->toArray());
        self::assertSame($surface->getPatch(1), $surface->getPatch(-1));
        self::assertSame($surface->getPatch(0), $surface->getPatch(2));
        self::assertSame($coordinates, new $class($surface->getPatches(), 4326)->toArray());
        $copies = [
            $surface->withArrayOfCoordinates($coordinates),
            $surface->withPatch(0, $coordinates[0]),
            $surface->withRing(0, 0, $coordinates[0][0]),
            $surface->withSrid(3857),
            $surface->withSpatialReference(SpatialReference::epsg(4326)),
        ];
        foreach ($copies as $copy) {
            self::assertSame($class, new \ReflectionClass($copy)->getName());
            self::assertSame($surface->getFamily(), $copy->getFamily());
            self::assertTrue($surface->hasSameDimension($copy));
            self::assertSame($coordinates, $copy->toArray());
            foreach ($surface->getPatches() as $index => $patch) {
                self::assertNotSame($patch, $copy->getPatch($index));
                self::assertNotSame($patch->getRing(0), $copy->getPatch($index)->getRing(0));
                self::assertNotSame($patch->getRing(0)->getPoint(0), $copy->getPatch($index)->getRing(0)->getPoint(0));
                self::assertSame($copy->getSrid(), $copy->getPatch($index)->getRing(0)->getPoint(0)->getSrid());
            }
        }
        self::assertSame(3857, $copies[3]->getSrid());
        self::assertSame('EPSG', $copies[4]->getPatch(0)->getSpatialReference()->authority);
        self::assertSame(4326, $surface->getSrid());
        self::assertSame($coordinates, $surface->toArray());
    }

    /**
     * @param string $class Surface class
     *
     * @phpstan-param class-string<AbstractPolyhedralSurfaceImmutable> $class
     */
    #[DataProvider('surfaces')]
    public function testPointReplacementAndSinglePatch(string $class): void
    {
        $coordinates = self::coordinates($class);
        $surface = new $class([$coordinates[0]], 4326);
        $point = $surface->hasM() ? Coordinates::xyzm(-1, 0, 0, 7) : Coordinates::xyz(-1, 0, 0);
        $copy = $surface->withPoint(0, -1, 0, $point);
        self::assertSame($class, new \ReflectionClass($copy)->getName());
        self::assertSame([$coordinates[0]], $surface->toArray());
        self::assertSame(-1, $copy->getPatch(0)->getRing(0)->getPoint(0)->getX());
        self::assertSame(-1, $copy->getPatch(0)->getRing(0)->getPoint(-1)->getX());
        self::assertNotSame($surface->getPatch(0), $copy->getPatch(0));
    }

    /**
     * @param string $class Surface class
     *
     * @phpstan-param class-string<AbstractPolyhedralSurfaceImmutable> $class
     */
    #[DataProvider('surfaces')]
    public function testReplacementsPreserveReferenceAuthority(string $class): void
    {
        $coordinates = self::coordinates($class);
        $reference = SpatialReference::epsg(4326);
        $surface = new $class($coordinates, $reference);
        foreach ([$surface->withPatch(0, $coordinates[0]), $surface->withRing(0, 0, $coordinates[0][0])] as $copy) {
            foreach ($copy->getPatches() as $patch) {
                self::assertTrue($reference->equals($patch->getSpatialReference()));
                self::assertTrue($reference->equals($patch->getRing(0)->getPoint(0)->getSpatialReference()));
            }
        }
    }
}
