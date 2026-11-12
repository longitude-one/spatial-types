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

namespace LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry;

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Interfaces\ImmutablePolygonInterface;
use LongitudeOne\SpatialTypes\Internal\Types\AbstractPolygonImmutable;

final class PolygonImmutable extends AbstractPolygonImmutable implements ImmutablePolygonInterface
{
    /**
     * Return the four-dimensional XYZM coordinate layout.
     */
    public function getDimension(): CoordinateDimensionEnum
    {
        return CoordinateDimensionEnum::XYZM;
    }

    /**
     * Return the geometry family.
     */
    public function getFamily(): SpatialModelEnum
    {
        return SpatialModelEnum::GEOMETRY;
    }

    /**
     * Return the polygon type.
     */
    public function getType(): GeometryTypeEnum
    {
        return GeometryTypeEnum::POLYGON;
    }
}
