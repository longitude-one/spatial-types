<?php
/**
 * This file is part of the spatial project.
 *
 * PHP 8.4 | 8.5
 *
 * Copyright Alexandre Tranchant <alexandre.tranchant@gmail.com> 2026
 * Copyright Longitude One 2026
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
     * @param float|int|string|null $x X coordinate or longitude
     * @param float|int|string|null $y Y coordinate or latitude
     * @param float|int|null        $z Z coordinate or elevation
     * @param float|int|null        $m M coordinate or measure
     */
    public function __construct(
        public float|int|string|null $x,
        public float|int|string|null $y,
        public float|int|null $z = null,
        public float|int|null $m = null,
    ) {
    }
}
