<?php
/**
 * This file is part of the spatial project.
 *
 * PHP 8.1 | 8.2 | 8.3
 *
 * Copyright Alexandre Tranchant <alexandre.tranchant@gmail.com> 2024
 * Copyright Longitude One 2024
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

declare(strict_types=1);

namespace LongitudeOne\SpatialTypes\Types\Geography;

use LongitudeOne\SpatialTypes\Enum\DimensionEnum;
use LongitudeOne\SpatialTypes\Enum\FamilyEnum;
use LongitudeOne\SpatialTypes\Enum\TypeEnum;
use LongitudeOne\SpatialTypes\Exception\BadMethodCallException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Interfaces\PointInterface;
use LongitudeOne\SpatialTypes\Types\AbstractPoint;

/**
 * Geographic POINT object for POINT spatial types.
 */
class Point extends AbstractPoint implements PointInterface
{
    /**
     * New point constructor.
     *
     * First coordinate is longitude then latitude, X then Y, abscisse then ordinate.
     *
     * @see https://stackoverflow.com/questions/7309121/preferred-order-of-writing-latitude-longitude-tuples
     * @see https://docs.geotools.org/latest/userguide/library/referencing/order.html
     *
     * @param float|int|string $x    X coordinate can be a string and will be parsed by the geo-parser
     * @param float|int|string $y    Y coordinate can be a string and will be parsed by the geo-parser
     * @param null|int         $srid SRID
     *
     * @throws InvalidValueException when point is invalid
     */
    public function __construct(float|int|string $x, float|int|string $y, ?int $srid = null)
    {
        $this->setLongitude($x);
        $this->setLatitude($y);
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
    public function getType(): string
    {
        return TypeEnum::POINT->value;
    }

    /**
     * Get the Z coordinate (elevation).
     *
     * @throws BadMethodCallException because the point has no Z coordinate
     */
    public function getZ(): float|int
    {
        throw BadMethodCallException::create(__METHOD__, $this->getDimension());
    }

    /**
     * Set the X coordinate. In Geography, the X coordinate is the longitude, this method is an alias of setLongitude.
     *
     * @param float|int|string $x X coordinate
     *
     * @throws InvalidValueException when x is not valid
     */
    public function setX(float|int|string $x): static
    {
        return $this->setLongitude($x);
    }

    /**
     * Set the Y coordinate. In Geography, the Y coordinate is the latitude, this method is an alias of setLatitude.
     *
     * @param float|int|string $y Y coordinate
     *
     * @throws InvalidValueException when y is not valid
     */
    public function setY(float|int|string $y): static
    {
        return $this->setLatitude($y);
    }

    /**
     * Initialize the dimension.
     *
     * @return DimensionEnum::X_Y
     */
    protected function getDimension(): DimensionEnum
    {
        return DimensionEnum::X_Y;
    }
}
