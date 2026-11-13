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

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Interfaces\MutableCollectionInterface;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Types\Dimension2\Point;
use LongitudeOne\SpatialTypes\Types\Dimension2\SpatialCollection;
use PHPUnit\Framework\TestCase;

/** Mutable collection graphs reject cycles while retaining shared descendants.
 * @internal
 *
 * @coversNothing
 */
class MutableSpatialCollectionCycleTest extends TestCase
{
    /** Complete replacement rejects a cyclic candidate after a valid earlier member. */
    public function testAlreadyCyclicMemberReplacement(): void
    {
        $point = new Point(1, null, 4326);
        $collection = new SpatialCollection([$point], 2154);
        $cyclic = self::createStub(MutableCollectionInterface::class);
        $cyclic->method('getElements')->willReturn([$cyclic]);
        $this->expectException(InvalidValueException::class);

        try {
            $collection->setElements([new Point(7, 8), $cyclic]);
        } finally {
            self::assertSame([$point], $collection->getElements());
            self::assertSame(2154, $collection->getSrid());
            self::assertSame(4326, $point->getSrid());
        }
    }

    /** Pre-existing cyclic input from a custom component is rejected on construction. */
    public function testConstructorRejectsAlreadyCyclicMember(): void
    {
        $cyclic = self::createStub(MutableCollectionInterface::class);
        $cyclic->method('getElements')->willReturn([$cyclic]);
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessageIsOrContains('Mutable aggregate members must not form reference cycles.');
        new SpatialCollection([$cyclic]);
    }

    /** Self-membership is rejected before any mutation. */
    public function testDirectAppend(): void
    {
        $point = new Point(1, null, 4326);
        $collection = new SpatialCollection([$point], 2154);
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessageIsOrContains('Mutable aggregate members must not form reference cycles.');

        try {
            $collection->addElement($collection);
        } finally {
            self::assertSame([$point], $collection->getElements());
            self::assertSame(2154, $collection->getSrid());
            self::assertSame(4326, $point->getSrid());
            self::assertSame([1, null], $point->toArray());
        }
    }

    /** Self-membership is rejected before any mutation. */
    public function testDirectInsert(): void
    {
        $point = new Point(1, null, 4326);
        $collection = new SpatialCollection([$point], 2154);
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessageIsOrContains('Mutable aggregate members must not form reference cycles.');

        try {
            $collection->insertElement(1, $collection);
        } finally {
            self::assertSame([$point], $collection->getElements());
            self::assertSame(2154, $collection->getSrid());
            self::assertSame(4326, $point->getSrid());
            self::assertSame([1, null], $point->toArray());
        }
    }

    /** Self-membership is rejected before any mutation. */
    public function testDirectReplace(): void
    {
        $point = new Point(1, null, 4326);
        $collection = new SpatialCollection([$point], 2154);
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessageIsOrContains('Mutable aggregate members must not form reference cycles.');

        try {
            $collection->setElement(0, $collection);
        } finally {
            self::assertSame([$point], $collection->getElements());
            self::assertSame(2154, $collection->getSrid());
            self::assertSame(4326, $point->getSrid());
            self::assertSame([1, null], $point->toArray());
        }
    }

    /** Self-membership is rejected before any mutation. */
    public function testDirectReplaceAll(): void
    {
        $point = new Point(1, null, 4326);
        $collection = new SpatialCollection([$point], 2154);
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessageIsOrContains('Mutable aggregate members must not form reference cycles.');

        try {
            $collection->setElements([$point, $collection]);
        } finally {
            self::assertSame([$point], $collection->getElements());
            self::assertSame(2154, $collection->getSrid());
            self::assertSame(4326, $point->getSrid());
            self::assertSame([1, null], $point->toArray());
        }
    }

    /** Cycle rejection is independent of the fixed coordinate layout. */
    public function testDirectXym(): void
    {
        $collection = new \LongitudeOne\SpatialTypes\Types\Dimension3m\SpatialCollection([], 4326);
        $this->expectException(InvalidValueException::class);

        try {
            $collection->addElement($collection);
        } finally {
            self::assertSame([], $collection->getElements());
            self::assertSame(4326, $collection->getSrid());
            self::assertSame(CoordinateDimensionEnum::XYM, $collection->getDimension());
        }
    }

    /** Cycle rejection is independent of the fixed coordinate layout. */
    public function testDirectXyz(): void
    {
        $collection = new \LongitudeOne\SpatialTypes\Types\Dimension3z\SpatialCollection([], 4326);
        $this->expectException(InvalidValueException::class);

        try {
            $collection->addElement($collection);
        } finally {
            self::assertSame([], $collection->getElements());
            self::assertSame(4326, $collection->getSrid());
            self::assertSame(CoordinateDimensionEnum::XYZ, $collection->getDimension());
        }
    }

