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

use LongitudeOne\SpatialTypes\Interfaces\ImmutableLineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\ImmutablePolygonInterface;

/**
 * Creates polygons for a spatial family.
 *
 * @internal
 */
interface PolygonFactoryInterface
{
    /**
     * Create a polygon.
     *
     * @param ImmutableLineStringInterface[] $rings   exterior ring followed by optional interior rings
     * @param SpatialContext                 $context family, dimension, and SRID to apply
     */
    public function create(array $rings, SpatialContext $context): ImmutablePolygonInterface;
}
