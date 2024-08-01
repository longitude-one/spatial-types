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

namespace LongitudeOne\SpatialTypes\Types;

use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Exception\InvalidSridException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\MissingValueException;
use LongitudeOne\SpatialTypes\Exception\OutOfBoundsException;
use LongitudeOne\SpatialTypes\Factory\FactoryPoint;
use LongitudeOne\SpatialTypes\Interfaces\LineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\PointInterface;

abstract class AbstractLineString extends AbstractSpatialType implements LineStringInterface
{
    /**
     * Points of this line string.
     *
     * @var PointInterface[]
     */
    private array $points = [];

    /**
     * AbstractLineString constructor.
     *
     * @param (array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|\DateTimeInterface|float|int}|PointInterface)[] $points points of the line string
     * @param null|int                                                                                                                        $srid   Spatial Reference Identifier
     *
     * @throws InvalidDimensionException when the point dimension is not compatible with the line string dimension
     * @throws InvalidSridException      when the point SRID is not compatible with the line string SRID
     * @throws InvalidValueException     when coordinates of the point are invalid
     * @throws MissingValueException     when the point is missing
     */
    public function __construct(array $points, ?int $srid = null)
    {
        $this->preConstruct();
        $this->setSrid($srid);

        foreach ($points as $point) {
            $this->addPoint($point);
        }
    }

    /**
     * Add a point to the line string.
     *
     * @param array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|\DateTimeInterface|float|int}|PointInterface $point point to add
     *
     * @throws InvalidDimensionException when the point dimension is not compatible with the line string dimension
     * @throws InvalidSridException      when the point SRID is not compatible with the line string SRID
     * @throws InvalidValueException     when coordinates of the point are invalid
     * @throws MissingValueException     when a coordinate of the point is missing
     */
    public function addPoint(array|PointInterface $point): AbstractLineString
    {
        if (is_array($point)) {
            $point = FactoryPoint::fromIndexedArray($point, $this->getSrid(), $this->getFamily(), $this->getDimension());
        }

        if (!$this->hasSameDimension($point)) {
            throw new InvalidDimensionException('The point dimension is not compatible with the line string dimension.');
        }

        if (!empty($point->getSrid()) && !empty($this->getSrid()) && $point->getSrid() !== $this->getSrid()) {
            throw new InvalidSridException('The point SRID is not compatible with the line string SRID.');
        }

        $this->points[] = $point;

        return $this;
    }

    /**
     * Get the elements of this line string.
     *
     * @return PointInterface[]
     */
    public function getElements(): array
    {
        return $this->getPoints();
    }

    /**
     * Get a point of the line string.
     *
     * @param int $index index of the point. -1 is the last point. -2 is the penultimate point, etc.
     *
     * @throws OutOfBoundsException when the line string is empty
     */
    public function getPoint(int $index): PointInterface
    {
        if (empty($this->points)) {
            throw new OutOfBoundsException('The line string is empty.');
        }

        $index = $index % count($this->points);

        if ($index < 0) {
            $index = count($this->points) + $index;
        }

        return $this->points[$index];
    }

    /**
     * Get the points of this line string.
     *
     * @return PointInterface[]
     */
    public function getPoints(): array
    {
        return $this->points;
    }

    /**
     * This line string is closed when the first point is the same as the last point.
     */
    public function isClosed(): bool
    {
        return $this->isLine() && $this->points[0]->equalsTo($this->points[count($this->points) - 1]);
    }

    /**
     * Is this line string a line?
     *
     * A line is composed of at least two points.
     */
    public function isLine(): bool
    {
        return count($this->points) >= 2;
    }

    /**
     * This line string is a string when the line is closed and simple.
     */
    public function isRing(): bool
    {
        return $this->isClosed();
    }

    /**
     * Return an array representation of the line string.
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
