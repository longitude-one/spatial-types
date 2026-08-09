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
use LongitudeOne\SpatialTypes\Factory\Coordinates;
use LongitudeOne\SpatialTypes\Factory\SpatialContext;
use LongitudeOne\SpatialTypes\Interfaces\PointInterface;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\Point as Point2D;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\Point as Point3Dm;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\Point as Point3Dz;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\Point as Point4Dzm;

/**
 * Creates geographic points.
 *
 * @internal
 */
final class GeographicPointFactory extends AbstractPointFactory
{
    /**
     * Create a geographic point.
     *
     * @param Coordinates    $coordinates normalized longitude, latitude, Z, and M values
     * @param SpatialContext $context     family, dimension, and SRID to apply
     */
    public function create(Coordinates $coordinates, SpatialContext $context): PointInterface
    {
        return match ($context->dimension) {
            DimensionEnum::X_Y => new Point2D($coordinates->x, $coordinates->y, $context->srid),
            DimensionEnum::X_Y_M => new Point3Dm($coordinates->x, $coordinates->y, self::requiredCoordinate($coordinates->m, 'third'), $context->srid),
            DimensionEnum::X_Y_Z => new Point3Dz($coordinates->x, $coordinates->y, self::requiredCoordinate($coordinates->z, 'third'), $context->srid),
            DimensionEnum::X_Y_Z_M => new Point4Dzm($coordinates->x, $coordinates->y, self::requiredCoordinate($coordinates->z, 'third'), self::requiredCoordinate($coordinates->m, 'fourth'), $context->srid),
        };
    }
}
