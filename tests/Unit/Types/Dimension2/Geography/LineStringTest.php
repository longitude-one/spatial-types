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

namespace LongitudeOne\SpatialTypes\Tests\Unit\Types\Dimension2\Geography;

use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\LineString;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\Point;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for the two-dimensional geographic line string.
 *
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractLineString
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractSpatialType
 * @covers \LongitudeOne\SpatialTypes\Types\Dimension2\Geography\LineString
 */
class LineStringTest extends TestCase
{
    /**
     * Test the constructor with points.
     */
    public function testConstructorWithCartesianPoints(): void
    {
        $lineString = new LineString([new Point(1, 2), new Point(3, 4)]);
        static::assertCount(2, $lineString->getPoints());
        static::assertFalse($lineString->isClosed());
        static::assertTrue($lineString->isLine());
        static::assertEquals([[1, 2], [3, 4]], $lineString->toArray());

        $lineString = new LineString([new Point(1, 2), new Point(3, 4), new Point(1, 2)]);
        static::assertCount(3, $lineString->getPoints());
        static::assertTrue($lineString->isClosed());
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
        static::assertTrue($lineString->isLine());
        static::assertEquals([[-40, -40], [-45, 45]], $lineString->toArray());

        $lineString = new LineString([new Point('40W', '40S', 4326), new Point('45W', '45N', 4326), new Point('40W', '40S', 4326)], 4326);
        static::assertCount(3, $lineString->getPoints());
        static::assertTrue($lineString->isLine());
        static::assertTrue($lineString->isClosed());
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
        static::assertFalse($lineString->isLine());
        static::assertEquals([], $lineString->toArray());
        static::assertEquals([], $lineString->getPoints());
    }

    /**
     * Test out of range constructor.
     */
    public function testOutOfRangeConstructor(): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessageIsOrContains('Out of range longitude value, longitude must be between -180 and 180, got "181".');
        new LineString([[181, 0]]);
    }
}
