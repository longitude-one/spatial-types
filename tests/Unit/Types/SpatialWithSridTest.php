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

use LongitudeOne\SpatialTypes\Interfaces\CollectionInterface;
use LongitudeOne\SpatialTypes\Interfaces\LineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\PointInterface;
use LongitudeOne\SpatialTypes\Interfaces\PolygonInterface;
use LongitudeOne\SpatialTypes\Interfaces\SpatialInterface;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\GeometryCollection;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineString;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiLineString;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiPoint;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiPolygon;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\Point;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\Polygon;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractCollection
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractMultiLineString
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractMultiPoint
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractMultiPolygon
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractPolygon
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractSpatialType
 */
class SpatialWithSridTest extends TestCase
{
    /**
     * Assert that an aggregate and every descendant were copied with SRID 2154.
     *
     * @param SpatialInterface $source original spatial object
     * @param SpatialInterface $copy   copied spatial object
     */
    private static function assertCopiedTree(SpatialInterface $source, SpatialInterface $copy): void
    {
        static::assertNotSame($source, $copy);
        static::assertSame($source::class, $copy::class);
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
     * @param SpatialInterface $spatial spatial object to inspect
     *
     * @return SpatialInterface[]
     */
    private static function childrenOf(SpatialInterface $spatial): array
    {
        return match (true) {
            $spatial instanceof PointInterface => [],
            $spatial instanceof LineStringInterface => $spatial->getPoints(),
            $spatial instanceof PolygonInterface => $spatial->getRings(),
            $spatial instanceof CollectionInterface => $spatial->getElements(),
            default => [],
        };
    }

    /**
     * Create a line string whose points share its spatial reference.
     */
    private static function createLineString(): LineString
    {
        return new LineString([
            new Point(1, 2, 4326),
            new Point(3, 4, 4326),
        ], 4326);
    }

    /**
     * Create a polygon whose boundary shares its spatial reference.
     */
    private static function createPolygon(): Polygon
    {
        return new Polygon([new LineString([
            new Point(0, 0, 4326),
            new Point(1, 0, 4326),
            new Point(0, 1, 4326),
            new Point(0, 0, 4326),
        ], 4326)], 4326);
    }

    /**
     * Verify that every nested spatial object receives the requested SRID.
     *
     * @param SpatialInterface $spatial spatial object to copy
     */
    #[DataProvider('provideSpatialTypes')]
    public function testWithSridCopiesAllNestedSpatialObjects(SpatialInterface $spatial): void
    {
        $spatialWithSrid = $spatial->withSrid(2154);

        static::assertSame(4326, $spatial->getSrid());
        self::assertCopiedTree($spatial, $spatialWithSrid);
    }

    /**
     * @return \Generator<string, array{0: SpatialInterface}, null, void>
     */
    public static function provideSpatialTypes(): \Generator
    {
        yield 'multipoint' => [new MultiPoint([
            new Point(1, 2, 4326),
            new Point(3, 4, 4326),
        ], 4326)];

        yield 'polygon' => [self::createPolygon()];

        yield 'multi-line string' => [new MultiLineString([self::createLineString()], 4326)];

        yield 'multi-polygon' => [new MultiPolygon([self::createPolygon()], 4326)];

        $collection = new GeometryCollection(4326, [self::createPolygon()]);

        yield 'geometry collection' => [$collection];
    }
}
