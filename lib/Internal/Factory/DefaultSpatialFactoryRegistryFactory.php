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

namespace LongitudeOne\SpatialTypes\Internal\Factory;

use LongitudeOne\Core\Enum\SpatialModelEnum;

/**
 * Creates the default registry used by the internal factory pipeline.
 *
 * @internal this registry builder is an implementation detail
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
                SpatialModelEnum::GEOGRAPHY,
                new GeographicPointFactory(),
                new GeographicLineStringFactory(),
                new GeographicPolygonFactory()
            ),
            new FamilyFactories(
                SpatialModelEnum::GEOMETRY,
                new GeometricPointFactory(),
                new GeometricLineStringFactory(),
                new GeometricPolygonFactory()
            )
        );
    }
}
