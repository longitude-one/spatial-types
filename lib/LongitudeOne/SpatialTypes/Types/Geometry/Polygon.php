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

namespace LongitudeOne\SpatialTypes\Types\Geometry;

use LongitudeOne\SpatialTypes\Enum\DimensionEnum;
use LongitudeOne\SpatialTypes\Enum\FamilyEnum;
use LongitudeOne\SpatialTypes\Enum\TypeEnum;
use LongitudeOne\SpatialTypes\Interfaces\PolygonInterface;
use LongitudeOne\SpatialTypes\Types\AbstractPolygon;

class Polygon extends AbstractPolygon implements PolygonInterface
{
    /**
     * Declare the family of the object.
     */
    public function getFamily(): FamilyEnum
    {
        return FamilyEnum::GEOMETRY;
    }

    /**
     * Declare the type of the object.
     */
    public function getType(): string
    {
        return TypeEnum::POLYGON->value;
    }

    /**
     * Declare the dimension of the object.
     */
    protected function getDimension(): DimensionEnum
    {
        return DimensionEnum::X_Y;
    }
}
