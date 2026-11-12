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

namespace LongitudeOne\SpatialTypes\Factory\Dimension3m\Geography;

use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Internal\Factory\RepresentationFactory;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\PointImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Point;

/** Creates Dimension3m Geography Point objects in the shared mode. */
final class PointFactory
{
    /**
     * Create a spatial object using the current shared representation.
     *
     * @param float|int|string|null $x    X ordinate
     * @param float|int|string|null $y    Y ordinate
     * @param float|int|null        $m    M ordinate
     * @param int|SpatialReference  $srid Declared reference
     */
    public static function create(float|int|string|null $x = null, float|int|string|null $y = null, float|int|null $m = null, int|SpatialReference $srid = 0): Point|PointImmutable
    {
        return RepresentationFactory::create(
            PointImmutable::class,
            Point::class,
            [$x, $y, $m, $srid],
            [$x, $y, $m, $srid, SpatialModelEnum::GEOGRAPHY]
        );
    }
}
