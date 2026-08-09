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

namespace LongitudeOne\SpatialTypes\Factory;

use LongitudeOne\SpatialTypes\Enum\FamilyEnum;
use LongitudeOne\SpatialTypes\Factory\Internal\GeographicLineStringFactory;
use LongitudeOne\SpatialTypes\Factory\Internal\GeographicPointFactory;
use LongitudeOne\SpatialTypes\Factory\Internal\GeographicPolygonFactory;
use LongitudeOne\SpatialTypes\Factory\Internal\GeometricLineStringFactory;
use LongitudeOne\SpatialTypes\Factory\Internal\GeometricPointFactory;
use LongitudeOne\SpatialTypes\Factory\Internal\GeometricPolygonFactory;

/**
 * Creates the default registry used by the static compatibility facades.
 */
final class DefaultSpatialFactoryRegistryFactory
{
    private static ?SpatialFactoryRegistry $registry = null;

    /**
     * Create the default registry once.
     */
    public static function create(): SpatialFactoryRegistry
    {
        return self::$registry ??= new SpatialFactoryRegistry(
            new FamilyFactories(
                FamilyEnum::GEOGRAPHY,
                new GeographicPointFactory(),
                new GeographicLineStringFactory(),
                new GeographicPolygonFactory()
            ),
            new FamilyFactories(
                FamilyEnum::GEOMETRY,
                new GeometricPointFactory(),
                new GeometricLineStringFactory(),
                new GeometricPolygonFactory()
            )
        );
    }
}
