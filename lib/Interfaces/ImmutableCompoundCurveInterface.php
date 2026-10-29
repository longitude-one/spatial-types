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

    /** @return (null|float|int)[][][] */
    public function toArray(): array;
}
