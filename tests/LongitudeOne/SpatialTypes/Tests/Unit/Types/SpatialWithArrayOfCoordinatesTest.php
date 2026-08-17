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

namespace LongitudeOne\SpatialTypes\Tests\Unit\Types;

use LongitudeOne\SpatialTypes\Enum\DimensionEnum;
use LongitudeOne\SpatialTypes\Enum\FamilyEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Factory\FromIndexedArrayFactory;
use LongitudeOne\SpatialTypes\Interfaces\LineStringInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractLineString
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractPolygon
 */
class SpatialWithArrayOfCoordinatesTest extends TestCase
{
    /**
     * Assert that replacement coordinates preserve line-string metadata without mutating the source.
     *
     * @param LineStringInterface                                                      $lineString             source line string
     * @param LineStringInterface                                                      $replacement            replacement line string
     * @param array<array{0: float|int, 1: float|int, 2 ?: float|int, 3 ?: float|int}> $initialCoordinates     initial coordinates
     * @param array<array{0: float|int, 1: float|int, 2 ?: float|int, 3 ?: float|int}> $replacementCoordinates replacement coordinates
     * @param FamilyEnum                                                               $family                 expected family
     * @param DimensionEnum                                                            $dimension              expected dimension
     */
    private static function assertLineStringWasReplaced(
        LineStringInterface $lineString,
        LineStringInterface $replacement,
        array $initialCoordinates,
        array $replacementCoordinates,
        FamilyEnum $family,
        DimensionEnum $dimension
    ): void {
        static::assertNotSame($lineString, $replacement);
        static::assertSame($lineString::class, $replacement::class);
        static::assertSame($family, $replacement->getFamily());
        static::assertSame(4326, $replacement->getSrid());
        static::assertSame($dimension->hasM(), $replacement->hasM());
        static::assertSame($dimension->hasZ(), $replacement->hasZ());
        static::assertSame($initialCoordinates, $lineString->toArray());
        static::assertSame($replacementCoordinates, $replacement->toArray());
        static::assertNotSame($lineString->getPoint(0), $replacement->getPoint(0));
    }

    /**
     * Verify that replacement coordinates create an immutable line string for every supported layout and family.
     *
     * @param DimensionEnum                                                            $dimension              dimension to test
     * @param FamilyEnum                                                               $family                 family to test
     * @param array<array{0: float|int, 1: float|int, 2 ?: float|int, 3 ?: float|int}> $initialCoordinates     initial coordinates
     * @param array<array{0: float|int, 1: float|int, 2 ?: float|int, 3 ?: float|int}> $replacementCoordinates replacement coordinates
     */
    #[DataProvider('provideContextsAndCoordinates')]
    public function testLineStringWithArrayOfCoordinates(
        DimensionEnum $dimension,
        FamilyEnum $family,
        array $initialCoordinates,
        array $replacementCoordinates
    ): void {
        $lineString = FromIndexedArrayFactory::createLineString($initialCoordinates, 4326, $family, $dimension);
        $replacement = $lineString->withArrayOfCoordinates($replacementCoordinates);

        self::assertLineStringWasReplaced($lineString, $replacement, $initialCoordinates, $replacementCoordinates, $family, $dimension);
    }

    /**
     * Verify that a replacement tuple is validated against the line string's retained dimension.
     */
    public function testLineStringWithArrayOfCoordinatesRejectsAnIncompatibleTuple(): void
    {
        $lineString = FromIndexedArrayFactory::createLineString([], 4326, FamilyEnum::GEOMETRY, DimensionEnum::X_Y);

        self::expectException(InvalidDimensionException::class);

        $lineString->withArrayOfCoordinates([[1, 2, 3]]);
    }

    /**
     * Verify that replacement coordinates create an immutable polygon for every supported layout and family.
     *
     * @param DimensionEnum                                                            $dimension              dimension to test
     * @param FamilyEnum                                                               $family                 family to test
     * @param array<array{0: float|int, 1: float|int, 2 ?: float|int, 3 ?: float|int}> $initialCoordinates     initial ring coordinates
     * @param array<array{0: float|int, 1: float|int, 2 ?: float|int, 3 ?: float|int}> $replacementCoordinates replacement ring coordinates
     */
    #[DataProvider('provideContextsAndCoordinates')]
    public function testPolygonWithArrayOfCoordinates(
        DimensionEnum $dimension,
        FamilyEnum $family,
        array $initialCoordinates,
        array $replacementCoordinates
    ): void {
        $polygon = FromIndexedArrayFactory::createPolygon([$initialCoordinates], 4326, $family, $dimension);
        $replacement = $polygon->withArrayOfCoordinates([$replacementCoordinates]);

        static::assertNotSame($polygon, $replacement);
        static::assertSame($polygon::class, $replacement::class);
        static::assertSame($family, $replacement->getFamily());
        static::assertSame(4326, $replacement->getSrid());
        static::assertSame([$initialCoordinates], $polygon->toArray());
        static::assertSame([$replacementCoordinates], $replacement->toArray());
        static::assertNotSame($polygon->getRing(0), $replacement->getRing(0));
    }

