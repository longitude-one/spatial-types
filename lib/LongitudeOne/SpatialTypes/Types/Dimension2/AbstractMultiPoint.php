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

namespace LongitudeOne\SpatialTypes\Types\Dimension2;

use LongitudeOne\SpatialTypes\Enum\DimensionEnum;
use LongitudeOne\SpatialTypes\Enum\TypeEnum;
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
     * AbstractMultiPoint constructor.
     *
     * @param (array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}|PointInterface)[] $points points of the multipoint
     * @param int|SpatialReference                                                                                         $srid   Spatial Reference Identifier
     *
     * @throws InvalidDimensionException when the point dimension is not compatible with the multipoint dimension
     * @throws InvalidSridException      when the point SRID is not compatible with the multipoint SRID
     * @throws InvalidValueException     when coordinates of the point are invalid
     * @throws MissingValueException     when the point is missing
     */
    public function __construct(array $points, int|SpatialReference $srid = 0)
    {
        $this->initializeSpatialReference($srid);

        $this->addPoints($points);
    }

    /**
     * Define the type.
     */
    public function getType(): TypeEnum
    {
        return TypeEnum::MULTIPOINT;
    }

    /**
     * Define the dimension.
     */
    protected function getDimension(): DimensionEnum
    {
        return DimensionEnum::X_Y;
    }
}
