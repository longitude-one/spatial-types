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

namespace LongitudeOne\SpatialTypes\Types;

use LongitudeOne\SpatialTypes\Interfaces\LineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\PointInterface;
use LongitudeOne\SpatialTypes\Trait\PointTrait;

/**
 * Abstract LineString class.
 *
 * @internal This class is internal. It is used to create geometry or geography line string of spatial objects.
 */
abstract class AbstractLineString extends AbstractSpatialType implements LineStringInterface
{
    use PointTrait;

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
