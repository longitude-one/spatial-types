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

/** Checks component ownership without checking geometric validity.
 * @internal
 */
final class ImmutableComponentGuard
{
    /** Reject mutable components at every aggregate nesting level.
     * @param SpatialInterface $component Component to inspect
     */
    public static function check(SpatialInterface $component): void
    {
        $visited = [];
        self::inspect($component, $visited);
    }

    /** Inspect each component once, including custom aggregate implementations.
     * @param SpatialInterface $component Component to inspect
     * @param array<int, true> $visited   Already inspected components
     */
    private static function inspect(SpatialInterface $component, array &$visited): void
    {
        if (!$component instanceof ImmutableSpatialInterface || $component instanceof MutableSpatialInterface) {
            throw new InvalidValueException('Immutable aggregates require exclusively immutable spatial components.');
        }
        if (isset($visited[spl_object_id($component)])) {
            return;
        }
        $visited[spl_object_id($component)] = true;
        if ($component instanceof CurveInterface) {
            $start = $component->getStartPoint();
            $end = $component->getEndPoint();
            if (null !== $start) {
                self::inspect($start, $visited);
            }
            if (null !== $end) {
                self::inspect($end, $visited);
            }
        }
        if ($component instanceof CollectionInterface || $component instanceof LineStringInterface
            || $component instanceof CircularStringInterface || $component instanceof CompoundCurveInterface
            || $component instanceof PolyhedralSurfaceInterface) {
            foreach ($component->getElements() as $child) {
                self::inspect($child, $visited);
            }
        } elseif ($component instanceof PolygonInterface) {
            foreach ($component->getRings() as $ring) {
                self::inspect($ring, $visited);
            }
        }
    }
}
