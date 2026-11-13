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
use LongitudeOne\SpatialTypes\Types\Dimension3z as XYZ;
use LongitudeOne\SpatialTypes\Types\Dimension4zm as XYZM;
use PHPUnit\Framework\TestCase;

/** Arc-editing regression coverage for #78.
 * @internal
 *
 * @coversNothing
 */
class MutableArcDimensionMutationTest extends TestCase
{
    /** Every arc argument is checked before any point is written. */
    public function testAppendDestinationRejectsAtomically(): void
    {
        $origin = new XY\Point(0, 0);
        $passing = new XY\Point(1, 1);
        $destination = new XY\Point(2, 0);
        $arc = new XY\CircularString([$origin, $passing, $destination], 2154);
        $incompatible = new XYZ\Point(3, 4, 5);
        $compatible = new XY\Point(3, 3);
        $this->expectException(InvalidDimensionException::class);

        try {
            $arc->appendArc($compatible, $incompatible);
        } finally {
            self::assertSame([$origin, $passing, $destination], $arc->getPoints());
            self::assertSame([[0, 0], [1, 1], [2, 0]], $arc->toArray());
            self::assertSame([3, 4, 5], $incompatible->toArray());
            self::assertSame([3, 3], $compatible->toArray());
            self::assertSame(2154, $arc->getSrid());
            $arc->setSrid(4326);
            self::assertSame(4326, $origin->getSrid());
            self::assertSame(4326, $passing->getSrid());
            self::assertSame(4326, $destination->getSrid());
            self::assertSame(0, $incompatible->getSrid());
            self::assertSame(0, $compatible->getSrid());
        }
    }

    /** Every arc argument is checked before any point is written. */
    public function testAppendPassingRejectsAtomically(): void
    {
        $origin = new XY\Point(0, 0);
        $passing = new XY\Point(1, 1);
        $destination = new XY\Point(2, 0);
        $arc = new XY\CircularString([$origin, $passing, $destination], 2154);
        $incompatible = new XYZ\Point(3, 4, 5);
        $compatible = new XY\Point(3, 3);
        $this->expectException(InvalidDimensionException::class);

        try {
            $arc->appendArc($incompatible, $compatible);
        } finally {
            self::assertSame([$origin, $passing, $destination], $arc->getPoints());
            self::assertSame([[0, 0], [1, 1], [2, 0]], $arc->toArray());
            self::assertSame([3, 4, 5], $incompatible->toArray());
            self::assertSame([3, 3], $compatible->toArray());
            self::assertSame(2154, $arc->getSrid());
            $arc->setSrid(4326);
            self::assertSame(4326, $origin->getSrid());
            self::assertSame(4326, $passing->getSrid());
            self::assertSame(4326, $destination->getSrid());
            self::assertSame(0, $incompatible->getSrid());
            self::assertSame(0, $compatible->getSrid());
        }
    }

    /** EMPTY circular strings also reject an origin in another layout. */
    public function testEmptyOriginRejectsAnotherLayout(): void
    {
        $arc = new XYZM\CircularString([], 2154);
        $point = new XYZ\Point(3, 4, 5);
        $this->expectException(InvalidDimensionException::class);

        try {
            $arc->setOrigin($point);
        } finally {
            self::assertSame([], $arc->getPoints());
            self::assertSame([], $arc->toArray());
            $arc->setSrid(4326);
            self::assertSame(0, $point->getSrid());
            self::assertSame([3, 4, 5], $point->toArray());
        }
    }

    /** XYZ and XYM remain different layouts during append. */
    public function testMeasuredAppendRejectsXyzDestination(): void
    {
        $arc = new XYM\CircularString([[0, 0, 1]], 2154);
        $this->expectException(InvalidDimensionException::class);

        try {
            $arc->appendArc(new XYM\Point(1, 1, 2), new XYZ\Point(2, 0, 3));
        } finally {
            self::assertSame([[0, 0, 1]], $arc->toArray());
        }
    }

