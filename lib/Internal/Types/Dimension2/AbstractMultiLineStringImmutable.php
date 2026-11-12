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
     * Initialize the dimension of the object.
     */
    public function getDimension(): CoordinateDimensionEnum
    {
        return CoordinateDimensionEnum::XY;
    }

    /**
     * Initialize the type of the object.
     */
    public function getType(): GeometryTypeEnum
    {
        return GeometryTypeEnum::MULTILINESTRING;
    }
}
