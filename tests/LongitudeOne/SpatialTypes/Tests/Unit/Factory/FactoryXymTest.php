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
use LongitudeOne\SpatialTypes\Factory\FactoryLineString;
use LongitudeOne\SpatialTypes\Factory\FactoryPoint;
use LongitudeOne\SpatialTypes\Factory\FactoryPolygon;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\LineString as GeographicLineString;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\Point as GeometricPoint;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\Polygon as GeometricPolygon;
use PHPUnit\Framework\TestCase;

/**
 * Contract tests for XYM creations through the public factories.
 *
 * @internal
 *
 * @coversNothing
 */
class FactoryXymTest extends TestCase
{
    /**
     * Verify XYM line-string creation from indexed coordinate arrays.
     */
    public function testCreatesGeographicXymLineStringFromIndexedArray(): void
    {
        $lineString = FactoryLineString::fromIndexedArray([[1, 2, 3], [4, 5, 6]], 4326, FamilyEnum::GEOGRAPHY, DimensionEnum::X_Y_M);

        static::assertInstanceOf(GeographicLineString::class, $lineString);
        static::assertSame([[1, 2, 3], [4, 5, 6]], $lineString->toArray());
        static::assertSame(4326, $lineString->getSrid());
    }

    /**
     * Verify XYM point creation from individual coordinates.
     */
    public function testCreatesGeometricXymPointFromCoordinates(): void
    {
        $point = FactoryPoint::fromCoordinates(1, 2, null, 3, 2154, FamilyEnum::GEOMETRY, DimensionEnum::X_Y_M);

        static::assertInstanceOf(GeometricPoint::class, $point);
        static::assertSame([1, 2, 3], $point->toArray());
        static::assertSame(2154, $point->getSrid());
    }

    /**
     * Verify XYM polygon creation from indexed rings.
     */
    public function testCreatesGeometricXymPolygonFromIndexedArray(): void
    {
        $polygon = FactoryPolygon::fromIndexedArray([[[0, 0, 1], [1, 1, 2], [0, 0, 1]]], 2154, FamilyEnum::GEOMETRY, DimensionEnum::X_Y_M);

        static::assertInstanceOf(GeometricPolygon::class, $polygon);
        static::assertSame([[[0, 0, 1], [1, 1, 2], [0, 0, 1]]], $polygon->toArray());
        static::assertSame(2154, $polygon->getSrid());
    }
}
