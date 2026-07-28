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

namespace LongitudeOne\SpatialTypes\Resolver;

use LongitudeOne\SpatialTypes\Enum\FamilyEnum;
use LongitudeOne\SpatialTypes\Factory\Internal\GeographicSpatialFamilyFactory;
use LongitudeOne\SpatialTypes\Factory\Internal\GeometricSpatialFamilyFactory;
use LongitudeOne\SpatialTypes\Factory\Internal\SpatialFamilyFactoryInterface;

/**
 * Resolves the factory that creates objects for a spatial family.
 *
 * @internal
 */
final class SpatialFamilyFactoryResolver
{
    /**
     * Resolves the factory associated with a given spatial family.
     *
     * @param FamilyEnum $family the spatial family to resolve
     *
     * @return SpatialFamilyFactoryInterface the factory that creates objects for the requested family
     */
    public static function resolve(FamilyEnum $family): SpatialFamilyFactoryInterface
    {
        return match ($family) {
            FamilyEnum::GEOGRAPHY => new GeographicSpatialFamilyFactory(),
            FamilyEnum::GEOMETRY => new GeometricSpatialFamilyFactory(),
        };
    }
}
