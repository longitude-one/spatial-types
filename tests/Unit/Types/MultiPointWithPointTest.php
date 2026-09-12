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

namespace LongitudeOne\SpatialTypes\Tests\Unit\Types;

use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\MultiPoint;
use LongitudeOne\SpatialTypes\Value\Coordinates;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractMultiPoint
 */
class MultiPointWithPointTest extends TestCase
{
    /**
     * Verify that a multi-point rejects a replacement coordinate from another dimension.
     */
    public function testWithPointRejectsAnotherDimension(): void
    {
        $multiPoint = new MultiPoint([[1, 2, 3]], 2154);

        self::expectException(InvalidDimensionException::class);

        $multiPoint->withPoint(0, Coordinates::xy(4, 5));
    }

    /**
     * Verify that a multi-point replaces one point in a deep immutable copy.
     */
    public function testWithPointReturnsAnIndependentCopy(): void
    {
        $multiPoint = new MultiPoint([[1, 2, 3], [4, 5, 6]], 2154);
        $replacement = $multiPoint->withPoint(-1, Coordinates::xym(7, 8, 9));

        static::assertNotSame($multiPoint, $replacement);
        static::assertSame($multiPoint::class, $replacement::class);
        static::assertSame(2154, $replacement->getSrid());
        static::assertTrue($replacement->hasM());
        static::assertFalse($replacement->hasZ());
        static::assertSame([[1, 2, 3], [4, 5, 6]], $multiPoint->toArray());
        static::assertSame([[1, 2, 3], [7, 8, 9]], $replacement->toArray());
        static::assertNotSame($multiPoint->getPoint(0), $replacement->getPoint(0));
        static::assertNotSame($multiPoint->getPoint(1), $replacement->getPoint(1));
    }
}
