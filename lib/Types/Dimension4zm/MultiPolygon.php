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

namespace LongitudeOne\SpatialTypes\Types\Dimension4zm;

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\SpatialTypes\Internal\Types\AbstractMultiPolygon;

/** Mutable XYZM MultiPolygon with instance-level declared family. */
final class MultiPolygon extends AbstractMultiPolygon
{
    /** Return the fixed coordinate layout. */
    public function getDimension(): CoordinateDimensionEnum
    {
        return CoordinateDimensionEnum::XYZM;
    }
}
