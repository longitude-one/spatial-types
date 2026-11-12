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

namespace LongitudeOne\SpatialTypes\Tests\Unit\Types\Dimension3m;

use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Exception\BadMethodCallException;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\GeographyCollectionImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\LineStringImmutable as GeographicLineString;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\MultiLineStringImmutable as GeographicMultiLineString;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\MultiPointImmutable as GeographicMultiPoint;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\MultiPolygonImmutable as GeographicMultiPolygon;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\PointImmutable as GeographicPoint;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\PolygonImmutable as GeographicPolygon;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\GeometryCollectionImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\LineStringImmutable as GeometricLineString;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\MultiLineStringImmutable as GeometricMultiLineString;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\MultiPointImmutable as GeometricMultiPoint;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\MultiPolygonImmutable as GeometricMultiPolygon;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\PointImmutable as GeometricPoint;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\PolygonImmutable as GeometricPolygon;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 *
 * @internal
 */
class SpatialTypesTest extends TestCase
{
    /**
     * Test the geographic spatial types using the XYM layout.
     */
    public function testGeographicXymTypes(): void
    {
        $first = new GeographicPoint('1W', '2N', 3);
        $second = new GeographicPoint('4W', '5N', 6);
        $third = new GeographicPoint('7W', '8N', 9);
        $lineString = new GeographicLineString([$first, $second, $third, $first]);
        $polygon = new GeographicPolygon([$lineString]);

        self::assertSame(SpatialModelEnum::GEOGRAPHY, $first->getFamily());
        self::assertTrue($first->hasM());
        self::assertFalse($first->hasZ());
        self::assertSame(3, $first->getM());
        self::assertSame([-1, 2, 3], $first->toArray());
        self::assertSame([$first, $second, $first], new GeographicMultiPoint([$first, $second, $first])->getElements());
        self::assertSame([$lineString], new GeographicMultiLineString([$lineString])->getElements());
        self::assertSame([$polygon], new GeographicMultiPolygon([$polygon])->getElements());

        $collection = new GeographyCollectionImmutable(0, [$polygon]);
        self::assertSame([$polygon], $collection->getElements());
    }

    /**
     * Test the geometric spatial types using the XYM layout.
     */
    public function testGeometricXymTypes(): void
    {
        $first = new GeometricPoint(1, 2, 3);
        $second = new GeometricPoint(4, 5, 6);
        $third = new GeometricPoint(7, 8, 9);
        $lineString = new GeometricLineString([$first, $second, $third, $first]);
        $polygon = new GeometricPolygon([$lineString]);

        self::assertSame(SpatialModelEnum::GEOMETRY, $first->getFamily());
        self::assertTrue($first->hasM());
        self::assertFalse($first->hasZ());
        self::assertSame(3, $first->getM());
        self::assertSame([1, 2, 3], $first->toArray());
        self::assertSame([$first, $second, $first], new GeometricMultiPoint([$first, $second, $first])->getElements());
        self::assertSame([$lineString], new GeometricMultiLineString([$lineString])->getElements());
        self::assertSame([$polygon], new GeometricMultiPolygon([$polygon])->getElements());

        $collection = new GeometryCollectionImmutable(0, [$polygon]);
        self::assertSame([$polygon], $collection->getElements());
    }

    /**
     * Test that an XYM point does not expose a Z coordinate.
     */
    public function testXymPointDoesNotExposeZ(): void
    {
        $point = new GeometricPoint(1, 2, 3);

        self::expectException(BadMethodCallException::class);
        $point->getZ();
    }
}
