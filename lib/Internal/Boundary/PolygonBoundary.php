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

namespace LongitudeOne\SpatialTypes\Internal\Boundary;

use LongitudeOne\SpatialTypes\Interfaces\ImmutableLineStringInterface;
use LongitudeOne\SpatialTypes\Internal\Collection\LineStringCollection;

/**
 * Polygon boundary: one exterior ring followed by zero or more interior rings.
 *
 * @internal
 */
final class PolygonBoundary
{
    private LineStringCollection $rings;

    /**
     * @param ImmutableLineStringInterface[] $rings Boundary rings, exterior first
     */
    public function __construct(array $rings = [])
    {
        $this->rings = new LineStringCollection($rings);
    }

    /**
     * Add a boundary ring.
     *
     * @param ImmutableLineStringInterface $ring Ring to add
     */
    public function addRing(ImmutableLineStringInterface $ring): void
    {
        $this->rings->add($ring);
    }

    /** Return the number of boundary rings. */
    public function count(): int
    {
        return $this->rings->count();
    }

    /**
     * Return a boundary ring using the collection index convention.
     *
     * @param int $index Requested index
     */
    public function ringAt(int $index): ImmutableLineStringInterface
    {
        return $this->rings->at($index);
    }

    /** @return ImmutableLineStringInterface[] */
    public function rings(): array
    {
        return $this->rings->all();
    }
}
