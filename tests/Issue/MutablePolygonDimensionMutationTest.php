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
class MutablePolygonDimensionMutationTest extends TestCase
{
    /** Rejection preserves membership and subsequent context propagation. */
    public function testAddRejectsAtomically(): void
    {
        $member = new XYZ\LineString([[1, 2, 3]]);
        $aggregate = new XYZ\Polygon([$member], 2154);
        $incompatible = new XYM\LineString([]);
        $this->expectException(InvalidDimensionException::class);

        try {
            $aggregate->addRing($incompatible);
        } finally {
            self::assertSame([$member], $aggregate->getRings());
            self::assertSame(2154, $aggregate->getSrid());
            $aggregate->setSrid(4326);
            self::assertSame(4326, $member->getSrid());
            self::assertSame(0, $incompatible->getSrid());
        }
    }

    /** Rejection preserves membership and subsequent context propagation. */
    public function testInsertRejectsAtomically(): void
    {
        $member = new XYZ\LineString([[1, 2, 3]]);
        $aggregate = new XYZ\Polygon([$member], 2154);
        $incompatible = new XYM\LineString([]);
        $this->expectException(InvalidDimensionException::class);

        try {
            $aggregate->insertRing(1, $incompatible);
        } finally {
            self::assertSame([$member], $aggregate->getRings());
            self::assertSame(2154, $aggregate->getSrid());
            $aggregate->setSrid(4326);
            self::assertSame(4326, $member->getSrid());
            self::assertSame(0, $incompatible->getSrid());
        }
    }

    /** Rejection preserves membership and subsequent context propagation. */
    public function testSetRejectsAtomically(): void
    {
        $member = new XYZ\LineString([[1, 2, 3]]);
        $aggregate = new XYZ\Polygon([$member], 2154);
        $incompatible = new XYM\LineString([]);
        $this->expectException(InvalidDimensionException::class);

        try {
            $aggregate->setRing(0, $incompatible);
        } finally {
            self::assertSame([$member], $aggregate->getRings());
            self::assertSame(2154, $aggregate->getSrid());
            $aggregate->setSrid(4326);
            self::assertSame(4326, $member->getSrid());
            self::assertSame(0, $incompatible->getSrid());
        }
    }
}
