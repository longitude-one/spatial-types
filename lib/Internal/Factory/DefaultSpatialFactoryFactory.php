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

use LongitudeOne\SpatialTypes\Factory\SpatialFactory;
use LongitudeOne\SpatialTypes\Internal\Factory\Hydrator\CoordinatesHydrator;
use LongitudeOne\SpatialTypes\Internal\Factory\Hydrator\SpatialArrayHydrator;

/**
 * Creates the default spatial factory used by internal aggregate hydration.
 *
 * @internal this factory builder is an implementation detail
 */
final class DefaultSpatialFactoryFactory
{
    private static ?SpatialFactory $factory = null;

    /**
     * Create the default spatial factory once.
     */
    public static function create(): SpatialFactory
    {
        if (null === self::$factory) {
            $factoryRegistry = DefaultSpatialFactoryRegistryFactory::create();
            $coordinatesHydrator = new CoordinatesHydrator();

            self::$factory = new SpatialFactory(
                $factoryRegistry,
                $coordinatesHydrator,
                new SpatialArrayHydrator($factoryRegistry, $coordinatesHydrator)
            );
        }

        return self::$factory;
    }
}
