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

namespace LongitudeOne\SpatialTypes\Tests\Unit\Factory;

use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Enum\DimensionEnum;
use LongitudeOne\SpatialTypes\Factory\SpatialContext;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Factory\SpatialContext
 */
class SpatialContextTest extends TestCase
{
    /**
     * Test a customized spatial context.
     */
    public function testCustomValues(): void
    {
        $context = new SpatialContext(4326, SpatialModelEnum::GEOGRAPHY, DimensionEnum::X_Y_Z_M);

        static::assertSame(4326, $context->srid);
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $context->family);
        static::assertSame(DimensionEnum::X_Y_Z_M, $context->dimension);
    }

    /**
     * Test the default spatial context.
     */
    public function testDefaultValues(): void
    {
        $context = new SpatialContext();

        static::assertSame(0, $context->srid);
        static::assertSame(0, $context->reference->srid());
        static::assertSame(SpatialModelEnum::GEOMETRY, $context->family);
        static::assertSame(DimensionEnum::X_Y, $context->dimension);
    }
}
