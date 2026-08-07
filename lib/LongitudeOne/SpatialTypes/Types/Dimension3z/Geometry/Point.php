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

namespace LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry;

use LongitudeOne\SpatialTypes\Enum\DimensionEnum;
use LongitudeOne\SpatialTypes\Enum\FamilyEnum;
use LongitudeOne\SpatialTypes\Enum\TypeEnum;
use LongitudeOne\SpatialTypes\Exception\BadMethodCallException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Interfaces\PointInterface;
use LongitudeOne\SpatialTypes\Types\AbstractPoint;

/**
 * GEOMETRIC POINT object for POINT spatial types.
 */
class Point extends AbstractPoint implements PointInterface
{
    /**
     * The Z coordinate or the elevation.
     */
    protected float|int $z;

    /**
     * New point constructor.
     *
     * First coordinate is X then Y, abscissa then ordinate, longitude then latitude.
     * Third coordinate is Z the elevation (altitude)
     *
     * @see https://stackoverflow.com/questions/7309121/preferred-order-of-writing-latitude-longitude-tuples
     * @see https://docs.geotools.org/latest/userguide/library/referencing/order.html
     *
     * @param float|int|string $x    X (abscissa) coordinate can be a string and will be parsed by the geo-parser
     * @param float|int|string $y    Y (ordinate) coordinate can be a string and will be parsed by the geo-parser
     * @param float|int        $z    Z (elevation) coordinate
     * @param null|int         $srid SRID
     *
     * @throws InvalidValueException when point is invalid
     */
    public function __construct(float|int|string $x, float|int|string $y, float|int $z, ?int $srid = null)
    {
        $this->setX($x);
        $this->setY($y);
        $this->setZ($z);

        $this->srid = $srid;
    }

    /**
     * Initialize the family.
     *
     * @return FamilyEnum::GEOMETRY
     */
    public function getFamily(): FamilyEnum
    {
        return FamilyEnum::GEOMETRY;
    }

    /**
     * Return the M coordinate of this point.
     *
     * @throws BadMethodCallException because the point has no M coordinate
     */
    public function getM(): float|int
    {
        throw BadMethodCallException::create(__METHOD__, $this->getDimension());
    }

    /**
     * Initialize the type.
     */
    public function getType(): string
    {
        return TypeEnum::POINT->value;
    }

    /**
     * Get the Z coordinate (elevation).
     */
    public function getZ(): float|int
    {
        return $this->z;
    }

    /**
     * Set the Z coordinate (elevation).
     *
     * @param float|int $z Z (elevation) coordinate
     */
    public function setZ(float|int $z): static
    {
        $this->z = $z;

        return $this;
    }

    /**
     * Convert point into an array of coordinates Latitude, longitude, Z.
     * SRID is NOT exported.
     *
     * Latitude, longitude, elevation.
     *
     * AbstractPoint does NOT contain SpatialInterface, only floats, integers in a one-dimensional arrays.
     *
     * @return array{0 : float|int, 1 : float|int, 2 : float|int}
     */
    public function toArray(): array
    {
        return [$this->x, $this->y, $this->z];
    }

    /**
     * Initialize the dimension.
     *
     * @return DimensionEnum::X_Y_Z
     */
    protected function getDimension(): DimensionEnum
    {
        return DimensionEnum::X_Y_Z;
    }
}
