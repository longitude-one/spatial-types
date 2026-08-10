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

namespace LongitudeOne\SpatialTypes\Interfaces;

use LongitudeOne\SpatialTypes\Value\Coordinates;

/**
 * Multi-line string interface.
 *
 * The MultiLineString type corresponds to the instantiable ST_MultiLineString
 * subtype of ST_MultiCurve defined by ISO/IEC 13249-3. Its elements are
 * restricted to LineString values.
 */
interface MultiLineStringInterface extends CollectionInterface
{
    /**
     * Return an ordered array of spatial interfaces in the collection.
     *
     * @return LineStringInterface[]
     */
    public function getElements(): array;

    /**
     * Return the line strings that compose the multi-line string.
     *
     * @return LineStringInterface[]
     */
    public function getLineStrings(): array;

    /**
     * Return an array of coordinates.
     *
     * @return (float|int)[][][]
     */
    public function toArray(): array;

    /**
     * Return a new multi-line string with one replacement line string.
     *
     * The returned multi-line string preserves this instance's family,
     * dimension, and Spatial Reference Identifier (SRID). Every replacement
     * coordinate tuple must match this instance's coordinate layout.
     *
     * @param int                                                                                              $lineStringIndex index of the line string to replace; negative indexes count from the end
     * @param array<array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}> $coordinates     replacement line-string coordinates
     */
    public function withLineString(int $lineStringIndex, array $coordinates): static;

    /**
     * Return a new multi-line string with one replacement point.
     *
     * @param int         $lineStringIndex index of the line string to replace; negative indexes count from the end
     * @param int         $pointIndex      index of the point to replace; negative indexes count from the end
     * @param Coordinates $coordinates     replacement point coordinates
     */
    public function withPoint(int $lineStringIndex, int $pointIndex, Coordinates $coordinates): static;
}
