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

namespace LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry;

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Types\AbstractCompoundCurve;

/** Compound curve with XYZ coordinates in the Geometry family. */
class CompoundCurve extends AbstractCompoundCurve
{
    /** Return the coordinate layout. */
    public function getDimension(): CoordinateDimensionEnum
    {
        return CoordinateDimensionEnum::XYZ;
    }

    /** Return the spatial family. */
    public function getFamily(): SpatialModelEnum
    {
        return SpatialModelEnum::GEOMETRY;
    }
}
