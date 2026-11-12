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

namespace LongitudeOne\SpatialTypes\Factory\Dimension4zm\Geometry;

use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Internal\Factory\RepresentationFactory;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\PointImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Point;

/** Creates Dimension4zm Geometry Point objects in the shared mode. */
final class PointFactory
{
    /**
     * Create a spatial object using the current shared representation.
     *
     * @param float|int|string|null $x    X ordinate
     * @param float|int|string|null $y    Y ordinate
     * @param float|int|null        $z    Z ordinate
     * @param float|int|null        $m    M ordinate
     * @param int|SpatialReference  $srid Declared reference
     */
    public static function create(float|int|string|null $x = null, float|int|string|null $y = null, float|int|null $z = null, float|int|null $m = null, int|SpatialReference $srid = 0): Point|PointImmutable
    {
        return RepresentationFactory::create(
            PointImmutable::class,
            Point::class,
            [$x, $y, $z, $m, $srid],
            [$x, $y, $z, $m, $srid, SpatialModelEnum::GEOMETRY]
        );
    }
}
