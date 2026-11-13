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
        self::assertSame(DefaultSpatialFactoryRegistryFactory::create(), DefaultSpatialFactoryRegistryFactory::create());
    }

    /**
     * Test the default spatial factory is built once.
     */
    public function testDefaultSpatialFactoryIsBuiltOnce(): void
    {
        self::assertSame(DefaultSpatialFactoryFactory::create(), DefaultSpatialFactoryFactory::create());
    }

    /**
     * Test the default registry provides geographic constructors.
     */
    public function testProvidesGeographicFactories(): void
    {
        $registry = DefaultSpatialFactoryRegistryFactory::create();

        self::assertInstanceOf(GeographicPointFactory::class, $registry->pointFactory(SpatialModelEnum::GEOGRAPHY));
        self::assertInstanceOf(GeographicLineStringFactory::class, $registry->lineStringFactory(SpatialModelEnum::GEOGRAPHY));
        self::assertInstanceOf(GeographicPolygonFactory::class, $registry->polygonFactory(SpatialModelEnum::GEOGRAPHY));
    }

    /**
     * Test the default registry provides geometric constructors.
     */
    public function testProvidesGeometricFactories(): void
    {
        $registry = DefaultSpatialFactoryRegistryFactory::create();

        self::assertInstanceOf(GeometricPointFactory::class, $registry->pointFactory(SpatialModelEnum::GEOMETRY));
        self::assertInstanceOf(GeometricLineStringFactory::class, $registry->lineStringFactory(SpatialModelEnum::GEOMETRY));
        self::assertInstanceOf(GeometricPolygonFactory::class, $registry->polygonFactory(SpatialModelEnum::GEOMETRY));
    }
}
