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

namespace LongitudeOne\SpatialTypes\Tests\Functional\Exception;

use Exception;
use LongitudeOne\SpatialTypes\Exception\BadMethodCallException;
use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\LogicException;
use LongitudeOne\SpatialTypes\Exception\MissingValueException;
use LongitudeOne\SpatialTypes\Exception\OutOfBoundsException;
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
 * @covers \LongitudeOne\SpatialTypes\Exception\InvalidValueException
 * @covers \LongitudeOne\SpatialTypes\Exception\MissingValueException
 * @covers \LongitudeOne\SpatialTypes\Exception\OutOfBoundsException
 */
class SpatialTypeExceptionInterfaceTest extends TestCase
{
    /**
     * Exception provider.
     *
     * @return \Generator<string, array{0: class-string<\Exception>}, null, void>
     */
    public static function exceptionProvider(): \Generator
    {
        yield 'BadMethodCallException' => [BadMethodCallException::class];

        yield 'InvalidDimensionException' => [InvalidDimensionException::class];

        yield 'InvalidValueException' => [InvalidValueException::class];

        yield 'MissingValueException' => [MissingValueException::class];

        yield 'OutOfBoundsException' => [OutOfBoundsException::class];
    }
    // phpcs:disable Squiz.Commenting.FunctionComment.IncorrectTypeHint
    // phpcs:disable Squiz.Commenting.FunctionComment.MissingParamComment

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
            static::fail(\sprintf('%s should be caught.', $exceptionClass));
        }
    }

    /**
     * Logic failures remain catchable through the public spatial exception contract.
     */
    public function testCatchLogicException(): void
    {
        try {
            throw new LogicException('Undefined implementation status.');
        } catch (SpatialTypeExceptionInterface $exception) {
            static::assertInstanceOf(\LogicException::class, $exception);
            static::assertSame('Undefined implementation status.', $exception->getMessage());
        }
    }
}
