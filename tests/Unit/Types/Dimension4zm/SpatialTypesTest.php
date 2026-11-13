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

namespace LongitudeOne\SpatialTypes\Tests\Unit\Types\Dimension4zm;

use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\GeographyCollectionImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\LineStringImmutable as GeographicLineString;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\MultiLineStringImmutable as GeographicMultiLineString;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\MultiPointImmutable as GeographicMultiPoint;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\MultiPolygonImmutable as GeographicMultiPolygon;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\PointImmutable as GeographicPoint;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\PolygonImmutable as GeographicPolygon;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\GeometryCollectionImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\LineStringImmutable as GeometricLineString;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\MultiLineStringImmutable as GeometricMultiLineString;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\MultiPointImmutable as GeometricMultiPoint;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\MultiPolygonImmutable as GeometricMultiPolygon;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\PointImmutable as GeometricPoint;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\PolygonImmutable as GeometricPolygon;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 *
 * @internal
 */
class SpatialTypesTest extends TestCase
{
    /**
     * Verify that geographic XYZM types expose their expected family, type, dimension, and coordinates.
     */
    public function testGeographicXyzmTypes(): void
    {
        $first = new GeographicPoint('1W', '2N', 3, 4);
        $second = new GeographicPoint('4W', '5N', 6, 7);
        $third = new GeographicPoint('7W', '8N', 9, 10);
        $lineString = new GeographicLineString([$first, $second, $third, $first]);
        $polygon = new GeographicPolygon([$lineString]);

        self::assertSame(SpatialModelEnum::GEOGRAPHY, $first->getFamily());
        self::assertTrue($first->hasM());
        self::assertTrue($first->hasZ());
        self::assertSame([-1, 2, 3, 4], $first->toArray());
        self::assertSame([$first, $second, $first], new GeographicMultiPoint([$first, $second, $first])->getElements());
        self::assertSame([$lineString], new GeographicMultiLineString([$lineString])->getElements());
        self::assertSame([$polygon], new GeographicMultiPolygon([$polygon])->getElements());

        $collection = new GeographyCollectionImmutable(0, [$polygon]);
        self::assertSame([$polygon], $collection->getElements());
    }

    /**
     * Verify that geometric XYZM types expose their expected family, type, dimension, and coordinates.
     */
    public function testGeometricXyzmTypes(): void
    {
        $first = new GeometricPoint(1, 2, 3, 4);
        $second = new GeometricPoint(4, 5, 6, 7);
        $third = new GeometricPoint(7, 8, 9, 10);
        $lineString = new GeometricLineString([$first, $second, $third, $first]);
        $polygon = new GeometricPolygon([$lineString]);

        self::assertSame(SpatialModelEnum::GEOMETRY, $first->getFamily());
        self::assertTrue($first->hasM());
        self::assertTrue($first->hasZ());
        self::assertSame(3, $first->getZ());
        self::assertSame(4, $first->getM());
        self::assertSame([1, 2, 3, 4], $first->toArray());
        self::assertSame([$first, $second, $first], new GeometricMultiPoint([$first, $second, $first])->getElements());
        self::assertSame([$lineString], new GeometricMultiLineString([$lineString])->getElements());
        self::assertSame([$polygon], new GeometricMultiPolygon([$polygon])->getElements());

        $collection = new GeometryCollectionImmutable(0, [$polygon]);
        self::assertSame([$polygon], $collection->getElements());
    }
}
