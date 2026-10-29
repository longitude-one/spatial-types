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
        $cyclic = static::createStub(MutableCollectionInterface::class);
        $cyclic->method('getElements')->willReturn([$cyclic]);
        $this->expectException(InvalidValueException::class);

        try {
            $collection->setElements([new Point(7, 8), $cyclic]);
        } finally {
            static::assertSame([$point], $collection->getElements());
            static::assertSame(2154, $collection->getSrid());
            static::assertSame(4326, $point->getSrid());
        }
    }

    /** Pre-existing cyclic input from a custom component is rejected on construction. */
    public function testConstructorRejectsAlreadyCyclicMember(): void
    {
        $cyclic = static::createStub(MutableCollectionInterface::class);
        $cyclic->method('getElements')->willReturn([$cyclic]);
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('Mutable aggregate members must not form reference cycles.');
        new SpatialCollection([$cyclic]);
    }

    /** Self-membership is rejected before any mutation. */
    public function testDirectAppend(): void
    {
        $point = new Point(1, null, 4326);
        $collection = new SpatialCollection([$point], 2154);
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('Mutable aggregate members must not form reference cycles.');

        try {
            $collection->addElement($collection);
        } finally {
            static::assertSame([$point], $collection->getElements());
            static::assertSame(2154, $collection->getSrid());
            static::assertSame(4326, $point->getSrid());
            static::assertSame([1, null], $point->toArray());
        }
    }

    /** Self-membership is rejected before any mutation. */
    public function testDirectInsert(): void
    {
        $point = new Point(1, null, 4326);
        $collection = new SpatialCollection([$point], 2154);
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('Mutable aggregate members must not form reference cycles.');

        try {
            $collection->insertElement(1, $collection);
        } finally {
            static::assertSame([$point], $collection->getElements());
            static::assertSame(2154, $collection->getSrid());
            static::assertSame(4326, $point->getSrid());
            static::assertSame([1, null], $point->toArray());
        }
    }

    /** Self-membership is rejected before any mutation. */
    public function testDirectReplace(): void
    {
        $point = new Point(1, null, 4326);
        $collection = new SpatialCollection([$point], 2154);
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('Mutable aggregate members must not form reference cycles.');

        try {
            $collection->setElement(0, $collection);
        } finally {
            static::assertSame([$point], $collection->getElements());
            static::assertSame(2154, $collection->getSrid());
            static::assertSame(4326, $point->getSrid());
            static::assertSame([1, null], $point->toArray());
        }
    }

    /** Self-membership is rejected before any mutation. */
    public function testDirectReplaceAll(): void
    {
        $point = new Point(1, null, 4326);
        $collection = new SpatialCollection([$point], 2154);
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('Mutable aggregate members must not form reference cycles.');

        try {
            $collection->setElements([$point, $collection]);
        } finally {
            static::assertSame([$point], $collection->getElements());
            static::assertSame(2154, $collection->getSrid());
            static::assertSame(4326, $point->getSrid());
            static::assertSame([1, null], $point->toArray());
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
            static::assertSame([], $collection->getElements());
            static::assertSame(4326, $collection->getSrid());
            static::assertSame(CoordinateDimensionEnum::XYM, $collection->getDimension());
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
            static::assertSame([], $collection->getElements());
            static::assertSame(4326, $collection->getSrid());
            static::assertSame(CoordinateDimensionEnum::XYZ, $collection->getDimension());
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
            static::assertSame([], $collection->getElements());
            static::assertSame(4326, $collection->getSrid());
            static::assertSame(CoordinateDimensionEnum::XYZM, $collection->getDimension());
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
            static::assertSame([$point], $collection->getElements());
            static::assertSame([$point], $owner->getElements());
            static::assertSame($bridge, $nested->getElement(0));
            static::assertSame([$collection], $bridge->getElements());
            static::assertSame(2154, $collection->getSrid());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
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
            static::assertSame([$point], $collection->getElements());
            static::assertSame([$point], $owner->getElements());
            static::assertSame($bridge, $nested->getElement(0));
            static::assertSame([$collection], $bridge->getElements());
            static::assertSame(2154, $collection->getSrid());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
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
            static::assertSame([$point], $collection->getElements());
            static::assertSame([$point], $owner->getElements());
            static::assertSame($bridge, $nested->getElement(0));
            static::assertSame([$collection], $bridge->getElements());
            static::assertSame(2154, $collection->getSrid());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
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
            static::assertSame([$point], $collection->getElements());
            static::assertSame([$point], $owner->getElements());
            static::assertSame($bridge, $nested->getElement(0));
            static::assertSame([$collection], $bridge->getElements());
            static::assertSame(2154, $collection->getSrid());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
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
        static::assertSame($collection, $collection->addElement($shared)->insertElement(0, $shared)->setElement(1, $right));
        static::assertSame($collection, $collection->setElements([$left, $right, $shared, $shared]));
        static::assertSame([$left, $right, $shared, $shared], $collection->getElements());
        static::assertSame($shared, $left->getElement(0));
        static::assertSame($shared, $right->getElement(0));
        static::assertSame($collection, $collection->setSrid(2154)->setFamily(SpatialModelEnum::GEOGRAPHY)->setSpatialReference(SpatialReference::epsg(2154)));
        static::assertSame(2154, $point->getSrid());
        static::assertSame('EPSG', $point->getSpatialReference()->authority);
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame([[[[1, null]]], [[[1, null]]], [[1, null]], [[1, null]]], $collection->toArray());
        static::assertSame($point, $shared->getElement(0));
    }
}
