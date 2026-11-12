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
use LongitudeOne\SpatialTypes\Types\Dimension3m as XYM;
use LongitudeOne\SpatialTypes\Types\Dimension3z as XYZ;
use PHPUnit\Framework\TestCase;

/** Regression coverage for #78.
 * @internal
 *
 * @coversNothing
 */
class MutableCircularStringDimensionMutationTest extends TestCase
{
    /** Rejection preserves membership and subsequent context propagation. */
    public function testAddRejectsAtomically(): void
    {
        $member = new XYM\Point(1, 2, 4);
        $aggregate = new XYM\CircularString([$member], 2154);
        $incompatible = new XYZ\Point(7, 8, 9);
        $this->expectException(InvalidDimensionException::class);

        try {
            $aggregate->addPoint($incompatible);
        } finally {
            static::assertSame([$member], $aggregate->getPoints());
            static::assertSame(2154, $aggregate->getSrid());
            $aggregate->setSrid(4326);
            static::assertSame(4326, $member->getSrid());
            static::assertSame(0, $incompatible->getSrid());
        }
    }

    /** Rejection preserves membership and subsequent context propagation. */
    public function testInsertRejectsAtomically(): void
    {
        $member = new XYM\Point(1, 2, 4);
        $aggregate = new XYM\CircularString([$member], 2154);
        $incompatible = new XYZ\Point(7, 8, 9);
        $this->expectException(InvalidDimensionException::class);

        try {
            $aggregate->insertPoint(1, $incompatible);
        } finally {
            static::assertSame([$member], $aggregate->getPoints());
            static::assertSame(2154, $aggregate->getSrid());
            $aggregate->setSrid(4326);
            static::assertSame(4326, $member->getSrid());
            static::assertSame(0, $incompatible->getSrid());
        }
    }

    /** Rejection preserves membership and subsequent context propagation. */
    public function testSetRejectsAtomically(): void
    {
        $member = new XYM\Point(1, 2, 4);
        $aggregate = new XYM\CircularString([$member], 2154);
        $incompatible = new XYZ\Point(7, 8, 9);
        $this->expectException(InvalidDimensionException::class);

        try {
            $aggregate->setPoint(0, $incompatible);
        } finally {
            static::assertSame([$member], $aggregate->getPoints());
            static::assertSame(2154, $aggregate->getSrid());
            $aggregate->setSrid(4326);
            static::assertSame(4326, $member->getSrid());
            static::assertSame(0, $incompatible->getSrid());
        }
    }
}
