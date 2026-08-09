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

namespace LongitudeOne\SpatialTypes\Tests\Unit\Resolver;

use LongitudeOne\SpatialTypes\Enum\FamilyEnum;
use LongitudeOne\SpatialTypes\Factory\Internal\GeographicLineStringFactory;
use LongitudeOne\SpatialTypes\Factory\Internal\GeographicPointFactory;
use LongitudeOne\SpatialTypes\Factory\Internal\GeographicPolygonFactory;
use LongitudeOne\SpatialTypes\Factory\Internal\GeometricLineStringFactory;
use LongitudeOne\SpatialTypes\Factory\Internal\GeometricPointFactory;
use LongitudeOne\SpatialTypes\Factory\Internal\GeometricPolygonFactory;
use LongitudeOne\SpatialTypes\Resolver\SpatialFamilyFactoryResolver;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Resolver\SpatialFamilyFactoryResolver
 */
class SpatialFamilyFactoryResolverTest extends TestCase
{
    /**
     * Test resolving specialized geographic factories.
     */
    public function testResolvesGeographicFactories(): void
    {
        static::assertInstanceOf(GeographicPointFactory::class, SpatialFamilyFactoryResolver::resolvePointFactory(FamilyEnum::GEOGRAPHY));
        static::assertInstanceOf(GeographicLineStringFactory::class, SpatialFamilyFactoryResolver::resolveLineStringFactory(FamilyEnum::GEOGRAPHY));
        static::assertInstanceOf(GeographicPolygonFactory::class, SpatialFamilyFactoryResolver::resolvePolygonFactory(FamilyEnum::GEOGRAPHY));
    }

    /**
     * Test resolving specialized geometric factories.
     */
    public function testResolvesGeometricFactories(): void
    {
        static::assertInstanceOf(GeometricPointFactory::class, SpatialFamilyFactoryResolver::resolvePointFactory(FamilyEnum::GEOMETRY));
        static::assertInstanceOf(GeometricLineStringFactory::class, SpatialFamilyFactoryResolver::resolveLineStringFactory(FamilyEnum::GEOMETRY));
        static::assertInstanceOf(GeometricPolygonFactory::class, SpatialFamilyFactoryResolver::resolvePolygonFactory(FamilyEnum::GEOMETRY));
    }
}
