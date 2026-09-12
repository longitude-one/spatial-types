<?php
/**
 * This file is part of the spatial project.
 *
 * PHP 8.4 | 8.5
 *
 * Copyright Alexandre Tranchant <alexandre.tranchant@gmail.com> 2024-2026
 * Copyright Longitude One 2024-2026
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

declare(strict_types=1);

namespace LongitudeOne\SpatialTypes\Tests\Unit\Types;

use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Exception\InvalidFamilyException;
use LongitudeOne\SpatialTypes\Exception\InvalidSridException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\OutOfBoundsException;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Types\AbstractPolyhedralSurface;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\PolyhedralSurface;
use LongitudeOne\SpatialTypes\Value\Coordinates;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractPolyhedralSurface
 *
 * @phpstan-type Position array{int, int, int, 3?: int}
 * @phpstan-type Patches list<list<list<Position>>>
 */
class PolyhedralSurfaceTest extends TestCase
{
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
     * @phpstan-param class-string<AbstractPolyhedralSurface> $class
     */
    #[DataProvider('surfaces')]
    public function testEmptySurface(string $class): void
    {
        $surface = new $class();
        static::assertTrue($surface->isEmpty());
        static::assertTrue($surface->hasZ());
        static::assertSame([], $surface->getElements());
        static::assertSame([], $surface->getPatches());
        static::assertSame([], $surface->toArray());
        static::assertSame(['type' => 'PolyhedralSurface', 'coordinates' => [], 'srid' => 0], $surface->jsonSerialize());
        $copy = $surface->withSpatialReference(SpatialReference::epsg(4326));
        static::assertSame($class, $copy::class);
        static::assertTrue($copy->isEmpty());
        static::assertSame(4326, $copy->getSrid());
        static::assertSame(0, $surface->getSrid());
        static::assertTrue($copy->withArrayOfCoordinates([])->isEmpty());
        $this->expectException(OutOfBoundsException::class);
        $surface->getPatch(0);
    }

    /**
     * @param string $class Surface class
     *
     * @phpstan-param class-string<AbstractPolyhedralSurface> $class
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
     * @phpstan-param class-string<AbstractPolyhedralSurface> $class
     */
    #[DataProvider('surfaces')]
    public function testOpenSurfaceAndIndependentCopies(string $class): void
    {
        $coordinates = self::coordinates($class);
        $surface = new $class($coordinates, 4326);
        static::assertFalse($surface->isEmpty());
        static::assertSame(GeometryTypeEnum::POLYHEDRALSURFACE, $surface->getType());
        static::assertSame(str_contains($class, '4zm'), $surface->hasM());
        static::assertSame($coordinates, $surface->toArray());
        static::assertSame($surface->getPatch(1), $surface->getPatch(-1));
        static::assertSame($surface->getPatch(0), $surface->getPatch(2));
        static::assertSame($coordinates, (new $class($surface->getPatches(), 4326))->toArray());
        $copies = [
            $surface->withArrayOfCoordinates($coordinates),
            $surface->withPatch(0, $coordinates[0]),
            $surface->withRing(0, 0, $coordinates[0][0]),
            $surface->withSrid(3857),
            $surface->withSpatialReference(SpatialReference::epsg(4326)),
        ];
        foreach ($copies as $copy) {
            static::assertSame($class, $copy::class);
            static::assertSame($surface->getFamily(), $copy->getFamily());
            static::assertTrue($surface->hasSameDimension($copy));
            static::assertSame($coordinates, $copy->toArray());
            foreach ($surface->getPatches() as $index => $patch) {
                static::assertNotSame($patch, $copy->getPatch($index));
                static::assertNotSame($patch->getRing(0), $copy->getPatch($index)->getRing(0));
                static::assertNotSame($patch->getRing(0)->getPoint(0), $copy->getPatch($index)->getRing(0)->getPoint(0));
                static::assertSame($copy->getSrid(), $copy->getPatch($index)->getRing(0)->getPoint(0)->getSrid());
            }
        }
        static::assertSame(3857, $copies[3]->getSrid());
        static::assertSame('EPSG', $copies[4]->getPatch(0)->getSpatialReference()->authority);
        static::assertSame(4326, $surface->getSrid());
        static::assertTrue($surface->withArrayOfCoordinates([])->isEmpty());
        static::assertSame($coordinates, $surface->toArray());
    }

    /**
     * @param string $class Surface class
     *
     * @phpstan-param class-string<AbstractPolyhedralSurface> $class
     */
    #[DataProvider('surfaces')]
    public function testPointReplacementAndSinglePatch(string $class): void
    {
        $coordinates = self::coordinates($class);
        $surface = new $class([$coordinates[0]], 4326);
        $point = $surface->hasM() ? Coordinates::xyzm(-1, 0, 0, 7) : Coordinates::xyz(-1, 0, 0);
        $copy = $surface->withPoint(0, -1, 0, $point);
        static::assertSame($class, $copy::class);
        static::assertSame([$coordinates[0]], $surface->toArray());
        static::assertSame(-1, $copy->getPatch(0)->getRing(0)->getPoint(0)->getX());
        static::assertSame(-1, $copy->getPatch(0)->getRing(0)->getPoint(-1)->getX());
        static::assertNotSame($surface->getPatch(0), $copy->getPatch(0));
    }