    /** Every arc argument is checked before any point is written. */
    public function testReplaceDestinationRejectsAtomically(): void
    {
        $origin = new XY\Point(0, 0);
        $passing = new XY\Point(1, 1);
        $destination = new XY\Point(2, 0);
        $arc = new XY\CircularString([$origin, $passing, $destination], 2154);
        $incompatible = new XYZ\Point(3, 4, 5);
        $compatible = new XY\Point(3, 3);
        $this->expectException(InvalidDimensionException::class);

        try {
            $arc->replaceArc(0, $compatible, $incompatible);
        } finally {
            self::assertSame([$origin, $passing, $destination], $arc->getPoints());
            self::assertSame([[0, 0], [1, 1], [2, 0]], $arc->toArray());
            self::assertSame([3, 4, 5], $incompatible->toArray());
            self::assertSame([3, 3], $compatible->toArray());
            self::assertSame(2154, $arc->getSrid());
            $arc->setSrid(4326);
            self::assertSame(4326, $origin->getSrid());
            self::assertSame(4326, $passing->getSrid());
            self::assertSame(4326, $destination->getSrid());
            self::assertSame(0, $incompatible->getSrid());
            self::assertSame(0, $compatible->getSrid());
        }
    }

    /** Every arc argument is checked before any point is written. */
    public function testReplacePassingRejectsAtomically(): void
    {
        $origin = new XY\Point(0, 0);
        $passing = new XY\Point(1, 1);
        $destination = new XY\Point(2, 0);
        $arc = new XY\CircularString([$origin, $passing, $destination], 2154);
        $incompatible = new XYZ\Point(3, 4, 5);
        $compatible = new XY\Point(3, 3);
        $this->expectException(InvalidDimensionException::class);

        try {
            $arc->replaceArc(0, $incompatible, $compatible);
        } finally {
            self::assertSame([$origin, $passing, $destination], $arc->getPoints());
            self::assertSame([[0, 0], [1, 1], [2, 0]], $arc->toArray());
            self::assertSame([3, 4, 5], $incompatible->toArray());
            self::assertSame([3, 3], $compatible->toArray());
            self::assertSame(2154, $arc->getSrid());
            $arc->setSrid(4326);
            self::assertSame(4326, $origin->getSrid());
            self::assertSame(4326, $passing->getSrid());
            self::assertSame(4326, $destination->getSrid());
            self::assertSame(0, $incompatible->getSrid());
            self::assertSame(0, $compatible->getSrid());
        }
    }

    /** Every arc argument is checked before any point is written. */
    public function testSetOriginRejectsAtomically(): void
    {
        $origin = new XY\Point(0, 0);
        $passing = new XY\Point(1, 1);
        $destination = new XY\Point(2, 0);
        $arc = new XY\CircularString([$origin, $passing, $destination], 2154);
        $incompatible = new XYZ\Point(3, 4, 5);
        $compatible = new XY\Point(3, 3);
        $this->expectException(InvalidDimensionException::class);

        try {
            $arc->setOrigin($incompatible);
        } finally {
            self::assertSame([$origin, $passing, $destination], $arc->getPoints());
            self::assertSame([[0, 0], [1, 1], [2, 0]], $arc->toArray());
            self::assertSame([3, 4, 5], $incompatible->toArray());
            self::assertSame([3, 3], $compatible->toArray());
            self::assertSame(2154, $arc->getSrid());
            $arc->setSrid(4326);
            self::assertSame(4326, $origin->getSrid());
            self::assertSame(4326, $passing->getSrid());
            self::assertSame(4326, $destination->getSrid());
            self::assertSame(0, $incompatible->getSrid());
            self::assertSame(0, $compatible->getSrid());
        }
    }

    /** A spatial replacement cannot silently reinterpret a measured point. */
    public function testXyzReplacementRejectsMeasuredPassingPoint(): void
    {
        $arc = new XYZ\CircularString([[0, 0, 1], [1, 1, 2], [2, 0, 3]], 2154);
        $this->expectException(InvalidDimensionException::class);

        try {
            $arc->replaceArc(0, new XYM\Point(3, 3, 4), new XYZ\Point(4, 0, 5));
        } finally {
            self::assertSame([[0, 0, 1], [1, 1, 2], [2, 0, 3]], $arc->toArray());
        }
    }
}
