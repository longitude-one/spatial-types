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
use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Interfaces\CollectionInterface;
use LongitudeOne\SpatialTypes\Types\AbstractCollection;

class GeometryCollection extends AbstractCollection implements CollectionInterface
{
    /**
     * Return the geometry family.
     */
    public function getFamily(): SpatialModelEnum
    {
        return SpatialModelEnum::GEOMETRY;
    }

    /**
     * Return the geometry collection type.
     */
    public function getType(): GeometryTypeEnum
    {
        return GeometryTypeEnum::GEOMETRYCOLLECTION;
    }

    /**
     * Return the three-dimensional coordinate layout.
     */
    protected function getDimension(): CoordinateDimensionEnum
    {
        return CoordinateDimensionEnum::XYZ;
    }
}
