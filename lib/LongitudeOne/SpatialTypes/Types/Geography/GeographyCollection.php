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

namespace LongitudeOne\SpatialTypes\Types\Geography;

use LongitudeOne\SpatialTypes\Enum\DimensionEnum;
use LongitudeOne\SpatialTypes\Enum\FamilyEnum;
use LongitudeOne\SpatialTypes\Enum\TypeEnum;
use LongitudeOne\SpatialTypes\Interfaces\CollectionInterface;
use LongitudeOne\SpatialTypes\Types\AbstractCollection;

class GeographyCollection extends AbstractCollection implements CollectionInterface
{
    /**
     * initDimension.
     */
    protected function initDimension(): DimensionEnum
    {
        return DimensionEnum::X_Y;
    }

    /**
     * init Family.
     */
    protected function initFamily(): FamilyEnum
    {
        return FamilyEnum::GEOGRAPHY;
    }

    /**
     * initType.
     */
    protected function initType(): TypeEnum
    {
        return TypeEnum::COLLECTION;
    }
}
