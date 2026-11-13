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

use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\MissingValueException;
use LongitudeOne\SpatialTypes\Exception\OutOfBoundsException;
use LongitudeOne\SpatialTypes\Interfaces\ImmutableSpatialInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutableCircularStringInterface;
use LongitudeOne\SpatialTypes\Types\Dimension2\CircularString;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PointImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Point;
use PHPUnit\Framework\TestCase;

/** Mutable arc structure, shared endpoints and incomplete sequences.
 * @internal
 *
 * @coversNothing
 */
class MutableCircularStringTest extends TestCase
{
    /** Appending after an incomplete pair leaves the sequence unchanged. */
    public function testAppendAfterIncompletePairIsAtomic(): void
    {
        $origin = new Point(0, 0);
        $existing = new Point(1, 2);
        $passing = new Point(3, 4);
        $destination = new Point(5, 6);
        $curve = new CircularString([$origin, $existing]);
        $this->expectException(InvalidValueException::class);

        try {
            $curve->appendArc($passing, $destination);
        } finally {
            self::assertSame([$origin, $existing], $curve->getPoints());
            self::assertSame([[0, 0], [1, 2]], $curve->toArray());
            self::assertSame([3, 4], $passing->toArray());
            self::assertSame([5, 6], $destination->toArray());
        }
    }

    /** AppendArc rejects an immutable input before any mutation. */
    public function testAppendArcRejectsImmutableInputAtomically(): void
    {
        $origin = new Point(0, 0);
        $passing = new Point(1, 1);
        $destination = new Point(2, 0);
        $mutable = new Point(3, 4);
        $immutable = new PointImmutable(5, 6);
        $curve = new CircularString([$origin, $passing, $destination]);
        $this->expectException(InvalidValueException::class);

        try {
            new \ReflectionMethod($curve, 'appendArc')->invoke($curve, $mutable, $immutable);
        } finally {
            self::assertSame([$origin, $passing, $destination], $curve->getPoints());
            self::assertSame([[0, 0], [1, 1], [2, 0]], $curve->toArray());
            self::assertSame([3, 4], $mutable->toArray());
            self::assertSame([5, 6], $immutable->toArray());
        }
    }

    /** Appending without an origin fails without editing supplied points. */
    public function testAppendWithoutOriginIsAtomic(): void
    {
        $passing = new Point(1, 2, 4326);
        $destination = new Point(3, 4, 4326);
        $curve = new CircularString([], 2154);
        $this->expectException(InvalidValueException::class);

        try {
            $curve->appendArc($passing, $destination);
        } finally {
            self::assertSame([], $curve->getPoints());
            self::assertSame([1, 2], $passing->toArray());
            self::assertSame([3, 4], $destination->toArray());
            self::assertSame(4326, $passing->getSrid());
            self::assertSame(4326, $destination->getSrid());
        }
    }

    /** Arc edits preserve point identity and the addressed origin. */
    public function testArcEditsPreserveIdentityAndSharedEndpoints(): void
    {
        $origin = new Point(0, null);
        $passing = new Point(1, 2);
        $destination = new Point(3, 4);
        $nextPassing = new Point(5, 6);
        $nextDestination = new Point(7, 8);
        $curve = new CircularString([]);
        self::assertSame($curve, $curve->setOrigin($origin)->appendArc($passing, $destination)->appendArc($nextPassing, $nextDestination));
        self::assertSame([$origin, $passing, $destination, $nextPassing, $nextDestination], $curve->getPoints());
        $replacementPassing = new Point(9, null);
        $replacementDestination = new Point(null, 10);
        self::assertSame($curve, $curve->replaceArc(0, $replacementPassing, $replacementDestination));
        self::assertSame($origin, $curve->getPoint(0));
        self::assertSame($replacementDestination, $curve->getPoint(2));
        self::assertSame([$origin, $replacementPassing, $replacementDestination, $nextPassing, $nextDestination], $curve->getElements());
        self::assertSame($curve, $curve->replaceArc(1, $passing, $destination));
        self::assertSame($replacementDestination, $curve->getPoint(2));
        self::assertSame([$origin, $replacementPassing, $replacementDestination, $passing, $destination], $curve->getPoints());
        self::assertSame([3, 4], $destination->toArray());
        self::assertSame(GeometryTypeEnum::CIRCULARSTRING, $curve->getType());
    }

    /** Closure reads current shared point values without repairing endpoints. */
    public function testClosureObservesSharedPointChanges(): void
    {
        $origin = new Point(0, 0);
        $destination = new Point(0, 0);
        $curve = new CircularString([$origin, new Point(1, 1), $destination]);
        $destination->setY(2);
        self::assertSame([0, 0], $origin->toArray());
        self::assertSame([[0, 0], [1, 1], [0, 2]], $curve->toArray());
    }

