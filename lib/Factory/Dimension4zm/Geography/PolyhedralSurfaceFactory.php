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

namespace LongitudeOne\SpatialTypes\Factory\Dimension4zm\Geography;

use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Interfaces\ImmutableLineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\ImmutablePointInterface;
use LongitudeOne\SpatialTypes\Interfaces\ImmutablePolygonInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutableLineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutablePointInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutablePolygonInterface;
use LongitudeOne\SpatialTypes\Internal\Factory\RepresentationFactory;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\PolyhedralSurfaceImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\PolyhedralSurface;

/** Creates Dimension4zm Geography PolyhedralSurface objects in the shared mode. */
final class PolyhedralSurfaceFactory
{
    /**
     * Create a spatial object using the current shared representation.
     *
     * @param array<array<array<array{0: null|float|int|string, 1: null|float|int|string, 2?: null|float|int, 3?: null|float|int}|ImmutablePointInterface|MutablePointInterface>|ImmutableLineStringInterface|MutableLineStringInterface>|ImmutablePolygonInterface|MutablePolygonInterface> $patches Constructor-compatible components or coordinate arrays
     * @param int|SpatialReference                                                                                                                                                                                                                                                           $srid    Declared reference
     */
    public static function create(array $patches = [], int|SpatialReference $srid = 0): PolyhedralSurface|PolyhedralSurfaceImmutable
    {
        return RepresentationFactory::create(
            PolyhedralSurfaceImmutable::class,
            PolyhedralSurface::class,
            [$patches, $srid],
            [$patches, $srid, SpatialModelEnum::GEOGRAPHY]
        );
    }
}
