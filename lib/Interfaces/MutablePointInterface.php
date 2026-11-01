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

namespace LongitudeOne\SpatialTypes\Interfaces;

/** In-place point editing; unavailable Z/M axes reject even null values. */
interface MutablePointInterface extends MutableSpatialInterface, PointInterface
{
    /** @param float|int|null $m Measure ordinate; the layout must contain M */
    public function setM(float|int|null $m): static;

    /** @param float|int|string|null $x X ordinate; strings use constructor parsing */
    public function setX(float|int|string|null $x): static;

    /** @param float|int|string|null $y Y ordinate; strings use constructor parsing */
    public function setY(float|int|string|null $y): static;

    /** @param float|int|null $z Elevation ordinate; the layout must contain Z */
    public function setZ(float|int|null $z): static;

    /** @return (float|int|null)[] */
    public function toArray(): array;
}
