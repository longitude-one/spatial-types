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
use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\Core\Enum\SpatialModelEnum;
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
     * @param null|float|int|string $x    X (abscissa) coordinate; strings are parsed by the geo-parser
     * @param null|float|int|string $y    Y (ordinate) coordinate; strings are parsed by the geo-parser
     * @param null|float|int        $z    Z (elevation) coordinate
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

        if (null === $x || null === $y || null === $z) {
            throw new InvalidValueException('All point coordinates must be provided, or all must be null for an empty point.');
        }

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
        return $this->isEmpty() ? null : $this->z;
    }

    /**
     * Convert this point to an array containing X, Y, and Z coordinates.
     * The SRID is not exported.
     *
     * @return array{0 : float|int, 1 : float|int, 2 : float|int}|array{}
     */
    public function toArray(): array
    {
        if ($this->isEmpty()) {
            return [];
        }

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
