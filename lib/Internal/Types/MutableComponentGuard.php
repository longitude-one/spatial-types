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

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
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

/** Checks mutable component ownership and layout without geometric validation.
 * @internal
 */
final class MutableComponentGuard
{
    /** Reject incompatible layouts, immutable components and containment cycles recursively.
     * @param SpatialInterface      $component Component to inspect
     * @param SpatialInterface|null $receiver  Aggregate receiving the component
     */
    public static function check(SpatialInterface $component, ?SpatialInterface $receiver = null): void
    {
        $states = [];
        if (null !== $receiver) {
            $states[spl_object_id($receiver)] = false;
        }
        self::inspect($component, $states, $receiver?->getDimension());
    }

    /** Distinguish active ancestors from completed shared components.
     * @param SpatialInterface             $component Component to inspect
     * @param array<int, bool>             $states    False while visiting, true once inspection completes
     * @param CoordinateDimensionEnum|null $dimension Declared receiver layout
     */
    private static function inspect(SpatialInterface $component, array &$states, ?CoordinateDimensionEnum $dimension): void
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
                self::inspect($start, $states, $dimension);
            }
            if (null !== $end) {
                self::inspect($end, $states, $dimension);
            }
        }
        if ($component instanceof CollectionInterface || $component instanceof LineStringInterface
            || $component instanceof CircularStringInterface || $component instanceof CompoundCurveInterface
            || $component instanceof PolyhedralSurfaceInterface) {
            foreach ($component->getElements() as $child) {
                self::inspect($child, $states, $dimension);
            }
        } elseif ($component instanceof PolygonInterface) {
            foreach ($component->getRings() as $ring) {
                self::inspect($ring, $states, $dimension);
            }
        }
        self::requireDimension($component, $dimension);
        $states[$identity] = true;
    }

    /** Return whether inspection completed, rejecting an active ancestor.
     * @param int              $identity Component object identifier
     * @param array<int, bool> $states   False while visiting, true once inspection completes
     */
    private static function isComplete(int $identity, array $states): bool
    {
        if (!\array_key_exists($identity, $states)) {
            return false;
        }
        if (!$states[$identity]) {
            throw new InvalidValueException('Mutable aggregate members must not form reference cycles.');
        }

        return true;
    }

    /** Require the receiver's exact layout without converting the component.
     * @param SpatialInterface             $component Component to inspect
     * @param CoordinateDimensionEnum|null $dimension Declared receiver layout
     */
    private static function requireDimension(SpatialInterface $component, ?CoordinateDimensionEnum $dimension): void
    {
        if (null !== $dimension && $component->getDimension() !== $dimension) {
            throw new InvalidDimensionException(\sprintf('Mutable components must use the declared %s coordinate dimension.', $dimension->value));
        }
    }
}
