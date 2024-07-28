<?php
/**
 * This file is part of the spatial project.
 *
 * PHP 8.1 | 8.2 | 8.3
 *
 * Copyright Alexandre Tranchant <alexandre.tranchant@gmail.com> 2024
 * Copyright Longitude One 2024
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

declare(strict_types=1);

namespace LongitudeOne\SpatialTypes\Enum;

/**
 * Dimension enumeration.
 *
 * This enumeration is used to define the dimensions of a geometry or a geography instance.
 */
enum DimensionEnum: string
{
    // 2 dimensions
    case X_Y = 'XY';
    // 2 spatial dimensions and 1 time dimension
    case X_Y_M = 'XYM';
    // 3 spatial dimensions
    case X_Y_Z = 'XYZ';
    // 4 dimensions (spatial and time)
    case X_Y_Z_M = 'XYZM';
}
