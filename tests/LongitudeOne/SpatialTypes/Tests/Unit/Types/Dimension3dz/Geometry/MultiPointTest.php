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

use LongitudeOne\SpatialTypes\Exception\InvalidSridException;
use LongitudeOne\SpatialTypes\Exception\OutOfBoundsException;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\MultiPoint;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\Point;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests of geometric multipoint.
 *
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractMultiPoint
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractSpatialType
 * @covers \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\MultiPoint
 */
class MultiPointTest extends TestCase
{
    /**
     * Test the addPoint method.
     */
    public function testAddPoint(): void
    {
        $multiPoint = new MultiPoint([], 4326);
        $multiPoint->addPoint(new Point(1, 2, 0));
        $multiPoint->addPoint([3, 4, 0]);
        static::assertCount(2, $multiPoint->getPoints());

        self::expectException(InvalidSridException::class);
        self::expectExceptionMessageIsOrContains('The point SRID is not compatible with the SRID of this current spatial collection.');
        $multiPoint->addPoint(new Point(1, 2, 0, 4327));
    }

    /**
     * Test the constructor with points.
     */
    public function testConstructorWithCartesianPoints(): void
    {
        $multiPoint = new MultiPoint([new Point(1, 2, 0), new Point(3, 4, 0)]);
        static::assertCount(2, $multiPoint->getPoints());
        static::assertTrue($multiPoint->isSimple());
        static::assertFalse($multiPoint->isEmpty());
        static::assertEquals([[1, 2, 0], [3, 4, 0]], $multiPoint->toArray());

        $multiPoint->addPoint(new Point(1, 2, 0));
        static::assertCount(3, $multiPoint->getPoints());
        static::assertFalse($multiPoint->isSimple());
        static::assertFalse($multiPoint->isEmpty());
        static::assertEquals([[1, 2, 0], [3, 4, 0], [1, 2, 0]], $multiPoint->toArray());
    }

    /**
     * Test the constructor with geodesic points.
     */
    public function testConstructorWithGeodesicPoints(): void
    {
        $multiPoint = new MultiPoint([new Point('40W', '40S', 0, 4326), new Point('45W', '45N', 0, 4326)], 4326);
        static::assertCount(2, $multiPoint->getPoints());
        static::assertTrue($multiPoint->isSimple());
        static::assertFalse($multiPoint->isEmpty());
        static::assertEquals([[-40, -40, 0], [-45, 45, 0]], $multiPoint->toArray());

        $multiPoint->addPoint(new Point('40W', '40S', 0, 4326));
        static::assertCount(3, $multiPoint->getPoints());
        static::assertFalse($multiPoint->isSimple());
        static::assertFalse($multiPoint->isEmpty());
        static::assertEquals([[-40, -40, 0], [-45, 45, 0], [-40, -40, 0]], $multiPoint->toArray());
    }

    /**
     * Test the empty constructor.
     */
    public function testEmptyConstructor(): void
    {
        $multiPoint = new MultiPoint([]);
        static::assertEmpty($multiPoint->getPoints());
        static::assertTrue($multiPoint->isSimple());
        static::assertTrue($multiPoint->isEmpty());
        static::assertEquals([], $multiPoint->toArray());
        static::assertEquals([], $multiPoint->getPoints());
    }

    /**
     * Test the element getter.
     */
    public function testGetElements(): void
    {
        $multiPoint = new MultiPoint([new Point(1, 2, 0), new Point(3, 4, 0)], 4326);
        static::assertSame($multiPoint->getElements(), $multiPoint->getPoints());
    }

    /**
     * Test the getPoint method.
     */
    public function testGetPoint(): void
    {
        $multiPoint = new MultiPoint([new Point(1, 2, 0), new Point(3, 4, 0)]);
        static::assertCount(2, $multiPoint->getPoints());
        static::assertSame(1, $multiPoint->getPoint(0)->getX());
        static::assertSame(3, $multiPoint->getPoint(1)->getX());
        static::assertSame(1, $multiPoint->getPoint(2)->getX());
        static::assertSame(3, $multiPoint->getPoint(3)->getX());
        static::assertSame(1, $multiPoint->getPoint(4)->getX());

        static::assertSame(1, $multiPoint->getPoint(0)->getX());
        static::assertSame(3, $multiPoint->getPoint(-1)->getX());
        static::assertSame(1, $multiPoint->getPoint(-2)->getX());
        static::assertSame(3, $multiPoint->getPoint(-3)->getX());

        self::expectException(OutOfBoundsException::class);
        self::expectExceptionMessageIsOrContains('The current collection of points is empty.');
        (new MultiPoint([]))->getPoint(0);
    }

    /**
     * Test the json serialize.
     */
    public function testJsonSerialize(): void
    {
        $multiPoint = new MultiPoint([new Point('40W', '40S', 0, 4326), new Point('45W', '45N', 0, 4326)], 4326);
        static::assertSame('{"type":"MultiPoint","coordinates":[[-40,-40,0],[-45,45,0]],"srid":4326}', json_encode($multiPoint));
        $multiPoint = new MultiPoint([new Point(1, 2, 0), new Point(3, 4, 0)]);
        static::assertSame('{"type":"MultiPoint","coordinates":[[1,2,0],[3,4,0]],"srid":null}', json_encode($multiPoint));
    }
}