    /** Cycle rejection is independent of the fixed coordinate layout. */
    public function testDirectXyzm(): void
    {
        $collection = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\SpatialCollection([], 4326);
        $this->expectException(InvalidValueException::class);

        try {
            $collection->addElement($collection);
        } finally {
            self::assertSame([], $collection->getElements());
            self::assertSame(4326, $collection->getSrid());
            self::assertSame(CoordinateDimensionEnum::XYZM, $collection->getDimension());
        }
    }

    /** A member referencing the receiver cannot close a membership cycle. */
    public function testIndirectAppend(): void
    {
        $point = new Point(1, null, 4326);
        $collection = new SpatialCollection([$point], 2154);
        $bridge = new SpatialCollection([$collection], 3857);
        $nested = new SpatialCollection([$bridge], 4326);
        $owner = new SpatialCollection([$point]);
        $this->expectException(InvalidValueException::class);

        try {
            $collection->addElement($nested);
        } finally {
            self::assertSame([$point], $collection->getElements());
            self::assertSame([$point], $owner->getElements());
            self::assertSame($bridge, $nested->getElement(0));
            self::assertSame([$collection], $bridge->getElements());
            self::assertSame(2154, $collection->getSrid());
            self::assertSame(4326, $point->getSrid());
            self::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        }
    }

    /** A member referencing the receiver cannot close a membership cycle. */
    public function testIndirectInsert(): void
    {
        $point = new Point(1, null, 4326);
        $collection = new SpatialCollection([$point], 2154);
        $bridge = new SpatialCollection([$collection], 3857);
        $nested = new SpatialCollection([$bridge], 4326);
        $owner = new SpatialCollection([$point]);
        $this->expectException(InvalidValueException::class);

        try {
            $collection->insertElement(1, $nested);
        } finally {
            self::assertSame([$point], $collection->getElements());
            self::assertSame([$point], $owner->getElements());
            self::assertSame($bridge, $nested->getElement(0));
            self::assertSame([$collection], $bridge->getElements());
            self::assertSame(2154, $collection->getSrid());
            self::assertSame(4326, $point->getSrid());
            self::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        }
    }

    /** A member referencing the receiver cannot close a membership cycle. */
    public function testIndirectReplace(): void
    {
        $point = new Point(1, null, 4326);
        $collection = new SpatialCollection([$point], 2154);
        $bridge = new SpatialCollection([$collection], 3857);
        $nested = new SpatialCollection([$bridge], 4326);
        $owner = new SpatialCollection([$point]);
        $this->expectException(InvalidValueException::class);

        try {
            $collection->setElement(0, $nested);
        } finally {
            self::assertSame([$point], $collection->getElements());
            self::assertSame([$point], $owner->getElements());
            self::assertSame($bridge, $nested->getElement(0));
            self::assertSame([$collection], $bridge->getElements());
            self::assertSame(2154, $collection->getSrid());
            self::assertSame(4326, $point->getSrid());
            self::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        }
    }

    /** A member referencing the receiver cannot close a membership cycle. */
    public function testIndirectReplaceAll(): void
    {
        $point = new Point(1, null, 4326);
        $collection = new SpatialCollection([$point], 2154);
        $bridge = new SpatialCollection([$collection], 3857);
        $nested = new SpatialCollection([$bridge], 4326);
        $owner = new SpatialCollection([$point]);
        $this->expectException(InvalidValueException::class);

        try {
            $collection->setElements([$point, $nested]);
        } finally {
            self::assertSame([$point], $collection->getElements());
            self::assertSame([$point], $owner->getElements());
            self::assertSame($bridge, $nested->getElement(0));
            self::assertSame([$collection], $bridge->getElements());
            self::assertSame(2154, $collection->getSrid());
            self::assertSame(4326, $point->getSrid());
            self::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        }
    }

    /** Repeated members and shared descendant diamonds are valid membership graphs. */
    public function testSharedAcyclicMembersRemainSupported(): void
    {
        $point = new Point(1, null, 4326);
        $shared = new SpatialCollection([$point], 4326);
        $left = new SpatialCollection([$shared], 4326);
        $right = new SpatialCollection([$shared], 4326);
        $collection = new SpatialCollection([$left, $right, $shared, $shared], 4326);
        self::assertSame($collection, $collection->addElement($shared)->insertElement(0, $shared)->setElement(1, $right));
        self::assertSame($collection, $collection->setElements([$left, $right, $shared, $shared]));
        self::assertSame([$left, $right, $shared, $shared], $collection->getElements());
        self::assertSame($shared, $left->getElement(0));
        self::assertSame($shared, $right->getElement(0));
        self::assertSame($collection, $collection->setSrid(2154)->setFamily(SpatialModelEnum::GEOGRAPHY)->setSpatialReference(SpatialReference::epsg(2154)));
        self::assertSame(2154, $point->getSrid());
        self::assertSame('EPSG', $point->getSpatialReference()->authority);
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        self::assertSame([[[[1, null]]], [[[1, null]]], [[1, null]], [[1, null]]], $collection->toArray());
        self::assertSame($point, $shared->getElement(0));
    }
}
