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

namespace LongitudeOne\SpatialTypes\Tests\Unit\Types\Dimension3z\Geography;

use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\LineString;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\Point;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests of geometric line string.
 *
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractLineString
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractSpatialType
 * @covers \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\LineString
 */
class LineStringTest extends TestCase
{
    /**
     * Test the constructor with points.
     */
    public function testConstructorWithCartesianPoints(): void
    {
        $lineString = new LineString([new Point(1, 2, 3), new Point(3, 4, 5)]);
        static::assertCount(2, $lineString->getPoints());
        static::assertFalse($lineString->isClosed());
        static::assertFalse($lineString->isRing());
        static::assertTrue($lineString->isLine());
        static::assertEquals([[1, 2, 3], [3, 4, 5]], $lineString->toArray());

        $lineString->addPoint(new Point(1, 2, 3));
        static::assertCount(3, $lineString->getPoints());
        static::assertTrue($lineString->isClosed());
        static::assertTrue($lineString->isRing());
        static::assertTrue($lineString->isLine());
        static::assertEquals([[1, 2, 3], [3, 4, 5], [1, 2, 3]], $lineString->toArray());
    }

    /**
     * Test the constructor with geodesic points.
     */
    public function testConstructorWithGeodesicPoints(): void
    {
        $lineString = new LineString([new Point('40W', '40S', 5, 4326), new Point('45W', '45N', 32.6, 4326)], 4326);
        static::assertCount(2, $lineString->getPoints());
        static::assertFalse($lineString->isClosed());
        static::assertFalse($lineString->isRing());
        static::assertTrue($lineString->isLine());
        static::assertEquals([[-40, -40, 5], [-45, 45, 32.6]], $lineString->toArray());

        $lineString->addPoint(new Point('40W', '40S', 5, 4326));
        static::assertCount(3, $lineString->getPoints());
        static::assertTrue($lineString->isLine());
        static::assertTrue($lineString->isClosed());
        static::assertTrue($lineString->isRing());
        static::assertEquals([[-40, -40, 5], [-45, 45, 32.6], [-40, -40, 5]], $lineString->toArray());
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
     * Test out of range constructor.
     */
    public function testOutOfRangeConstructor(): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessageIsOrContains('Out of range longitude value, longitude must be between -180 and 180, got "181".');
        new LineString([[181, 0, 3]]);
    }
}
