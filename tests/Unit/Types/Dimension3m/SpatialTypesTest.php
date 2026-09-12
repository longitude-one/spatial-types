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

namespace LongitudeOne\SpatialTypes\Tests\Unit\Types\Dimension3m;

use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Exception\BadMethodCallException;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\GeographyCollection;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\LineString as GeographicLineString;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\MultiLineString as GeographicMultiLineString;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\MultiPoint as GeographicMultiPoint;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\MultiPolygon as GeographicMultiPolygon;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\Point as GeographicPoint;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\Polygon as GeographicPolygon;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\GeometryCollection;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\LineString as GeometricLineString;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\MultiLineString as GeometricMultiLineString;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\MultiPoint as GeometricMultiPoint;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\MultiPolygon as GeometricMultiPolygon;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\Point as GeometricPoint;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\Polygon as GeometricPolygon;
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

        static::assertSame(SpatialModelEnum::GEOGRAPHY, $first->getFamily());
        static::assertTrue($first->hasM());
        static::assertFalse($first->hasZ());
        static::assertSame(3, $first->getM());
        static::assertSame([-1, 2, 3], $first->toArray());
        static::assertSame([$first, $second, $first], (new GeographicMultiPoint([$first, $second, $first]))->getElements());
        static::assertSame([$lineString], (new GeographicMultiLineString([$lineString]))->getElements());
        static::assertSame([$polygon], (new GeographicMultiPolygon([$polygon]))->getElements());

        $collection = new GeographyCollection(0, [$polygon]);
        static::assertSame([$polygon], $collection->getElements());
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

        static::assertSame(SpatialModelEnum::GEOMETRY, $first->getFamily());
        static::assertTrue($first->hasM());
        static::assertFalse($first->hasZ());
        static::assertSame(3, $first->getM());
        static::assertSame([1, 2, 3], $first->toArray());
        static::assertSame([$first, $second, $first], (new GeometricMultiPoint([$first, $second, $first]))->getElements());
        static::assertSame([$lineString], (new GeometricMultiLineString([$lineString]))->getElements());
        static::assertSame([$polygon], (new GeometricMultiPolygon([$polygon]))->getElements());

        $collection = new GeometryCollection(0, [$polygon]);
        static::assertSame([$polygon], $collection->getElements());
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