    /**
     * @param string $class Surface class
     *
     * @phpstan-param class-string<AbstractPolyhedralSurface> $class
     */
    #[DataProvider('surfaces')]
    public function testPointReplacementCannotBreakAdjacency(string $class): void
    {
        $surface = new $class(self::coordinates($class));
        $point = $surface->hasM() ? Coordinates::xyzm(-1, -1, 0, 7) : Coordinates::xyz(-1, -1, 0);
        $this->expectException(InvalidValueException::class);
        $surface->withPoint(0, 0, 0, $point);
    }

    /**
     * @param string $class Surface class
     *
     * @phpstan-param class-string<AbstractPolyhedralSurface> $class
     */
    #[DataProvider('surfaces')]
    public function testRejectsDisconnectedReplacement(string $class): void
    {
        $coordinates = self::coordinates($class);
        $surface = new $class($coordinates);
        foreach ($coordinates[1][0] as &$position) {
            $position[0] += 10;
        }
        unset($position);

        try {
            $surface->withPatch(1, $coordinates[1]);
            static::fail('Disconnected patches must be rejected.');
        } catch (InvalidValueException) {
            static::assertSame(self::coordinates($class), $surface->toArray());
        }
    }

    /**
     * @param string $class Surface class
     *
     * @phpstan-param class-string<AbstractPolyhedralSurface> $class
     */
    #[DataProvider('surfaces')]
    public function testRejectsEmptyPatch(string $class): void
    {
        $this->expectException(InvalidValueException::class);
        new $class([[]]);
    }

    /**
     * @param string $class Surface class
     *
     * @phpstan-param class-string<AbstractPolyhedralSurface> $class
     */
    #[DataProvider('surfaces')]
    public function testRejectsEqualEdgeOrientation(string $class): void
    {
        $coordinates = self::coordinates($class);
        $coordinates[1][0] = array_reverse($coordinates[1][0]);
        $this->expectException(InvalidValueException::class);
        new $class($coordinates);
    }

    /**
     * @param string $class Surface class
     *
     * @phpstan-param class-string<AbstractPolyhedralSurface> $class
     */
    #[DataProvider('surfaces')]
    public function testRejectsMixedDimension(string $class): void
    {
        $otherClass = str_contains($class, '4zm') ? str_replace('Dimension4zm', 'Dimension3z', $class) : str_replace('Dimension3z', 'Dimension4zm', $class);
        $other = new $otherClass(self::coordinates($otherClass));
        static::assertInstanceOf(AbstractPolyhedralSurface::class, $other);
        $this->expectException(InvalidDimensionException::class);
        new $class($other->getPatches());
    }

    /**
     * @param string $class Surface class
     *
     * @phpstan-param class-string<AbstractPolyhedralSurface> $class
     */
    #[DataProvider('surfaces')]
    public function testRejectsMixedFamily(string $class): void
    {
        $otherClass = str_contains($class, 'Geography') ? str_replace('Geography', 'Geometry', $class) : str_replace('Geometry', 'Geography', $class);
        $other = new $otherClass(self::coordinates($class));
        static::assertInstanceOf(AbstractPolyhedralSurface::class, $other);
        $this->expectException(InvalidFamilyException::class);
        new $class($other->getPatches());
    }

    /**
     * @param string $class Surface class
     *
     * @phpstan-param class-string<AbstractPolyhedralSurface> $class
     */
    #[DataProvider('surfaces')]
    public function testRejectsMixedReference(string $class): void
    {
        $surface = new $class(self::coordinates($class), SpatialReference::epsg(4326));
        $this->expectException(InvalidSridException::class);
        new $class($surface->getPatches(), 4326);
    }

    /**
     * @param string $class Surface class
     *
     * @phpstan-param class-string<AbstractPolyhedralSurface> $class
     */
    #[DataProvider('surfaces')]
    public function testReplacementsPreserveReferenceAuthority(string $class): void
    {
        $coordinates = self::coordinates($class);
        $reference = SpatialReference::epsg(4326);
        $surface = new $class($coordinates, $reference);
        foreach ([$surface->withPatch(0, $coordinates[0]), $surface->withRing(0, 0, $coordinates[0][0])] as $copy) {
            foreach ($copy->getPatches() as $patch) {
                static::assertTrue($reference->equals($patch->getSpatialReference()));
                static::assertTrue($reference->equals($patch->getRing(0)->getPoint(0)->getSpatialReference()));
            }
        }
    }

    /** @return iterable<string, array{class-string<AbstractPolyhedralSurface>}> */
    public static function surfaces(): iterable
    {
        yield 'Dimension3z Geometry' => [PolyhedralSurface::class];

        yield 'Dimension3z Geography' => [\LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\PolyhedralSurface::class];

        yield 'Dimension4zm Geometry' => [\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\PolyhedralSurface::class];

        yield 'Dimension4zm Geography' => [\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\PolyhedralSurface::class];
    }
}
