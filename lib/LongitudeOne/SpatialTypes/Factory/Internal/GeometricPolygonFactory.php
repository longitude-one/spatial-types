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

namespace LongitudeOne\SpatialTypes\Factory\Internal;

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\SpatialTypes\Factory\SpatialContext;
use LongitudeOne\SpatialTypes\Interfaces\LineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\PolygonInterface;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\Polygon as Polygon2D;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\Polygon as Polygon3Dm;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\Polygon as Polygon3Dz;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\Polygon as Polygon4Dzm;

/**
 * Creates geometric polygons.
 *
 * @internal
 */
final class GeometricPolygonFactory implements PolygonFactoryInterface
{
    /**
     * Create a geometric polygon.
     *
     * @param LineStringInterface[] $rings   exterior ring followed by optional interior rings
     * @param SpatialContext        $context family, dimension, and SRID to apply
     */
    public function create(array $rings, SpatialContext $context): PolygonInterface
    {
        return match ($context->dimension) {
            CoordinateDimensionEnum::XY => new Polygon2D($rings, $context->reference),
            CoordinateDimensionEnum::XYM => new Polygon3Dm($rings, $context->reference),
            CoordinateDimensionEnum::XYZ => new Polygon3Dz($rings, $context->reference),
            CoordinateDimensionEnum::XYZM => new Polygon4Dzm($rings, $context->reference),
        };
    }
}
