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

use LongitudeOne\SpatialTypes\Enum\DimensionEnum;
use LongitudeOne\SpatialTypes\Factory\SpatialContext;
use LongitudeOne\SpatialTypes\Interfaces\LineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\PolygonInterface;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\Polygon as Polygon2D;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\Polygon as Polygon3Dm;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\Polygon as Polygon3Dz;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\Polygon as Polygon4Dzm;

/**
 * Creates geographic polygons.
 *
 * @internal
 */
final class GeographicPolygonFactory implements PolygonFactoryInterface
{
    /**
     * Create a geographic polygon.
     *
     * @param LineStringInterface[] $rings   exterior ring followed by optional interior rings
     * @param SpatialContext        $context family, dimension, and SRID to apply
     */
    public function create(array $rings, SpatialContext $context): PolygonInterface
    {
        return match ($context->dimension) {
            DimensionEnum::X_Y => new Polygon2D($rings, $context->reference),
            DimensionEnum::X_Y_M => new Polygon3Dm($rings, $context->reference),
            DimensionEnum::X_Y_Z => new Polygon3Dz($rings, $context->reference),
            DimensionEnum::X_Y_Z_M => new Polygon4Dzm($rings, $context->reference),
        };
    }
}
