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

namespace LongitudeOne\SpatialTypes\Tests\Unit\Types\Dimension2\Geometry;

use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Exception\InvalidFamilyException;
use LongitudeOne\SpatialTypes\Exception\InvalidSridException;
use LongitudeOne\SpatialTypes\Exception\OutOfBoundsException;
use LongitudeOne\SpatialTypes\Exception\SpatialTypeExceptionInterface;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\Polygon as GeographicPolygon;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineString;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiPolygon;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\Point;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\Polygon;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\Polygon as Polygon3D;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for the two-dimensional geometric multi-polygon.
 *
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractMultiPolygon
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractSpatialType
 * @covers \LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiPolygon
 */
class MultiPolygonTest extends TestCase
{
    /**
     * Test that an exception is thrown when we add a geographic linestring in a geometric polygon.
     */
    public function testAddGeographicPolygonInGeometricMultiPolygon(): void
    {
        $polygon = new GeographicPolygon([], 4326);
        $multiPolygon = new MultiPolygon([], 4326);
        static::assertTrue($multiPolygon->isEmpty());
        static::expectException(InvalidFamilyException::class);
        static::expectExceptionMessageIsOrContains('The polygon family is not compatible with the family of the current multipolygon.');
        $multiPolygon->addPolygon($polygon);
    }

    /**
     * Test that a polygon with a different dimension cannot be added.
     */
    public function testAddPolygonWithInvalidDimension(): void
    {
        $polygon = new Polygon3D([]);

        self::expectException(InvalidDimensionException::class);
        self::expectExceptionMessageIsOrContains('The polygon is not compatible with the dimension of the current multipolygon.');

        (new MultiPolygon([]))->addPolygon($polygon);
    }

    /**
     * Test the constructor with array of cartesian coordinates.
     *
     * @throws SpatialTypeExceptionInterface test shall not throw any exception
     */
    public function testConstructorWithCartesianCoordinates(): void
    {
        $multiPolygon = new MultiPolygon([[[[1, 2], [3, 4], [3, 6], [1, 2]]]]);
        static::assertFalse($multiPolygon->isEmpty());
        static::assertCount(1, $multiPolygon->getPolygons());
        static::assertSame([[[[1, 2], [3, 4], [3, 6], [1, 2]]]], $multiPolygon->toArray());

        $multiPolygon->addPolygon([[[3, 4], [997, 997], [992, 811], [3, 4]]]);
        static::assertFalse($multiPolygon->isEmpty());
        static::assertCount(2, $multiPolygon->getPolygons());
        static::assertSame([[[[1, 2], [3, 4], [3, 6], [1, 2]]], [[[3, 4], [997, 997], [992, 811], [3, 4]]]], $multiPolygon->toArray());
    }

    /**
     * Test the constructor with geodesic points.
     *
     * @throws SpatialTypeExceptionInterface test shall not throw any exception
     */
    public function testConstructorWithGeodesicPoints(): void
    {
        $multiPolygon = new MultiPolygon([[[['10N', '20W'], ['15N', '12W'], ['23S', '7E'], ['10N', '20W']]]]);
        static::assertCount(1, $multiPolygon->getPolygons());
        static::assertSame([[[[10, -20], [15, -12], [-23, 7], [10, -20]]]], $multiPolygon->toArray());

        $multiPolygon->addPolygon([[['10S', '20E'], ['15S', '12E'], ['23N', '7W'], ['10S', '20E']]]);
        static::assertCount(2, $multiPolygon->getPolygons());
        static::assertSame([[[[10, -20], [15, -12], [-23, 7], [10, -20]]], [[[-10, 20], [-15, 12], [23, -7], [-10, 20]]]], $multiPolygon->toArray());
    }

    /**
     * Test the constructor with points and addPolygon method.
     *
     * @throws SpatialTypeExceptionInterface test shall not throw any exception
     */
    public function testConstructorWithPoints(): void
    {
        $multiPolygon = new MultiPolygon([new Polygon([new LineString([new Point(1, 2), new Point(2, 4), new Point(3, 6), new Point(1, 2)])])], 4326);
        static::assertCount(1, $multiPolygon->getPolygons());
        static::assertSame([[[[1, 2], [2, 4], [3, 6], [1, 2]]]], $multiPolygon->toArray());

        $multiPolygon->addPolygon(new Polygon([new LineString([new Point(3, 4), new Point(7, 7), new Point(2, 11), new Point(3, 4)])]));
        static::assertCount(2, $multiPolygon->getPolygons());
        static::assertSame([[[[1, 2], [2, 4], [3, 6], [1, 2]]], [[[3, 4], [7, 7], [2, 11], [3, 4]]]], $multiPolygon->toArray());

        self::expectException(InvalidSridException::class);
        self::expectExceptionMessageIsOrContains('The polygon SRID is not compatible with the SRID of the current multipolygon.');
        $multiPolygon->addPolygon(new Polygon([new LineString([[0, 0], [1, 1], [1, 0], [0, 0]])], 4327));
    }

    /**
     * Test the empty constructor.
     *
     * @throws SpatialTypeExceptionInterface test shall not throw any exception
     */
    public function testEmptyConstructor(): void
    {
        $multiPolygon = new MultiPolygon([]);
        static::assertTrue($multiPolygon->isEmpty());
        static::assertEmpty($multiPolygon->getPolygons());
        static::assertEmpty($multiPolygon->getElements());
        static::assertSame([], $multiPolygon->toArray());
        static::assertSame([], $multiPolygon->getPolygons());

        static::expectException(OutOfBoundsException::class);
        static::expectExceptionMessageIsOrContains('The current collection of polygons is empty.');
        $multiPolygon->getPolygon(0);
    }

    /**
     * Test the element getter.
     */
    public function testGetElements(): void
    {
        $multiPolygon = new MultiPolygon([[[[1, 2], [3, 4], [3, 6], [1, 2]]]]);
        static::assertSame($multiPolygon->getElements(), $multiPolygon->getPolygons());
    }

    /**
     * Test the getPolygon method.
     *
     * @throws SpatialTypeExceptionInterface test shall not throw any exception
     */
    public function testGetPolygon(): void
    {
        $polygonA = [[[1, 2], [2, 4], [3, 6], [1, 2]]];
        $polygonB = [[[3, 4], [7, 7], [2, 11], [3, 4]]];
        $multiPolygon = new MultiPolygon([$polygonA, $polygonB]);
        static::assertCount(2, $multiPolygon->getPolygons());
        static::assertSame($polygonA, $multiPolygon->getPolygon(0)->toArray());
        static::assertSame($polygonB, $multiPolygon->getPolygon(1)->toArray());
        static::assertSame($polygonA, $multiPolygon->getPolygon(-2)->toArray());
        static::assertSame($polygonB, $multiPolygon->getPolygon(-1)->toArray());
    }

    /**
     * Test the json serialize.
     */
    public function testJsonSerialize(): void
    {
        $multiPolygon = new MultiPolygon([[[[1, 2], [3, 4], [3, 6], [1, 2]]]]);
        static::assertSame('{"type":"MultiPolygon","coordinates":[[[[1,2],[3,4],[3,6],[1,2]]]],"srid":0}', json_encode($multiPolygon));
        $multiPolygon = new MultiPolygon([[[[1, 2], [3, 4], [3, 6], [1, 2]]]], 4326);
        static::assertSame('{"type":"MultiPolygon","coordinates":[[[[1,2],[3,4],[3,6],[1,2]]]],"srid":4326}', json_encode($multiPolygon));
    }
}
