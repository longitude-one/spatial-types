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

namespace LongitudeOne\SpatialTypes\Tests\Unit\Factory;

use LongitudeOne\SpatialTypes\Enum\DimensionEnum;
use LongitudeOne\SpatialTypes\Enum\FamilyEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidSridException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\SpatialTypeExceptionInterface;
use LongitudeOne\SpatialTypes\Factory\FactoryLineString;
use LongitudeOne\SpatialTypes\Factory\FactoryPolygon;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineString;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\Polygon as Polygon3Dz;
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

        static::assertCount(2, $polygon->getRings());
    }

    /**
     * Test fromArrayOfLineStrings method with invalid value.
     */
    public function testFromArrayOfLineStringsInvalidValue(): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessageIsOrContains('The array must contain only objects implementing LineStringInterface.');

        $lineStrings = [
            [[0, 0], [1, 1], [2, 2], [0, 0]],
        ];

        FactoryPolygon::fromArrayOfLineStrings($lineStrings, 4326, FamilyEnum::GEOMETRY, DimensionEnum::X_Y);
    }

    /**
     * Test fromArrayOfLineStrings method with valid three-dimensional elevation LineStrings.
     */
    public function testFromArrayOfLineStringsWithThreeDimensions(): void
    {
        $lineStrings = [
            FactoryLineString::fromIndexedArray([[0, 0, 1], [1, 1, 2], [2, 2, 3], [0, 0, 1]], 4326, FamilyEnum::GEOMETRY, DimensionEnum::X_Y_Z),
        ];

        $polygon = FactoryPolygon::fromArrayOfLineStrings($lineStrings, 4326, FamilyEnum::GEOMETRY, DimensionEnum::X_Y_Z);

        static::assertInstanceOf(Polygon3Dz::class, $polygon);
        static::assertCount(1, $polygon->getRings());
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
