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

namespace LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry;

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Interfaces\PointInterface;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Types\AbstractPoint;

class Point extends AbstractPoint implements PointInterface
{
    protected float|int $m;

    protected float|int $z;

    /**
     * Create a four-dimensional geometric point.
     *
     * Coordinates are ordered as X (abscissa), Y (ordinate), Z (elevation),
     * then M (measure).
     *
     * @param float|int|string     $x    X (abscissa) coordinate; strings are parsed by the geo-parser
     * @param float|int|string     $y    Y (ordinate) coordinate; strings are parsed by the geo-parser
     * @param float|int            $z    Z (elevation) coordinate
     * @param float|int            $m    M (measure) coordinate
     * @param int|SpatialReference $srid Spatial Reference Identifier
     *
     * @throws InvalidValueException when a coordinate is invalid
     */
    public function __construct(float|int|string $x, float|int|string $y, float|int $z, float|int $m, int|SpatialReference $srid = 0)
    {
        $this->initializeX($x);
        $this->initializeY($y);
        $this->initializeZ($z);
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
     * Return the M (measure) coordinate.
     */
    public function getM(): float|int
    {
        return $this->m;
    }

    /**
     * Return the point type.
     */
    public function getType(): GeometryTypeEnum
    {
        return GeometryTypeEnum::POINT;
    }

    /**
     * Return the Z (elevation) coordinate.
     */
    public function getZ(): float|int
    {
        return $this->z;
    }

    /**
     * Convert the point to its ordered XYZM coordinates; the SRID is omitted.
     *
     * @return array{0: float|int, 1: float|int, 2: float|int, 3: float|int}
     */
    public function toArray(): array
    {
        return [$this->x, $this->y, $this->z, $this->m];
    }

    /**
     * Return the four-dimensional XYZM coordinate layout.
     */
    protected function getDimension(): CoordinateDimensionEnum
    {
        return CoordinateDimensionEnum::XYZM;
    }

    /**
     * Set the M (measure) coordinate.
     *
     * @param float|int $m M (measure) coordinate
     */
    protected function initializeM(float|int $m): static
    {
        $this->m = $m;

        return $this;
    }

    /**
     * Set the Z (elevation) coordinate.
     *
     * @param float|int $z Z (elevation) coordinate
     */
    protected function initializeZ(float|int $z): static
    {
        $this->z = $z;

        return $this;
    }
}
