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

namespace LongitudeOne\SpatialTypes\Factory;

use LongitudeOne\SpatialTypes\Enum\DimensionEnum;
use LongitudeOne\SpatialTypes\Enum\FamilyEnum;
use LongitudeOne\SpatialTypes\Interfaces\SpatialInterface;

/**
 * Immutable context used to create a spatial type.
 *
 * @internal this context supports the internal factory pipeline
 */
final readonly class SpatialContext
{
    /**
     * @param int           $srid      Spatial Reference Identifier
     * @param FamilyEnum    $family    Spatial family
     * @param DimensionEnum $dimension Coordinate dimension
     */
    public function __construct(
        public int $srid = SpatialInterface::DEFAULT_SRID,
        public FamilyEnum $family = FamilyEnum::GEOMETRY,
        public DimensionEnum $dimension = DimensionEnum::X_Y
    ) {
    }
}
