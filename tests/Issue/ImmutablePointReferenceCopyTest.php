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
use LongitudeOne\SpatialTypes\Value\Coordinates;
use PHPUnit\Framework\TestCase;

/** Regression coverage for authority-qualified immutable copies.
 *
 * {@link https://github.com/longitude-one/spatial-types/issues/77}
 *
 * @internal
 *
 * @coversNothing
 */
final class ImmutablePointReferenceCopyTest extends TestCase
{
    /** Xy geography linestring copies retain each point's reference. */
    public function testXyGeographyLineStringPreservesPointReferences(): void
    {
        $reference = SpatialReference::epsg(4326);
        $otherReference = new SpatialReference(2154, 'LOCAL');
        $source = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geography\LineStringImmutable([
            new \LongitudeOne\SpatialTypes\Types\Dimension2\Geography\PointImmutable(1, 2, $reference),
            new \LongitudeOne\SpatialTypes\Types\Dimension2\Geography\PointImmutable(3, 4, $otherReference),
        ], $reference);

        $copy = $source->withPoint(0, Coordinates::xy(10, 20));

        self::assertSame($reference, $copy->getSpatialReference());
        self::assertSame($reference, $copy->getPoint(0)->getSpatialReference());
        self::assertSame($otherReference, $copy->getPoint(1)->getSpatialReference());
        self::assertSame(2154, $copy->getPoint(1)->getSrid());
        self::assertNotSame($source, $copy);
        self::assertNotSame($source->getPoint(0), $copy->getPoint(0));
        self::assertNotSame($source->getPoint(1), $copy->getPoint(1));
        self::assertSame([[10, 20], [3, 4]], $copy->toArray());
        self::assertSame([[1, 2], [3, 4]], $source->toArray());
        self::assertSame($reference, $source->getPoint(0)->getSpatialReference());
        self::assertSame($otherReference, $source->getPoint(1)->getSpatialReference());
    }

    /** Xy geography multipoint copies retain each point's reference. */
    public function testXyGeographyMultiPointPreservesPointReferences(): void
    {
        $reference = SpatialReference::epsg(4326);
        $otherReference = new SpatialReference(2154, 'LOCAL');
        $source = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geography\MultiPointImmutable([
            new \LongitudeOne\SpatialTypes\Types\Dimension2\Geography\PointImmutable(1, 2, $reference),
            new \LongitudeOne\SpatialTypes\Types\Dimension2\Geography\PointImmutable(3, 4, $otherReference),
        ], $reference);

        $copy = $source->withPoint(0, Coordinates::xy(10, 20));

        self::assertSame($reference, $copy->getSpatialReference());
        self::assertSame($reference, $copy->getPoint(0)->getSpatialReference());
        self::assertSame($otherReference, $copy->getPoint(1)->getSpatialReference());
        self::assertSame(2154, $copy->getPoint(1)->getSrid());
        self::assertNotSame($source, $copy);
        self::assertNotSame($source->getPoint(0), $copy->getPoint(0));
        self::assertNotSame($source->getPoint(1), $copy->getPoint(1));
        self::assertSame([[10, 20], [3, 4]], $copy->toArray());
        self::assertSame([[1, 2], [3, 4]], $source->toArray());
        self::assertSame($reference, $source->getPoint(0)->getSpatialReference());
        self::assertSame($otherReference, $source->getPoint(1)->getSpatialReference());
    }

    /** Xy geometry linestring copies retain each point's reference. */
    public function testXyGeometryLineStringPreservesPointReferences(): void
    {
        $reference = SpatialReference::epsg(4326);
        $otherReference = new SpatialReference(2154, 'LOCAL');
        $source = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineStringImmutable([
            new \LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PointImmutable(1, 2, $reference),
            new \LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PointImmutable(3, 4, $otherReference),
        ], $reference);

        $copy = $source->withPoint(0, Coordinates::xy(10, 20));

        self::assertSame($reference, $copy->getSpatialReference());
        self::assertSame($reference, $copy->getPoint(0)->getSpatialReference());
        self::assertSame($otherReference, $copy->getPoint(1)->getSpatialReference());
        self::assertSame(2154, $copy->getPoint(1)->getSrid());
        self::assertNotSame($source, $copy);
        self::assertNotSame($source->getPoint(0), $copy->getPoint(0));
        self::assertNotSame($source->getPoint(1), $copy->getPoint(1));
        self::assertSame([[10, 20], [3, 4]], $copy->toArray());
        self::assertSame([[1, 2], [3, 4]], $source->toArray());
        self::assertSame($reference, $source->getPoint(0)->getSpatialReference());
        self::assertSame($otherReference, $source->getPoint(1)->getSpatialReference());
    }

