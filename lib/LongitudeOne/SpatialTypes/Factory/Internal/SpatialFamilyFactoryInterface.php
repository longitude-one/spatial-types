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
     * @param PointInterface[] $points points
     * @param ?int             $srid   SRID
     */
    public function createLineString(array $points, ?int $srid = null): LineStringInterface;

    /**
     * Create a two-dimensional point.
     *
     * @param float|int|string $x    X coordinate or longitude
     * @param float|int|string $y    Y coordinate or latitude
     * @param ?int             $srid SRID
     */
    public function createPoint(float|int|string $x, float|int|string $y, ?int $srid = null): PointInterface;

    /**
     * Create a polygon from closed line strings.
     *
     * @param LineStringInterface[] $rings rings
     * @param ?int                  $srid  SRID
     */
    public function createPolygon(array $rings, ?int $srid = null): PolygonInterface;
}
