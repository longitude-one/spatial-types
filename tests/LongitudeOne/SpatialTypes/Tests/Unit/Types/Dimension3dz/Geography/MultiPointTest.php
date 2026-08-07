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
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\MultiPoint;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\Point;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests of geometric line string.
 *
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractMultiPoint
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractSpatialType
 * @covers \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\MultiPoint
 */
class MultiPointTest extends TestCase
{
    /**
     * Test the constructor with points.
     */
    public function testConstructorWithCartesianPoints(): void
    {
        $multiPoint = new MultiPoint([new Point(1, 2, 3), new Point(3, 4, 5)]);
        static::assertCount(2, $multiPoint->getPoints());
        static::assertTrue($multiPoint->isSimple());
        static::assertFalse($multiPoint->isEmpty());
        static::assertEquals([[1, 2, 3], [3, 4, 5]], $multiPoint->toArray());

        $multiPoint->addPoint(new Point(1, 2, 3));
        static::assertCount(3, $multiPoint->getPoints());
        static::assertFalse($multiPoint->isSimple());
        static::assertEquals([[1, 2, 3], [3, 4, 5], [1, 2, 3]], $multiPoint->toArray());
    }

    /**
     * Test the constructor with geodesic points.
     */
    public function testConstructorWithGeodesicPoints(): void
    {
        $multiPoint = new MultiPoint([new Point('40W', '40S', 0, 4326), new Point('45W', '45N', 2, 4326)], 4326);
        static::assertCount(2, $multiPoint->getPoints());
        static::assertFalse($multiPoint->isEmpty());
        static::assertTrue($multiPoint->isSimple());
        static::assertEquals([[-40, -40, 0], [-45, 45, 2]], $multiPoint->toArray());

        $multiPoint->addPoint(new Point('40W', '40S', 0, 4326));
        static::assertCount(3, $multiPoint->getPoints());
        static::assertFalse($multiPoint->isSimple());
        static::assertFalse($multiPoint->isEmpty());
        static::assertEquals([[-40, -40, 0], [-45, 45, 2], [-40, -40, 0]], $multiPoint->toArray());
    }

    /**
     * Test the empty constructor.
     */
    public function testEmptyConstructor(): void
    {
        $multiPoint = new MultiPoint([]);
        static::assertEmpty($multiPoint->getPoints());
        static::assertTrue($multiPoint->isEmpty());
        static::assertTrue($multiPoint->isSimple());
        static::assertEquals([], $multiPoint->toArray());
        static::assertEquals([], $multiPoint->getPoints());
    }

    /**
     * Test out of range constructor.
     */
    public function testOutOfRangeConstructor(): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessageIsOrContains('Out of range longitude value, longitude must be between -180 and 180, got "181".');
        new MultiPoint([[181, 0, 5]]);
    }
}
