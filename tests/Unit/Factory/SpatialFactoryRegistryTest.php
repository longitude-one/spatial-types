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

use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Internal\Factory\DefaultSpatialFactoryFactory;
use LongitudeOne\SpatialTypes\Internal\Factory\DefaultSpatialFactoryRegistryFactory;
use LongitudeOne\SpatialTypes\Internal\Factory\GeographicLineStringFactory;
use LongitudeOne\SpatialTypes\Internal\Factory\GeographicPointFactory;
use LongitudeOne\SpatialTypes\Internal\Factory\GeographicPolygonFactory;
use LongitudeOne\SpatialTypes\Internal\Factory\GeometricLineStringFactory;
use LongitudeOne\SpatialTypes\Internal\Factory\GeometricPointFactory;
use LongitudeOne\SpatialTypes\Internal\Factory\GeometricPolygonFactory;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Internal\Factory\DefaultSpatialFactoryFactory
 * @covers \LongitudeOne\SpatialTypes\Internal\Factory\DefaultSpatialFactoryRegistryFactory
 * @covers \LongitudeOne\SpatialTypes\Internal\Factory\SpatialFactoryRegistry
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
     * Test the default spatial factory is built once.
     */
    public function testDefaultSpatialFactoryIsBuiltOnce(): void
    {
        static::assertSame(DefaultSpatialFactoryFactory::create(), DefaultSpatialFactoryFactory::create());
    }

    /**
     * Test the default registry provides geographic constructors.
     */
    public function testProvidesGeographicFactories(): void
    {
        $registry = DefaultSpatialFactoryRegistryFactory::create();

        static::assertInstanceOf(GeographicPointFactory::class, $registry->pointFactory(SpatialModelEnum::GEOGRAPHY));
        static::assertInstanceOf(GeographicLineStringFactory::class, $registry->lineStringFactory(SpatialModelEnum::GEOGRAPHY));
        static::assertInstanceOf(GeographicPolygonFactory::class, $registry->polygonFactory(SpatialModelEnum::GEOGRAPHY));
    }

    /**
     * Test the default registry provides geometric constructors.
     */
    public function testProvidesGeometricFactories(): void
    {
        $registry = DefaultSpatialFactoryRegistryFactory::create();

        static::assertInstanceOf(GeometricPointFactory::class, $registry->pointFactory(SpatialModelEnum::GEOMETRY));
        static::assertInstanceOf(GeometricLineStringFactory::class, $registry->lineStringFactory(SpatialModelEnum::GEOMETRY));
        static::assertInstanceOf(GeometricPolygonFactory::class, $registry->polygonFactory(SpatialModelEnum::GEOMETRY));
    }
}
