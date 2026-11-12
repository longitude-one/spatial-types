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

namespace LongitudeOne\SpatialTypes\Factory\Dimension3z\Geometry;

use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Interfaces\ImmutablePointInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutablePointInterface;
use LongitudeOne\SpatialTypes\Internal\Factory\RepresentationFactory;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\MultiPointImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension3z\MultiPoint;

/** Creates Dimension3z Geometry MultiPoint objects in the shared mode. */
final class MultiPointFactory
{
    /**
     * Create a spatial object using the current shared representation.
     *
     * @param array<array{0: float|int|string|null, 1: float|int|string|null, 2?: float|int|null, 3?: float|int|null}|ImmutablePointInterface|MutablePointInterface> $points Constructor-compatible components or coordinate arrays
     * @param int|SpatialReference                                                                                                                                   $srid   Declared reference
     */
    public static function create(array $points, int|SpatialReference $srid = 0): MultiPoint|MultiPointImmutable
    {
        return RepresentationFactory::create(
            MultiPointImmutable::class,
            MultiPoint::class,
            [$points, $srid],
            [$points, $srid, SpatialModelEnum::GEOMETRY]
        );
    }
}
