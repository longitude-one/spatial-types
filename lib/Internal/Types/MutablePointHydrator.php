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
use LongitudeOne\SpatialTypes\Interfaces\MutablePointInterface;
use LongitudeOne\SpatialTypes\Internal\Factory\Hydrator\CoordinatesHydrator;
use LongitudeOne\SpatialTypes\Internal\Factory\SpatialContext;
use LongitudeOne\SpatialTypes\Types\Dimension2;
use LongitudeOne\SpatialTypes\Types\Dimension3m;
use LongitudeOne\SpatialTypes\Types\Dimension3z;
use LongitudeOne\SpatialTypes\Types\Dimension4zm;

/** Tuple hydration for mutable point sequences.
 * @internal
 */
final class MutablePointHydrator
{
    /**
     * @param array{0: float|int|string|null, 1: float|int|string|null, 2 ?: float|int|null, 3 ?: float|int|null} $tuple   Point tuple
     * @param SpatialContext                                                                                      $context Declared sequence context
     */
    public static function hydrate(array $tuple, SpatialContext $context): MutablePointInterface
    {
        $coordinates = new CoordinatesHydrator()->hydrate($tuple, $context);

        return match ($context->dimension) {
            CoordinateDimensionEnum::XY => new Dimension2\Point($coordinates->x, $coordinates->y, $context->reference, $context->family),
            CoordinateDimensionEnum::XYZ => new Dimension3z\Point($coordinates->x, $coordinates->y, $coordinates->z, $context->reference, $context->family),
            CoordinateDimensionEnum::XYM => new Dimension3m\Point($coordinates->x, $coordinates->y, $coordinates->m, $context->reference, $context->family),
            CoordinateDimensionEnum::XYZM => new Dimension4zm\Point($coordinates->x, $coordinates->y, $coordinates->z, $coordinates->m, $context->reference, $context->family),
        };
    }
}
