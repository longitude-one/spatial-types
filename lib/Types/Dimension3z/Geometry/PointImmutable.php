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

namespace LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry;

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Exception\BadMethodCallException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Interfaces\ImmutablePointInterface;
use LongitudeOne\SpatialTypes\Internal\Types\AbstractPointImmutable;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;

/**
 * Three-dimensional geometric point.
 */
final class PointImmutable extends AbstractPointImmutable implements ImmutablePointInterface
{
    /**
     * The Z coordinate or the elevation.
     */
    protected float|int|null $z = null;

    /**
     * New point constructor.
     *
     * The first coordinate is X (abscissa); the second is Y (ordinate).
     * The third coordinate is Z (elevation or altitude).
     *
     * @see https://stackoverflow.com/questions/7309121/preferred-order-of-writing-latitude-longitude-tuples
     * @see https://docs.geotools.org/latest/userguide/library/referencing/order.html
     *
     * @param float|int|string|null $x    X (abscissa) coordinate; strings are parsed by the geo-parser
     * @param float|int|string|null $y    Y (ordinate) coordinate; strings are parsed by the geo-parser
     * @param float|int|null        $z    Z (elevation) coordinate
     * @param int|SpatialReference  $srid SRID
     *
     * @throws InvalidValueException when point is invalid
     */
    public function __construct(float|int|string|null $x = null, float|int|string|null $y = null, float|int|null $z = null, int|SpatialReference $srid = 0)
    {
        if ($this->hasOnlyNullCoordinates($x, $y, $z)) {
            $this->initializeSpatialReference($srid);

            return;
        }

        $this->initializeX($x);
        $this->initializeY($y);
        $this->initializeZ($z);

        $this->initializeSpatialReference($srid);
    }

    /**
     * Initialize the dimension.
     *
     * @return CoordinateDimensionEnum::XYZ
     */
    public function getDimension(): CoordinateDimensionEnum
    {
        return CoordinateDimensionEnum::XYZ;
    }

    /**
     * Initialize the family.
     *
     * @return SpatialModelEnum::GEOMETRY
     */
    public function getFamily(): SpatialModelEnum
    {
        return SpatialModelEnum::GEOMETRY;
    }

    /**
     * Return the M coordinate of this point.
     *
     * @throws BadMethodCallException because the point has no M coordinate
     */
    public function getM(): float|int|null
    {
        throw BadMethodCallException::create(__METHOD__, $this->getDimension());
    }

    /**
     * Initialize the type.
     */
    public function getType(): GeometryTypeEnum
    {
        return GeometryTypeEnum::POINT;
    }

    /**
     * Get the Z coordinate (elevation).
     */
    public function getZ(): float|int|null
    {
        return $this->empty ? null : $this->z;
    }

    /**
     * Convert this point to an array containing X, Y, and Z coordinates.
     * The SRID is not exported.
     *
     * @return array{0 : float|int|null, 1 : float|int|null, 2 : float|int|null}|array{}
     */
    public function toArray(): array
    {
        if ($this->empty) {
            return [];
        }

        return [$this->x, $this->y, $this->z];
    }

    /**
     * Set the Z coordinate (elevation).
     *
     * @param float|int|null $z Z (elevation) coordinate
     */
    protected function initializeZ(float|int|null $z): static
    {
        $this->z = $z;

        return $this;
    }
}
