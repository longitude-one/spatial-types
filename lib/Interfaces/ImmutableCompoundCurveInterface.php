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

/** Immutable compoundcurve read and copy contract. */
interface ImmutableCompoundCurveInterface extends CompoundCurveInterface, ImmutableCurveInterface
{
    /**
     * Return a component using the collection index convention.
     *
     * @param int $index Requested index; negative indexes count from the end
     */
    public function getCurve(int $index): ImmutableCurveInterface;

    /** @return ImmutableCurveInterface[] */
    public function getCurves(): array;

    /** @return ImmutableCurveInterface[] */
    public function getElements(): array;

    /** Return the immutable end point, or null for EMPTY. */
    public function getEndPoint(): ?ImmutablePointInterface;

    /** Return the immutable start point, or null for EMPTY. */
    public function getStartPoint(): ?ImmutablePointInterface;

    /** @return (float|int|null)[][][] */
    public function toArray(): array;
}
