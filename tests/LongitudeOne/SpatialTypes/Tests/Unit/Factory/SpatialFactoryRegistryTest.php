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

namespace LongitudeOne\SpatialTypes\Tests\Unit\Factory;

use LongitudeOne\SpatialTypes\Enum\FamilyEnum;
use LongitudeOne\SpatialTypes\Factory\DefaultSpatialFactoryRegistryFactory;
use LongitudeOne\SpatialTypes\Factory\Internal\GeographicLineStringFactory;
use LongitudeOne\SpatialTypes\Factory\Internal\GeographicPointFactory;
use LongitudeOne\SpatialTypes\Factory\Internal\GeographicPolygonFactory;
use LongitudeOne\SpatialTypes\Factory\Internal\GeometricLineStringFactory;
use LongitudeOne\SpatialTypes\Factory\Internal\GeometricPointFactory;
use LongitudeOne\SpatialTypes\Factory\Internal\GeometricPolygonFactory;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Factory\DefaultSpatialFactoryRegistryFactory
 * @covers \LongitudeOne\SpatialTypes\Factory\SpatialFactoryRegistry
 */
class SpatialFactoryRegistryTest extends TestCase
{
    /**
     * Test the default registry is built once.
     */
    public function testDefaultRegistryIsBuiltOnce(): void
    {
        static::assertSame(DefaultSpatialFactoryRegistryFactory::create(), DefaultSpatialFactoryRegistryFactory::create());
    }

    /**
     * Test the default registry provides geographic constructors.
     */
    public function testProvidesGeographicFactories(): void
    {
        $registry = DefaultSpatialFactoryRegistryFactory::create();

        static::assertInstanceOf(GeographicPointFactory::class, $registry->pointFactory(FamilyEnum::GEOGRAPHY));
        static::assertInstanceOf(GeographicLineStringFactory::class, $registry->lineStringFactory(FamilyEnum::GEOGRAPHY));
        static::assertInstanceOf(GeographicPolygonFactory::class, $registry->polygonFactory(FamilyEnum::GEOGRAPHY));
    }

    /**
     * Test the default registry provides geometric constructors.
     */
    public function testProvidesGeometricFactories(): void
    {
        $registry = DefaultSpatialFactoryRegistryFactory::create();

        static::assertInstanceOf(GeometricPointFactory::class, $registry->pointFactory(FamilyEnum::GEOMETRY));
        static::assertInstanceOf(GeometricLineStringFactory::class, $registry->lineStringFactory(FamilyEnum::GEOMETRY));
        static::assertInstanceOf(GeometricPolygonFactory::class, $registry->polygonFactory(FamilyEnum::GEOMETRY));
    }
}
