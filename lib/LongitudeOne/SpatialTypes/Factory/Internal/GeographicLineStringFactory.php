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
use LongitudeOne\SpatialTypes\Interfaces\PointInterface;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\LineString as LineString2D;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\LineString as LineString3Dm;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\LineString as LineString3Dz;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\LineString as LineString4Dzm;

/**
 * Creates geographic line strings.
 *
 * @internal
 */
final class GeographicLineStringFactory implements LineStringFactoryInterface
{
    /**
     * Create a geographic line string.
     *
     * @param PointInterface[] $points  points composing the line string
     * @param SpatialContext   $context family, dimension, and SRID to apply
     */
    public function create(array $points, SpatialContext $context): LineStringInterface
    {
        return match ($context->dimension) {
            DimensionEnum::X_Y => new LineString2D($points, $context->reference),
            DimensionEnum::X_Y_M => new LineString3Dm($points, $context->reference),
            DimensionEnum::X_Y_Z => new LineString3Dz($points, $context->reference),
            DimensionEnum::X_Y_Z_M => new LineString4Dzm($points, $context->reference),
        };
    }
}
