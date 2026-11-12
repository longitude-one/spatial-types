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

namespace LongitudeOne\SpatialTypes\Tests\Issue;

use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Types\Dimension2 as XY;
use LongitudeOne\SpatialTypes\Types\Dimension3m as XYM;
use PHPUnit\Framework\TestCase;

/** Regression coverage for #78.
 * @internal
 *
 * @coversNothing
 */
class MutableCompoundCurveDimensionMutationTest extends TestCase
{
    /** Rejection preserves membership and subsequent context propagation. */
    public function testAddRejectsAtomically(): void
    {
        $member = new XY\LineString([[1, 2]]);
        $aggregate = new XY\CompoundCurve([$member], 2154);
        $incompatible = new XYM\CircularString([]);
        $this->expectException(InvalidDimensionException::class);

        try {
            $aggregate->addCurve($incompatible);
        } finally {
            static::assertSame([$member], $aggregate->getCurves());
            static::assertSame(2154, $aggregate->getSrid());
            $aggregate->setSrid(4326);
            static::assertSame(4326, $member->getSrid());
            static::assertSame(0, $incompatible->getSrid());
        }
    }

    /** Rejection preserves membership and subsequent context propagation. */
    public function testInsertRejectsAtomically(): void
    {
        $member = new XY\LineString([[1, 2]]);
        $aggregate = new XY\CompoundCurve([$member], 2154);
        $incompatible = new XYM\CircularString([]);
        $this->expectException(InvalidDimensionException::class);

        try {
            $aggregate->insertCurve(1, $incompatible);
        } finally {
            static::assertSame([$member], $aggregate->getCurves());
            static::assertSame(2154, $aggregate->getSrid());
            $aggregate->setSrid(4326);
            static::assertSame(4326, $member->getSrid());
            static::assertSame(0, $incompatible->getSrid());
        }
    }

    /** Rejection preserves membership and subsequent context propagation. */
    public function testSetallRejectsAtomically(): void
    {
        $member = new XY\LineString([[1, 2]]);
        $aggregate = new XY\CompoundCurve([$member], 2154);
        $incompatible = new XYM\CircularString([]);
        $this->expectException(InvalidDimensionException::class);

        try {
            $aggregate->setCurves([$member, $incompatible]);
        } finally {
            static::assertSame([$member], $aggregate->getCurves());
            static::assertSame(2154, $aggregate->getSrid());
            $aggregate->setSrid(4326);
            static::assertSame(4326, $member->getSrid());
            static::assertSame(0, $incompatible->getSrid());
        }
    }

    /** Rejection preserves membership and subsequent context propagation. */
    public function testSetRejectsAtomically(): void
    {
        $member = new XY\LineString([[1, 2]]);
        $aggregate = new XY\CompoundCurve([$member], 2154);
        $incompatible = new XYM\CircularString([]);
        $this->expectException(InvalidDimensionException::class);

        try {
            $aggregate->setCurve(0, $incompatible);
        } finally {
            static::assertSame([$member], $aggregate->getCurves());
            static::assertSame(2154, $aggregate->getSrid());
            $aggregate->setSrid(4326);
            static::assertSame(4326, $member->getSrid());
            static::assertSame(0, $incompatible->getSrid());
        }
    }
}
