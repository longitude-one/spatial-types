<?php

/*
 * This file is part of the LongitudeOne Spatial project.
 *
 * Copyright (c) 2026 Alexandre Tranchant <alexandre.tranchant@gmail.com>
 *
 * See LICENSE for copyright and license information.
 *
 */

declare(strict_types=1);

namespace LongitudeOne\SpatialTypes\Tests\Unit\Factory;

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Internal\Factory\SpatialContext;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Internal\Factory\SpatialContext
 */
class SpatialContextTest extends TestCase
{
    /**
     * Test a customized spatial context.
     */
    public function testCustomValues(): void
    {
        $context = new SpatialContext(4326, SpatialModelEnum::GEOGRAPHY, CoordinateDimensionEnum::XYZM);

        self::assertSame(4326, $context->srid);
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $context->family);
        self::assertSame(CoordinateDimensionEnum::XYZM, $context->dimension);
    }

    /**
     * Test the default spatial context.
     */
    public function testDefaultValues(): void
    {
        $context = new SpatialContext();

        self::assertSame(0, $context->srid);
        self::assertSame(0, $context->reference->srid());
        self::assertSame(SpatialModelEnum::GEOMETRY, $context->family);
        self::assertSame(CoordinateDimensionEnum::XY, $context->dimension);
    }
}
