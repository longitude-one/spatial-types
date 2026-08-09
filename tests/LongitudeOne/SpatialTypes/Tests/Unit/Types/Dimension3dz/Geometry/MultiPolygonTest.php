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

namespace LongitudeOne\SpatialTypes\Tests\Unit\Types\Dimension3z\Geometry;

use LongitudeOne\SpatialTypes\Enum\FamilyEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Exception\InvalidFamilyException;
use LongitudeOne\SpatialTypes\Exception\InvalidSridException;
use LongitudeOne\SpatialTypes\Exception\OutOfBoundsException;
use LongitudeOne\SpatialTypes\Exception\SpatialTypeExceptionInterface;
use LongitudeOne\SpatialTypes\Interfaces\PolygonInterface;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\Polygon as GeographicPolygon;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\LineString;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\MultiPolygon;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\Point;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\Polygon;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for the three-dimensional geometric multi-polygon.
 *
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractMultiPolygon
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractSpatialType
 * @covers \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\MultiPolygon
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
        $polygon = static::createStub(PolygonInterface::class);
        $polygon->method('getFamily')->willReturn(FamilyEnum::GEOMETRY);
        $polygon->method('getSrid')->willReturn(0);
        $polygon->method('hasSameDimension')->willReturn(false);

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
        $multiPolygon = new MultiPolygon([[[[1, 2, 3], [3, 4, 5], [3, 6, 7], [1, 2, 3]]]]);
        static::assertFalse($multiPolygon->isEmpty());
        static::assertCount(1, $multiPolygon->getPolygons());
        static::assertSame([[[[1, 2, 3], [3, 4, 5], [3, 6, 7], [1, 2, 3]]]], $multiPolygon->toArray());

        $multiPolygon->addPolygon([[[3, 4, 5], [997, 997, 6], [992, 811, 7], [3, 4, 5]]]);
        static::assertFalse($multiPolygon->isEmpty());
        static::assertCount(2, $multiPolygon->getPolygons());
        static::assertSame([[[[1, 2, 3], [3, 4, 5], [3, 6, 7], [1, 2, 3]]], [[[3, 4, 5], [997, 997, 6], [992, 811, 7], [3, 4, 5]]]], $multiPolygon->toArray());
    }

    /**
     * Test the constructor with geodesic points.
     *
     * @throws SpatialTypeExceptionInterface test shall not throw any exception
     */
    public function testConstructorWithGeodesicPoints(): void
    {
        $multiPolygon = new MultiPolygon([[[['10N', '20W', 0], ['15N', '12W', 0], ['23S', '7E', 0], ['10N', '20W', 0]]]]);
        static::assertCount(1, $multiPolygon->getPolygons());
        static::assertSame([[[[10, -20, 0], [15, -12, 0], [-23, 7, 0], [10, -20, 0]]]], $multiPolygon->toArray());

        $multiPolygon->addPolygon([[['10S', '20E', 0], ['15S', '12E', 0], ['23N', '7W', 0], ['10S', '20E', 0]]]);
        static::assertCount(2, $multiPolygon->getPolygons());
        static::assertSame([[[[10, -20, 0], [15, -12, 0], [-23, 7, 0], [10, -20, 0]]], [[[-10, 20, 0], [-15, 12, 0], [23, -7, 0], [-10, 20, 0]]]], $multiPolygon->toArray());
    }

    /**
     * Test the constructor with points and addPolygon method.
     *
     * @throws SpatialTypeExceptionInterface test shall not throw any exception
     */
    public function testConstructorWithPoints(): void
    {
        $multiPolygon = new MultiPolygon([new Polygon([new LineString([new Point(1, 2, 3), new Point(2, 4, 5), new Point(3, 6, 7), new Point(1, 2, 3)])])], 4326);
        static::assertCount(1, $multiPolygon->getPolygons());
        static::assertSame([[[[1, 2, 3], [2, 4, 5], [3, 6, 7], [1, 2, 3]]]], $multiPolygon->toArray());

        $multiPolygon->addPolygon(new Polygon([new LineString([new Point(3, 4, 5), new Point(7, 7, 6), new Point(2, 11, 7), new Point(3, 4, 5)])]));
        static::assertCount(2, $multiPolygon->getPolygons());
        static::assertSame([[[[1, 2, 3], [2, 4, 5], [3, 6, 7], [1, 2, 3]]], [[[3, 4, 5], [7, 7, 6], [2, 11, 7], [3, 4, 5]]]], $multiPolygon->toArray());

        self::expectException(InvalidSridException::class);
        self::expectExceptionMessageIsOrContains('The polygon SRID is not compatible with the SRID of the current multipolygon.');
        $multiPolygon->addPolygon(new Polygon([new LineString([[0, 0, 0], [1, 1, 0], [1, 0, 0], [0, 0, 0]])], 4327));
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
        $multiPolygon = new MultiPolygon([[[[1, 2, 3], [3, 4, 5], [3, 6, 7], [1, 2, 3]]]]);
        static::assertSame($multiPolygon->getElements(), $multiPolygon->getPolygons());
    }

    /**
     * Test the getPolygon method.
     *
     * @throws SpatialTypeExceptionInterface test shall not throw any exception
     */
    public function testGetPolygon(): void
    {
        $polygonA = [[[1, 2, 3], [2, 4, 5], [3, 6, 7], [1, 2, 3]]];
        $polygonB = [[[3, 4, 5], [7, 7, 6], [2, 11, 7], [3, 4, 5]]];
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
        $multiPolygon = new MultiPolygon([[[[1, 2, 3], [3, 4, 5], [3, 6, 7], [1, 2, 3]]]]);
        static::assertSame('{"type":"MultiPolygon","coordinates":[[[[1,2,3],[3,4,5],[3,6,7],[1,2,3]]]],"srid":0}', json_encode($multiPolygon));
        $multiPolygon = new MultiPolygon([[[[1, 2, 3], [3, 4, 5], [3, 6, 7], [1, 2, 3]]]], 4326);
        static::assertSame('{"type":"MultiPolygon","coordinates":[[[[1,2,3],[3,4,5],[3,6,7],[1,2,3]]]],"srid":4326}', json_encode($multiPolygon));
    }
}
