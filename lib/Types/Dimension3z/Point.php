<?php
/**
 * This file is part of the spatial project.
 *
 * PHP 8.4 | 8.5
 *
 * Copyright Alexandre Tranchant <alexandre.tranchant@gmail.com> 2024-2026
 * Copyright Longitude One 2024-2026
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

declare(strict_types=1);

namespace LongitudeOne\SpatialTypes\Types\Dimension3z;

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Types\AbstractPoint;

/** Mutable XYZ point with independently editable declared context. */
final class Point extends AbstractPoint
{
    /**
     * @param null|float|int|string $x      X ordinate; strings use geo-parser
     * @param null|float|int|string $y      Y ordinate; strings use geo-parser
     * @param null|float|int        $z      Z ordinate
     * @param int|SpatialReference  $srid   Declared reference
     * @param SpatialModelEnum      $family Declared family
     */
    public function __construct(float|int|string|null $x = null, float|int|string|null $y = null, float|int|null $z = null, int|SpatialReference $srid = 0, SpatialModelEnum $family = SpatialModelEnum::GEOMETRY)
    {
        $this->setFamily($family);
        $this->initializeSpatialReference($srid);
        $this->setX($x);
        $this->setY($y);
        $this->setZ($z);
    }

    /** Return the dimension. */
    public function getDimension(): CoordinateDimensionEnum
    {
        return CoordinateDimensionEnum::XYZ;
    }
}
