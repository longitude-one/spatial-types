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

namespace LongitudeOne\SpatialTypes\Collection;

use LongitudeOne\SpatialTypes\Exception\OutOfBoundsException;
use LongitudeOne\SpatialTypes\Interfaces\LineStringInterface;

/**
 * Collection of line strings used by multi-line strings.
 *
 * @internal
 */
final class LineStringCollection
{
    /** @var LineStringInterface[] */
    private array $lineStrings = [];

    /**
     * @param LineStringInterface[] $lineStrings Line strings in this collection
     */
    public function __construct(array $lineStrings = [])
    {
        foreach ($lineStrings as $lineString) {
            $this->add($lineString);
        }
    }

    /**
     * Add a line string.
     *
     * @param LineStringInterface $lineString Line string to add
     */
    public function add(LineStringInterface $lineString): void
    {
        $this->lineStrings[] = $lineString;
    }

    /** @return LineStringInterface[] */
    public function all(): array
    {
        return $this->lineStrings;
    }

    /**
     * Return a line string using the collection index convention.
     *
     * @param int $index Requested index
     */
    public function at(int $index): LineStringInterface
    {
        if ([] === $this->lineStrings) {
            throw new OutOfBoundsException('The current collection of line strings is empty.');
        }

        $index %= count($this->lineStrings);

        return $this->lineStrings[$index < 0 ? count($this->lineStrings) + $index : $index];
    }

    /** Return the number of line strings. */
    public function count(): int
    {
        return count($this->lineStrings);
    }

    /** Whether the collection is empty. */
    public function isEmpty(): bool
    {
        return [] === $this->lineStrings;
    }

    /**
     * @param LineStringInterface[] $lineStrings Replacement line strings
     */
    public function replace(array $lineStrings): void
    {
        $this->lineStrings = $lineStrings;
    }
}