    /** Constructor rejects immutable points without conversion. */
    public function testConstructorRejectsImmutablePoints(): void
    {
        $point = new PointImmutable(1, 2);
        $this->expectException(InvalidValueException::class);

        try {
            new \ReflectionClass(CircularString::class)->newInstance([$point]);
        } finally {
            self::assertSame([1, 2], $point->toArray());
        }
    }

    /** EmptyArc addressing fails without mutation. */
    public function testEmptyArcIsAtomic(): void
    {
        $origin = new Point(0, 0);
        $passing = new Point(1, 2);
        $destination = new Point(3, 4);
        $replacementPassing = new Point(5, 6);
        $replacementDestination = new Point(7, 8);
        $curve = new CircularString([]);
        $this->expectException(OutOfBoundsException::class);

        try {
            $curve->replaceArc(0, $replacementPassing, $replacementDestination);
        } finally {
            self::assertSame([], $curve->getPoints());
            self::assertSame([0, 0], $origin->toArray());
            self::assertSame([1, 2], $passing->toArray());
            self::assertSame([3, 4], $destination->toArray());
            self::assertSame([5, 6], $replacementPassing->toArray());
            self::assertSame([7, 8], $replacementDestination->toArray());
        }
    }

    /** General editing can construct and preserve incomplete sequences. */
    public function testGeneralEditingAllowsIncompleteSequences(): void
    {
        $origin = new Point(0, 0);
        $point = new Point(null, null);
        $replacement = new Point(3, 4);
        $curve = new CircularString([]);
        self::assertNull($curve->getStartPoint());
        self::assertNull($curve->getEndPoint());
        self::assertSame($curve, $curve->addPoint($origin)->insertPoint(1, $point)->setPoint(0, $replacement));
        self::assertSame([$replacement, $point], $curve->getPoints());
        self::assertSame([[3, 4], []], $curve->toArray());
        self::assertSame($curve, $curve->removePoint(0));
        self::assertSame([$point], $curve->getPoints());
        self::assertSame($curve, $curve->setArrayOfCoordinates([[0, null], [2, 3]]));
        self::assertSame([[0, null], [2, 3]], $curve->toArray());
        self::assertSame($curve, $curve->setArrayOfCoordinates([]));
    }

    /** IncompleteArc addressing fails without mutation. */
    public function testIncompleteArcIsAtomic(): void
    {
        $origin = new Point(0, 0);
        $passing = new Point(1, 2);
        $destination = new Point(3, 4);
        $replacementPassing = new Point(5, 6);
        $replacementDestination = new Point(7, 8);
        $curve = new CircularString([$origin, $passing]);
        $this->expectException(OutOfBoundsException::class);

        try {
            $curve->replaceArc(0, $replacementPassing, $replacementDestination);
        } finally {
            self::assertSame([$origin, $passing], $curve->getPoints());
            self::assertSame([0, 0], $origin->toArray());
            self::assertSame([1, 2], $passing->toArray());
            self::assertSame([3, 4], $destination->toArray());
            self::assertSame([5, 6], $replacementPassing->toArray());
            self::assertSame([7, 8], $replacementDestination->toArray());
        }
    }

    /** Replacing all coordinates rejects later malformed tuples atomically. */
    public function testMalformedTupleReplacementIsAtomic(): void
    {
        $point = new Point(0, null, 4326);
        $curve = new CircularString([$point], 4326);
        $this->expectException(MissingValueException::class);

        try {
            new \ReflectionMethod($curve, 'setArrayOfCoordinates')->invoke($curve, [[1, 2], [3]]);
        } finally {
            self::assertSame([$point], $curve->getPoints());
            self::assertSame([[0, null]], $curve->toArray());
            self::assertSame(4326, $point->getSrid());
        }
    }

    /** MissingArc addressing fails without mutation. */
    public function testMissingArcIsAtomic(): void
    {
        $origin = new Point(0, 0);
        $passing = new Point(1, 2);
        $destination = new Point(3, 4);
        $replacementPassing = new Point(5, 6);
        $replacementDestination = new Point(7, 8);
        $curve = new CircularString([$origin, $passing, $destination]);
        $this->expectException(OutOfBoundsException::class);

        try {
            $curve->replaceArc(1, $replacementPassing, $replacementDestination);
        } finally {
            self::assertSame([$origin, $passing, $destination], $curve->getPoints());
            self::assertSame([0, 0], $origin->toArray());
            self::assertSame([1, 2], $passing->toArray());
            self::assertSame([3, 4], $destination->toArray());
            self::assertSame([5, 6], $replacementPassing->toArray());
            self::assertSame([7, 8], $replacementDestination->toArray());
        }
    }

    /** Mutable circular contracts exclude immutable operations. */
    public function testMutableCircularContractExcludesCopyOperations(): void
    {
        $curve = new CircularString([]);
        self::assertContains(MutableCircularStringInterface::class, class_implements($curve));
        self::assertNotContains(ImmutableSpatialInterface::class, class_implements($curve));
        self::assertFalse(new \ReflectionClass($curve)->hasMethod('withPoint'));
        self::assertFalse(new \ReflectionClass($curve)->hasMethod('withArrayOfCoordinates'));
        self::assertFalse(new \ReflectionClass($curve)->hasMethod('withSrid'));
        self::assertFalse(new \ReflectionClass($curve)->hasMethod('withSpatialReference'));
        self::assertFalse(new \ReflectionClass($curve)->hasMethod('setDimension'));
    }

