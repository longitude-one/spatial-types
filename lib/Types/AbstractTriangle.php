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
use LongitudeOne\SpatialTypes\Interfaces\TriangleInterface;
use LongitudeOne\SpatialTypes\Validator\TriangleValidation;
use LongitudeOne\SpatialTypes\Value\Coordinates;

/**
 * Shared structural constraints for SQL/MM triangles.
 *
 * @internal
 */
abstract class AbstractTriangle extends AbstractPolygon implements TriangleInterface
{
    /**
     * Replace a vertex while retaining a valid triangular ring.
     *
     * @param int         $ringIndex   Ring index
     * @param int         $pointIndex  Point index
     * @param Coordinates $coordinates Replacement coordinates
     */
    public function withPoint(int $ringIndex, int $pointIndex, Coordinates $coordinates): static
    {
        $triangle = parent::withPoint($ringIndex, $pointIndex, $coordinates);
        TriangleValidation::assertValid($triangle);

        return $triangle;
    }

    /**
     * Add the sole exterior ring, containing three vertices and the closure point.
     *
     * @param array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}[]|LineStringInterface|PointInterface[] $ring Exterior ring
     */
    protected function addRing(array|LineStringInterface $ring): static
    {
        parent::addRing($ring);
        TriangleValidation::assertValid($this);

        return $this;
    }
}
