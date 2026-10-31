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

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\SpatialTypes\Interfaces\ImmutableLineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\ImmutablePolygonInterface;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\PolygonImmutable as Polygon2D;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\PolygonImmutable as Polygon3Dm;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\PolygonImmutable as Polygon3Dz;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\PolygonImmutable as Polygon4Dzm;

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
     * @param ImmutableLineStringInterface[] $rings   exterior ring followed by optional interior rings
     * @param SpatialContext                 $context family, dimension, and SRID to apply
     */
    public function create(array $rings, SpatialContext $context): ImmutablePolygonInterface
    {
        return match ($context->dimension) {
            CoordinateDimensionEnum::XY => new Polygon2D($rings, $context->reference),
            CoordinateDimensionEnum::XYM => new Polygon3Dm($rings, $context->reference),
            CoordinateDimensionEnum::XYZ => new Polygon3Dz($rings, $context->reference),
            CoordinateDimensionEnum::XYZM => new Polygon4Dzm($rings, $context->reference),
        };
    }
}
