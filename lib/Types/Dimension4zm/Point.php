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
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Internal\Types\AbstractPoint;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;

/** Mutable XYZM point with independently editable declared context. */
final class Point extends AbstractPoint
{
    /**
     * @param float|int|string|null $x      X ordinate; strings use geo-parser
     * @param float|int|string|null $y      Y ordinate; strings use geo-parser
     * @param float|int|null        $z      Z ordinate
     * @param float|int|null        $m      M ordinate
     * @param int|SpatialReference  $srid   Declared reference
     * @param SpatialModelEnum      $family Declared family
     */
    public function __construct(float|int|string|null $x = null, float|int|string|null $y = null, float|int|null $z = null, float|int|null $m = null, int|SpatialReference $srid = 0, SpatialModelEnum $family = SpatialModelEnum::GEOMETRY)
    {
        $this->setFamily($family);
        $this->initializeSpatialReference($srid);
        $this->setX($x);
        $this->setY($y);
        $this->setZ($z);
        $this->setM($m);
    }

    /** Return the dimension. */
    public function getDimension(): CoordinateDimensionEnum
    {
        return CoordinateDimensionEnum::XYZM;
    }
}
