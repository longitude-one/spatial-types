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

use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Exception\InvalidFamilyException;
use LongitudeOne\SpatialTypes\Exception\InvalidSridException;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\Point as Point2D;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\LineString as GeographicLineString;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\Point as GeographicPoint;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\LineString as GeometricLineString;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\Point as GeometricPoint;
use PHPUnit\Framework\TestCase;

/**
 * Contract tests for XYM spatial objects.
 *
 * @internal
 *
 * @coversNothing
 */
class XymContractTest extends TestCase
{
    /**
     * Verify that XYM points preserve their coordinate order and serialize it unchanged.
     */
    public function testCoordinatesUseXymOrder(): void
    {
        $point = new GeometricPoint(1, 2, 3, 2154);

        static::assertSame(1, $point->getX());
        static::assertSame(2, $point->getY());
        static::assertSame(3, $point->getM());
        static::assertSame([1, 2, 3], $point->toArray());
        static::assertSame('{"type":"Point","coordinates":[1,2,3],"srid":2154}', json_encode($point));
    }

    /**
     * Verify that geographic XYM points preserve their coordinate order.
     */
    public function testGeographicCoordinatesUseXymOrder(): void
    {
        $point = new GeographicPoint('1W', '2N', 3, 4326);

        static::assertSame(-1, $point->getX());
        static::assertSame(2, $point->getY());
        static::assertSame(3, $point->getM());
        static::assertSame([-1, 2, 3], $point->toArray());
        static::assertSame('{"type":"Point","coordinates":[-1,2,3],"srid":4326}', json_encode($point));
    }

    /**
     * Verify that an XYM line string rejects a point with an incompatible dimension.
     */
    public function testRejectsPointWithIncompatibleDimension(): void
    {
        self::expectException(InvalidDimensionException::class);

        new GeometricLineString([new Point2D(1, 2)]);
    }

    /**
     * Verify that an XYM line string rejects a point with an incompatible family.
     */
    public function testRejectsPointWithIncompatibleFamily(): void
    {
        self::expectException(InvalidFamilyException::class);

        new GeometricLineString([new GeographicPoint(1, 2, 3)]);
    }

    /**
     * Verify that an XYM line string rejects a point with an incompatible SRID.
     */
    public function testRejectsPointWithIncompatibleSrid(): void
    {
        self::expectException(InvalidSridException::class);

        new GeographicLineString([new GeographicPoint(1, 2, 3, 2154)], 4326);
    }
}
