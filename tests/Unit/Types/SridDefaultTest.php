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

namespace LongitudeOne\SpatialTypes\Tests\Unit\Types;

use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PointImmutable;
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
        $point = new PointImmutable(1, 2);

        self::assertSame(0, $point->getSrid());
        self::assertSame(0, $point->getSpatialReference()->srid());
        self::assertSame(0, $point->jsonSerialize()['srid']);
    }
}
