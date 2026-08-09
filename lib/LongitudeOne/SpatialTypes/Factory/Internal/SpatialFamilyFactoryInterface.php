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

use LongitudeOne\SpatialTypes\Enum\DimensionEnum;
use LongitudeOne\SpatialTypes\Interfaces\LineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\PointInterface;
use LongitudeOne\SpatialTypes\Interfaces\PolygonInterface;

/**
 * Creates spatial types belonging to one spatial family.
 *
 * @internal
 */
interface SpatialFamilyFactoryInterface
{
    /**
     * Create a line string from points.
     *
     * @param PointInterface[] $points    points
     * @param int              $srid      SRID
     * @param DimensionEnum    $dimension dimension
     *
     * @return LineStringInterface The line string matching the requested dimension
     */
    public function createLineString(array $points, int $srid, DimensionEnum $dimension): LineStringInterface;

    /**
     * Create a point of the specified dimension.
     *
     * @param float|int|string $x         the X coordinate of the point, the longitude
     * @param float|int|string $y         the Y coordinate of the point, the latitude
     * @param null|float|int   $z         the Z coordinate of the point, the elevation
     * @param null|float|int   $m         the M coordinate of the point
     * @param int              $srid      the spatial reference identifier
     * @param DimensionEnum    $dimension the dimension of the point
     *
     * @return PointInterface The point matching the requested dimension
     */
    public function createPoint(float|int|string $x, float|int|string $y, float|int|null $z, float|int|null $m, int $srid, DimensionEnum $dimension): PointInterface;

    /**
     * Create a polygon from closed line strings.
     *
     * @param LineStringInterface[] $rings     rings
     * @param int                   $srid      SRID
     * @param DimensionEnum         $dimension dimension
     *
     * @return PolygonInterface The polygon matching the requested dimension
     */
    public function createPolygon(array $rings, int $srid, DimensionEnum $dimension): PolygonInterface;
}
