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

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\SpatialTypes\Factory\SpatialContext;
use LongitudeOne\SpatialTypes\Interfaces\MutablePointInterface;
use LongitudeOne\SpatialTypes\Internal\Factor\Hydrator\CoordinatesHydrator;

/** Tuple hydration for mutable point sequences.
 * @internal
 */
final class MutablePointHydrator
{
    /**
     * @param array{0: null|float|int|string, 1: null|float|int|string, 2 ?: null|float|int, 3 ?: null|float|int} $tuple   Point tuple
     * @param SpatialContext                                                                                      $context Declared sequence context
     */
    public static function hydrate(array $tuple, SpatialContext $context): MutablePointInterface
    {
        $coordinates = (new CoordinatesHydrator())->hydrate($tuple, $context);

        return match ($context->dimension) {
            CoordinateDimensionEnum::XY => new Dimension2\Point($coordinates->x, $coordinates->y, $context->reference, $context->family),
            CoordinateDimensionEnum::XYZ => new Dimension3z\Point($coordinates->x, $coordinates->y, $coordinates->z, $context->reference, $context->family),
            CoordinateDimensionEnum::XYM => new Dimension3m\Point($coordinates->x, $coordinates->y, $coordinates->m, $context->reference, $context->family),
            CoordinateDimensionEnum::XYZM => new Dimension4zm\Point($coordinates->x, $coordinates->y, $coordinates->z, $coordinates->m, $context->reference, $context->family),
        };
    }
}
