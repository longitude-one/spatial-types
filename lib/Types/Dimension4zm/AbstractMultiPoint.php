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

namespace LongitudeOne\SpatialTypes\Types\Dimension4zm;

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Exception\InvalidSridException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\MissingValueException;
use LongitudeOne\SpatialTypes\Interfaces\MultiPointInterface;
use LongitudeOne\SpatialTypes\Interfaces\PointInterface;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Types\AbstractMultiPoint as ParentMultiPoint;

abstract class AbstractMultiPoint extends ParentMultiPoint implements MultiPointInterface
{
    /**
     * Create a multipoint from XYZM coordinate arrays or point instances.
     *
     * @param (array{0: float|int|string, 1: float|int|string, 2: float|int, 3: float|int}|PointInterface)[] $points points of the multipoint
     * @param int|SpatialReference                                                                           $srid   Spatial Reference Identifier
     *
     * @throws InvalidDimensionException when a point has an incompatible dimension
     * @throws InvalidSridException      when a point has an incompatible SRID
     * @throws InvalidValueException     when point coordinates are invalid
     * @throws MissingValueException     when a point is missing
     */
    public function __construct(array $points, int|SpatialReference $srid = 0)
    {
        $this->initializeSpatialReference($srid);
        $this->addPoints($points);
    }

    /**
     * Define the multipoint type.
     */
    public function getType(): GeometryTypeEnum
    {
        return GeometryTypeEnum::MULTIPOINT;
    }

    /**
     * Define the four-dimensional XYZM coordinate layout.
     */
    protected function getDimension(): CoordinateDimensionEnum
    {
        return CoordinateDimensionEnum::XYZM;
    }
}
