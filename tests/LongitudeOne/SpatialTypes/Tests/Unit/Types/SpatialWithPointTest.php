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
use LongitudeOne\SpatialTypes\Factory\FromIndexedArrayFactory;
use LongitudeOne\SpatialTypes\Value\Coordinates;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractLineString
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractPolygon
 */
class SpatialWithPointTest extends TestCase
{
    /**
     * Verify that a line string rejects coordinates from another dimension.
     */
    public function testLineStringWithPointRejectsAnotherDimension(): void
    {
        $lineString = FromIndexedArrayFactory::createLineString([[1, 2]], 2154);

        self::expectException(InvalidDimensionException::class);

        $lineString->withPoint(0, Coordinates::xyz(3, 4, 5));
    }

    /**
     * Verify that a line string replaces one point in a deep immutable copy.
     */
    public function testLineStringWithPointReturnsAnIndependentCopy(): void
    {
        $lineString = FromIndexedArrayFactory::createLineString([[1, 2, 3], [4, 5, 6]], 2154, FamilyEnum::GEOMETRY, DimensionEnum::X_Y_M);
        $replacement = $lineString->withPoint(1, Coordinates::xym(7, 8, 9));

        static::assertNotSame($lineString, $replacement);
        static::assertSame($lineString::class, $replacement::class);
        static::assertSame(2154, $replacement->getSrid());
        static::assertSame([[1, 2, 3], [4, 5, 6]], $lineString->toArray());
        static::assertSame([[1, 2, 3], [7, 8, 9]], $replacement->toArray());
        static::assertNotSame($lineString->getPoint(0), $replacement->getPoint(0));
        static::assertNotSame($lineString->getPoint(1), $replacement->getPoint(1));
    }

    /**
     * Verify that replacing the first exterior-ring point also replaces its closing point.
     */
    public function testPolygonWithPointPreservesExteriorRingClosure(): void
    {
        $polygon = FromIndexedArrayFactory::createPolygon([
            [[0, 0], [10, 0], [0, 10], [0, 0]],
            [[1, 1], [2, 1], [1, 2], [1, 1]],
        ], 2154);
        $replacement = $polygon->withPoint(0, 0, Coordinates::xy(-1, -1));

        static::assertNotSame($polygon, $replacement);
        static::assertSame($polygon::class, $replacement::class);
        static::assertSame(2154, $replacement->getSrid());
        static::assertSame([[[0, 0], [10, 0], [0, 10], [0, 0]], [[1, 1], [2, 1], [1, 2], [1, 1]]], $polygon->toArray());
        static::assertSame([[[-1, -1], [10, 0], [0, 10], [-1, -1]], [[1, 1], [2, 1], [1, 2], [1, 1]]], $replacement->toArray());
        static::assertNotSame($polygon->getRing(0), $replacement->getRing(0));
        static::assertNotSame($polygon->getRing(1), $replacement->getRing(1));
    }

    /**
     * Verify that replacing the final point of an inner ring also replaces its first point.
     */
    public function testPolygonWithPointPreservesInnerRingClosure(): void
    {
        $polygon = FromIndexedArrayFactory::createPolygon([
            [[0, 0], [10, 0], [0, 10], [0, 0]],
            [[1, 1], [2, 1], [1, 2], [1, 1]],
        ], 2154);
        $replacement = $polygon->withPoint(-1, -1, Coordinates::xy(0.5, 0.5));

        static::assertSame([[[0, 0], [10, 0], [0, 10], [0, 0]], [[0.5, 0.5], [2, 1], [1, 2], [0.5, 0.5]]], $replacement->toArray());
        static::assertTrue($replacement->getRing(-1)->isRing());
    }
}
