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

use LongitudeOne\SpatialTypes\Exception\InvalidSridException;
use LongitudeOne\SpatialTypes\Exception\OutOfBoundsException;
use LongitudeOne\SpatialTypes\Types\Geometry\LineString;
use LongitudeOne\SpatialTypes\Types\Geometry\Point;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests of geometric line string.
 *
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractLineString
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractSpatialType
 * @covers \LongitudeOne\SpatialTypes\Types\Geometry\LineString
 */
class LineStringTest extends TestCase
{
    /**
     * Test the addPoint method.
     */
    public function testAddPoint(): void
    {
        $lineString = new LineString([], 4326);
        $lineString->addPoint(new Point(1, 2));
        $lineString->addPoint([3, 4]);
        static::assertCount(2, $lineString->getPoints());

        self::expectException(InvalidSridException::class);
        self::expectExceptionMessage('The point SRID is not compatible with the SRID of this current spatial collection.');
        $lineString->addPoint(new Point(1, 2, 4327));
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

        $lineString->addPoint(new Point(1, 2));
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

        $lineString->addPoint(new Point('40W', '40S', 4326));
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
        self::expectExceptionMessage('The current collection of points is empty.');
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
        static::assertSame('{"type":"LineString","coordinates":[[1,2],[3,4]],"srid":null}', json_encode($lineString));
    }
}
