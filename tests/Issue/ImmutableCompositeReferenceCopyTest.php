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
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\CircularStringImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\CompoundCurveImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\GeometryCollectionImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineStringImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiLineStringImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiPolygonImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PointImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PolygonImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\TriangleImmutable;
use LongitudeOne\SpatialTypes\Value\Coordinates;
use PHPUnit\Framework\TestCase;

/** Composite copies retain the complete reference at every unchanged level.
 *
 * {@link https://github.com/longitude-one/spatial-types/issues/77}
 *
 * @internal
 *
 * @coversNothing
 */
final class ImmutableCompositeReferenceCopyTest extends TestCase
{
    /** Circular strings preserve independent references, including EMPTY members. */
    public function testCircularStringPreservesEmptyPointReference(): void
    {
        $reference = SpatialReference::epsg(4326);
        $emptyReference = new SpatialReference(0, 'LOCAL');
        $source = new CircularStringImmutable([
            new PointImmutable(1, 2, $reference),
            new PointImmutable(null, null, $emptyReference),
            new PointImmutable(3, 4, $reference),
        ], $reference);

        $copy = $source->withPoint(0, Coordinates::xy(10, 20));

        static::assertSame($emptyReference, $copy->getPoint(1)->getSpatialReference());
        static::assertNotSame($source->getPoint(1), $copy->getPoint(1));
        static::assertSame([[10, 20], [], [3, 4]], $copy->toArray());
        static::assertSame([[1, 2], [], [3, 4]], $source->toArray());
    }

    /** Nested collections also retain circular-string and compound-curve references. */
    public function testCollectionPreservesNestedCompoundCurveReferences(): void
    {
        $reference = SpatialReference::epsg(4326);
        $collectionReference = new SpatialReference(2154, 'LOCAL');
        $curveReference = new SpatialReference(99, 'CURVE');
        $pointReference = new SpatialReference(123, 'CUSTOM');
        $source = new GeometryCollectionImmutable($reference, [
            new PointImmutable(0, 0, $reference),
            new GeometryCollectionImmutable($collectionReference, [
                new CompoundCurveImmutable([
                    new CircularStringImmutable([
                        new PointImmutable(1, 1, $pointReference),
                        new PointImmutable(2, 2, $curveReference),
                        new PointImmutable(3, 1, $curveReference),
                    ], $curveReference),
                    new LineStringImmutable([], $pointReference),
                ], $reference),
            ]),
        ]);
        $replacement = new PointImmutable(10, 20, $reference);

        $copy = $source->withElement(0, $replacement);
        $nested = $copy->getElements()[1];
        static::assertInstanceOf(GeometryCollectionImmutable::class, $nested);
        $compound = $nested->getElements()[0];
        static::assertInstanceOf(CompoundCurveImmutable::class, $compound);
        $circular = $compound->getCurve(0);
        static::assertInstanceOf(CircularStringImmutable::class, $circular);

        static::assertSame($replacement, $copy->getElements()[0]);
        static::assertSame($collectionReference, $nested->getSpatialReference());
        static::assertSame($reference, $compound->getSpatialReference());
        static::assertSame($curveReference, $circular->getSpatialReference());
        static::assertSame($pointReference, $circular->getPoint(0)->getSpatialReference());
        static::assertSame($pointReference, $compound->getCurve(1)->getSpatialReference());
        static::assertNotSame($source->getElements()[1], $nested);
        static::assertSame([[10, 20], [[[[1, 1], [2, 2], [3, 1]], []]]], $copy->toArray());
        static::assertSame([[0, 0], [[[[1, 1], [2, 2], [3, 1]], []]]], $source->toArray());
    }

