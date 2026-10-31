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

namespace LongitudeOne\SpatialTypes\Internal\Types;

use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Interfaces\CircularStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\CollectionInterface;
use LongitudeOne\SpatialTypes\Interfaces\CompoundCurveInterface;
use LongitudeOne\SpatialTypes\Interfaces\CurveInterface;
use LongitudeOne\SpatialTypes\Interfaces\ImmutableSpatialInterface;
use LongitudeOne\SpatialTypes\Interfaces\LineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutableSpatialInterface;
use LongitudeOne\SpatialTypes\Interfaces\PolygonInterface;
use LongitudeOne\SpatialTypes\Interfaces\PolyhedralSurfaceInterface;
use LongitudeOne\SpatialTypes\Interfaces\SpatialInterface;

/** Checks mutable component ownership without checking geometric validity.
 * @internal
 */
final class MutableComponentGuard
{
    /** Reject immutable components and containment cycles at every nesting level.
     * @param SpatialInterface      $component Component to inspect
     * @param null|SpatialInterface $receiver  Aggregate receiving the component
     */
    public static function check(SpatialInterface $component, ?SpatialInterface $receiver = null): void
    {
        $states = [];
        if (null !== $receiver) {
            $states[spl_object_id($receiver)] = false;
        }
        self::inspect($component, $states);
    }

    /** Distinguish active ancestors from completed shared components.
     * @param SpatialInterface $component Component to inspect
     * @param array<int, bool> $states    False while visiting, true once inspection completes
     */
    private static function inspect(SpatialInterface $component, array &$states): void
    {
        if (!$component instanceof MutableSpatialInterface || $component instanceof ImmutableSpatialInterface) {
            throw new InvalidValueException('Mutable aggregates require exclusively mutable spatial components.');
        }
        $identity = spl_object_id($component);
        if (self::isComplete($identity, $states)) {
            return;
        }
        $states[$identity] = false;
        if ($component instanceof CurveInterface) {
            $start = $component->getStartPoint();
            $end = $component->getEndPoint();
            if (null !== $start) {
                self::inspect($start, $states);
            }
            if (null !== $end) {
                self::inspect($end, $states);
            }
        }
        if ($component instanceof CollectionInterface || $component instanceof LineStringInterface
            || $component instanceof CircularStringInterface || $component instanceof CompoundCurveInterface
            || $component instanceof PolyhedralSurfaceInterface) {
            foreach ($component->getElements() as $child) {
                self::inspect($child, $states);
            }
        } elseif ($component instanceof PolygonInterface) {
            foreach ($component->getRings() as $ring) {
                self::inspect($ring, $states);
            }
        }
        $states[$identity] = true;
    }

    /** Return whether inspection completed, rejecting an active ancestor.
     * @param int              $identity Component object identifier
     * @param array<int, bool> $states   False while visiting, true once inspection completes
     */
    private static function isComplete(int $identity, array $states): bool
    {
        if (!array_key_exists($identity, $states)) {
            return false;
        }
        if (!$states[$identity]) {
            throw new InvalidValueException('Mutable aggregate members must not form reference cycles.');
        }

        return true;
    }
}
