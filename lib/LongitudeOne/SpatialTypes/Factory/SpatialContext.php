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

use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Enum\DimensionEnum;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;

/**
 * Immutable context used to create a spatial type.
 *
 * @internal this context supports the internal factory pipeline
 */
final readonly class SpatialContext
{
    /** Spatial reference propagated to every value created by this context. */
    public SpatialReference $reference;

    /** Legacy SRID view retained for source compatibility with factory clients. */
    public int $srid;

    /**
     * @param int|SpatialReference $srid      Spatial reference or legacy SRID
     * @param SpatialModelEnum     $family    Spatial family
     * @param DimensionEnum        $dimension Coordinate dimension
     */
    public function __construct(
        int|SpatialReference $srid = 0,
        public SpatialModelEnum $family = SpatialModelEnum::GEOMETRY,
        public DimensionEnum $dimension = DimensionEnum::X_Y
    ) {
        $this->reference = $srid instanceof SpatialReference ? $srid : SpatialReference::fromSrid($srid);
        $this->srid = $this->reference->srid();
    }
}
