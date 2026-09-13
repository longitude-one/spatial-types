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

namespace LongitudeOne\SpatialTypes\Tests\Issue\Issue2;

use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\Point;
use PHPUnit\Framework\TestCase;

/**
 * Regression tests for issue #2: negative geodesic coordinates.
 *
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractPoint
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractSpatialType
 * @covers \LongitudeOne\SpatialTypes\Types\Dimension2\Geography\Point
 */
class PointTest extends TestCase
{
    /**
     * Regression test for issue #2: accept a negative south latitude.
     */
    public function testConstructsNegativeSouthLatitude(): void
    {
        static::assertSame(-40, (new Point(0, '-40S'))->getLatitude());
    }

    /**
     * Regression test for issue #2: accept a negative west longitude.
     */
    public function testConstructsNegativeWestLongitude(): void
    {
        static::assertSame(-40, (new Point('-40W', 0))->getLongitude());
    }
}
