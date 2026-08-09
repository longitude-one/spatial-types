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

namespace LongitudeOne\SpatialTypes\Types\Dimension3m;

use LongitudeOne\SpatialTypes\Enum\DimensionEnum;
use LongitudeOne\SpatialTypes\Enum\TypeEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Exception\InvalidSridException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\MissingValueException;
use LongitudeOne\SpatialTypes\Interfaces\MultiPointInterface;
use LongitudeOne\SpatialTypes\Interfaces\PointInterface;
use LongitudeOne\SpatialTypes\Types\AbstractMultiPoint as ParentMultiPoint;

/**
 * Base class for multi-points using the XYM layout.
 */
abstract class AbstractMultiPoint extends ParentMultiPoint implements MultiPointInterface
{
    /**
     * @param (array{0: float|int|string, 1: float|int|string, 2: float|int}|PointInterface)[] $points points of the multipoint
     * @param int                                                                              $srid   Spatial Reference Identifier
     *
     * @throws InvalidDimensionException when a point dimension is incompatible
     * @throws InvalidSridException      when a point SRID is incompatible
     * @throws InvalidValueException     when point coordinates are invalid
     * @throws MissingValueException     when a point is missing
     */
    public function __construct(array $points, int $srid = self::DEFAULT_SRID)
    {
        $this->srid = $srid;
        $this->addPoints($points);
    }

    /**
     * Return the multi-point type.
     */
    public function getType(): TypeEnum
    {
        return TypeEnum::MULTIPOINT;
    }

    /**
     * Return the XYM dimension.
     */
    protected function getDimension(): DimensionEnum
    {
        return DimensionEnum::X_Y_M;
    }
}
