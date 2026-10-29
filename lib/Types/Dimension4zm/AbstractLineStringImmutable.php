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
use LongitudeOne\SpatialTypes\Interfaces\ImmutableLineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\ImmutablePointInterface;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Types\AbstractLineStringImmutable as ParentLineString;

abstract class AbstractLineStringImmutable extends ParentLineString implements ImmutableLineStringInterface
{
    /**
     * @param (array{0: null|float|int|string, 1: null|float|int|string, 2: null|float|int, 3: null|float|int}|ImmutablePointInterface)[] $points points of the line string
     * @param int|SpatialReference                                                                                                        $srid   Spatial Reference Identifier
     */
    public function __construct(array $points, int|SpatialReference $srid = 0)
    {
        $this->initializeSpatialReference($srid);
        $this->addPoints($points);
    }

    /**
     * Define the four-dimensional XYZM coordinate layout.
     */
    public function getDimension(): CoordinateDimensionEnum
    {
        return CoordinateDimensionEnum::XYZM;
    }

    /**
     * Define the line string type.
     */
    public function getType(): GeometryTypeEnum
    {
        return GeometryTypeEnum::LINESTRING;
    }
}
