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

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\MissingValueException;
use LongitudeOne\SpatialTypes\Interfaces\ImmutableMultiPointInterface;
use LongitudeOne\SpatialTypes\Interfaces\ImmutablePointInterface;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Types\AbstractMultiPointImmutable as ParentMultiPoint;

/**
 * Base class for multi-points using the XYM layout.
 */
abstract class AbstractMultiPointImmutable extends ParentMultiPoint implements ImmutableMultiPointInterface
{
    /**
     * @param (array{0: null|float|int|string, 1: null|float|int|string, 2: null|float|int}|ImmutablePointInterface)[] $points points of the multipoint
     * @param int|SpatialReference                                                                                     $srid   Spatial Reference Identifier
     *
     * @throws InvalidDimensionException when a point dimension is incompatible
     * @throws InvalidValueException     when point coordinates are invalid
     * @throws MissingValueException     when a point is missing
     */
    public function __construct(array $points, int|SpatialReference $srid = 0)
    {
        $this->initializeSpatialReference($srid);
        $this->addPoints($points);
    }

    /**
     * Return the XYM dimension.
     */
    public function getDimension(): CoordinateDimensionEnum
    {
        return CoordinateDimensionEnum::XYM;
    }

    /**
     * Return the multi-point type.
     */
    public function getType(): GeometryTypeEnum
    {
        return GeometryTypeEnum::MULTIPOINT;
    }
}
