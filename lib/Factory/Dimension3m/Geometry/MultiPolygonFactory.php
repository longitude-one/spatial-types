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

namespace LongitudeOne\SpatialTypes\Factory\Dimension3m\Geometry;

use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Interfaces\ImmutableLineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\ImmutablePointInterface;
use LongitudeOne\SpatialTypes\Interfaces\ImmutablePolygonInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutableLineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutablePointInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutablePolygonInterface;
use LongitudeOne\SpatialTypes\Internal\Factory\RepresentationFactory;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\MultiPolygonImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension3m\MultiPolygon;

/** Creates Dimension3m Geometry MultiPolygon objects in the shared mode. */
final class MultiPolygonFactory
{
    /**
     * Create a spatial object using the current shared representation.
     *
     * @param array<array<array<array{0: float|int|string|null, 1: float|int|string|null, 2?: float|int|null, 3?: float|int|null}|ImmutablePointInterface|MutablePointInterface>|ImmutableLineStringInterface|MutableLineStringInterface>|ImmutablePolygonInterface|MutablePolygonInterface> $polygons Constructor-compatible components or coordinate arrays
     * @param int|SpatialReference                                                                                                                                                                                                                                                           $srid     Declared reference
     */
    public static function create(array $polygons, int|SpatialReference $srid = 0): MultiPolygon|MultiPolygonImmutable
    {
        return RepresentationFactory::create(
            MultiPolygonImmutable::class,
            MultiPolygon::class,
            [$polygons, $srid],
            [$polygons, $srid, SpatialModelEnum::GEOMETRY]
        );
    }
}
