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

namespace LongitudeOne\SpatialTypes\Internal\Types\Dimension4zm;

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\MissingValueException;
use LongitudeOne\SpatialTypes\Interfaces\ImmutableMultiPointInterface;
use LongitudeOne\SpatialTypes\Interfaces\ImmutablePointInterface;
use LongitudeOne\SpatialTypes\Internal\Types\AbstractMultiPointImmutable as ParentMultiPoint;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;

/**
 * Dimension-specific immutable implementation base.
 *
 * @internal
 */
abstract class AbstractMultiPointImmutable extends ParentMultiPoint implements ImmutableMultiPointInterface
{
    /**
     * Create a multipoint from XYZM coordinate arrays or point instances.
     *
     * @param (array{0: float|int|string|null, 1: float|int|string|null, 2: float|int|null, 3: float|int|null}|ImmutablePointInterface)[] $points points of the multipoint
     * @param int|SpatialReference                                                                                                        $srid   Spatial Reference Identifier
     *
     * @throws InvalidDimensionException when a point has an incompatible dimension
     * @throws InvalidValueException     when point coordinates are invalid
     * @throws MissingValueException     when a point is missing
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
     * Define the multipoint type.
     */
    public function getType(): GeometryTypeEnum
    {
        return GeometryTypeEnum::MULTIPOINT;
    }
}
