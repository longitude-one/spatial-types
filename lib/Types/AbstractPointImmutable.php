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

use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Interfaces\ImmutablePointInterface;
use LongitudeOne\SpatialTypes\Value\Coordinates;

/** Immutable point coordinate replacement.
 * @internal
 */
abstract class AbstractPointImmutable extends AbstractSpatialTypeImmutable implements ImmutablePointInterface
{
    use PointBehaviorTrait;

    /**
     * Return a copy of this point with the supplied normalized coordinates.
     *
     * The coordinate dimension must match the concrete point type. The point's
     * family and Spatial Reference Identifier (SRID) are preserved.
     *
     * @param Coordinates $coordinates replacement coordinates
     *
     * @throws InvalidDimensionException when the coordinate dimension differs from this point's dimension
     */
    public function withCoordinates(Coordinates $coordinates): static
    {
        if ($this->getDimension() !== $coordinates->dimension) {
            throw new InvalidDimensionException(sprintf('The %s coordinates are incompatible with the %s point dimension.', $coordinates->dimension->value, $this->getDimension()->value));
        }

        $point = clone $this;
        $point->initializeX($coordinates->x);
        $point->initializeY($coordinates->y);

        if ($coordinates->dimension->hasZ()) {
            $point->initializeZ($coordinates->getZ());
        }

        if ($coordinates->dimension->hasM()) {
            $point->initializeM($coordinates->getM());
        }

        $point->hasOnlyNullCoordinates(...$coordinates->toArray());

        return $point;
    }
}
