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

use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiLineString;
use LongitudeOne\SpatialTypes\Value\Coordinates;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractMultiLineString
 */
class MultiLineStringWithPointTest extends TestCase
{
    /**
     * Verify that one point is replaced in a deep copy of a multi-line string.
     */
    public function testWithPointReturnsAnIndependentCopy(): void
    {
        $multiLineString = new MultiLineString([
            [[0, 0], [1, 1]],
            [[2, 2], [3, 3]],
        ], 2154);
        $replacement = $multiLineString->withPoint(-1, -1, Coordinates::xy(4, 4));

        static::assertNotSame($multiLineString, $replacement);
        static::assertSame($multiLineString::class, $replacement::class);
        static::assertSame(2154, $replacement->getSrid());
        static::assertSame([[[0, 0], [1, 1]], [[2, 2], [3, 3]]], $multiLineString->toArray());
        static::assertSame([[[0, 0], [1, 1]], [[2, 2], [4, 4]]], $replacement->toArray());
        static::assertNotSame($multiLineString->getLineString(0), $replacement->getLineString(0));
        static::assertNotSame($multiLineString->getLineString(1), $replacement->getLineString(1));
    }
}
