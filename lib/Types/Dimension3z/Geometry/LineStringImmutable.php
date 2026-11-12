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

namespace LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry;

use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Interfaces\ImmutableLineStringInterface;
use LongitudeOne\SpatialTypes\Internal\Types\Dimension3z\AbstractLineStringImmutable;

final class LineStringImmutable extends AbstractLineStringImmutable implements ImmutableLineStringInterface
{
    /**
     * Define the family.
     */
    public function getFamily(): SpatialModelEnum
    {
        return SpatialModelEnum::GEOMETRY;
    }
}
