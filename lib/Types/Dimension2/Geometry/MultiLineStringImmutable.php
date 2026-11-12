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

namespace LongitudeOne\SpatialTypes\Types\Dimension2\Geometry;

use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Interfaces\ImmutableMultiLineStringInterface;
use LongitudeOne\SpatialTypes\Internal\Types\Dimension2\AbstractMultiLineStringImmutable;

final class MultiLineStringImmutable extends AbstractMultiLineStringImmutable implements ImmutableMultiLineStringInterface
{
    /**
     * Initialize the family of the object.
     */
    public function getFamily(): SpatialModelEnum
    {
        return SpatialModelEnum::GEOMETRY;
    }
}
