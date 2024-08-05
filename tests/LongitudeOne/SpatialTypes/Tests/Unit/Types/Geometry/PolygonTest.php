<?php
/**
 * This file is part of the spatial project.
 *
 * PHP 8.1 | 8.2 | 8.3
 *
 * Copyright Alexandre Tranchant <alexandre.tranchant@gmail.com> 2024
 * Copyright Longitude One 2024
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

declare(strict_types=1);

namespace LongitudeOne\SpatialTypes\Tests\Unit\Types\Geometry;

use LongitudeOne\SpatialTypes\Enum\FamilyEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidFamilyException;
use LongitudeOne\SpatialTypes\Exception\InvalidSridException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\OutOfBoundsException;
use LongitudeOne\SpatialTypes\Exception\SpatialTypeExceptionInterface;
use LongitudeOne\SpatialTypes\Types\Geography\LineString as GeographicLineString;
use LongitudeOne\SpatialTypes\Types\Geometry\LineString;
use LongitudeOne\SpatialTypes\Types\Geometry\Point;
use LongitudeOne\SpatialTypes\Types\Geometry\Polygon;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests of geometric line string.
 *
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Trait\LineStringTrait
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractPolygon
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractSpatialType
 * @covers \LongitudeOne\SpatialTypes\Types\Geometry\Polygon
 */
class PolygonTest extends TestCase
{
    /**
     * Test that an exception is thrown when we add a geographic linestring in a geometric polygon.
     */
    public function testAddGeographicLineStringInGeometricPolygon(): void
    {
        $ring = static::createMock(GeographicLineString::class);
        $ring->method('isRing')->willReturn(true);
        $ring->method('getFamily')->willReturn(FamilyEnum::GEOGRAPHY);
        $polygon = new Polygon([], 4326);
        static::expectException(InvalidFamilyException::class);
        static::expectExceptionMessage('The ring family is not compatible with the family of the current polygon.');
        $polygon->addRing($ring);
    }

    /**
     * Test the addRing with a non-closed LineString.
     */
    public function testAddRingWithNonClosedLineString(): void
    {
        $multiLineString = new Polygon([]);
        $lineString = new LineString([new Point(1, 2), new Point(2, 4), new Point(3, 6)]);
        static::expectException(InvalidValueException::class);
        static::expectExceptionMessage('The line string is not a ring.');
        $multiLineString->addRing($lineString);
    }

    /**
     * Test the constructor with array of cartesian coordinates.
     *
     * @throws SpatialTypeExceptionInterface test shall not throw any exception
     */
    public function testConstructorWithCartesianCoordinates(): void
    {
        $polygon = new Polygon([[[1, 2], [3, 4], [3, 6], [1, 2]]]);
        static::assertCount(1, $polygon->getRings());
        static::assertEquals([[[1, 2], [3, 4], [3, 6], [1, 2]]], $polygon->toArray());

        $polygon->addRing([[3, 4], [997, 997], [992, 811], [3, 4]]);
        static::assertCount(2, $polygon->getRings());
        static::assertEquals([[[1, 2], [3, 4], [3, 6], [1, 2]], [[3, 4], [997, 997], [992, 811], [3, 4]]], $polygon->toArray());
    }

    /**
     * Test the constructor with geodesic points.
     *
     * @throws SpatialTypeExceptionInterface test shall not throw any exception
     */
    public function testConstructorWithGeodesicPoints(): void
    {
        $lineString = new LineString([new Point('40W', '40S', 4326), new Point('45W', '45N', 4326)], 4326);
        static::assertCount(2, $lineString->getPoints());
        static::assertFalse($lineString->isClosed());
        static::assertFalse($lineString->isRing());
        static::assertTrue($lineString->isLine());
        static::assertEquals([[-40, -40], [-45, 45]], $lineString->toArray());

        $lineString->addPoint(new Point('40W', '40S', 4326));
        static::assertCount(3, $lineString->getPoints());
        static::assertTrue($lineString->isLine());
        static::assertTrue($lineString->isClosed());
        static::assertTrue($lineString->isRing());
        static::assertEquals([[-40, -40], [-45, 45], [-40, -40]], $lineString->toArray());
    }

    /**
     * Test the constructor with points and addRing method.
     *
     * @throws SpatialTypeExceptionInterface test shall not throw any exception
     */
    public function testConstructorWithPoints(): void
    {
        $polygon = new Polygon([new LineString([new Point(1, 2), new Point(2, 4), new Point(3, 6), new Point(1, 2)])], 4326);
        static::assertCount(1, $polygon->getRings());
        static::assertEquals([[[1, 2], [2, 4], [3, 6], [1, 2]]], $polygon->toArray());

        $polygon->addRing(new LineString([new Point(3, 4), new Point(7, 7), new Point(2, 11), new Point(3, 4)]));
        static::assertCount(2, $polygon->getRings());
        static::assertEquals([[[1, 2], [2, 4], [3, 6], [1, 2]], [[3, 4], [7, 7], [2, 11], [3, 4]]], $polygon->toArray());

        self::expectException(InvalidSridException::class);
        self::expectExceptionMessage('The point SRID is not compatible with the SRID of this current spatial collection.');
        $polygon->addRing(new LineString([[0, 0], [1, 1], [1, 0], [0, 0]], 4327));
    }

    /**
     * Test the empty constructor.
     *
     * @throws SpatialTypeExceptionInterface test shall not throw any exception
     */
    public function testEmptyConstructor(): void
    {
        $polygon = new Polygon([]);
        static::assertEmpty($polygon->getRings());
        static::assertEmpty($polygon->getElements());
        static::assertEquals([], $polygon->toArray());
        static::assertEquals([], $polygon->getRings());

        static::expectException(OutOfBoundsException::class);
        static::expectExceptionMessage('The current collection of rings is empty.');
        $polygon->getRing(0);
    }

    /**
     * Test the element getter.
     */
    public function testGetElements(): void
    {
        $polygon = new Polygon([[[1, 2], [3, 4], [3, 6], [1, 2]]]);
        static::assertSame($polygon->getElements(), $polygon->getRings());
    }

    /**
     * Test the getRing method.
     *
     * @throws SpatialTypeExceptionInterface test shall not throw any exception
     */
    public function testGetRing(): void
    {
        $lineA = [[1, 2], [2, 4], [3, 6], [1, 2]];
        $lineB = [[3, 4], [7, 7], [2, 11], [3, 4]];
        $polygon = new Polygon([$lineA, $lineB]);
        static::assertCount(2, $polygon->getRings());
        static::assertSame($lineA, $polygon->getRing(0)->toArray());
        static::assertSame($lineB, $polygon->getRing(1)->toArray());
        static::assertSame($lineA, $polygon->getRing(-2)->toArray());
        static::assertSame($lineB, $polygon->getRing(-1)->toArray());
    }

    /**
     * Test the json serialize.
     */
    public function testJsonSerialize(): void
    {
        $polygon = new Polygon([[[1, 2], [3, 4], [3, 6], [1, 2]]]);
        static::assertSame('{"type":"Polygon","coordinates":[[[1,2],[3,4],[3,6],[1,2]]],"srid":null}', json_encode($polygon));
        $polygon = new Polygon([[[1, 2], [3, 4], [3, 6], [1, 2]]], 4326);
        static::assertSame('{"type":"Polygon","coordinates":[[[1,2],[3,4],[3,6],[1,2]]],"srid":4326}', json_encode($polygon));
    }
}
