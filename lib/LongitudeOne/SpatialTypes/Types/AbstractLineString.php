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
     * @param (float|int|PointInterface)[] $points
     */
    public function __construct(array $points)
    {
        $this->preConstruct();

        foreach ($points as $point) {
            $this->addPoint($point);
        }
    }

    public function addPoint(array|PointInterface $point): AbstractLineString
    {
        if (is_array($point)) {
            $point = FactoryPoint::fromArray($point, $this->getSrid(), $this->getFamily(), $this->getDimension());
        }

        if ($point->getDimension() !== $this->getDimension()) {
            throw new InvalidDimensionException('The point dimension is not compatible with the line string dimension.');
        }

        if (!empty($point->getSrid()) && $point->getSrid() !== $this->getSrid()) {
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
        return $this->isLine() && $this->points[0] === $this->points[count($this->points) - 1];
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
     * @return (float|int)[][]
     */
    public function toArray(): array
    {
        $points = $this->getPoints();

        return array_map(
            static fn (PointInterface $point) => $point->toArray(),
            $points
        );
    }

    public function __toString(): string
    {
        return '('.implode('), (', $this->toArray()).')';
    }
}
