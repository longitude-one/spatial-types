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

use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\SpatialTypeExceptionInterface;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Exception\InvalidValueException
 */
class InvalidValueExceptionTest extends TestCase
{
    /**
     * Let's check that the exception is an instance of SpatialTypeExceptionInterface.
     */
    public function testInstance(): void
    {
        $exception = new InvalidValueException();
        self::assertInstanceOf(SpatialTypeExceptionInterface::class, $exception);
    }
}
