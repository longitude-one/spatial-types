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

namespace LongitudeOne\SpatialTypes\Factory\Internal;

use LongitudeOne\SpatialTypes\Interfaces\LineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\PointInterface;
use LongitudeOne\SpatialTypes\Interfaces\PolygonInterface;
use LongitudeOne\SpatialTypes\Types\Geography\LineString;
use LongitudeOne\SpatialTypes\Types\Geography\Point;
use LongitudeOne\SpatialTypes\Types\Geography\Polygon;

/**
 * Creates geographic spatial types.
 *
 * @internal
 */
final class GeographicSpatialFamilyFactory implements SpatialFamilyFactoryInterface
{
    /**
     * Creates a geographic line string instance.
     *
     * @param PointInterface[] $points the points that compose the line string
     * @param null|int         $srid   the spatial reference identifier
     *
     * @return LineStringInterface the created geographic line string
     */
    public function createLineString(array $points, ?int $srid = null): LineStringInterface
    {
        return new LineString($points, $srid);
    }

    /**
     * Creates a geographic point instance.
     *
     * @param float|int|string $x    the X coordinate of the point
     * @param float|int|string $y    the Y coordinate of the point
     * @param null|int         $srid the spatial reference identifier
     *
     * @return PointInterface the created geographic point
     */
    public function createPoint(float|int|string $x, float|int|string $y, ?int $srid = null): PointInterface
    {
        return new Point($x, $y, $srid);
    }

    /**
     * Creates a geographic polygon instance.
     *
     * @param LineString[] $rings the rings that compose the polygon
     * @param null|int     $srid  the spatial reference identifier
     *
     * @return PolygonInterface the created geographic polygon
     */
    public function createPolygon(array $rings, ?int $srid = null): PolygonInterface
    {
        return new Polygon($rings, $srid);
    }
}
