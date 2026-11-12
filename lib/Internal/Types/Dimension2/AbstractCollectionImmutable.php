<?php
/*
 * This file is part of the LongitudeOne Spatial project.
 *
 * Copyright (c) 2026 Alexandre Tranchant <alexandre.tranchant@gmail.com>
 *
 * See LICENSE for copyright and license information.
 *
 */

declare(strict_types=1);

namespace LongitudeOne\SpatialTypes\Internal\Types\Dimension2;

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\SpatialTypes\Interfaces\ImmutableCollectionInterface;
use LongitudeOne\SpatialTypes\Internal\Types\AbstractCollectionImmutable as ParentCollection;

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
