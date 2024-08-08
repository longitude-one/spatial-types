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
 * Family enumeration.
 *
 * This enumeration is used to define the family of spatial instances.
 *
 * Geography instances use by default Longitude and latitude.
 * So, coordinates are in degrees and shall respect ranges.
 *
 * Geometric instances use by default Cartesian coordinates.
 * So, coordinates shall not respect any ranges.
 */
enum FamilyEnum: string
{
    case GEOGRAPHY = 'Geography';
    case GEOMETRY = 'Geometry';
}
