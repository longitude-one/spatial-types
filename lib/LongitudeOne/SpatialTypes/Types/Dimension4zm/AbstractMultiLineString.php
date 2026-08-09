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

namespace LongitudeOne\SpatialTypes\Types\Dimension4zm;

use LongitudeOne\SpatialTypes\Enum\DimensionEnum;
use LongitudeOne\SpatialTypes\Enum\TypeEnum;
use LongitudeOne\SpatialTypes\Interfaces\MultiLineStringInterface;
use LongitudeOne\SpatialTypes\Types\AbstractMultiLineString as ParentMultiLineString;

abstract class AbstractMultiLineString extends ParentMultiLineString implements MultiLineStringInterface
{
    public function getType(): TypeEnum
    {
        return TypeEnum::MULTILINESTRING;
    }

    protected function getDimension(): DimensionEnum
    {
        return DimensionEnum::X_Y_Z_M;
    }
}