    /** NegativeArc addressing fails without mutation. */
    public function testNegativeArcIsAtomic(): void
    {
        $origin = new Point(0, 0);
        $passing = new Point(1, 2);
        $destination = new Point(3, 4);
        $replacementPassing = new Point(5, 6);
        $replacementDestination = new Point(7, 8);
        $curve = new CircularString([$origin, $passing, $destination]);
        $this->expectException(OutOfBoundsException::class);

        try {
            $curve->replaceArc(-1, $replacementPassing, $replacementDestination);
        } finally {
            self::assertSame([$origin, $passing, $destination], $curve->getPoints());
            self::assertSame([0, 0], $origin->toArray());
            self::assertSame([1, 2], $passing->toArray());
            self::assertSame([3, 4], $destination->toArray());
            self::assertSame([5, 6], $replacementPassing->toArray());
            self::assertSame([7, 8], $replacementDestination->toArray());
        }
    }

    /** Negative access is rejected without wrapping. */
    public function testNegativePointAccessDoesNotWrap(): void
    {
        $point = new Point(1, 2);
        $curve = new CircularString([$point]);
        $this->expectException(OutOfBoundsException::class);

        try {
            $curve->getPoint(-1);
        } finally {
            self::assertSame([$point], $curve->getPoints());
        }
    }

    /** Origin rejects an immutable input before any mutation. */
    public function testOriginRejectsImmutableInputAtomically(): void
    {
        $origin = new Point(0, 0);
        $passing = new Point(1, 1);
        $destination = new Point(2, 0);
        $mutable = new Point(3, 4);
        $immutable = new PointImmutable(5, 6);
        $curve = new CircularString([$origin, $passing, $destination]);
        $this->expectException(InvalidValueException::class);

        try {
            new \ReflectionMethod($curve, 'setOrigin')->invoke($curve, $immutable);
        } finally {
            self::assertSame([$origin, $passing, $destination], $curve->getPoints());
            self::assertSame([[0, 0], [1, 1], [2, 0]], $curve->toArray());
            self::assertSame([3, 4], $mutable->toArray());
            self::assertSame([5, 6], $immutable->toArray());
        }
    }

    /** Setting an origin changes only the first point. */
    public function testOriginReplacementChangesOnlyFirstPoint(): void
    {
        $origin = new Point(0, 0);
        $passing = new Point(1, 1);
        $destination = new Point(2, 0);
        $replacement = new Point(null, 3);
        $curve = new CircularString([$origin, $passing, $destination]);
        self::assertSame($curve, $curve->setOrigin($replacement));
        self::assertSame([$replacement, $passing, $destination], $curve->getPoints());
        self::assertSame([0, 0], $origin->toArray());
        self::assertSame($replacement, $curve->getStartPoint());
        self::assertSame($destination, $curve->getEndPoint());
    }

    /** OverflowArc addressing fails without mutation. */
    public function testOverflowArcIsAtomic(): void
    {
        $origin = new Point(0, 0);
        $passing = new Point(1, 2);
        $destination = new Point(3, 4);
        $replacementPassing = new Point(5, 6);
        $replacementDestination = new Point(7, 8);
        $curve = new CircularString([$origin, $passing, $destination]);
        $this->expectException(OutOfBoundsException::class);

        try {
            $curve->replaceArc(9223372036854775807, $replacementPassing, $replacementDestination);
        } finally {
            self::assertSame([$origin, $passing, $destination], $curve->getPoints());
            self::assertSame([0, 0], $origin->toArray());
            self::assertSame([1, 2], $passing->toArray());
            self::assertSame([3, 4], $destination->toArray());
            self::assertSame([5, 6], $replacementPassing->toArray());
            self::assertSame([7, 8], $replacementDestination->toArray());
        }
    }

    /** ReplaceArc rejects an immutable input before any mutation. */
    public function testReplaceArcRejectsImmutableInputAtomically(): void
    {
        $origin = new Point(0, 0);
        $passing = new Point(1, 1);
        $destination = new Point(2, 0);
        $mutable = new Point(3, 4);
        $immutable = new PointImmutable(5, 6);
        $curve = new CircularString([$origin, $passing, $destination]);
        $this->expectException(InvalidValueException::class);

        try {
            new \ReflectionMethod($curve, 'replaceArc')->invoke($curve, 0, $mutable, $immutable);
        } finally {
            self::assertSame([$origin, $passing, $destination], $curve->getPoints());
            self::assertSame([[0, 0], [1, 1], [2, 0]], $curve->toArray());
            self::assertSame([3, 4], $mutable->toArray());
            self::assertSame([5, 6], $immutable->toArray());
        }
    }
}
