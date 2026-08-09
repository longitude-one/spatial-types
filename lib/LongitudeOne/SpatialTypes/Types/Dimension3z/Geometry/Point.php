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
 * Three-dimensional geometric point.
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
     * The first coordinate is X (abscissa); the second is Y (ordinate).
     * The third coordinate is Z (elevation or altitude).
     *
     * @see https://stackoverflow.com/questions/7309121/preferred-order-of-writing-latitude-longitude-tuples
     * @see https://docs.geotools.org/latest/userguide/library/referencing/order.html
     *
     * @param float|int|string $x    X (abscissa) coordinate; strings are parsed by the geo-parser
     * @param float|int|string $y    Y (ordinate) coordinate; strings are parsed by the geo-parser
     * @param float|int        $z    Z (elevation) coordinate
     * @param int              $srid SRID
     *
     * @throws InvalidValueException when point is invalid
     */
    public function __construct(float|int|string $x, float|int|string $y, float|int $z, int $srid = self::DEFAULT_SRID)
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
    public function getType(): TypeEnum
    {
        return TypeEnum::POINT;
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
     * Convert this point to an array containing X, Y, and Z coordinates.
     * The SRID is not exported.
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
