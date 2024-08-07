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
use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Exception\InvalidSridException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\SpatialTypeExceptionInterface;
use LongitudeOne\SpatialTypes\Factory\FactoryLineString;
use LongitudeOne\SpatialTypes\Factory\FactoryPolygon;
use LongitudeOne\SpatialTypes\Interfaces\PolygonInterface;
use LongitudeOne\SpatialTypes\Types\Geometry\LineString;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Factory\FactoryPolygon
 */
class FactoryPolygonTest extends TestCase
{
    /**
     * Test fromArrayOfLineStrings method with valid LineStrings.
     */
    public function testFromArrayOfLineStrings(): void
    {
        $lineStrings = [
            new LineString([[0, 0], [1, 1], [2, 2], [0, 0]], 4326),
            new LineString([[3, 3], [4, 4], [5, 5], [3, 3]]),
        ];

        $polygon = FactoryPolygon::fromArrayOfLineStrings($lineStrings, 4326, FamilyEnum::GEOMETRY, DimensionEnum::X_Y);

        static::assertInstanceOf(PolygonInterface::class, $polygon);
        static::assertCount(2, $polygon->getRings());
    }

    /**
     * Test fromArrayOfLineStrings method with invalid a X_Y_Z dimension.
     *
     * TODO Add a "if" statement to check if the dimension is X_Y_Z and throw an InvalidDimensionException.
     */
    public function testFromArrayOfLineStringsInvalidDimension(): void
    {
        $this->expectException(InvalidDimensionException::class);

        $lineStrings = [
            new LineString([[0, 0], [1, 1], [2, 2], [0, 0]], 4326),
        ];

        FactoryPolygon::fromArrayOfLineStrings($lineStrings, 4326, FamilyEnum::GEOMETRY, DimensionEnum::X_Y_Z);
    }

    /**
     * Test fromArrayOfLineStrings method with invalid value.
     */
    public function testFromArrayOfLineStringsInvalidValue(): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('The array must contain only objects implementing LineStringInterface.');

        $lineStrings = [
            [[0, 0], [1, 1], [2, 2], [0, 0]],
        ];

        FactoryPolygon::fromArrayOfLineStrings($lineStrings, 4326, FamilyEnum::GEOMETRY, DimensionEnum::X_Y);
    }

    /**
     * Test fromIndexedArray method with valid integers.
     *
     * @throws SpatialTypeExceptionInterface this shall not happen
     */
    public function testFromIndexedArray(): void
    {
        $indexedArray = [
            [[0, 0], [1, 1], [2, 2], [0, 0]],
            [[3, 3], [4, 4], [5, 5], [3, 3]],
            FactoryLineString::fromIndexedArray([[6, 6], [7, 7], [8, 8], [6, 6]], 4326, FamilyEnum::GEOMETRY, DimensionEnum::X_Y),
        ];

        $polygon = FactoryPolygon::fromIndexedArray($indexedArray, 4326, FamilyEnum::GEOMETRY, DimensionEnum::X_Y);

        static::assertInstanceOf(PolygonInterface::class, $polygon);
        static::assertCount(3, $polygon->getRings());
    }

    /**
     * Test fromIndexedArray method with two different SRID.
     *
     * @throws SpatialTypeExceptionInterface this shall happen and be caught by PHPUnit
     */
    public function testFromIndexedArrayInvalidValue(): void
    {
        $this->expectException(InvalidSridException::class);

        $indexedArray = [
            [[0, 0], [1, 1], [2, 2], [0, 0]],
            FactoryLineString::fromIndexedArray([[3, 3], [4, 4], [5, 5], [3, 3]], 1234),
        ];

        FactoryPolygon::fromIndexedArray($indexedArray, 4326, FamilyEnum::GEOMETRY, DimensionEnum::X_Y);
    }
}
