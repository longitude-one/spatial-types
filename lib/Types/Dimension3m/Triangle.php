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

namespace LongitudeOne\SpatialTypes\Types\Dimension3m;

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\SpatialTypes\Interfaces\MutableTriangleInterface;
use LongitudeOne\SpatialTypes\Types\AbstractPolygon;

/** Mutable XYM Triangle with instance-level declared family. */
final class Triangle extends AbstractPolygon implements MutableTriangleInterface
{
    /** Return the fixed coordinate layout. */
    public function getDimension(): CoordinateDimensionEnum
    {
        return CoordinateDimensionEnum::XYM;
    }

    /** Return the fixed spatial type. */
    public function getType(): GeometryTypeEnum
    {
        return GeometryTypeEnum::TRIANGLE;
    }
}
