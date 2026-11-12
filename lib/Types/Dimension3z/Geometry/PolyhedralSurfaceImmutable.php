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

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Interfaces\ImmutablePolyhedralSurfaceInterface;
use LongitudeOne\SpatialTypes\Internal\Types\AbstractPolyhedralSurfaceImmutable;

final class PolyhedralSurfaceImmutable extends AbstractPolyhedralSurfaceImmutable implements ImmutablePolyhedralSurfaceInterface
{
    /**
     * Initialize the dimension of the object.
     */
    public function getDimension(): CoordinateDimensionEnum
    {
        return CoordinateDimensionEnum::XYZ;
    }

    /**
     * Initialize the family of the object.
     */
    public function getFamily(): SpatialModelEnum
    {
        return SpatialModelEnum::GEOMETRY;
    }

    /**
     * Initialize the type of the object.
     */
    public function getType(): GeometryTypeEnum
    {
        return GeometryTypeEnum::POLYHEDRALSURFACE;
    }
}
