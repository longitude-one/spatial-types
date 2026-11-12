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

namespace LongitudeOne\SpatialTypes\Tests\Unit\Exception;

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\SpatialTypes\Exception\BadMethodCallException;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Exception\BadMethodCallException
 */
class BadMethodCallExceptionTest extends TestCase
{
    /**
     * Let's check that the create method is working as expected.
     */
    public function testCreate(): void
    {
        $exception = BadMethodCallException::create('Foo::getM', CoordinateDimensionEnum::XYZ);
        static::assertSame('The method "Foo::getM" cannot be called with a spatial object with dimensions "XYZ".', $exception->getMessage());
    }
}
