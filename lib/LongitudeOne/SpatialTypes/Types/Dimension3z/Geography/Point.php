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

namespace LongitudeOne\SpatialTypes\Types\Dimension3z\Geography;

use LongitudeOne\SpatialTypes\Enum\DimensionEnum;
use LongitudeOne\SpatialTypes\Enum\FamilyEnum;
use LongitudeOne\SpatialTypes\Enum\TypeEnum;
use LongitudeOne\SpatialTypes\Exception\BadMethodCallException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Interfaces\PointInterface;
use LongitudeOne\SpatialTypes\Types\AbstractPoint;

/**
 * Three-dimensional geographic point.
 */
class Point extends AbstractPoint implements PointInterface
{
    /**
     * The Z coordinate (altitude or elevation).
     */
    protected float|int $z;

    /**
     * New point constructor.
     *
     * The first coordinate is longitude (X); the second is latitude (Y).
     *
     * @see https://stackoverflow.com/questions/7309121/preferred-order-of-writing-latitude-longitude-tuples
     * @see https://docs.geotools.org/latest/userguide/library/referencing/order.html
     *
     * @param float|int|string $x    X coordinate can be a string and will be parsed by the geo-parser
     * @param float|int|string $y    Y coordinate can be a string and will be parsed by the geo-parser
     * @param float|int        $z    Z coordinate (altitude, elevation)
     * @param int              $srid SRID
     *
     * @throws InvalidValueException when point is invalid
     */
    public function __construct(float|int|string $x, float|int|string $y, float|int $z, int $srid = self::DEFAULT_SRID)
    {
        $this->initializeLongitude($x);
        $this->initializeLatitude($y);
        $this->initializeZ($z);
        $this->srid = $srid;
    }

    /**
     * Initialize the family.
     *
     * @return FamilyEnum::GEOGRAPHY
     */
    public function getFamily(): FamilyEnum
    {
        return FamilyEnum::GEOGRAPHY;
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
     *
     * @throws BadMethodCallException because the point has no Z coordinate
     */
    public function getZ(): float|int
    {
        return $this->z;
    }

    /**
     * Set the Z coordinate. In geography, the Z coordinate is the elevation.
     *
     * @param float|int $z Z coordinate
     *
     * @throws InvalidValueException when y is not valid
     */
    protected function initializeZ(float|int $z): static
    {
        $this->z = $z;

        return $this;
    }

    /**
     * Convert this point to an array containing longitude (X), latitude (Y), and Z.
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
