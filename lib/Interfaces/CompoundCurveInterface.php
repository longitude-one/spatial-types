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

namespace LongitudeOne\SpatialTypes\Interfaces;

/** Ordered, continuous components of a compound curve. */
interface CompoundCurveInterface extends CurveInterface
{
    /**
     * Return a component using the collection index convention.
     *
     * @param int $index Requested index; negative indexes count from the end
     */
    public function getCurve(int $index): CurveInterface;

    /** @return CurveInterface[] */
    public function getCurves(): array;

    /** @return CurveInterface[] */
    public function getElements(): array;

    /** @return (float|int)[][][] */
    public function toArray(): array;
}
