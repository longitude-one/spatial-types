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

use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\OutOfBoundsException;
use LongitudeOne\SpatialTypes\Interfaces\MutableCircularStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutablePointInterface;

/** Mutable circular point sequences and structural arc editing.
 * @internal
 */
abstract class AbstractCircularString extends AbstractPointSequence implements MutableCircularStringInterface
{
    use PointSequenceEditingTrait;

    /**
     * @param MutablePointInterface $passingPoint     Intermediate point
     * @param MutablePointInterface $destinationPoint End point
     */
    public function appendArc(MutablePointInterface $passingPoint, MutablePointInterface $destinationPoint): static
    {
        if (0 === count($this->points) % 2) {
            throw new InvalidValueException('Appending an arc requires an origin followed by complete point pairs.');
        }

        $this->points[] = $passingPoint;
        $this->points[] = $destinationPoint;

        return $this;
    }

    /** Return the fixed spatial type. */
    public function getType(): GeometryTypeEnum
    {
        return GeometryTypeEnum::CIRCULARSTRING;
    }

    /**
     * @param int                   $index            Zero-based arc index
     * @param MutablePointInterface $passingPoint     Replacement intermediate point
     * @param MutablePointInterface $destinationPoint Replacement shared endpoint
     */
    public function replaceArc(int $index, MutablePointInterface $passingPoint, MutablePointInterface $destinationPoint): static
    {
        if ($index < 0 || $index >= intdiv(max(0, count($this->points) - 1), 2)) {
            throw new OutOfBoundsException(sprintf('Arc index %d does not address three existing points.', $index));
        }

        array_splice($this->points, 2 * $index + 1, 2, [$passingPoint, $destinationPoint]);

        return $this;
    }

    /** @param MutablePointInterface $point Origin retained without cloning */
    public function setOrigin(MutablePointInterface $point): static
    {
        $this->points[0] = $point;

        return $this;
    }
}
