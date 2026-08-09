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
     * @param (array{0: float|int|string, 1: float|int|string, 2: float|int, 3: float|int}|PointInterface)[] $points
     */
    public function __construct(array $points, ?int $srid = null)
    {
        $this->setSrid($srid);
        $this->addPoints($points);
    }

    public function getType(): TypeEnum
    {
        return TypeEnum::LINESTRING;
    }

    protected function getDimension(): DimensionEnum
    {
        return DimensionEnum::X_Y_Z_M;
    }
}
