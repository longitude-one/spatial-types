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

use LongitudeOne\SpatialTypes\Enum\DimensionEnum;
use LongitudeOne\SpatialTypes\Exception\BadMethodCallException;
use LongitudeOne\SpatialTypes\Exception\SpatialTypeExceptionInterface;
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
        $exception = BadMethodCallException::create('Foo::getM', DimensionEnum::X_Y_Z);
        static::assertInstanceOf(BadMethodCallException::class, $exception);
        static::assertSame('The method "Foo::getM" cannot be called with a spatial object with dimensions "XYZ".', $exception->getMessage());
    }

    /**
     * Let's check that the BadMethodCallException class is an instance of SpatialTypeExceptionInterface.
     */
    public function testInstance(): void
    {
        $exception = new BadMethodCallException();
        static::assertInstanceOf(BadMethodCallException::class, $exception);
        static::assertInstanceOf(SpatialTypeExceptionInterface::class, $exception);
    }
}
