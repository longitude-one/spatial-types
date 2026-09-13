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
use LongitudeOne\SpatialTypes\Interfaces\MultiLineStringInterface;
use LongitudeOne\SpatialTypes\Types\AbstractMultiLineString as ParentMultiLineString;

/**
 * Base class for multi-line strings using the XYM layout.
 */
abstract class AbstractMultiLineString extends ParentMultiLineString implements MultiLineStringInterface
{
    /**
     * Return the multi-line string type.
     */
    public function getType(): GeometryTypeEnum
    {
        return GeometryTypeEnum::MULTILINESTRING;
    }

    /**
     * Return the XYM dimension.
     */
    protected function getDimension(): CoordinateDimensionEnum
    {
        return CoordinateDimensionEnum::XYM;
    }
}