    /** Xy geometry multipoint copies retain each point's reference. */
    public function testXyGeometryMultiPointPreservesPointReferences(): void
    {
        $reference = SpatialReference::epsg(4326);
        $otherReference = new SpatialReference(2154, 'LOCAL');
        $source = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiPointImmutable([
            new \LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PointImmutable(1, 2, $reference),
            new \LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PointImmutable(3, 4, $otherReference),
        ], $reference);

        $copy = $source->withPoint(0, Coordinates::xy(10, 20));

        self::assertSame($reference, $copy->getSpatialReference());
        self::assertSame($reference, $copy->getPoint(0)->getSpatialReference());
        self::assertSame($otherReference, $copy->getPoint(1)->getSpatialReference());
        self::assertSame(2154, $copy->getPoint(1)->getSrid());
        self::assertNotSame($source, $copy);
        self::assertNotSame($source->getPoint(0), $copy->getPoint(0));
        self::assertNotSame($source->getPoint(1), $copy->getPoint(1));
        self::assertSame([[10, 20], [3, 4]], $copy->toArray());
        self::assertSame([[1, 2], [3, 4]], $source->toArray());
        self::assertSame($reference, $source->getPoint(0)->getSpatialReference());
        self::assertSame($otherReference, $source->getPoint(1)->getSpatialReference());
    }

    /** Xym geography linestring copies retain each point's reference. */
    public function testXymGeographyLineStringPreservesPointReferences(): void
    {
        $reference = SpatialReference::epsg(4326);
        $otherReference = new SpatialReference(2154, 'LOCAL');
        $source = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\LineStringImmutable([
            new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\PointImmutable(1, 2, 7, $reference),
            new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\PointImmutable(3, 4, 8, $otherReference),
        ], $reference);

        $copy = $source->withPoint(0, Coordinates::xym(10, 20, 70));

        self::assertSame($reference, $copy->getSpatialReference());
        self::assertSame($reference, $copy->getPoint(0)->getSpatialReference());
        self::assertSame($otherReference, $copy->getPoint(1)->getSpatialReference());
        self::assertSame(2154, $copy->getPoint(1)->getSrid());
        self::assertNotSame($source, $copy);
        self::assertNotSame($source->getPoint(0), $copy->getPoint(0));
        self::assertNotSame($source->getPoint(1), $copy->getPoint(1));
        self::assertSame([[10, 20, 70], [3, 4, 8]], $copy->toArray());
        self::assertSame([[1, 2, 7], [3, 4, 8]], $source->toArray());
        self::assertSame($reference, $source->getPoint(0)->getSpatialReference());
        self::assertSame($otherReference, $source->getPoint(1)->getSpatialReference());
    }

    /** Xym geography multipoint copies retain each point's reference. */
    public function testXymGeographyMultiPointPreservesPointReferences(): void
    {
        $reference = SpatialReference::epsg(4326);
        $otherReference = new SpatialReference(2154, 'LOCAL');
        $source = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\MultiPointImmutable([
            new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\PointImmutable(1, 2, 7, $reference),
            new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\PointImmutable(3, 4, 8, $otherReference),
        ], $reference);

        $copy = $source->withPoint(0, Coordinates::xym(10, 20, 70));

        self::assertSame($reference, $copy->getSpatialReference());
        self::assertSame($reference, $copy->getPoint(0)->getSpatialReference());
        self::assertSame($otherReference, $copy->getPoint(1)->getSpatialReference());
        self::assertSame(2154, $copy->getPoint(1)->getSrid());
        self::assertNotSame($source, $copy);
        self::assertNotSame($source->getPoint(0), $copy->getPoint(0));
        self::assertNotSame($source->getPoint(1), $copy->getPoint(1));
        self::assertSame([[10, 20, 70], [3, 4, 8]], $copy->toArray());
        self::assertSame([[1, 2, 7], [3, 4, 8]], $source->toArray());
        self::assertSame($reference, $source->getPoint(0)->getSpatialReference());
        self::assertSame($otherReference, $source->getPoint(1)->getSpatialReference());
    }

