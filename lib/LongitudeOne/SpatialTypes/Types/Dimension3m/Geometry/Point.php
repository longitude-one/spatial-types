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

namespace LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry;

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Exception\BadMethodCallException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Interfaces\PointInterface;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Types\AbstractPoint;

/**
 * Three-dimensional geometric point with a measure coordinate.
 */
class Point extends AbstractPoint implements PointInterface
{
    /**
     * The M coordinate.
     */
    protected float|int $m;

    /**
     * @param null|float|int|string $x    X coordinate
     * @param null|float|int|string $y    Y coordinate
     * @param null|float|int        $m    M coordinate
     * @param int|SpatialReference  $srid SRID
     *
     * @throws InvalidValueException when a coordinate is invalid
     */
    public function __construct(float|int|string|null $x = null, float|int|string|null $y = null, float|int|null $m = null, int|SpatialReference $srid = 0)
    {
        if ($this->hasOnlyNullCoordinates($x, $y, $m)) {
            $this->initializeSpatialReference($srid);

            return;
        }

        if (null === $x || null === $y || null === $m) {
            throw new InvalidValueException('All point coordinates must be provided, or all must be null for an empty point.');
        }

        $this->initializeX($x);
        $this->initializeY($y);
        $this->initializeM($m);
        $this->initializeSpatialReference($srid);
    }

    /**
     * Return the geometry family.
     */
    public function getFamily(): SpatialModelEnum
    {
        return SpatialModelEnum::GEOMETRY;
    }

    /**
     * Return the M coordinate.
     */
    public function getM(): float|int|null
    {
        return $this->isEmpty() ? null : $this->m;
    }

    /**
     * Return the point type.
     */
    public function getType(): GeometryTypeEnum
    {
        return GeometryTypeEnum::POINT;
    }

    /**
     * @throws BadMethodCallException because the point has no Z coordinate
     */
    public function getZ(): float|int|null
    {
        throw BadMethodCallException::create(__METHOD__, $this->getDimension());
    }

    /**
     * @return array{0: float|int, 1: float|int, 2: float|int}|array{}
     */
    public function toArray(): array
    {
        if ($this->isEmpty()) {
            return [];
        }

        return [$this->x, $this->y, $this->m];
    }

    /**
     * Return the XYM dimension.
     */
    protected function getDimension(): CoordinateDimensionEnum
    {
        return CoordinateDimensionEnum::XYM;
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