    /** Both multi-line editing operations copy an unchanged line without homogenizing it. */
    public function testMultiLineStringPreservesDescendantReferences(): void
    {
        $reference = SpatialReference::epsg(4326);
        $lineReference = new SpatialReference(2154, 'LOCAL');
        $pointReference = new SpatialReference(123, 'CUSTOM');
        $source = new MultiLineStringImmutable([
            new LineStringImmutable([[0, 0], [1, 1]], $reference),
            new LineStringImmutable([
                new PointImmutable(2, 2, $pointReference),
                new PointImmutable(3, 3, $lineReference),
            ], $lineReference),
        ], $reference);

        $pointCopy = $source->withPoint(0, 0, Coordinates::xy(10, 20));
        $lineCopy = $source->withLineString(0, [[10, 20], [30, 40]]);

        static::assertSame($lineReference, $pointCopy->getLineString(1)->getSpatialReference());
        static::assertSame($pointReference, $pointCopy->getLineString(1)->getPoint(0)->getSpatialReference());
        static::assertSame($lineReference, $lineCopy->getLineString(1)->getSpatialReference());
        static::assertSame($pointReference, $lineCopy->getLineString(1)->getPoint(0)->getSpatialReference());
        static::assertNotSame($source->getLineString(1), $pointCopy->getLineString(1));
        static::assertNotSame($source->getLineString(1)->getPoint(0), $lineCopy->getLineString(1)->getPoint(0));
        static::assertSame([[[10, 20], [1, 1]], [[2, 2], [3, 3]]], $pointCopy->toArray());
        static::assertSame([[[10, 20], [30, 40]], [[2, 2], [3, 3]]], $lineCopy->toArray());
        static::assertSame([[[0, 0], [1, 1]], [[2, 2], [3, 3]]], $source->toArray());
    }

    /** All multi-polygon edits preserve the unchanged polygon hierarchy. */
    public function testMultiPolygonPreservesDescendantReferences(): void
    {
        $reference = SpatialReference::epsg(4326);
        $polygonReference = new SpatialReference(2154, 'LOCAL');
        $ringReference = new SpatialReference(99, 'RING');
        $pointReference = new SpatialReference(123, 'CUSTOM');
        $source = new MultiPolygonImmutable([
            new PolygonImmutable([[[0, 0], [4, 0], [0, 4], [0, 0]]], $reference),
            new PolygonImmutable([
                new LineStringImmutable([
                    new PointImmutable(10, 10, $pointReference),
                    new PointImmutable(14, 10, $ringReference),
                    new PointImmutable(10, 14, $ringReference),
                    new PointImmutable(10, 10, $pointReference),
                ], $ringReference),
            ], $polygonReference),
        ], $reference);

        $pointCopy = $source->withPoint(0, 0, 1, Coordinates::xy(5, 0));
        $ringCopy = $source->withRing(0, 0, [[0, 0], [6, 0], [0, 6], [0, 0]]);
        $polygonCopy = $source->withPolygon(0, [[[0, 0], [7, 0], [0, 7], [0, 0]]]);

        static::assertSame($polygonReference, $pointCopy->getPolygon(1)->getSpatialReference());
        static::assertSame($ringReference, $pointCopy->getPolygon(1)->getRing(0)->getSpatialReference());
        static::assertSame($pointReference, $pointCopy->getPolygon(1)->getRing(0)->getPoint(0)->getSpatialReference());
        static::assertSame($pointReference, $ringCopy->getPolygon(1)->getRing(0)->getPoint(0)->getSpatialReference());
        static::assertSame($polygonReference, $polygonCopy->getPolygon(1)->getSpatialReference());
        static::assertSame($ringReference, $polygonCopy->getPolygon(1)->getRing(0)->getSpatialReference());
        static::assertSame($pointReference, $polygonCopy->getPolygon(1)->getRing(0)->getPoint(0)->getSpatialReference());
        static::assertNotSame($source->getPolygon(1), $pointCopy->getPolygon(1));
        static::assertNotSame($source->getPolygon(1)->getRing(0), $ringCopy->getPolygon(1)->getRing(0));
        static::assertNotSame($source->getPolygon(1)->getRing(0)->getPoint(0), $polygonCopy->getPolygon(1)->getRing(0)->getPoint(0));
        static::assertSame([[[[0, 0], [5, 0], [0, 4], [0, 0]]], [[[10, 10], [14, 10], [10, 14], [10, 10]]]], $pointCopy->toArray());
        static::assertSame([[[[0, 0], [6, 0], [0, 6], [0, 0]]], [[[10, 10], [14, 10], [10, 14], [10, 10]]]], $ringCopy->toArray());
        static::assertSame([[[[0, 0], [7, 0], [0, 7], [0, 0]]], [[[10, 10], [14, 10], [10, 14], [10, 10]]]], $polygonCopy->toArray());
        static::assertSame([[[[0, 0], [4, 0], [0, 4], [0, 0]]], [[[10, 10], [14, 10], [10, 14], [10, 10]]]], $source->toArray());
    }

