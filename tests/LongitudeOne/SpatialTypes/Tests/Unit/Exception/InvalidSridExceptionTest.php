<?php
/**
 * This file is part of the spatial project.
 *
 * PHP 8.1 | 8.2 | 8.3
 *
 * Copyright Alexandre Tranchant <alexandre.tranchant@gmail.com> 2024
 * Copyright Longitude One 2024
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

declare(strict_types=1);

namespace LongitudeOne\SpatialTypes\Tests\Unit\Exception;

use LongitudeOne\SpatialTypes\Exception\InvalidSridException;
use LongitudeOne\SpatialTypes\Exception\SpatialTypeExceptionInterface;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Exception\InvalidSridException
 */
class InvalidSridExceptionTest extends TestCase
{
    /**
     * Let's check that the exception is an instance of SpatialTypeExceptionInterface.
     */
    public function testInstance(): void
    {
        $exception = new InvalidSridException();
        static::assertInstanceOf(SpatialTypeExceptionInterface::class, $exception);
    }
}
