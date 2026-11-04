<?php
/**
 * This file is part of the spatial project.
 *
 * PHP 8.4 | 8.5
 *
 * Copyright Alexandre Tranchant <alexandre.tranchant@gmail.com> 2026
 * Copyright Longitude One 2026
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

declare(strict_types=1);

namespace LongitudeOne\SpatialTypes\Internal\Types;

use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\OutOfBoundsException;
use LongitudeOne\SpatialTypes\Interfaces\MutablePointInterface;
use LongitudeOne\SpatialTypes\Interfaces\SpatialInterface;

/** Atomic membership editing shared by mutable linear and circular paths.
 * @internal
 */
trait PointSequenceEditingTrait
{
    /** @param SpatialInterface $point Point retained without cloning */
    public function addPoint(SpatialInterface $point): static
    {
        if (!$point instanceof MutablePointInterface) {
            throw new InvalidValueException('A mutable point sequence requires MutablePointInterface components.');
        }
        MutableComponentGuard::check($point, $this);
        $this->points[] = $point;

        return $this;
    }

    /**
     * @param int              $index Zero-based insertion index, up to the point count
     * @param SpatialInterface $point Point retained without cloning
     */
    public function insertPoint(int $index, SpatialInterface $point): static
    {
        if (!$point instanceof MutablePointInterface) {
            throw new InvalidValueException('A mutable point sequence requires MutablePointInterface components.');
        }
        if ($index < 0 || $index > \count($this->points)) {
            throw new OutOfBoundsException(\sprintf('Point index %d is outside the insertion range.', $index));
        }

        MutableComponentGuard::check($point, $this);
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
     * @param array<array{0: float|int|string|null, 1: float|int|string|null, 2 ?: float|int|null, 3 ?: float|int|null}> $coordinates Replacement tuples
     */
    public function setArrayOfCoordinates(array $coordinates): static
    {
        $this->points = $this->hydratePoints($coordinates);

        return $this;
    }

    /**
     * @param int              $index Existing zero-based point index
     * @param SpatialInterface $point Point retained without cloning
     */
    public function setPoint(int $index, SpatialInterface $point): static
    {
        if (!$point instanceof MutablePointInterface) {
            throw new InvalidValueException('A mutable point sequence requires MutablePointInterface components.');
        }
        $this->requirePointIndex($index);
        MutableComponentGuard::check($point, $this);
        array_splice($this->points, $index, 1, [$point]);

        return $this;
    }
}
