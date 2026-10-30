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

namespace LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography;

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Interfaces\ImmutablePointInterface;
use LongitudeOne\SpatialTypes\Internal\Types\AbstractPointImmutable;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;

final class PointImmutable extends AbstractPointImmutable implements ImmutablePointInterface
{
    protected float|int|null $m = null;

    protected float|int|null $z = null;

    /**
     * Create a four-dimensional geographic point.
     *
     * Coordinates are ordered as longitude, latitude, Z (elevation), then M
     * (measure).
     *
     * @param null|float|int|string $x    longitude; strings are parsed by the geo-parser
     * @param null|float|int|string $y    latitude; strings are parsed by the geo-parser
     * @param null|float|int        $z    Z (elevation) coordinate
     * @param null|float|int        $m    M (measure) coordinate
     * @param int|SpatialReference  $srid Spatial Reference Identifier
     *
     * @throws InvalidValueException when a coordinate is invalid
     */
    public function __construct(float|int|string|null $x = null, float|int|string|null $y = null, float|int|null $z = null, float|int|null $m = null, int|SpatialReference $srid = 0)
    {
        if ($this->hasOnlyNullCoordinates($x, $y, $z, $m)) {
            $this->initializeSpatialReference($srid);

            return;
        }

        $this->initializeX($x);
        $this->initializeY($y);
        $this->initializeZ($z);
        $this->initializeM($m);
        $this->initializeSpatialReference($srid);
    }

    /**
     * Return the four-dimensional XYZM coordinate layout.
     */
    public function getDimension(): CoordinateDimensionEnum
    {
        return CoordinateDimensionEnum::XYZM;
    }

    /**
     * Return the geography family.
     */
    public function getFamily(): SpatialModelEnum
    {
        return SpatialModelEnum::GEOGRAPHY;
    }

    /**
     * Return the M (measure) coordinate.
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
     * Return the Z (elevation) coordinate.
     */
    public function getZ(): float|int|null
    {
        return $this->isEmpty() ? null : $this->z;
    }

    /**
     * Convert the point to its ordered longitude-latitude-Z-M coordinates; the SRID is omitted.
     *
     * @return array{0: null|float|int, 1: null|float|int, 2: null|float|int, 3: null|float|int}|array{}
     */
    public function toArray(): array
    {
        if ($this->isEmpty()) {
            return [];
        }

        return [$this->x, $this->y, $this->z, $this->m];
    }

    /**
     * Set the M (measure) coordinate.
     *
     * @param null|float|int $m M (measure) coordinate
     */
    protected function initializeM(float|int|null $m): static
    {
        $this->m = $m;

        return $this;
    }

    /**
     * Set the Z (elevation) coordinate.
     *
     * @param null|float|int $z Z (elevation) coordinate
     */
    protected function initializeZ(float|int|null $z): static
    {
        $this->z = $z;

        return $this;
    }
}
