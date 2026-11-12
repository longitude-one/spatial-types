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
        static::assertInstanceOf(ExternalCollection::class, $first);
        static::assertInstanceOf(ExternalCollection::class, $second);

        static::assertNotSame($external, $first);
        static::assertNotSame($external, $second);
        static::assertNotSame($first, $second);
        static::assertNotSame($emptyPoint, $first->getElements()[0]);
        static::assertNotSame($first->getElements()[0], $first->getElements()[1]);
        static::assertNotSame($first->getElements()[0], $second->getElements()[0]);
        static::assertSame($parentReference, $first->getSpatialReference());
        static::assertSame($parentReference, $second->getSpatialReference());
        static::assertSame($childReference, $first->getElements()[0]->getSpatialReference());
        static::assertSame($childReference, $first->getElements()[1]->getSpatialReference());
        static::assertSame($childReference, $second->getElements()[0]->getSpatialReference());
        static::assertSame($childReference, $second->getElements()[1]->getSpatialReference());
        static::assertSame([[[], []], [[], []]], $copy->toArray());
        static::assertSame([[[], []], [[], []]], $source->toArray());
        static::assertSame($external, $source->getElements()[0]);
        static::assertSame($emptyPoint, $external->getElements()[0]);
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

        static::assertSame($targetReference, $referenceCopy->getSpatialReference());
        static::assertSame($targetReference, $referenceCopy->getElements()[0]->getSpatialReference());
        static::assertSame(3857, $identifierCopy->getSrid());
        static::assertSame(3857, $identifierCopy->getElements()[0]->getSrid());
        static::assertNull($identifierCopy->getSpatialReference()->authority);
        static::assertNull($identifierCopy->getElements()[0]->getSpatialReference()->authority);
        static::assertSame([[1, 2]], $referenceCopy->toArray());
        static::assertSame([[1, 2]], $identifierCopy->toArray());
        static::assertSame([[1, 2]], $external->toArray());
        static::assertSame($parentReference, $external->getSpatialReference());
        static::assertSame($childReference, $pointA->getSpatialReference());
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
        static::assertInstanceOf(ExternalCollection::class, $externalCopy);
        $pointACopy = $externalCopy->getElements()[0];
        static::assertInstanceOf(PointImmutable::class, $pointACopy);

        static::assertSame('LOCAL', $pointACopy->getSpatialReference()->authority);
        static::assertSame(2154, $pointACopy->getSrid());
        static::assertSame($childReference, $pointACopy->getSpatialReference());
        static::assertSame($parentReference, $externalCopy->getSpatialReference());
        static::assertNotSame($external, $externalCopy);
        static::assertNotSame($pointA, $pointACopy);
        static::assertSame($replacementB, $copy->getElements()[0]);
        static::assertSame([[10, 20], [[1, 2]]], $copy->toArray());
        static::assertSame([[0, 0], [[1, 2]]], $source->toArray());
        static::assertSame($pointB, $source->getElements()[0]);
        static::assertSame($external, $source->getElements()[1]);
        static::assertSame($pointA, $external->getElements()[0]);
        static::assertSame($childReference, $pointA->getSpatialReference());
        static::assertSame($parentReference, $external->getSpatialReference());
    }
}
