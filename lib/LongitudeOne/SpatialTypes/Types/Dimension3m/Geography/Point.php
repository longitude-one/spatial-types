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

namespace LongitudeOne\SpatialTypes\Types\Dimension3m\Geography;

use LongitudeOne\SpatialTypes\Enum\DimensionEnum;
use LongitudeOne\SpatialTypes\Enum\FamilyEnum;
use LongitudeOne\SpatialTypes\Enum\TypeEnum;
use LongitudeOne\SpatialTypes\Exception\BadMethodCallException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Interfaces\PointInterface;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Types\AbstractPoint;

/**
 * Three-dimensional geographic point with a measure coordinate.
 */
class Point extends AbstractPoint implements PointInterface
{
    /**
     * The M coordinate.
     */
    protected float|int $m;

    /**
     * @param float|int|string     $x    longitude
     * @param float|int|string     $y    latitude
     * @param float|int            $m    M coordinate
     * @param int|SpatialReference $srid SRID
     *
     * @throws InvalidValueException when a coordinate is invalid
     */
    public function __construct(float|int|string $x, float|int|string $y, float|int $m, int|SpatialReference $srid = 0)
    {
        $this->initializeLongitude($x);
        $this->initializeLatitude($y);
        $this->initializeM($m);
        $this->initializeSpatialReference($srid);
    }

    /**
     * Return the geography family.
     */
    public function getFamily(): FamilyEnum
    {
        return FamilyEnum::GEOGRAPHY;
    }

    /**
     * Return the M coordinate.
     */
    public function getM(): float|int
    {
        return $this->m;
    }

    /**
     * Return the point type.
     */
    public function getType(): TypeEnum
    {
        return TypeEnum::POINT;
    }

    /**
     * @throws BadMethodCallException because the point has no Z coordinate
     */
    public function getZ(): float|int
    {
        throw BadMethodCallException::create(__METHOD__, $this->getDimension());
    }

    /**
     * @return array{0: float|int, 1: float|int, 2: float|int}
     */
    public function toArray(): array
    {
        return [$this->x, $this->y, $this->m];
    }

    /**
     * Return the XYM dimension.
     */
    protected function getDimension(): DimensionEnum
    {
        return DimensionEnum::X_Y_M;
    }

    /**
     * Set the M coordinate.
     *
     * @param float|int $m M coordinate
     */
    protected function initializeM(float|int $m): static
    {
        $this->m = $m;

        return $this;
    }
}
