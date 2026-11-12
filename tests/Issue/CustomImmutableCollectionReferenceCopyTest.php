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

use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Tests\Issue\Support\ExternalCollection;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\GeometryCollectionImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PointImmutable;
use PHPUnit\Framework\TestCase;

/** Regression for the exact custom-composite scenario reviewed on PR #83.
 *
 * {@link https://github.com/longitude-one/spatial-types/issues/77}
 *
 * @internal
 *
 * @coversNothing
 */
final class CustomImmutableCollectionReferenceCopyTest extends TestCase
{
    /** Neutral copies also preserve repeated custom composites and EMPTY descendants. */
    public function testCopyPreservesRepeatedCustomComponentsAndEmptyDescendants(): void
    {
        $childReference = new SpatialReference(0, 'LOCAL');
        $parentReference = SpatialReference::epsg(4326);
        $emptyPoint = new PointImmutable(null, null, $childReference);
        $external = new ExternalCollection(new GeometryCollectionImmutable($parentReference, [$emptyPoint, $emptyPoint]));
        $source = new GeometryCollectionImmutable(0, [$external, $external]);

        $copy = $source->copy();
        $first = $copy->getElements()[0];
        $second = $copy->getElements()[1];
        self::assertInstanceOf(ExternalCollection::class, $first);
        self::assertInstanceOf(ExternalCollection::class, $second);

        self::assertNotSame($external, $first);
        self::assertNotSame($external, $second);
        self::assertNotSame($first, $second);
        self::assertNotSame($emptyPoint, $first->getElements()[0]);
        self::assertNotSame($first->getElements()[0], $first->getElements()[1]);
        self::assertNotSame($first->getElements()[0], $second->getElements()[0]);
        self::assertSame($parentReference, $first->getSpatialReference());
        self::assertSame($parentReference, $second->getSpatialReference());
        self::assertSame($childReference, $first->getElements()[0]->getSpatialReference());
        self::assertSame($childReference, $first->getElements()[1]->getSpatialReference());
        self::assertSame($childReference, $second->getElements()[0]->getSpatialReference());
        self::assertSame($childReference, $second->getElements()[1]->getSpatialReference());
        self::assertSame([[[], []], [[], []]], $copy->toArray());
        self::assertSame([[[], []], [[], []]], $source->toArray());
        self::assertSame($external, $source->getElements()[0]);
        self::assertSame($emptyPoint, $external->getElements()[0]);
    }

    /** Explicit reference changes on a custom composite still propagate to A. */
    public function testExplicitReferenceReplacementStillPropagates(): void
    {
        $childReference = new SpatialReference(2154, 'LOCAL');
        $parentReference = SpatialReference::epsg(4326);
        $pointA = new PointImmutable(1, 2, $childReference);
        $external = new ExternalCollection(new GeometryCollectionImmutable($parentReference, [$pointA]));
        $targetReference = SpatialReference::epsg(3857);

        $referenceCopy = $external->withSpatialReference($targetReference);
        $identifierCopy = $external->withSrid(3857);

        self::assertSame($targetReference, $referenceCopy->getSpatialReference());
        self::assertSame($targetReference, $referenceCopy->getElements()[0]->getSpatialReference());
        self::assertSame(3857, $identifierCopy->getSrid());
        self::assertSame(3857, $identifierCopy->getElements()[0]->getSrid());
        self::assertNull($identifierCopy->getSpatialReference()->authority);
        self::assertNull($identifierCopy->getElements()[0]->getSpatialReference()->authority);
        self::assertSame([[1, 2]], $referenceCopy->toArray());
        self::assertSame([[1, 2]], $identifierCopy->toArray());
        self::assertSame([[1, 2]], $external->toArray());
        self::assertSame($parentReference, $external->getSpatialReference());
        self::assertSame($childReference, $pointA->getSpatialReference());
    }

    /** Replacing B preserves A's LOCAL:2154 reference inside an EPSG:4326 wrapper. */
    public function testReplacingAnotherElementPreservesCustomDescendantReference(): void
    {
        $childReference = new SpatialReference(2154, 'LOCAL');
        $parentReference = SpatialReference::epsg(4326);
        $pointA = new PointImmutable(1, 2, $childReference);
        $pointB = new PointImmutable(0, 0);
        $external = new ExternalCollection(new GeometryCollectionImmutable($parentReference, [$pointA]));
        $source = new GeometryCollectionImmutable(0, [$pointB, $external]);
        $replacementB = new PointImmutable(10, 20);

        $copy = $source->withElement(0, $replacementB);
        $externalCopy = $copy->getElements()[1];
        self::assertInstanceOf(ExternalCollection::class, $externalCopy);
        $pointACopy = $externalCopy->getElements()[0];
        self::assertInstanceOf(PointImmutable::class, $pointACopy);

        self::assertSame('LOCAL', $pointACopy->getSpatialReference()->authority);
        self::assertSame(2154, $pointACopy->getSrid());
        self::assertSame($childReference, $pointACopy->getSpatialReference());
        self::assertSame($parentReference, $externalCopy->getSpatialReference());
        self::assertNotSame($external, $externalCopy);
        self::assertNotSame($pointA, $pointACopy);
        self::assertSame($replacementB, $copy->getElements()[0]);
        self::assertSame([[10, 20], [[1, 2]]], $copy->toArray());
        self::assertSame([[0, 0], [[1, 2]]], $source->toArray());
        self::assertSame($pointB, $source->getElements()[0]);
        self::assertSame($external, $source->getElements()[1]);
        self::assertSame($pointA, $external->getElements()[0]);
        self::assertSame($childReference, $pointA->getSpatialReference());
        self::assertSame($parentReference, $external->getSpatialReference());
    }
}
