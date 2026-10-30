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

use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\LineStringImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\PointImmutable;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for the two-dimensional geographic line string.
 *
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Internal\Types\AbstractLineStringImmutable
 * @covers \LongitudeOne\SpatialTypes\Internal\Types\AbstractSpatialTypeImmutable
 * @covers \LongitudeOne\SpatialTypes\Types\Dimension2\Geography\LineStringImmutable
 */
class LineStringTest extends TestCase
{
    /**
     * Test the constructor with points.
     */
    public function testConstructorWithCartesianPoints(): void
    {
        $lineString = new LineStringImmutable([new PointImmutable(1, 2), new PointImmutable(3, 4)]);
        static::assertCount(2, $lineString->getPoints());
        static::assertFalse($lineString->isClosed());
        static::assertTrue($lineString->isLine());
        static::assertEquals([[1, 2], [3, 4]], $lineString->toArray());

        $lineString = new LineStringImmutable([new PointImmutable(1, 2), new PointImmutable(3, 4), new PointImmutable(1, 2)]);
        static::assertCount(3, $lineString->getPoints());
        static::assertTrue($lineString->isClosed());
        static::assertFalse($lineString->isLine());
        static::assertEquals([[1, 2], [3, 4], [1, 2]], $lineString->toArray());
    }

    /**
     * Test the constructor with geodesic points.
     */
    public function testConstructorWithGeodesicPoints(): void
    {
        $lineString = new LineStringImmutable([new PointImmutable('40W', '40S', 4326), new PointImmutable('45W', '45N', 4326)], 4326);
        static::assertCount(2, $lineString->getPoints());
        static::assertFalse($lineString->isClosed());
        static::assertTrue($lineString->isLine());
        static::assertEquals([[-40, -40], [-45, 45]], $lineString->toArray());

        $lineString = new LineStringImmutable([new PointImmutable('40W', '40S', 4326), new PointImmutable('45W', '45N', 4326), new PointImmutable('40W', '40S', 4326)], 4326);
        static::assertCount(3, $lineString->getPoints());
        static::assertFalse($lineString->isLine());
        static::assertTrue($lineString->isClosed());
        static::assertEquals([[-40, -40], [-45, 45], [-40, -40]], $lineString->toArray());
    }

    /**
     * Test the empty constructor.
     */
    public function testEmptyConstructor(): void
    {
        $lineString = new LineStringImmutable([]);
        static::assertEmpty($lineString->getPoints());
        static::assertFalse($lineString->isClosed());
        static::assertFalse($lineString->isLine());
        static::assertEquals([], $lineString->toArray());
        static::assertEquals([], $lineString->getPoints());
    }
}
