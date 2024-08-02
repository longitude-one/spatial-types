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

namespace LongitudeOne\SpatialTypes\Tests\Unit\Factory;

use LongitudeOne\SpatialTypes\Enum\DimensionEnum;
use LongitudeOne\SpatialTypes\Enum\FamilyEnum;
use LongitudeOne\SpatialTypes\Enum\TypeEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Exception\MissingValueException;
use LongitudeOne\SpatialTypes\Exception\SpatialTypeExceptionInterface;
use LongitudeOne\SpatialTypes\Factory\FactoryPoint;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Test the FactoryPoint class.
 *
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Factory\FactoryPoint
 */
class FactoryPointTest extends TestCase
{
    /**
     * Provide bad values to test the factory.
     *
     * @return \Generator<string, array{0: int[], 1: class-string<SpatialTypeExceptionInterface>, 2: string}, null, void>
     */
    public static function provideBadValues(): \Generator
    {
        yield 'Empty array' => [
            [],
            MissingValueException::class,
            'The array must contain at least two coordinates to create a point.',
        ];

        yield 'One value' => [
            [1],
            MissingValueException::class,
            'The array must contain at least two coordinates to create a point.',
        ];

        yield 'Three values' => [
            [1, 2, 3],
            InvalidDimensionException::class,
            'The third and fourth dimensions are not supported for two-dimensions points. Did you miss the 7th parameter DimensionEnum?',
        ];

        yield 'Four values' => [
            [1, 2, 3, 4],
            InvalidDimensionException::class,
            'The third and fourth dimensions are not supported for two-dimensions points. Did you miss the 7th parameter DimensionEnum?',
        ];

        yield 'Five values' => [
            [1, 2, 3, 4, 5],
            InvalidDimensionException::class,
            'The array must contain at most four coordinates.',
        ];
    }

    /**
     * Test the factory with some good coordinates.
     */
    public function testFromCoordinates(): void
    {
        $point = FactoryPoint::fromCoordinates(1, 2);
        static::assertSame(1, $point->getX());
        static::assertSame(2, $point->getY());
        static::assertFalse($point->hasM());
        static::assertFalse($point->hasZ());
        static::assertSame(FamilyEnum::GEOMETRY, $point->getFamily());
        static::assertSame(TypeEnum::POINT->value, $point->getType());

        $point = FactoryPoint::fromCoordinates(42.1, 42.2, null, null, 4326, FamilyEnum::GEOGRAPHY, DimensionEnum::X_Y);
        static::assertSame(42.1, $point->getX());
        static::assertSame(42.2, $point->getY());
        static::assertFalse($point->hasM());
        static::assertFalse($point->hasZ());
        static::assertSame(FamilyEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame(TypeEnum::POINT->value, $point->getType());
    }

    /**
     * Test the factory with some bad dimension.
     */
    public function testFromCoordinatesWithBadDimension(): void
    {
        self::expectException(InvalidDimensionException::class);
        self::expectExceptionMessage('The third and fourth dimensions are not supported for two-dimensions points. Did you miss the 7th parameter DimensionEnum?');
        FactoryPoint::fromCoordinates(1, 2, 3, 4);
    }

    /**
     * Test the factory with some good coordinates in an array.
     */
    public function testFromIndexedArray(): void
    {
        $point = FactoryPoint::fromIndexedArray([1, 2]);
        static::assertSame(1, $point->getX());
        static::assertSame(2, $point->getY());
        static::assertFalse($point->hasM());
        static::assertFalse($point->hasZ());
        static::assertSame(FamilyEnum::GEOMETRY, $point->getFamily());
        static::assertSame(TypeEnum::POINT->value, $point->getType());

        $point = FactoryPoint::fromIndexedArray([42.1, 42.2, null, null], 4326, FamilyEnum::GEOGRAPHY, DimensionEnum::X_Y);
        static::assertSame(42.1, $point->getX());
        static::assertSame(42.2, $point->getY());
        static::assertFalse($point->hasM());
        static::assertFalse($point->hasZ());
        static::assertSame(FamilyEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame(TypeEnum::POINT->value, $point->getType());
    }

    // phpcs:disable Squiz.Commenting.FunctionComment.IncorrectTypeHint

    /**
     * Test the factory with some bad values in an array.
     *
     * @param int[]                                       $values            The values to test
     * @param class-string<SpatialTypeExceptionInterface> $exceptedException The expected exception
     * @param string                                      $expectedMessage   The expected message
     */
    #[DataProvider('provideBadValues')]
    public function testFromIndexedArrayWithBadValues(array $values, string $exceptedException, string $expectedMessage): void
    {
        self::expectException($exceptedException);
        self::expectExceptionMessage($expectedMessage);
        FactoryPoint::fromIndexedArray($values);
    }
}
