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

namespace LongitudeOne\SpatialTypes\Types\Dimension3m;

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\SpatialTypes\Internal\Types\AbstractLineString;

/** Mutable XYM LineString with instance-level declared family. */
final class LineString extends AbstractLineString
{
    /** Return the fixed coordinate layout. */
    public function getDimension(): CoordinateDimensionEnum
    {
        return CoordinateDimensionEnum::XYM;
    }
}
