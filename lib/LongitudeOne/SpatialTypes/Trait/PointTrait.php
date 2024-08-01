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

namespace LongitudeOne\SpatialTypes\Trait;

use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Exception\InvalidSridException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\MissingValueException;
use LongitudeOne\SpatialTypes\Exception\OutOfBoundsException;
use LongitudeOne\SpatialTypes\Factory\FactoryPoint;
use LongitudeOne\SpatialTypes\Interfaces\PointInterface;

/**
 * This trait helps to manage point in linestring and multipoint classes.
 *
 * Line string and multipoint are considered as spatial collections of points.
 */
trait PointTrait
{
    /**
     * Points of the spatial points collection.
     *
     * @var PointInterface[]
     */
    private array $points = [];

    /**
     * Add a point to the spatial point collection.
     *
     * @param array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|\DateTimeInterface|float|int}|PointInterface $point point to add
     *
     * @throws InvalidDimensionException when the point dimension is not compatible with the current dimension
     * @throws InvalidSridException      when the point SRID is not compatible with the current SRID
     * @throws InvalidValueException     when coordinates of the point are invalid
     * @throws MissingValueException     when a coordinate of the point is missing
     */
    public function addPoint(array|PointInterface $point): static
    {
        if (is_array($point)) {
            $point = FactoryPoint::fromIndexedArray($point, $this->getSrid(), $this->getFamily(), $this->getDimension());
        }

        if (!$this->hasSameDimension($point)) {
            throw new InvalidDimensionException('The point dimension is not compatible with the dimension of the current spatial collection.');
        }

        if (!empty($point->getSrid()) && !empty($this->getSrid()) && $point->getSrid() !== $this->getSrid()) {
            throw new InvalidSridException('The point SRID is not compatible with the SRID of this current spatial collection.');
        }

        $this->points[] = $point;

        return $this;
    }

    /**
     * Add points to the spatial collection.
     *
     * @param array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|\DateTimeInterface|float|int}[]|PointInterface[] $points points to add
     *
     * @throws InvalidDimensionException when the point dimension is not compatible with the dimension of the current instance
     * @throws InvalidSridException      when the point SRID is not compatible with the SRID of the current instance
     * @throws InvalidValueException     when the array is not an array of points
     * @throws MissingValueException     when a coordinate of a point is missing
     */
    public function addPoints(array $points): static
    {
        foreach ($points as $point) {
            if (!is_array($point) && !$point instanceof PointInterface) {
                throw new InvalidValueException('The point is missing.');
            }

            $this->addPoint($point);
        }

        return $this;
    }

    /**
     * Get a point of this spatial collection of points.
     *
     * @param int $index index of the point. -1 is the last point. -2 is the penultimate point, etc.
     *
     * @throws OutOfBoundsException when the spatial collection contains no points
     */
    public function getPoint(int $index): PointInterface
    {
        if (empty($this->points)) {
            throw new OutOfBoundsException('The current collection of points is empty.');
        }

        $index = $index % count($this->points);

        if ($index < 0) {
            $index = count($this->points) + $index;
        }

        return $this->points[$index];
    }

    /**
     * Get the points of this spatial collection of points.
     *
     * @return PointInterface[]
     */
    public function getPoints(): array
    {
        return $this->points;
    }

    /**
     * Is this spatial collection of points empty?
     */
    public function isEmpty(): bool
    {
        return empty($this->points);
    }

    /**
     * Is this spatial collection of points simple?
     *
     * A spatial collection of points is considered as simple, if, and only if, no two Point values in the set are equal.
     */
    public function isSimple(): bool
    {
        foreach ($this->getPoints() as $point) {
            foreach ($this->getPoints() as $otherPoint) {
                if ($point !== $otherPoint && $point->equalsTo($otherPoint)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Return an array representation of the multipoint.
     *
     * @return (\DateTimeInterface|float|int)[][]
     */
    public function toArray(): array
    {
        $points = $this->getPoints();

        return array_map(
            static fn (PointInterface $point) => $point->toArray(),
            $points
        );
    }
}
