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
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Factory\FactoryLineString;
use LongitudeOne\SpatialTypes\Interfaces\LineStringInterface;
use LongitudeOne\SpatialTypes\Types\Geography\Point as GeographicPoint;
use LongitudeOne\SpatialTypes\Types\Geometry\Point;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Factory\FactoryLineString
 */
class FactoryLineStringTest extends TestCase
{
    /**
     * Test the creation of a line string from an array of points.
     */
    public function testFromArrayOfPoints(): void
    {
        $points = [
            new GeographicPoint(1, 2),
            new GeographicPoint(3, 4),
        ];

        $lineString = FactoryLineString::fromArrayOfPoints($points, 4326, FamilyEnum::GEOGRAPHY, DimensionEnum::X_Y);

        static::assertInstanceOf(LineStringInterface::class, $lineString);
        static::assertCount(2, $lineString->getPoints());
        static::assertSame(4326, $lineString->getSrid());
        static::assertSame(FamilyEnum::GEOGRAPHY, $lineString->getFamily());
        static::assertFalse($lineString->hasM());
        static::assertFalse($lineString->hasZ());
        static::assertFalse($lineString->isEmpty());
    }

    /**
     * Test the creation of a line string from an array of points with an invalid dimension.
     */
    public function testFromArrayOfPointsInvalidDimension(): void
    {
        $this->expectException(InvalidDimensionException::class);

        $points = [
            new Point(1, 2),
        ];

        FactoryLineString::fromArrayOfPoints($points, 4326, FamilyEnum::GEOGRAPHY, DimensionEnum::X_Y_Z);
    }

    /**
     * Test the creation of a line string from an array of points with an invalid value.
     */
    public function testFromArrayOfPointsInvalidValue(): void
    {
        $this->expectException(InvalidValueException::class);

        FactoryLineString::fromArrayOfPoints(['invalid_point'], 4326, FamilyEnum::GEOGRAPHY, DimensionEnum::X_Y);
    }

    /**
     * Test the creation of a line string from an indexed array of coordinates.
     */
    public function testFromIndexedArray(): void
    {
        $indexedArray = [
            [0.0, 0.0],
            [1.0, 1.0],
        ];

        $lineString = FactoryLineString::fromIndexedArray($indexedArray, 4326, FamilyEnum::GEOGRAPHY, DimensionEnum::X_Y);

        static::assertInstanceOf(LineStringInterface::class, $lineString);
        static::assertCount(2, $lineString->getPoints());
        static::assertSame(4326, $lineString->getSrid());
        static::assertSame(FamilyEnum::GEOGRAPHY, $lineString->getFamily());
        static::assertFalse($lineString->hasM());
        static::assertFalse($lineString->hasZ());
        static::assertFalse($lineString->isEmpty());
    }

    /**
     * Test the creation of a line string from an indexed array of points.
     */
    public function testFromIndexedArrayWithPoints(): void
    {
        $points = [
            new Point(1, 2),
            new Point(3, 4),
        ];

        $lineString = FactoryLineString::fromIndexedArray($points, 4326, FamilyEnum::GEOMETRY, DimensionEnum::X_Y);

        static::assertInstanceOf(LineStringInterface::class, $lineString);
        static::assertCount(2, $lineString->getPoints());
        static::assertSame(4326, $lineString->getSrid());
        static::assertSame(FamilyEnum::GEOMETRY, $lineString->getFamily());
        static::assertFalse($lineString->hasM());
        static::assertFalse($lineString->hasZ());
        static::assertFalse($lineString->isEmpty());
    }
}
