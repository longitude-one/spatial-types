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

namespace LongitudeOne\SpatialTypes\Internal\Types\Dimension4zm;

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\SpatialTypes\Interfaces\ImmutableMultiLineStringInterface;
use LongitudeOne\SpatialTypes\Internal\Types\AbstractMultiLineStringImmutable as ParentMultiLineString;

/**
 * Dimension-specific immutable implementation base.
 *
 * @internal
 */
abstract class AbstractMultiLineStringImmutable extends ParentMultiLineString implements ImmutableMultiLineStringInterface
{
    /**
     * Define the four-dimensional XYZM coordinate layout.
     */
    public function getDimension(): CoordinateDimensionEnum
    {
        return CoordinateDimensionEnum::XYZM;
    }

    /**
     * Define the multi-line string type.
     */
    public function getType(): GeometryTypeEnum
    {
        return GeometryTypeEnum::MULTILINESTRING;
    }
}
