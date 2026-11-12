<?php

/*
 * This file is part of the LongitudeOne Spatial project.
 *
 * Copyright (c) 2026 Alexandre Tranchant <alexandre.tranchant@gmail.com>
 *
 * See LICENSE for copyright and license information.
 *
 */

declare(strict_types=1);

namespace LongitudeOne\SpatialTypes\Internal\Types;

use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\OutOfBoundsException;
use LongitudeOne\SpatialTypes\Interfaces\MutableCircularStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutablePointInterface;
use LongitudeOne\SpatialTypes\Interfaces\SpatialInterface;

/** Mutable circular point sequences and structural arc editing.
 * @internal
 */
abstract class AbstractCircularString extends AbstractPointSequence implements MutableCircularStringInterface
{
    use PointSequenceEditingTrait;

    /**
     * @param SpatialInterface $passingPoint     Intermediate point
     * @param SpatialInterface $destinationPoint End point
     */
    public function appendArc(SpatialInterface $passingPoint, SpatialInterface $destinationPoint): static
    {
        if (!$passingPoint instanceof MutablePointInterface) {
            throw new InvalidValueException('A mutable circular string requires MutablePointInterface components.');
        }
        if (!$destinationPoint instanceof MutablePointInterface) {
            throw new InvalidValueException('A mutable circular string requires MutablePointInterface components.');
        }
        if (0 === \count($this->points) % 2) {
            throw new InvalidValueException('Appending an arc requires an origin followed by complete point pairs.');
        }

        MutableComponentGuard::check($passingPoint, $this);
        MutableComponentGuard::check($destinationPoint, $this);
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
     * @param int              $index            Zero-based arc index
     * @param SpatialInterface $passingPoint     Replacement intermediate point
     * @param SpatialInterface $destinationPoint Replacement shared endpoint
     */
    public function replaceArc(int $index, SpatialInterface $passingPoint, SpatialInterface $destinationPoint): static
    {
        if (!$passingPoint instanceof MutablePointInterface) {
            throw new InvalidValueException('A mutable circular string requires MutablePointInterface components.');
        }
        if (!$destinationPoint instanceof MutablePointInterface) {
            throw new InvalidValueException('A mutable circular string requires MutablePointInterface components.');
        }
        if ($index < 0 || $index >= intdiv(max(0, \count($this->points) - 1), 2)) {
            throw new OutOfBoundsException(\sprintf('Arc index %d does not address three existing points.', $index));
        }

        MutableComponentGuard::check($passingPoint, $this);
        MutableComponentGuard::check($destinationPoint, $this);
        array_splice($this->points, 2 * $index + 1, 2, [$passingPoint, $destinationPoint]);

        return $this;
    }

    /** @param SpatialInterface $point Origin retained without cloning */
    public function setOrigin(SpatialInterface $point): static
    {
        if (!$point instanceof MutablePointInterface) {
            throw new InvalidValueException('A mutable circular string requires MutablePointInterface components.');
        }
        MutableComponentGuard::check($point, $this);
        $this->points[0] = $point;

        return $this;
    }
}