    /**
     * Provide every coordinate layout for both spatial families.
     *
     * @return \Generator<string, array{0: DimensionEnum, 1: FamilyEnum, 2: array<array{0: float|int, 1: float|int, 2 ?: float|int, 3 ?: float|int}>, 3: array<array{0: float|int, 1: float|int, 2 ?: float|int, 3 ?: float|int}>}, null, void>
     */
    public static function provideContextsAndCoordinates(): \Generator
    {
        foreach ([FamilyEnum::GEOMETRY, FamilyEnum::GEOGRAPHY] as $family) {
            yield sprintf('%s XY', $family->value) => [DimensionEnum::X_Y, $family, [[1, 2], [3, 4], [3, 6], [1, 2]], [[5, 6], [7, 8], [7, 10], [5, 6]]];

            yield sprintf('%s XYM', $family->value) => [DimensionEnum::X_Y_M, $family, [[1, 2, 3], [4, 5, 6], [7, 8, 9], [1, 2, 3]], [[7, 8, 9], [10, 11, 12], [13, 14, 15], [7, 8, 9]]];

            yield sprintf('%s XYZ', $family->value) => [DimensionEnum::X_Y_Z, $family, [[1, 2, 3], [4, 5, 6], [7, 8, 9], [1, 2, 3]], [[7, 8, 9], [10, 11, 12], [13, 14, 15], [7, 8, 9]]];

            yield sprintf('%s XYZM', $family->value) => [DimensionEnum::X_Y_Z_M, $family, [[1, 2, 3, 4], [5, 6, 7, 8], [9, 10, 11, 12], [1, 2, 3, 4]], [[9, 10, 11, 12], [13, 14, 15, 16], [17, 18, 19, 20], [9, 10, 11, 12]]];
        }
    }

    /**
     * Verify that every replacement polygon ring remains subject to ring validation.
     */
    public function testPolygonWithArrayOfCoordinatesRejectsAnOpenRing(): void
    {
        $polygon = FromIndexedArrayFactory::createPolygon([], 4326, FamilyEnum::GEOMETRY, DimensionEnum::X_Y);

        self::expectException(InvalidValueException::class);
        self::expectExceptionMessageIsOrContains('at least 4 points');

        $polygon->withArrayOfCoordinates([[[1, 2], [3, 4]]]);
    }

    /**
     * Verify that coordinate replacement returns an independent clone of each aggregate.
     */
    public function testWithArrayOfCoordinatesReturnsIndependentClones(): void
    {
        $lineString = FromIndexedArrayFactory::createLineString([[1, 2, 3], [4, 5, 6]], 2154, FamilyEnum::GEOMETRY, DimensionEnum::X_Y_M);
        $lineStringClone = $lineString->withArrayOfCoordinates([[7, 8, 9], [10, 11, 12]]);
        $polygon = FromIndexedArrayFactory::createPolygon([[[1, 2, 3], [4, 5, 6], [7, 8, 9], [1, 2, 3]]], 2154, FamilyEnum::GEOMETRY, DimensionEnum::X_Y_M);
        $polygonClone = $polygon->withArrayOfCoordinates([[[7, 8, 9], [10, 11, 12], [13, 14, 15], [7, 8, 9]]]);

        static::assertNotSame($lineString, $lineStringClone);
        static::assertSame($lineString::class, $lineStringClone::class);
        static::assertSame(2154, $lineStringClone->getSrid());
        static::assertNotSame($lineString->getPoint(0), $lineStringClone->getPoint(0));
        static::assertSame([[1, 2, 3], [4, 5, 6]], $lineString->toArray());

        static::assertNotSame($polygon, $polygonClone);
        static::assertSame($polygon::class, $polygonClone::class);
        static::assertSame(2154, $polygonClone->getSrid());
        static::assertNotSame($polygon->getRing(0), $polygonClone->getRing(0));
        static::assertNotSame($polygon->getRing(0)->getPoint(0), $polygonClone->getRing(0)->getPoint(0));
        static::assertSame([[[1, 2, 3], [4, 5, 6], [7, 8, 9], [1, 2, 3]]], $polygon->toArray());
    }
}
