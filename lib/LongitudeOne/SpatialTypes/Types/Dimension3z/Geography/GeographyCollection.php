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

namespace LongitudeOne\SpatialTypes\Types\Dimension3z\Geography;

use LongitudeOne\SpatialTypes\Enum\DimensionEnum;
use LongitudeOne\SpatialTypes\Enum\FamilyEnum;
use LongitudeOne\SpatialTypes\Enum\TypeEnum;
use LongitudeOne\SpatialTypes\Interfaces\CollectionInterface;
use LongitudeOne\SpatialTypes\Types\AbstractCollection;

class GeographyCollection extends AbstractCollection implements CollectionInterface
{
    /**
     * Return the geography family.
     */
    public function getFamily(): FamilyEnum
    {
        return FamilyEnum::GEOGRAPHY;
    }

    /**
     * Return the geometry collection type.
     */
    public function getType(): TypeEnum
    {
        return TypeEnum::COLLECTION;
    }

    /**
     * Return the three-dimensional coordinate layout.
     */
    protected function getDimension(): DimensionEnum
    {
        return DimensionEnum::X_Y_Z;
    }
}
