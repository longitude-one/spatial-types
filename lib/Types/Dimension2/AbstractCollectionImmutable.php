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

namespace LongitudeOne\SpatialTypes\Types\Dimension2;

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\SpatialTypes\Interfaces\ImmutableCollectionInterface;
use LongitudeOne\SpatialTypes\Types\AbstractCollectionImmutable as ParentCollection;

/**
 * Dimension-specific immutable implementation base.
 *
 * @internal
 */
abstract class AbstractCollectionImmutable extends ParentCollection implements ImmutableCollectionInterface
{
    /**
     * Return the two-dimensional coordinate layout.
     */
    public function getDimension(): CoordinateDimensionEnum
    {
        return CoordinateDimensionEnum::XY;
    }

    /**
     * Return the geometry collection type.
     */
    public function getType(): GeometryTypeEnum
    {
        return GeometryTypeEnum::GEOMETRYCOLLECTION;
    }
}
