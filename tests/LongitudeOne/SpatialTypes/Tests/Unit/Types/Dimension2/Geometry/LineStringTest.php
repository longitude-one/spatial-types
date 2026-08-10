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
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\Point as GeographicPoint;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineString;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\Point;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\Point as Point3M;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests of geometric line string.
 *
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractLineString
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractSpatialType
 * @covers \LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineString
 */
class LineStringTest extends TestCase
{
    /**
     * Test that construction rejects a geographic point in a geometric line string.
     */
    public function testConstructorRejectsGeographicPoint(): void
    {
        static::expectException(InvalidFamilyException::class);
        static::expectExceptionMessageIsOrContains('The ');
        new LineString([new GeographicPoint('40W', '40S', 4326)], 4326);
    }

    /**
     * Test constructor SRID validation.
     */
    public function testConstructorRejectsIncompatibleSrid(): void
    {
        $lineString = new LineString([new Point(1, 2), [3, 4]], 4326);
        static::assertCount(2, $lineString->getPoints());

        self::expectException(InvalidSridException::class);
        self::expectExceptionMessageIsOrContains('The point SRID is not compatible with the SRID of this current spatial collection.');
        new LineString([new Point(1, 2, 4327)], 4326);
    }

    /**
     * Test that a point with a different dimension cannot be added to a line string.
     */
    public function testConstructorRejectsPointWithInvalidDimension(): void
    {
        $point = new Point3M(1, 2, 3);

        self::expectException(InvalidDimensionException::class);
        self::expectExceptionMessageIsOrContains('The point dimension is not compatible with the dimension of the current spatial collection.');

        new LineString([$point]);
    }

    /**
     * Test the constructor with points.
     */
    public function testConstructorWithCartesianPoints(): void
    {
        $lineString = new LineString([new Point(1, 2), new Point(3, 4)]);
        static::assertCount(2, $lineString->getPoints());
        static::assertFalse($lineString->isClosed());
        static::assertFalse($lineString->isRing());
        static::assertTrue($lineString->isLine());
        static::assertEquals([[1, 2], [3, 4]], $lineString->toArray());

        $lineString = new LineString([new Point(1, 2), new Point(3, 4), new Point(1, 2)]);
        static::assertCount(3, $lineString->getPoints());
        static::assertTrue($lineString->isClosed());
        static::assertTrue($lineString->isRing());
        static::assertTrue($lineString->isLine());
        static::assertEquals([[1, 2], [3, 4], [1, 2]], $lineString->toArray());
    }

    /**
     * Test the constructor with geodesic points.
     */
    public function testConstructorWithGeodesicPoints(): void
    {
        $lineString = new LineString([new Point('40W', '40S', 4326), new Point('45W', '45N', 4326)], 4326);
        static::assertCount(2, $lineString->getPoints());
        static::assertFalse($lineString->isClosed());
        static::assertFalse($lineString->isRing());
        static::assertTrue($lineString->isLine());
        static::assertEquals([[-40, -40], [-45, 45]], $lineString->toArray());

        $lineString = new LineString([new Point('40W', '40S', 4326), new Point('45W', '45N', 4326), new Point('40W', '40S', 4326)], 4326);
        static::assertCount(3, $lineString->getPoints());
        static::assertTrue($lineString->isLine());
        static::assertTrue($lineString->isClosed());
        static::assertTrue($lineString->isRing());
        static::assertEquals([[-40, -40], [-45, 45], [-40, -40]], $lineString->toArray());
    }

    /**
     * Test the empty constructor.
     */
    public function testEmptyConstructor(): void
    {
        $lineString = new LineString([]);
        static::assertEmpty($lineString->getPoints());
        static::assertFalse($lineString->isClosed());
        static::assertFalse($lineString->isRing());
        static::assertFalse($lineString->isLine());
        static::assertEquals([], $lineString->toArray());
        static::assertEquals([], $lineString->getPoints());
    }

    /**
     * Test the element getter.
     */
    public function testGetElements(): void
    {
        $lineString = new LineString([new Point(1, 2), new Point(3, 4)], 4326);
        static::assertSame($lineString->getElements(), $lineString->getPoints());
    }

    /**
     * Test the getPoint method.
     */
    public function testGetPoint(): void
    {
        $lineString = new LineString([new Point(1, 2), new Point(3, 4)]);
        static::assertCount(2, $lineString->getPoints());
        static::assertSame(1, $lineString->getPoint(0)->getX());
        static::assertSame(3, $lineString->getPoint(1)->getX());
        static::assertSame(1, $lineString->getPoint(2)->getX());
        static::assertSame(3, $lineString->getPoint(3)->getX());
        static::assertSame(1, $lineString->getPoint(4)->getX());

        static::assertSame(1, $lineString->getPoint(0)->getX());
        static::assertSame(3, $lineString->getPoint(-1)->getX());
        static::assertSame(1, $lineString->getPoint(-2)->getX());
        static::assertSame(3, $lineString->getPoint(-3)->getX());

        self::expectException(OutOfBoundsException::class);
        self::expectExceptionMessageIsOrContains('The current collection of points is empty.');
        (new LineString([]))->getPoint(0);
    }

    /**
     * Test the json serialize.
     */
    public function testJsonSerialize(): void
    {
        $lineString = new LineString([new Point('40W', '40S', 4326), new Point('45W', '45N', 4326)], 4326);
        static::assertSame('{"type":"LineString","coordinates":[[-40,-40],[-45,45]],"srid":4326}', json_encode($lineString));
        $lineString = new LineString([new Point(1, 2), new Point(3, 4)]);
        static::assertSame('{"type":"LineString","coordinates":[[1,2],[3,4]],"srid":0}', json_encode($lineString));
    }
}
