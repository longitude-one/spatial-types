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

use LongitudeOne\SpatialTypes\Enum\DimensionEnum;
use LongitudeOne\SpatialTypes\Enum\TypeEnum;
use LongitudeOne\SpatialTypes\Interfaces\LineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\PointInterface;
use LongitudeOne\SpatialTypes\Types\AbstractLineString as ParentLineString;

abstract class AbstractLineString extends ParentLineString implements LineStringInterface
{
    /**
     * @param (array{0: float|int|string, 1: float|int|string, 2: float|int, 3: float|int}|PointInterface)[] $points points of the line string
     * @param int                                                                                            $srid   Spatial Reference Identifier
     */
    public function __construct(array $points, int $srid = self::DEFAULT_SRID)
    {
        $this->srid = $srid;
        $this->addPoints($points);
    }

    /**
     * Define the line string type.
     */
    public function getType(): TypeEnum
    {
        return TypeEnum::LINESTRING;
    }

    /**
     * Define the four-dimensional XYZM coordinate layout.
     */
    protected function getDimension(): DimensionEnum
    {
        return DimensionEnum::X_Y_Z_M;
    }
}
