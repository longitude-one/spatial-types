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

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Enum\TypeEnum;
use LongitudeOne\SpatialTypes\Exception\BadMethodCallException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Interfaces\PointInterface;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
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
     * @param float|int|string     $x    X (abscissa) coordinate; strings are parsed by the geo-parser
     * @param float|int|string     $y    Y (ordinate) coordinate; strings are parsed by the geo-parser
     * @param float|int            $z    Z (elevation) coordinate
     * @param int|SpatialReference $srid SRID
     *
     * @throws InvalidValueException when point is invalid
     */
    public function __construct(float|int|string $x, float|int|string $y, float|int $z, int|SpatialReference $srid = 0)
    {
        $this->initializeX($x);
        $this->initializeY($y);
        $this->initializeZ($z);

        $this->initializeSpatialReference($srid);
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
     * @return CoordinateDimensionEnum::XYZ
     */
    protected function getDimension(): CoordinateDimensionEnum
    {
        return CoordinateDimensionEnum::XYZ;
    }

    /**
     * Set the Z coordinate (elevation).
     *
     * @param float|int $z Z (elevation) coordinate
     */
    protected function initializeZ(float|int $z): static
    {
        $this->z = $z;

        return $this;
    }
}
