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
use LongitudeOne\SpatialTypes\Factory\SpatialContext;
use LongitudeOne\SpatialTypes\Interfaces\ImmutableLineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\ImmutablePointInterface;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineStringImmutable as LineString2D;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\LineStringImmutable as LineString3Dm;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\LineStringImmutable as LineString3Dz;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\LineStringImmutable as LineString4Dzm;

/**
 * Creates geometric line strings.
 *
 * @internal
 */
final class GeometricLineStringFactory implements LineStringFactoryInterface
{
    /**
     * Create a geometric line string.
     *
     * @param ImmutablePointInterface[] $points  points composing the line string
     * @param SpatialContext            $context family, dimension, and SRID to apply
     */
    public function create(array $points, SpatialContext $context): ImmutableLineStringInterface
    {
        return match ($context->dimension) {
            CoordinateDimensionEnum::XY => new LineString2D($points, $context->reference),
            CoordinateDimensionEnum::XYM => new LineString3Dm($points, $context->reference),
            CoordinateDimensionEnum::XYZ => new LineString3Dz($points, $context->reference),
            CoordinateDimensionEnum::XYZM => new LineString4Dzm($points, $context->reference),
        };
    }
}
