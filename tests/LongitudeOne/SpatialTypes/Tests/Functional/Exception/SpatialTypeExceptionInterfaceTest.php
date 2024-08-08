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

namespace LongitudeOne\SpatialTypes\Tests\Functional\Exception;

use Exception;
use LongitudeOne\SpatialTypes\Exception\BadMethodCallException;
use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Exception\InvalidFamilyException;
use LongitudeOne\SpatialTypes\Exception\InvalidSridException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\MissingValueException;
use LongitudeOne\SpatialTypes\Exception\OutOfBoundsException;
use LongitudeOne\SpatialTypes\Exception\RangeException;
use LongitudeOne\SpatialTypes\Exception\SpatialTypeExceptionInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * SpatialTypeExceptionInterface test.
 *
 * We check that each exception thrown by the library can be caught with the SpatialTypeExceptionInterface.
 *
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Exception\BadMethodCallException
 * @covers \LongitudeOne\SpatialTypes\Exception\InvalidDimensionException
 * @covers \LongitudeOne\SpatialTypes\Exception\InvalidFamilyException
 * @covers \LongitudeOne\SpatialTypes\Exception\InvalidSridException
 * @covers \LongitudeOne\SpatialTypes\Exception\InvalidValueException
 * @covers \LongitudeOne\SpatialTypes\Exception\MissingValueException
 * @covers \LongitudeOne\SpatialTypes\Exception\OutOfBoundsException
 * @covers \LongitudeOne\SpatialTypes\Exception\RangeException
 */
class SpatialTypeExceptionInterfaceTest extends TestCase
{
    public static function exceptionProvider(): \Generator
    {
        yield 'BadMethodCallException' => [BadMethodCallException::class];

        yield 'InvalidDimensionException' => [InvalidDimensionException::class];

        yield 'InvalidFamilyException' => [InvalidFamilyException::class];

        yield 'InvalidSridException' => [InvalidSridException::class];

        yield 'InvalidValueException' => [InvalidValueException::class];

        yield 'MissingValueException' => [MissingValueException::class];

        yield 'OutOfBoundsException' => [OutOfBoundsException::class];

        yield 'RangeException' => [RangeException::class];
    }

    /**
     * Let's check that each exception thrown by the library can be caught with the SpatialTypeExceptionInterface.
     *
     * @param class-string<\Exception> $exceptionClass
     */
    #[DataProvider('exceptionProvider')]
    public function testCatch(string $exceptionClass): void
    {
        try {
            throw new $exceptionClass();
        } catch (SpatialTypeExceptionInterface $exception) {
            static::assertInstanceOf($exceptionClass, $exception);
        } catch (\Exception) {
            static::fail(sprintf('%s should be caught.', $exceptionClass));
        }
    }
}
