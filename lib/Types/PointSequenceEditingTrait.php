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

use LongitudeOne\SpatialTypes\Exception\OutOfBoundsException;
use LongitudeOne\SpatialTypes\Interfaces\MutablePointInterface;

/** Atomic membership editing shared by mutable linear and circular paths.
 * @internal
 */
trait PointSequenceEditingTrait
{
    /** @param MutablePointInterface $point Point retained without cloning */
    public function addPoint(MutablePointInterface $point): static
    {
        $this->points[] = $point;

        return $this;
    }

    /**
     * @param int                   $index Zero-based insertion index, up to the point count
     * @param MutablePointInterface $point Point retained without cloning
     */
    public function insertPoint(int $index, MutablePointInterface $point): static
    {
        if ($index < 0 || $index > count($this->points)) {
            throw new OutOfBoundsException(sprintf('Point index %d is outside the insertion range.', $index));
        }

        array_splice($this->points, $index, 0, [$point]);

        return $this;
    }

    /** @param int $index Existing zero-based point index */
    public function removePoint(int $index): static
    {
        $this->requirePointIndex($index);
        array_splice($this->points, $index, 1);

        return $this;
    }

    /**
     * @param array<array{0: null|float|int|string, 1: null|float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}> $coordinates Replacement tuples
     */
    public function setArrayOfCoordinates(array $coordinates): static
    {
        $this->points = $this->hydratePoints($coordinates);

        return $this;
    }

    /**
     * @param int                   $index Existing zero-based point index
     * @param MutablePointInterface $point Point retained without cloning
     */
    public function setPoint(int $index, MutablePointInterface $point): static
    {
        $this->requirePointIndex($index);
        array_splice($this->points, $index, 1, [$point]);

        return $this;
    }
}