    /** Xym geometry linestring copies retain each point's reference. */
    public function testXymGeometryLineStringPreservesPointReferences(): void
    {
        $reference = SpatialReference::epsg(4326);
        $otherReference = new SpatialReference(2154, 'LOCAL');
        $source = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\LineStringImmutable([
            new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\PointImmutable(1, 2, 7, $reference),
            new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\PointImmutable(3, 4, 8, $otherReference),
        ], $reference);

        $copy = $source->withPoint(0, Coordinates::xym(10, 20, 70));

        self::assertSame($reference, $copy->getSpatialReference());
        self::assertSame($reference, $copy->getPoint(0)->getSpatialReference());
        self::assertSame($otherReference, $copy->getPoint(1)->getSpatialReference());
        self::assertSame(2154, $copy->getPoint(1)->getSrid());
        self::assertNotSame($source, $copy);
        self::assertNotSame($source->getPoint(0), $copy->getPoint(0));
        self::assertNotSame($source->getPoint(1), $copy->getPoint(1));
        self::assertSame([[10, 20, 70], [3, 4, 8]], $copy->toArray());
        self::assertSame([[1, 2, 7], [3, 4, 8]], $source->toArray());
        self::assertSame($reference, $source->getPoint(0)->getSpatialReference());
        self::assertSame($otherReference, $source->getPoint(1)->getSpatialReference());
    }

    /** Xym geometry multipoint copies retain each point's reference. */
    public function testXymGeometryMultiPointPreservesPointReferences(): void
    {
        $reference = SpatialReference::epsg(4326);
        $otherReference = new SpatialReference(2154, 'LOCAL');
        $source = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\MultiPointImmutable([
            new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\PointImmutable(1, 2, 7, $reference),
            new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\PointImmutable(3, 4, 8, $otherReference),
        ], $reference);

        $copy = $source->withPoint(0, Coordinates::xym(10, 20, 70));

        self::assertSame($reference, $copy->getSpatialReference());
        self::assertSame($reference, $copy->getPoint(0)->getSpatialReference());
        self::assertSame($otherReference, $copy->getPoint(1)->getSpatialReference());
        self::assertSame(2154, $copy->getPoint(1)->getSrid());
        self::assertNotSame($source, $copy);
        self::assertNotSame($source->getPoint(0), $copy->getPoint(0));
        self::assertNotSame($source->getPoint(1), $copy->getPoint(1));
        self::assertSame([[10, 20, 70], [3, 4, 8]], $copy->toArray());
        self::assertSame([[1, 2, 7], [3, 4, 8]], $source->toArray());
        self::assertSame($reference, $source->getPoint(0)->getSpatialReference());
        self::assertSame($otherReference, $source->getPoint(1)->getSpatialReference());
    }

    /** Xyz geography linestring copies retain each point's reference. */
    public function testXyzGeographyLineStringPreservesPointReferences(): void
    {
        $reference = SpatialReference::epsg(4326);
        $otherReference = new SpatialReference(2154, 'LOCAL');
        $source = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\LineStringImmutable([
            new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\PointImmutable(1, 2, 3, $reference),
            new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\PointImmutable(4, 5, 6, $otherReference),
        ], $reference);

        $copy = $source->withPoint(0, Coordinates::xyz(10, 20, 30));

        self::assertSame($reference, $copy->getSpatialReference());
        self::assertSame($reference, $copy->getPoint(0)->getSpatialReference());
        self::assertSame($otherReference, $copy->getPoint(1)->getSpatialReference());
        self::assertSame(2154, $copy->getPoint(1)->getSrid());
        self::assertNotSame($source, $copy);
        self::assertNotSame($source->getPoint(0), $copy->getPoint(0));
        self::assertNotSame($source->getPoint(1), $copy->getPoint(1));
        self::assertSame([[10, 20, 30], [4, 5, 6]], $copy->toArray());
        self::assertSame([[1, 2, 3], [4, 5, 6]], $source->toArray());
        self::assertSame($reference, $source->getPoint(0)->getSpatialReference());
        self::assertSame($otherReference, $source->getPoint(1)->getSpatialReference());
    }

    /** Xyz geography multipoint copies retain each point's reference. */
    public function testXyzGeographyMultiPointPreservesPointReferences(): void
    {
        $reference = SpatialReference::epsg(4326);
        $otherReference = new SpatialReference(2154, 'LOCAL');
        $source = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\MultiPointImmutable([
            new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\PointImmutable(1, 2, 3, $reference),
            new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\PointImmutable(4, 5, 6, $otherReference),
        ], $reference);

        $copy = $source->withPoint(0, Coordinates::xyz(10, 20, 30));

        self::assertSame($reference, $copy->getSpatialReference());
        self::assertSame($reference, $copy->getPoint(0)->getSpatialReference());
        self::assertSame($otherReference, $copy->getPoint(1)->getSpatialReference());
        self::assertSame(2154, $copy->getPoint(1)->getSrid());
        self::assertNotSame($source, $copy);
        self::assertNotSame($source->getPoint(0), $copy->getPoint(0));
        self::assertNotSame($source->getPoint(1), $copy->getPoint(1));
        self::assertSame([[10, 20, 30], [4, 5, 6]], $copy->toArray());
        self::assertSame([[1, 2, 3], [4, 5, 6]], $source->toArray());
        self::assertSame($reference, $source->getPoint(0)->getSpatialReference());
        self::assertSame($otherReference, $source->getPoint(1)->getSpatialReference());
    }

