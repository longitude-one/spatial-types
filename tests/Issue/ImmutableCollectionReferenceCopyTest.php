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

use LongitudeOne\SpatialTypes\Interfaces\ImmutableSpatialInterface;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\GeographyCollectionImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\LineStringImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\MultiLineStringImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\MultiPointImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\MultiPolygonImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\PointImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\PolygonImmutable;
use PHPUnit\Framework\TestCase;

/** Collection edits copy nested multi-objects without changing their references.
 *
 * {@link https://github.com/longitude-one/spatial-types/issues/77}
 *
 * @internal
 *
 * @coversNothing
 */
final class ImmutableCollectionReferenceCopyTest extends TestCase
{
    /** External immutable implementations supply their own reference-neutral copy. */
    public function testExternalComponentSuppliesItsNeutralCopy(): void
    {
        $reference = new SpatialReference(123, 'CUSTOM');
        $component = static::createMock(ImmutableSpatialInterface::class);
        $componentCopy = clone $component;
        $component->method('getSpatialReference')->willReturn($reference);
        $component->expects($this->once())->method('copy')->willReturn($componentCopy);
        $component->expects($this->never())->method('withSpatialReference');
        $source = new GeographyCollectionImmutable(4326, [
            new PointImmutable(0, 0, 4326),
            $component,
        ]);

        $copy = $source->withElement(0, new PointImmutable(10, 20, 4326));

        self::assertSame($component, $source->getElements()[1]);
        self::assertSame($componentCopy, $copy->getElements()[1]);
    }

    /** Nested EMPTY surfaces are still independently copied with their authority. */
    public function testNestedEmptySurface(): void
    {
        $reference = SpatialReference::epsg(4326);
        $surfaceReference = new SpatialReference(99, 'LOCAL');
        $surface = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\PolyhedralSurfaceImmutable([], $surfaceReference);
        $source = new GeographyCollectionImmutable($reference, [
            new PointImmutable(0, 0, $reference),
            $surface,
        ]);

        $copy = $source->withElement(0, new PointImmutable(10, 20, $reference));
        $surfaceCopy = $copy->getElements()[1];

        self::assertSame($surfaceReference, $surfaceCopy->getSpatialReference());
        self::assertNotSame($surface, $surfaceCopy);
        self::assertSame([[10, 20], []], $copy->toArray());
        self::assertSame([[0, 0], []], $source->toArray());
    }

    /** Nested multilines keep their line and point metadata. */
    public function testNestedMultiLineString(): void
    {
        $reference = SpatialReference::epsg(4326);
        $pointReference = new SpatialReference(99, 'LOCAL');
        $source = new GeographyCollectionImmutable($reference, [
            new PointImmutable(0, 0, $reference),
            new MultiLineStringImmutable([
                new LineStringImmutable([
                    new PointImmutable(1, 2, $pointReference),
                    new PointImmutable(3, 4, $reference),
                ], $reference),
            ], $reference),
        ]);

        $copy = $source->withElement(0, new PointImmutable(10, 20, $reference));
        $multiLine = $copy->getElements()[1];
        self::assertInstanceOf(MultiLineStringImmutable::class, $multiLine);
        self::assertSame($pointReference, $multiLine->getLineString(0)->getPoint(0)->getSpatialReference());
        self::assertNotSame($source->getElements()[1], $multiLine);
        self::assertSame([[10, 20], [[[1, 2], [3, 4]]]], $copy->toArray());
        self::assertSame([[0, 0], [[[1, 2], [3, 4]]]], $source->toArray());
    }

    /** Nested multipoints retain distinct point references and EMPTY values. */
    public function testNestedMultiPoint(): void
    {
        $reference = SpatialReference::epsg(4326);
        $pointReference = new SpatialReference(0, 'LOCAL');
        $source = new GeographyCollectionImmutable($reference, [
            new PointImmutable(0, 0, $reference),
            new MultiPointImmutable([
                new PointImmutable(1, 2, $reference),
                new PointImmutable(null, null, $pointReference),
            ], $reference),
        ]);

        $copy = $source->withElement(0, new PointImmutable(10, 20, $reference));
        $multiPoint = $copy->getElements()[1];
        self::assertInstanceOf(MultiPointImmutable::class, $multiPoint);
        self::assertSame($pointReference, $multiPoint->getPoint(1)->getSpatialReference());
        self::assertNotSame($source->getElements()[1], $multiPoint);
        self::assertSame([[10, 20], [[1, 2], []]], $copy->toArray());
        self::assertSame([[0, 0], [[1, 2], []]], $source->toArray());
    }

    /** Nested multipolygons retain metadata down to their points. */
    public function testNestedMultiPolygon(): void
    {
        $reference = SpatialReference::epsg(4326);
        $pointReference = new SpatialReference(99, 'LOCAL');
        $source = new GeographyCollectionImmutable($reference, [
            new PointImmutable(0, 0, $reference),
            new MultiPolygonImmutable([
                new PolygonImmutable([
                    new LineStringImmutable([
                        new PointImmutable(1, 2, $pointReference),
                        new PointImmutable(3, 4, $reference),
                        new PointImmutable(5, 6, $reference),
                        new PointImmutable(1, 2, $pointReference),
                    ], $reference),
                ], $reference),
            ], $reference),
        ]);

        $copy = $source->withElement(0, new PointImmutable(10, 20, $reference));
        $multiPolygon = $copy->getElements()[1];
        self::assertInstanceOf(MultiPolygonImmutable::class, $multiPolygon);
        self::assertSame($pointReference, $multiPolygon->getPolygon(0)->getRing(0)->getPoint(0)->getSpatialReference());
        self::assertNotSame($source->getElements()[1], $multiPolygon);
        self::assertSame([[10, 20], [[[[1, 2], [3, 4], [5, 6], [1, 2]]]]], $copy->toArray());
        self::assertSame([[0, 0], [[[[1, 2], [3, 4], [5, 6], [1, 2]]]]], $source->toArray());
    }
}
