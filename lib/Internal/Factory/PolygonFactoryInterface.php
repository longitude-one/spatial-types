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

namespace LongitudeOne\SpatialTypes\Internal\Factory;

use LongitudeOne\SpatialTypes\Factory\SpatialContext;
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
