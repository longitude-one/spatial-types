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

namespace LongitudeOne\SpatialTypes\Internal\Types;

use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\SpatialTypes\Interfaces\MutableLineStringInterface;

/** Mutable line-string operations without geometric validation.
 * @internal
 */
abstract class AbstractLineString extends AbstractPointSequence implements MutableLineStringInterface
{
    use PointSequenceEditingTrait;

    /** Return the fixed spatial type. */
    public function getType(): GeometryTypeEnum
    {
        return GeometryTypeEnum::LINESTRING;
    }
}
