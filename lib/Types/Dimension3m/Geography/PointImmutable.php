<?php
/*
 * This file is part of the LongitudeOne Spatial project.
 *
 * Copyright (c) 2026 Alexandre Tranchant <alexandre.tranchant@gmail.com>
 *
 * See LICENSE for copyright and license information.
 *
 */

declare(strict_types=1);

namespace LongitudeOne\SpatialTypes\Types\Dimension3m\Geography;

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Exception\BadMethodCallException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Interfaces\ImmutablePointInterface;
use LongitudeOne\SpatialTypes\Internal\Types\AbstractPointImmutable;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;

/**
 * Three-dimensional geographic point with a measure coordinate.
 */
final class PointImmutable extends AbstractPointImmutable implements ImmutablePointInterface
{
    /**
     * The M coordinate.
     */
    protected float|int|null $m = null;

    /**
     * @param float|int|string|null $x    longitude
     * @param float|int|string|null $y    latitude
     * @param float|int|null        $m    M coordinate
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

        $this->initializeX($x);
        $this->initializeY($y);
        $this->initializeM($m);
        $this->initializeSpatialReference($srid);
    }

    /**
     * Return the XYM dimension.
     */
    public function getDimension(): CoordinateDimensionEnum
    {
        return CoordinateDimensionEnum::XYM;
    }

    /**
     * Return the geography family.
     */
    public function getFamily(): SpatialModelEnum
    {
        return SpatialModelEnum::GEOGRAPHY;
    }

    /**
     * Return the M coordinate.
     */
    public function getM(): float|int|null
    {
        return $this->empty ? null : $this->m;
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
     * @return array{0: float|int|null, 1: float|int|null, 2: float|int|null}|array{}
     */
    public function toArray(): array
    {
        if ($this->empty) {
            return [];
        }

        return [$this->x, $this->y, $this->m];
    }

    /**
     * Set the M coordinate.
     *
     * @param float|int|null $m M coordinate
     */
    protected function initializeM(float|int|null $m): static
    {
        $this->m = $m;

        return $this;
    }
}