    /** Xyz geometry linestring copies retain each point's reference. */
    public function testXyzGeometryLineStringPreservesPointReferences(): void
    {
        $reference = SpatialReference::epsg(4326);
        $otherReference = new SpatialReference(2154, 'LOCAL');
        $source = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\LineStringImmutable([
            new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\PointImmutable(1, 2, 3, $reference),
            new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\PointImmutable(4, 5, 6, $otherReference),
        ], $reference);

        $copy = $source->withPoint(0, Coordinates::xyz(10, 20, 30));

        self::assertSame($reference, $copy->getSpatialReference());
        self::assertSame($reference, $copy->getPoint(0)->getSpatialReference());
        self::assertSame($otherReference, $copy->getPoint(1)->getSpatialReference());
        self::assertSame(2154, $copy->getPoint(1)->getSrid());
        self::assertNotSame($source, $copy);
        self::assertNotSame($source->getPoint(0), $copy->getPoint(0));
        self::assertNotSame($source->getPoint(1), $copy->getPoint(1));
        self::assertSame([[10, 20, 30], [4, 5, 6]], $copy->toArray());
        self::assertSame([[1, 2, 3], [4, 5, 6]], $source->toArray());
        self::assertSame($reference, $source->getPoint(0)->getSpatialReference());
        self::assertSame($otherReference, $source->getPoint(1)->getSpatialReference());
    }

    /** Xyz geometry multipoint copies retain each point's reference. */
    public function testXyzGeometryMultiPointPreservesPointReferences(): void
    {
        $reference = SpatialReference::epsg(4326);
        $otherReference = new SpatialReference(2154, 'LOCAL');
        $source = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\MultiPointImmutable([
            new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\PointImmutable(1, 2, 3, $reference),
            new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\PointImmutable(4, 5, 6, $otherReference),
        ], $reference);

        $copy = $source->withPoint(0, Coordinates::xyz(10, 20, 30));

        self::assertSame($reference, $copy->getSpatialReference());
        self::assertSame($reference, $copy->getPoint(0)->getSpatialReference());
        self::assertSame($otherReference, $copy->getPoint(1)->getSpatialReference());
        self::assertSame(2154, $copy->getPoint(1)->getSrid());
        self::assertNotSame($source, $copy);
        self::assertNotSame($source->getPoint(0), $copy->getPoint(0));
        self::assertNotSame($source->getPoint(1), $copy->getPoint(1));
        self::assertSame([[10, 20, 30], [4, 5, 6]], $copy->toArray());
        self::assertSame([[1, 2, 3], [4, 5, 6]], $source->toArray());
        self::assertSame($reference, $source->getPoint(0)->getSpatialReference());
        self::assertSame($otherReference, $source->getPoint(1)->getSpatialReference());
    }

    /** Xyzm geography linestring copies retain each point's reference. */
    public function testXyzmGeographyLineStringPreservesPointReferences(): void
    {
        $reference = SpatialReference::epsg(4326);
        $otherReference = new SpatialReference(2154, 'LOCAL');
        $source = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\LineStringImmutable([
            new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\PointImmutable(1, 2, 3, 7, $reference),
            new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\PointImmutable(4, 5, 6, 8, $otherReference),
        ], $reference);

        $copy = $source->withPoint(0, Coordinates::xyzm(10, 20, 30, 70));

        self::assertSame($reference, $copy->getSpatialReference());
        self::assertSame($reference, $copy->getPoint(0)->getSpatialReference());
        self::assertSame($otherReference, $copy->getPoint(1)->getSpatialReference());
        self::assertSame(2154, $copy->getPoint(1)->getSrid());
        self::assertNotSame($source, $copy);
        self::assertNotSame($source->getPoint(0), $copy->getPoint(0));
        self::assertNotSame($source->getPoint(1), $copy->getPoint(1));
        self::assertSame([[10, 20, 30, 70], [4, 5, 6, 8]], $copy->toArray());
        self::assertSame([[1, 2, 3, 7], [4, 5, 6, 8]], $source->toArray());
        self::assertSame($reference, $source->getPoint(0)->getSpatialReference());
        self::assertSame($otherReference, $source->getPoint(1)->getSpatialReference());
    }

