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

use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\Point;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class SridDefaultTest extends TestCase
{
    /**
     * SQL/MM's default constructor SRID is represented as a concrete value.
     */
    public function testSpatialObjectsUseTheUnnamedReference(): void
    {
        $point = new Point(1, 2);

        static::assertSame(0, $point->getSrid());
        static::assertSame(0, $point->getSpatialReference()->srid());
        static::assertSame(0, $point->jsonSerialize()['srid']);
    }
}
