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

namespace LongitudeOne\SpatialTypes\Internal\Types\Dimension3m;

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\SpatialTypes\Interfaces\ImmutableMultiLineStringInterface;
use LongitudeOne\SpatialTypes\Internal\Types\AbstractMultiLineStringImmutable as ParentMultiLineString;

/**
 * Base class for multi-line strings using the XYM layout.
 */
/**
 * Dimension-specific immutable implementation base.
 *
 * @internal
 */
abstract class AbstractMultiLineStringImmutable extends ParentMultiLineString implements ImmutableMultiLineStringInterface
{
    /**
     * Return the XYM dimension.
     */
    public function getDimension(): CoordinateDimensionEnum
    {
        return CoordinateDimensionEnum::XYM;
    }

    /**
     * Return the multi-line string type.
     */
    public function getType(): GeometryTypeEnum
    {
        return GeometryTypeEnum::MULTILINESTRING;
    }
}
