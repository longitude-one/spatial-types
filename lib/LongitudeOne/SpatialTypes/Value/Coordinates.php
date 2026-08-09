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

namespace LongitudeOne\SpatialTypes\Value;

use LongitudeOne\SpatialTypes\Enum\DimensionEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;

/**
 * Immutable, normalized ordinates of a point.
 *
 * Unlike the internal Factory\Coordinates input DTO, this value object only
 * accepts numeric ordinates and always carries its coordinate dimension.
 */
final readonly class Coordinates
{
    /**
     * @param DimensionEnum  $dimension coordinate layout represented by these ordinates
     * @param float|int      $x         X coordinate or longitude
     * @param float|int      $y         Y coordinate or latitude
     * @param null|float|int $z         Z coordinate or elevation
     * @param null|float|int $m         M coordinate or measure
     *
     * @throws InvalidDimensionException when Z or M does not match the coordinate layout
     */
    public function __construct(
        public DimensionEnum $dimension,
        public float|int $x,
        public float|int $y,
        public float|int|null $z = null,
        public float|int|null $m = null
    ) {
        if ($dimension->hasZ() !== (null !== $z) || $dimension->hasM() !== (null !== $m)) {
            throw new InvalidDimensionException(sprintf('The supplied ordinates do not match the %s coordinate dimension.', $dimension->value));
        }
    }

    /**
     * Create a two-dimensional XY coordinate value.
     *
     * @param float|int $x X coordinate or longitude
     * @param float|int $y Y coordinate or latitude
     */
    public static function xy(float|int $x, float|int $y): self
    {
        return new self(DimensionEnum::X_Y, $x, $y);
    }

    /**
     * Create a three-dimensional XYM coordinate value.
     *
     * @param float|int $x X coordinate or longitude
     * @param float|int $y Y coordinate or latitude
     * @param float|int $m M coordinate or measure
     */
    public static function xym(float|int $x, float|int $y, float|int $m): self
    {
        return new self(DimensionEnum::X_Y_M, $x, $y, null, $m);
    }

    /**
     * Create a three-dimensional XYZ coordinate value.
     *
     * @param float|int $x X coordinate or longitude
     * @param float|int $y Y coordinate or latitude
     * @param float|int $z Z coordinate or elevation
     */
    public static function xyz(float|int $x, float|int $y, float|int $z): self
    {
        return new self(DimensionEnum::X_Y_Z, $x, $y, $z);
    }

    /**
     * Create a four-dimensional XYZM coordinate value.
     *
     * @param float|int $x X coordinate or longitude
     * @param float|int $y Y coordinate or latitude
     * @param float|int $z Z coordinate or elevation
     * @param float|int $m M coordinate or measure
     */
    public static function xyzm(float|int $x, float|int $y, float|int $z, float|int $m): self
    {
        return new self(DimensionEnum::X_Y_Z_M, $x, $y, $z, $m);
    }

    /**
     * Return the M coordinate.
     *
     * @throws InvalidDimensionException when the coordinate value has no M ordinate
     */
    public function getM(): float|int
    {
        if (null === $this->m) {
            throw new InvalidDimensionException(sprintf('The %s coordinate dimension has no M ordinate.', $this->dimension->value));
        }

        return $this->m;
    }

    /**
     * Return the Z coordinate.
     *
     * @throws InvalidDimensionException when the coordinate value has no Z ordinate
     */
    public function getZ(): float|int
    {
        if (null === $this->z) {
            throw new InvalidDimensionException(sprintf('The %s coordinate dimension has no Z ordinate.', $this->dimension->value));
        }

        return $this->z;
    }

    /**
     * Return the ordinates in the order defined by the coordinate dimension.
     *
     * @return array{0: float|int, 1: float|int, 2: float|int, 3: float|int}|array{0: float|int, 1: float|int, 2: float|int}|array{0: float|int, 1: float|int}
     */
    public function toArray(): array
    {
        return match ($this->dimension) {
            DimensionEnum::X_Y => [$this->x, $this->y],
            DimensionEnum::X_Y_M => [$this->x, $this->y, $this->getM()],
            DimensionEnum::X_Y_Z => [$this->x, $this->y, $this->getZ()],
            DimensionEnum::X_Y_Z_M => [$this->x, $this->y, $this->getZ(), $this->getM()],
        };
    }

    /**
     * Return a copy with a different M coordinate.
     *
     * @param float|int $m M coordinate or measure
     *
     * @throws InvalidDimensionException when the coordinate value has no M ordinate
     */
    public function withM(float|int $m): self
    {
        if (!$this->dimension->hasM()) {
            throw new InvalidDimensionException(sprintf('The %s coordinate dimension has no M ordinate.', $this->dimension->value));
        }

        return new self($this->dimension, $this->x, $this->y, $this->z, $m);
    }

    /**
     * Return a copy with a different X coordinate.
     *
     * @param float|int $x X coordinate or longitude
     */
    public function withX(float|int $x): self
    {
        return new self($this->dimension, $x, $this->y, $this->z, $this->m);
    }

    /**
     * Return a copy with a different Y coordinate.
     *
     * @param float|int $y Y coordinate or latitude
     */
    public function withY(float|int $y): self
    {
        return new self($this->dimension, $this->x, $y, $this->z, $this->m);
    }

    /**
     * Return a copy with a different Z coordinate.
     *
     * @param float|int $z Z coordinate or elevation
     *
     * @throws InvalidDimensionException when the coordinate value has no Z ordinate
     */
    public function withZ(float|int $z): self
    {
        if (!$this->dimension->hasZ()) {
            throw new InvalidDimensionException(sprintf('The %s coordinate dimension has no Z ordinate.', $this->dimension->value));
        }

        return new self($this->dimension, $this->x, $this->y, $z, $this->m);
    }
}
