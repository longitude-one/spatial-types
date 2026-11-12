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

namespace LongitudeOne\SpatialTypes\Tests\Unit\Types;

use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Exception\OutOfBoundsException;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\CircularStringImmutable;
use LongitudeOne\SpatialTypes\Value\Coordinates;
use PHPUnit\Framework\TestCase;

/**
 * Immutable edits preserve references and preserve circular-string representation.
 *
 * @internal
 *
 * @coversNothing
 */
class CircularStringImmutabilityTest extends TestCase
{
    /** Empty values have no point to replace. */
    public function testRejectEmptyPointReplacement(): void
    {
        $this->expectException(OutOfBoundsException::class);
        new CircularStringImmutable([])->withPoint(0, Coordinates::xy(1, 2));
    }

    /** Replacement coordinates cannot change the curve layout. */
    public function testRejectWrongReplacementDimension(): void
    {
        $curve = new CircularStringImmutable([[0, 0], [1, 1], [2, 0]]);
        $this->expectException(InvalidDimensionException::class);

        try {
            $curve->withPoint(1, Coordinates::xyz(1, 2, 3));
        } finally {
            self::assertSame([[0, 0], [1, 1], [2, 0]], $curve->toArray());
        }
    }

    /** Whole replacement supports empty and non-empty values. */
    public function testReplaceCoordinates(): void
    {
        $curve = new CircularStringImmutable([[0, 0], [1, 1], [2, 0]], SpatialReference::epsg(4326));
        $replacement = $curve->withArrayOfCoordinates([[3, 0], [4, 1], [5, 0]]);
        $empty = $curve->withArrayOfCoordinates([]);

        self::assertSame([[0, 0], [1, 1], [2, 0]], $curve->toArray());
        self::assertSame([[3, 0], [4, 1], [5, 0]], $replacement->toArray());
        self::assertSame([], $empty->toArray());
        self::assertSame($curve->getSpatialReference(), $replacement->getPoint(0)->getSpatialReference());
        self::assertSame($curve->getSpatialReference(), $empty->getSpatialReference());
    }

    /** Reference changes retain empty values. */
    public function testReplaceEmptyReference(): void
    {
        $curve = new CircularStringImmutable([], 4326);
        $replacement = $curve->withSrid(2154);

        self::assertSame([], $replacement->getPoints());
        self::assertSame(2154, $replacement->getSrid());
        self::assertSame(4326, $curve->getSrid());
    }

    /** Indexed replacement copies points and retains the full reference identity. */
    public function testReplacePoint(): void
    {
        $curve = new CircularStringImmutable([[0, 0], [1, 1], [2, 0]], SpatialReference::epsg(4326));
        $replacement = $curve->withPoint(-2, Coordinates::xy(1, 2));

        self::assertNotSame($curve, $replacement);
        self::assertSame([[0, 0], [1, 1], [2, 0]], $curve->toArray());
        self::assertSame([[0, 0], [1, 2], [2, 0]], $replacement->toArray());
        self::assertNotSame($curve->getPoint(0), $replacement->getPoint(0));
        self::assertNotSame($curve->getPoint(2), $replacement->getPoint(2));
        self::assertSame($curve->getSpatialReference(), $replacement->getSpatialReference());
        self::assertSame($curve->getSpatialReference(), $replacement->getPoint(0)->getSpatialReference());
    }

    /** A reference edit recursively changes declarations without transforming coordinates. */
    public function testReplaceSpatialReference(): void
    {
        $curve = new CircularStringImmutable([[0, 0], [1, 1], [2, 0]], SpatialReference::epsg(4326));
        $reference = SpatialReference::epsg(2154);
        $replacement = $curve->withSpatialReference($reference);

        self::assertSame([[0, 0], [1, 1], [2, 0]], $replacement->toArray());
        self::assertSame($reference, $replacement->getSpatialReference());
        self::assertSame($reference, $replacement->getPoint(0)->getSpatialReference());
        self::assertSame($reference, $replacement->getPoint(1)->getSpatialReference());
        self::assertSame($reference, $replacement->getPoint(2)->getSpatialReference());
        self::assertNotSame($curve->getPoint(0), $replacement->getPoint(0));
        self::assertSame(4326, $curve->getSrid());
        self::assertSame(4326, $curve->getPoint(0)->getSrid());
    }

    /** The legacy integer adapter also copies every defining point. */
    public function testReplaceSrid(): void
    {
        $curve = new CircularStringImmutable([[0, 0], [1, 1], [2, 0]], 4326);
        $replacement = $curve->withSrid(2154);

        self::assertSame(2154, $replacement->getSrid());
        self::assertSame(2154, $replacement->getPoint(0)->getSrid());
        self::assertSame(2154, $replacement->getPoint(1)->getSrid());
        self::assertSame(2154, $replacement->getPoint(2)->getSrid());
        self::assertSame([[0, 0], [1, 1], [2, 0]], $replacement->toArray());
        self::assertSame(4326, $curve->getSrid());
        self::assertSame(4326, $curve->getPoint(0)->getSrid());
    }
}
