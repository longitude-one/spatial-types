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

use LongitudeOne\SpatialTypes\Interfaces\ImmutableCollectionInterface;
use LongitudeOne\SpatialTypes\Interfaces\ImmutableLineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\ImmutablePolygonInterface;
use LongitudeOne\SpatialTypes\Interfaces\ImmutableSpatialInterface;
use LongitudeOne\SpatialTypes\Interfaces\PointInterface;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\GeometryCollectionImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineStringImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiLineStringImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiPointImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiPolygonImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PointImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PolygonImmutable;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Internal\Types\AbstractCollectionImmutable
 * @covers \LongitudeOne\SpatialTypes\Internal\Types\AbstractMultiLineStringImmutable
 * @covers \LongitudeOne\SpatialTypes\Internal\Types\AbstractMultiPointImmutable
 * @covers \LongitudeOne\SpatialTypes\Internal\Types\AbstractMultiPolygonImmutable
 * @covers \LongitudeOne\SpatialTypes\Internal\Types\AbstractPolygonImmutable
 * @covers \LongitudeOne\SpatialTypes\Internal\Types\AbstractSpatialTypeImmutable
 */
class SpatialWithSridTest extends TestCase
{
    /**
     * Assert that an aggregate and every descendant were copied with SRID 2154.
     *
     * @param ImmutableSpatialInterface $source original spatial object
     * @param ImmutableSpatialInterface $copy   copied spatial object
     */
    private static function assertCopiedTree(ImmutableSpatialInterface $source, ImmutableSpatialInterface $copy): void
    {
        static::assertNotSame($source, $copy);
        static::assertSame($source::class, (new \ReflectionClass($copy))->getName());
        static::assertSame($source->toArray(), $copy->toArray());
        static::assertSame(2154, $copy->getSrid());

        $sourceChildren = self::childrenOf($source);
        $copyChildren = self::childrenOf($copy);
        static::assertCount(count($sourceChildren), $copyChildren);

        foreach ($sourceChildren as $index => $sourceChild) {
            self::assertCopiedTree($sourceChild, $copyChildren[$index]);
        }
    }

    /**
     * Return direct child spatial objects for a spatial aggregate.
     *
     * @param ImmutableSpatialInterface $spatial spatial object to inspect
     *
     * @return ImmutableSpatialInterface[]
     */
    private static function childrenOf(ImmutableSpatialInterface $spatial): array
    {
        return match (true) {
            $spatial instanceof PointInterface => [],
            $spatial instanceof ImmutableLineStringInterface => $spatial->getPoints(),
            $spatial instanceof ImmutablePolygonInterface => $spatial->getRings(),
            $spatial instanceof ImmutableCollectionInterface => $spatial->getElements(),
            default => [],
        };
    }

    /**
     * Create a line string whose points share its spatial reference.
     */
    private static function createLineString(): LineStringImmutable
    {
        return new LineStringImmutable([
            new PointImmutable(1, 2, 4326),
            new PointImmutable(3, 4, 4326),
        ], 4326);
    }

    /**
     * Create a polygon whose boundary shares its spatial reference.
     */
    private static function createPolygon(): PolygonImmutable
    {
        return new PolygonImmutable([new LineStringImmutable([
            new PointImmutable(0, 0, 4326),
            new PointImmutable(1, 0, 4326),
            new PointImmutable(0, 1, 4326),
            new PointImmutable(0, 0, 4326),
        ], 4326)], 4326);
    }

    /**
     * Verify that every nested spatial object receives the requested SRID.
     *
     * @param ImmutableSpatialInterface $spatial spatial object to copy
     */
    #[DataProvider('provideSpatialTypes')]
    public function testWithSridCopiesAllNestedSpatialObjects(ImmutableSpatialInterface $spatial): void
    {
        $spatialWithSrid = $spatial->withSrid(2154);

        static::assertSame(4326, $spatial->getSrid());
        self::assertCopiedTree($spatial, $spatialWithSrid);
    }

    /**
     * @return \Generator<string, array{0: ImmutableSpatialInterface}, null, void>
     */
    public static function provideSpatialTypes(): \Generator
    {
        yield 'multipoint' => [new MultiPointImmutable([
            new PointImmutable(1, 2, 4326),
            new PointImmutable(3, 4, 4326),
        ], 4326)];

        yield 'polygon' => [self::createPolygon()];

        yield 'multi-line string' => [new MultiLineStringImmutable([self::createLineString()], 4326)];

        yield 'multi-polygon' => [new MultiPolygonImmutable([self::createPolygon()], 4326)];

        $collection = new GeometryCollectionImmutable(4326, [self::createPolygon()]);

        yield 'geometry collection' => [$collection];
    }
}
