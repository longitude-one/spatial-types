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

namespace LongitudeOne\SpatialTypes\Factory\Dimension2\Geography;

use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Interfaces\ImmutableCurveInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutableCurveInterface;
use LongitudeOne\SpatialTypes\Internal\Factory\RepresentationFactory;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Types\Dimension2\CompoundCurve;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\CompoundCurveImmutable;

/** Creates Dimension2 Geography CompoundCurve objects in the shared mode. */
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
            [$curves, $srid, SpatialModelEnum::GEOGRAPHY]
        );
    }
}
