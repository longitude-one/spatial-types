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
use LongitudeOne\SpatialTypes\Interfaces\ImmutableCurveInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutableCurveInterface;
use LongitudeOne\SpatialTypes\Internal\Factory\RepresentationFactory;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\CompoundCurve;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\CompoundCurveImmutable;

/** Creates Dimension4zm Geometry CompoundCurve objects in the shared mode. */
final class CompoundCurveFactory
{
    /**
     * Create a spatial object using the current shared representation.
     *
     * @param array<ImmutableCurveInterface|MutableCurveInterface> $curves Spatial components in the selected representation
     * @param int|SpatialReference                                 $srid   Declared reference
     */
    public static function create(array $curves = [], int|SpatialReference $srid = 0): CompoundCurve|CompoundCurveImmutable
    {
        return RepresentationFactory::create(
            CompoundCurveImmutable::class,
            CompoundCurve::class,
            [$curves, $srid],
            [$curves, $srid, SpatialModelEnum::GEOMETRY]
        );
    }
}
