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
use LongitudeOne\SpatialTypes\Factory\Internal\GeographicLineStringFactory;
use LongitudeOne\SpatialTypes\Factory\Internal\GeographicPointFactory;
use LongitudeOne\SpatialTypes\Factory\Internal\GeographicPolygonFactory;
use LongitudeOne\SpatialTypes\Factory\Internal\GeometricLineStringFactory;
use LongitudeOne\SpatialTypes\Factory\Internal\GeometricPointFactory;
use LongitudeOne\SpatialTypes\Factory\Internal\GeometricPolygonFactory;
use LongitudeOne\SpatialTypes\Factory\Internal\LineStringFactoryInterface;
use LongitudeOne\SpatialTypes\Factory\Internal\PointFactoryInterface;
use LongitudeOne\SpatialTypes\Factory\Internal\PolygonFactoryInterface;

/**
 * Resolves specialized factories for a spatial family.
 *
 * @internal
 */
final class SpatialFamilyFactoryResolver
{
    /**
     * Resolve a line string factory associated with a spatial family.
     *
     * @param FamilyEnum $family the spatial family to resolve
     *
     * @return LineStringFactoryInterface the factory for the requested family
     */
    public static function resolveLineStringFactory(FamilyEnum $family): LineStringFactoryInterface
    {
        return match ($family) {
            FamilyEnum::GEOGRAPHY => new GeographicLineStringFactory(),
            FamilyEnum::GEOMETRY => new GeometricLineStringFactory(),
        };
    }

    /**
     * Resolve a point factory associated with a spatial family.
     *
     * @param FamilyEnum $family the spatial family to resolve
     *
     * @return PointFactoryInterface the factory for the requested family
     */
    public static function resolvePointFactory(FamilyEnum $family): PointFactoryInterface
    {
        return match ($family) {
            FamilyEnum::GEOGRAPHY => new GeographicPointFactory(),
            FamilyEnum::GEOMETRY => new GeometricPointFactory(),
        };
    }

    /**
     * Resolve a polygon factory associated with a spatial family.
     *
     * @param FamilyEnum $family the spatial family to resolve
     *
     * @return PolygonFactoryInterface the factory for the requested family
     */
    public static function resolvePolygonFactory(FamilyEnum $family): PolygonFactoryInterface
    {
        return match ($family) {
            FamilyEnum::GEOGRAPHY => new GeographicPolygonFactory(),
            FamilyEnum::GEOMETRY => new GeometricPolygonFactory(),
        };
    }
}
