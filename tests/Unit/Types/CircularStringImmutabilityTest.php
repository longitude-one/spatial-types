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

namespace LongitudeOne\SpatialTypes\Tests\Unit\Types;

use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Exception\InvalidSridException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\OutOfBoundsException;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\CircularString;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\Point;
use LongitudeOne\SpatialTypes\Value\Coordinates;
use PHPUnit\Framework\TestCase;

/**
 * Immutable edits preserve references and revalidate circular-string invariants.
 *
 * @internal
 *
 * @coversNothing
 */
class CircularStringImmutabilityTest extends TestCase
{
    /** Replacement revalidates intermediate-point distinctness. */
    public function testRejectCoincidentReplacement(): void
    {
        $curve = new CircularString([[0, 0], [1, 1], [2, 0]]);
        $this->expectException(InvalidValueException::class);

        try {
            $curve->withPoint(1, Coordinates::xy(0, 0));
        } finally {
            static::assertSame([[0, 0], [1, 1], [2, 0]], $curve->toArray());
        }
    }

    /** The complete reference identity participates in membership validation. */
    public function testRejectDifferentAuthority(): void
    {
        $this->expectException(InvalidSridException::class);
        new CircularString([
            new Point(0, 0, new SpatialReference(4326, 'LOCAL')),
            new Point(1, 1, SpatialReference::epsg(4326)),
            new Point(2, 0, SpatialReference::epsg(4326)),
        ], SpatialReference::epsg(4326));
    }

    /** Empty values have no point to replace. */
    public function testRejectEmptyPointReplacement(): void
    {
        $this->expectException(OutOfBoundsException::class);
        (new CircularString([]))->withPoint(0, Coordinates::xy(1, 2));
    }

    /** Whole replacement revalidates the number of defining points. */
    public function testRejectIncompleteReplacementArc(): void
    {
        $curve = new CircularString([[0, 0], [1, 1], [2, 0]]);
        $this->expectException(InvalidValueException::class);

        try {
            $curve->withArrayOfCoordinates([[3, 0], [4, 1]]);
        } finally {
            static::assertSame([[0, 0], [1, 1], [2, 0]], $curve->toArray());
        }
    }

    /** Replacement coordinates cannot change the curve layout. */
    public function testRejectWrongReplacementDimension(): void
    {
        $curve = new CircularString([[0, 0], [1, 1], [2, 0]]);
        $this->expectException(InvalidDimensionException::class);

        try {
            $curve->withPoint(1, Coordinates::xyz(1, 2, 3));
        } finally {
            static::assertSame([[0, 0], [1, 1], [2, 0]], $curve->toArray());
        }
    }

    /** Whole replacement supports empty and non-empty values. */
    public function testReplaceCoordinates(): void
    {
        $curve = new CircularString([[0, 0], [1, 1], [2, 0]], SpatialReference::epsg(4326));
        $replacement = $curve->withArrayOfCoordinates([[3, 0], [4, 1], [5, 0]]);
        $empty = $curve->withArrayOfCoordinates([]);

        static::assertSame([[0, 0], [1, 1], [2, 0]], $curve->toArray());
        static::assertSame([[3, 0], [4, 1], [5, 0]], $replacement->toArray());
        static::assertSame([], $empty->toArray());
        static::assertTrue($empty->isEmpty());
        static::assertSame($curve->getSpatialReference(), $replacement->getPoint(0)->getSpatialReference());
        static::assertSame($curve->getSpatialReference(), $empty->getSpatialReference());
    }

    /** Reference changes retain empty values. */
    public function testReplaceEmptyReference(): void
    {
        $curve = new CircularString([], 4326);
        $replacement = $curve->withSrid(2154);

        static::assertSame([], $replacement->getPoints());
        static::assertSame(2154, $replacement->getSrid());
        static::assertSame(4326, $curve->getSrid());
    }

    /** Indexed replacement copies points and retains the full reference identity. */
    public function testReplacePoint(): void
    {
        $curve = new CircularString([[0, 0], [1, 1], [2, 0]], SpatialReference::epsg(4326));
        $replacement = $curve->withPoint(-2, Coordinates::xy(1, 2));

        static::assertNotSame($curve, $replacement);
        static::assertSame([[0, 0], [1, 1], [2, 0]], $curve->toArray());
        static::assertSame([[0, 0], [1, 2], [2, 0]], $replacement->toArray());
        static::assertNotSame($curve->getPoint(0), $replacement->getPoint(0));
        static::assertNotSame($curve->getPoint(2), $replacement->getPoint(2));
        static::assertSame($curve->getSpatialReference(), $replacement->getSpatialReference());
        static::assertSame($curve->getSpatialReference(), $replacement->getPoint(0)->getSpatialReference());
    }

    /** A reference edit recursively changes declarations without transforming coordinates. */
    public function testReplaceSpatialReference(): void
    {
        $curve = new CircularString([[0, 0], [1, 1], [2, 0]], SpatialReference::epsg(4326));
        $reference = SpatialReference::epsg(2154);
        $replacement = $curve->withSpatialReference($reference);

        static::assertSame([[0, 0], [1, 1], [2, 0]], $replacement->toArray());
        static::assertSame($reference, $replacement->getSpatialReference());
        static::assertSame($reference, $replacement->getPoint(0)->getSpatialReference());
        static::assertSame($reference, $replacement->getPoint(1)->getSpatialReference());
        static::assertSame($reference, $replacement->getPoint(2)->getSpatialReference());
        static::assertNotSame($curve->getPoint(0), $replacement->getPoint(0));
        static::assertSame(4326, $curve->getSrid());
        static::assertSame(4326, $curve->getPoint(0)->getSrid());
    }

    /** The legacy integer adapter also copies every defining point. */
    public function testReplaceSrid(): void
    {
        $curve = new CircularString([[0, 0], [1, 1], [2, 0]], 4326);
        $replacement = $curve->withSrid(2154);

        static::assertSame(2154, $replacement->getSrid());
        static::assertSame(2154, $replacement->getPoint(0)->getSrid());
        static::assertSame(2154, $replacement->getPoint(1)->getSrid());
        static::assertSame(2154, $replacement->getPoint(2)->getSrid());
        static::assertSame([[0, 0], [1, 1], [2, 0]], $replacement->toArray());
        static::assertSame(4326, $curve->getSrid());
        static::assertSame(4326, $curve->getPoint(0)->getSrid());
    }
}
