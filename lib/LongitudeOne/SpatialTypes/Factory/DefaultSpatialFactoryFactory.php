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

use LongitudeOne\SpatialTypes\Factory\Hydrator\CoordinatesHydrator;
use LongitudeOne\SpatialTypes\Factory\Hydrator\SpatialArrayHydrator;

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