    /** Xyzm geography multipoint copies retain each point's reference. */
    public function testXyzmGeographyMultiPointPreservesPointReferences(): void
    {
        $reference = SpatialReference::epsg(4326);
        $otherReference = new SpatialReference(2154, 'LOCAL');
        $source = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\MultiPointImmutable([
            new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\PointImmutable(1, 2, 3, 7, $reference),
            new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\PointImmutable(4, 5, 6, 8, $otherReference),
        ], $reference);

        $copy = $source->withPoint(0, Coordinates::xyzm(10, 20, 30, 70));

        self::assertSame($reference, $copy->getSpatialReference());
        self::assertSame($reference, $copy->getPoint(0)->getSpatialReference());
        self::assertSame($otherReference, $copy->getPoint(1)->getSpatialReference());
        self::assertSame(2154, $copy->getPoint(1)->getSrid());
        self::assertNotSame($source, $copy);
        self::assertNotSame($source->getPoint(0), $copy->getPoint(0));
        self::assertNotSame($source->getPoint(1), $copy->getPoint(1));
        self::assertSame([[10, 20, 30, 70], [4, 5, 6, 8]], $copy->toArray());
        self::assertSame([[1, 2, 3, 7], [4, 5, 6, 8]], $source->toArray());
        self::assertSame($reference, $source->getPoint(0)->getSpatialReference());
        self::assertSame($otherReference, $source->getPoint(1)->getSpatialReference());
    }

    /** Xyzm geometry linestring copies retain each point's reference. */
    public function testXyzmGeometryLineStringPreservesPointReferences(): void
    {
        $reference = SpatialReference::epsg(4326);
        $otherReference = new SpatialReference(2154, 'LOCAL');
        $source = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\LineStringImmutable([
            new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\PointImmutable(1, 2, 3, 7, $reference),
            new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\PointImmutable(4, 5, 6, 8, $otherReference),
        ], $reference);

        $copy = $source->withPoint(0, Coordinates::xyzm(10, 20, 30, 70));

        self::assertSame($reference, $copy->getSpatialReference());
        self::assertSame($reference, $copy->getPoint(0)->getSpatialReference());
        self::assertSame($otherReference, $copy->getPoint(1)->getSpatialReference());
        self::assertSame(2154, $copy->getPoint(1)->getSrid());
        self::assertNotSame($source, $copy);
        self::assertNotSame($source->getPoint(0), $copy->getPoint(0));
        self::assertNotSame($source->getPoint(1), $copy->getPoint(1));
        self::assertSame([[10, 20, 30, 70], [4, 5, 6, 8]], $copy->toArray());
        self::assertSame([[1, 2, 3, 7], [4, 5, 6, 8]], $source->toArray());
        self::assertSame($reference, $source->getPoint(0)->getSpatialReference());
        self::assertSame($otherReference, $source->getPoint(1)->getSpatialReference());
    }

    /** Xyzm geometry multipoint copies retain each point's reference. */
    public function testXyzmGeometryMultiPointPreservesPointReferences(): void
    {
        $reference = SpatialReference::epsg(4326);
        $otherReference = new SpatialReference(2154, 'LOCAL');
        $source = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\MultiPointImmutable([
            new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\PointImmutable(1, 2, 3, 7, $reference),
            new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\PointImmutable(4, 5, 6, 8, $otherReference),
        ], $reference);

        $copy = $source->withPoint(0, Coordinates::xyzm(10, 20, 30, 70));

        self::assertSame($reference, $copy->getSpatialReference());
        self::assertSame($reference, $copy->getPoint(0)->getSpatialReference());
        self::assertSame($otherReference, $copy->getPoint(1)->getSpatialReference());
        self::assertSame(2154, $copy->getPoint(1)->getSrid());
        self::assertNotSame($source, $copy);
        self::assertNotSame($source->getPoint(0), $copy->getPoint(0));
        self::assertNotSame($source->getPoint(1), $copy->getPoint(1));
        self::assertSame([[10, 20, 30, 70], [4, 5, 6, 8]], $copy->toArray());
        self::assertSame([[1, 2, 3, 7], [4, 5, 6, 8]], $source->toArray());
        self::assertSame($reference, $source->getPoint(0)->getSpatialReference());
        self::assertSame($otherReference, $source->getPoint(1)->getSpatialReference());
    }
}