    /** Point and ring editing preserve unrelated rings and nested point references. */
    public function testPolygonPreservesRingAndPointReferences(): void
    {
        $reference = SpatialReference::epsg(4326);
        $ringReference = new SpatialReference(2154, 'LOCAL');
        $pointReference = new SpatialReference(0);
        $source = new PolygonImmutable([
            new LineStringImmutable([[0, 0], [4, 0], [0, 4], [0, 0]], $reference),
            new LineStringImmutable([
                new PointImmutable(1, 1, $pointReference),
                new PointImmutable(2, 1, $ringReference),
                new PointImmutable(1, 2, $ringReference),
                new PointImmutable(1, 1, $pointReference),
            ], $ringReference),
        ], $reference);

        $pointCopy = $source->withPoint(0, 1, Coordinates::xy(5, 0));
        $ringCopy = $source->withRing(0, [[0, 0], [6, 0], [0, 6], [0, 0]]);

        static::assertSame($ringReference, $pointCopy->getRing(1)->getSpatialReference());
        static::assertSame($pointReference, $pointCopy->getRing(1)->getPoint(0)->getSpatialReference());
        static::assertSame($ringReference, $ringCopy->getRing(1)->getSpatialReference());
        static::assertSame($pointReference, $ringCopy->getRing(1)->getPoint(0)->getSpatialReference());
        static::assertNotSame($source->getRing(1), $pointCopy->getRing(1));
        static::assertNotSame($source->getRing(1)->getPoint(0), $ringCopy->getRing(1)->getPoint(0));
        static::assertSame([[[0, 0], [5, 0], [0, 4], [0, 0]], [[1, 1], [2, 1], [1, 2], [1, 1]]], $pointCopy->toArray());
        static::assertSame([[[0, 0], [6, 0], [0, 6], [0, 0]], [[1, 1], [2, 1], [1, 2], [1, 1]]], $ringCopy->toArray());
        static::assertSame([[[0, 0], [4, 0], [0, 4], [0, 0]], [[1, 1], [2, 1], [1, 2], [1, 1]]], $source->toArray());
    }

    /** Triangle point copies keep closure and each unaffected point's metadata. */
    public function testTrianglePreservesReferenceDuringEndpointReplacement(): void
    {
        $reference = SpatialReference::epsg(4326);
        $pointReference = new SpatialReference(123, 'CUSTOM');
        $source = new TriangleImmutable([
            new LineStringImmutable([
                new PointImmutable(0, 0, $reference),
                new PointImmutable(4, 0, $pointReference),
                new PointImmutable(0, 4, $reference),
                new PointImmutable(0, 0, $reference),
            ], $reference),
        ], $reference);

        $copy = $source->withPoint(0, 0, Coordinates::xy(-1, -1));

        static::assertSame($pointReference, $copy->getRing(0)->getPoint(1)->getSpatialReference());
        static::assertNotSame($source->getRing(0)->getPoint(1), $copy->getRing(0)->getPoint(1));
        static::assertSame([[[-1, -1], [4, 0], [0, 4], [-1, -1]]], $copy->toArray());
        static::assertSame([[[0, 0], [4, 0], [0, 4], [0, 0]]], $source->toArray());
    }
}
