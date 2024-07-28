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

namespace LongitudeOne\SpatialTypes\Types\Geometry;

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
     * New point constructor.
     *
     * @param float|int|string $x    X coordinate can be a string and will be parsed by the geo-parser
     * @param float|int|string $y    Y coordinate can be a string and will be parsed by the geo-parser
     * @param null|int         $srid SRID
     *
     * @throws InvalidValueException when point is invalid
     */
    public function __construct(float|int|string $x, float|int|string $y, ?int $srid = null)
    {
        $this->preConstruct();
        $this->setX($x);
        $this->setY($y);
        $this->srid = $srid;
    }

    /**
     * Return the M coordinate of this point.
     *
     * @throws BadMethodCallException because the point has no M coordinate
     */
    public function getM(): float|int
    {
        throw BadMethodCallException::create(__METHOD__, $this->dimension);
    }

    /**
     * Get the Z coordinate (elevation).
     *
     * @throws BadMethodCallException because the point has no Z coordinate
     */
    public function getZ(): float|int
    {
        throw BadMethodCallException::create(__METHOD__, $this->dimension);
    }

    /**
     * Initialize the dimension.
     *
     * @return DimensionEnum::X_Y
     */
    protected function initDimension(): DimensionEnum
    {
        return DimensionEnum::X_Y;
    }

    /**
     * Initialize the family.
     *
     * @return FamilyEnum::GEOMETRY
     */
    protected function initFamily(): FamilyEnum
    {
        return FamilyEnum::GEOMETRY;
    }

    /**
     * Initialize the type.
     *
     * @return TypeEnum::POINT
     */
    protected function initType(): TypeEnum
    {
        return TypeEnum::POINT;
    }

    /**
     * Convert coordinates to their string representation.
     * Example: '(42 42)'.
     */
    public function __toString(): string
    {
        return sprintf('%s %s', $this->x, $this->y);
    }
}
