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

namespace LongitudeOne\SpatialTypes\Internal\Factory;

/**
 * Immutable coordinates used to create a point.
 *
 * @internal this data-transfer object supports the internal factory pipeline
 */
final readonly class Coordinates
{
    /**
     * @param null|float|int|string $x X coordinate or longitude
     * @param null|float|int|string $y Y coordinate or latitude
     * @param null|float|int        $z Z coordinate or elevation
     * @param null|float|int        $m M coordinate or measure
     */
    public function __construct(
        public float|int|string|null $x,
        public float|int|string|null $y,
        public float|int|null $z = null,
        public float|int|null $m = null
    ) {
    }
}
