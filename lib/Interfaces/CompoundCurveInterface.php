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

    /** @return (float|int|null)[][][] */
    public function toArray(): array;
}
