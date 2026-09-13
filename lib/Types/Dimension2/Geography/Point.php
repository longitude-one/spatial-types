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

namespace LongitudeOne\SpatialTypes\Types\Dimension2\Geography;

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Exception\BadMethodCallException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Interfaces\PointInterface;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Types\AbstractPoint;

/**
 * Geographic point.
 */
class Point extends AbstractPoint implements PointInterface
{
    /**
     * New point constructor.
     *
     * The first coordinate is longitude (X); the second is latitude (Y).
     *
     * @see https://stackoverflow.com/questions/7309121/preferred-order-of-writing-latitude-longitude-tuples
     * @see https://docs.geotools.org/latest/userguide/library/referencing/order.html
     *
     * @param null|float|int|string $x    X coordinate can be a string and will be parsed by the geo-parser; null creates an empty point when Y is also null
     * @param null|float|int|string $y    Y coordinate can be a string and will be parsed by the geo-parser; null creates an empty point when X is also null
     * @param int|SpatialReference  $srid SRID
     *
     * @throws InvalidValueException when point is invalid
     */
    public function __construct(float|int|string|null $x = null, float|int|string|null $y = null, int|SpatialReference $srid = 0)
    {
        if ($this->hasOnlyNullCoordinates($x, $y)) {
            $this->initializeSpatialReference($srid);

            return;
        }

        if (null === $x || null === $y) {
            throw new InvalidValueException('All point coordinates must be provided, or all must be null for an empty point.');
        }

        $this->initializeLongitude($x);
        $this->initializeLatitude($y);
        $this->initializeSpatialReference($srid);
    }

    /**
     * Initialize the family.
     *
     * @return SpatialModelEnum::GEOGRAPHY
     */
    public function getFamily(): SpatialModelEnum
    {
        return SpatialModelEnum::GEOGRAPHY;
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
     *
     * @throws BadMethodCallException because the point has no Z coordinate
     */
    public function getZ(): float|int|null
    {
        throw BadMethodCallException::create(__METHOD__, $this->getDimension());
    }

    /**
     * Convert this point to an array containing longitude (X) and latitude (Y).
     * The SRID is not exported.
     *
     * @return array{0 : float|int, 1 : float|int}|array{}
     */
    public function toArray(): array
    {
        if ($this->isEmpty()) {
            return [];
        }

        return [$this->x, $this->y];
    }

    /**
     * Initialize the dimension.
     *
     * @return CoordinateDimensionEnum::XY
     */
    protected function getDimension(): CoordinateDimensionEnum
    {
        return CoordinateDimensionEnum::XY;
    }
}
