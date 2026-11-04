<?php
/**
 * This file is part of the spatial project.
 *
 * PHP 8.4 | 8.5
 *
 * Copyright Alexandre Tranchant <alexandre.tranchant@gmail.com> 2026
 * Copyright Longitude One 2026
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

declare(strict_types=1);

namespace LongitudeOne\SpatialTypes\Tests\Issue;

use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Value\Coordinates;
use PHPUnit\Framework\TestCase;

/** Polyhedral surface replacement retains the reference of each unchanged descendant.
 *
 * {@link https://github.com/longitude-one/spatial-types/issues/77}
 *
 * @internal
 *
 * @coversNothing
 */
final class ImmutableSurfaceReferenceCopyTest extends TestCase
{
    /** Xyz geography patches preserve independent polygon, ring and point references. */
    public function testXyzGeographyPreservesPatchDescendants(): void
    {
        $reference = SpatialReference::epsg(4326);
        $patchReference = new SpatialReference(2154, 'LOCAL');
        $ringReference = new SpatialReference(99, 'RING');
        $pointReference = new SpatialReference(123, 'CUSTOM');
        $source = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\PolyhedralSurfaceImmutable([
            new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\PolygonImmutable([[[0, 0, 0], [4, 0, 0], [0, 4, 0], [0, 0, 0]]], $reference),
            new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\PolygonImmutable([
                new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\LineStringImmutable([
                    new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\PointImmutable(0, 0, 0, $pointReference),
                    new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\PointImmutable(4, 0, 0, $ringReference),
                    new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\PointImmutable(0, 4, 0, $ringReference),
                    new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\PointImmutable(0, 0, 0, $pointReference),
                ], $ringReference),
            ], $patchReference),
        ], $reference);

        $pointCopy = $source->withPoint(0, 0, 1, Coordinates::xyz(5, 0, 0));
        $ringCopy = $source->withRing(0, 0, [[0, 0, 0], [5, 0, 0], [0, 4, 0], [0, 0, 0]]);
        $patchCopy = $source->withPatch(0, [[[0, 0, 0], [5, 0, 0], [0, 4, 0], [0, 0, 0]]]);

        static::assertSame($reference, $pointCopy->getSpatialReference());
        static::assertSame($patchReference, $pointCopy->getPatch(1)->getSpatialReference());
        static::assertSame($ringReference, $pointCopy->getPatch(1)->getRing(0)->getSpatialReference());
        static::assertSame($pointReference, $pointCopy->getPatch(1)->getRing(0)->getPoint(0)->getSpatialReference());
        static::assertSame($pointReference, $ringCopy->getPatch(1)->getRing(0)->getPoint(0)->getSpatialReference());
        static::assertSame($patchReference, $patchCopy->getPatch(1)->getSpatialReference());
        static::assertSame($ringReference, $patchCopy->getPatch(1)->getRing(0)->getSpatialReference());
        static::assertSame($pointReference, $patchCopy->getPatch(1)->getRing(0)->getPoint(0)->getSpatialReference());
        static::assertNotSame($source->getPatch(1), $pointCopy->getPatch(1));
        static::assertNotSame($source->getPatch(1)->getRing(0), $ringCopy->getPatch(1)->getRing(0));
        static::assertNotSame($source->getPatch(1)->getRing(0)->getPoint(0), $patchCopy->getPatch(1)->getRing(0)->getPoint(0));
        static::assertSame([[[[0, 0, 0], [5, 0, 0], [0, 4, 0], [0, 0, 0]]], [[[0, 0, 0], [4, 0, 0], [0, 4, 0], [0, 0, 0]]]], $pointCopy->toArray());
        static::assertSame([[[[0, 0, 0], [5, 0, 0], [0, 4, 0], [0, 0, 0]]], [[[0, 0, 0], [4, 0, 0], [0, 4, 0], [0, 0, 0]]]], $ringCopy->toArray());
        static::assertSame([[[[0, 0, 0], [5, 0, 0], [0, 4, 0], [0, 0, 0]]], [[[0, 0, 0], [4, 0, 0], [0, 4, 0], [0, 0, 0]]]], $patchCopy->toArray());
        static::assertSame([[[[0, 0, 0], [4, 0, 0], [0, 4, 0], [0, 0, 0]]], [[[0, 0, 0], [4, 0, 0], [0, 4, 0], [0, 0, 0]]]], $source->toArray());
    }

    /** Xyz geometry patches preserve independent polygon, ring and point references. */
    public function testXyzGeometryPreservesPatchDescendants(): void
    {
        $reference = SpatialReference::epsg(4326);
        $patchReference = new SpatialReference(2154, 'LOCAL');
        $ringReference = new SpatialReference(99, 'RING');
        $pointReference = new SpatialReference(123, 'CUSTOM');
        $source = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\PolyhedralSurfaceImmutable([
            new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\PolygonImmutable([[[0, 0, 0], [4, 0, 0], [0, 4, 0], [0, 0, 0]]], $reference),
            new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\PolygonImmutable([
                new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\LineStringImmutable([
                    new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\PointImmutable(0, 0, 0, $pointReference),
                    new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\PointImmutable(4, 0, 0, $ringReference),
                    new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\PointImmutable(0, 4, 0, $ringReference),
                    new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\PointImmutable(0, 0, 0, $pointReference),
                ], $ringReference),
            ], $patchReference),
        ], $reference);

        $pointCopy = $source->withPoint(0, 0, 1, Coordinates::xyz(5, 0, 0));
        $ringCopy = $source->withRing(0, 0, [[0, 0, 0], [5, 0, 0], [0, 4, 0], [0, 0, 0]]);
        $patchCopy = $source->withPatch(0, [[[0, 0, 0], [5, 0, 0], [0, 4, 0], [0, 0, 0]]]);

        static::assertSame($reference, $pointCopy->getSpatialReference());
        static::assertSame($patchReference, $pointCopy->getPatch(1)->getSpatialReference());
        static::assertSame($ringReference, $pointCopy->getPatch(1)->getRing(0)->getSpatialReference());
        static::assertSame($pointReference, $pointCopy->getPatch(1)->getRing(0)->getPoint(0)->getSpatialReference());
        static::assertSame($pointReference, $ringCopy->getPatch(1)->getRing(0)->getPoint(0)->getSpatialReference());
        static::assertSame($patchReference, $patchCopy->getPatch(1)->getSpatialReference());
        static::assertSame($ringReference, $patchCopy->getPatch(1)->getRing(0)->getSpatialReference());
        static::assertSame($pointReference, $patchCopy->getPatch(1)->getRing(0)->getPoint(0)->getSpatialReference());
        static::assertNotSame($source->getPatch(1), $pointCopy->getPatch(1));
        static::assertNotSame($source->getPatch(1)->getRing(0), $ringCopy->getPatch(1)->getRing(0));
        static::assertNotSame($source->getPatch(1)->getRing(0)->getPoint(0), $patchCopy->getPatch(1)->getRing(0)->getPoint(0));
        static::assertSame([[[[0, 0, 0], [5, 0, 0], [0, 4, 0], [0, 0, 0]]], [[[0, 0, 0], [4, 0, 0], [0, 4, 0], [0, 0, 0]]]], $pointCopy->toArray());
        static::assertSame([[[[0, 0, 0], [5, 0, 0], [0, 4, 0], [0, 0, 0]]], [[[0, 0, 0], [4, 0, 0], [0, 4, 0], [0, 0, 0]]]], $ringCopy->toArray());
        static::assertSame([[[[0, 0, 0], [5, 0, 0], [0, 4, 0], [0, 0, 0]]], [[[0, 0, 0], [4, 0, 0], [0, 4, 0], [0, 0, 0]]]], $patchCopy->toArray());
        static::assertSame([[[[0, 0, 0], [4, 0, 0], [0, 4, 0], [0, 0, 0]]], [[[0, 0, 0], [4, 0, 0], [0, 4, 0], [0, 0, 0]]]], $source->toArray());
    }

    /** Xyzm geography patches preserve independent polygon, ring and point references. */
    public function testXyzmGeographyPreservesPatchDescendants(): void
    {
        $reference = SpatialReference::epsg(4326);
        $patchReference = new SpatialReference(2154, 'LOCAL');
        $ringReference = new SpatialReference(99, 'RING');
        $pointReference = new SpatialReference(123, 'CUSTOM');
        $source = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\PolyhedralSurfaceImmutable([
            new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\PolygonImmutable([[[0, 0, 0, 7], [4, 0, 0, 7], [0, 4, 0, 7], [0, 0, 0, 7]]], $reference),
            new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\PolygonImmutable([
                new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\LineStringImmutable([
                    new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\PointImmutable(0, 0, 0, 7, $pointReference),
                    new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\PointImmutable(4, 0, 0, 7, $ringReference),
                    new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\PointImmutable(0, 4, 0, 7, $ringReference),
                    new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\PointImmutable(0, 0, 0, 7, $pointReference),
                ], $ringReference),
            ], $patchReference),
        ], $reference);

        $pointCopy = $source->withPoint(0, 0, 1, Coordinates::xyzm(5, 0, 0, 7));
        $ringCopy = $source->withRing(0, 0, [[0, 0, 0, 7], [5, 0, 0, 7], [0, 4, 0, 7], [0, 0, 0, 7]]);
        $patchCopy = $source->withPatch(0, [[[0, 0, 0, 7], [5, 0, 0, 7], [0, 4, 0, 7], [0, 0, 0, 7]]]);

        static::assertSame($reference, $pointCopy->getSpatialReference());
        static::assertSame($patchReference, $pointCopy->getPatch(1)->getSpatialReference());
        static::assertSame($ringReference, $pointCopy->getPatch(1)->getRing(0)->getSpatialReference());
        static::assertSame($pointReference, $pointCopy->getPatch(1)->getRing(0)->getPoint(0)->getSpatialReference());
        static::assertSame($pointReference, $ringCopy->getPatch(1)->getRing(0)->getPoint(0)->getSpatialReference());
        static::assertSame($patchReference, $patchCopy->getPatch(1)->getSpatialReference());
        static::assertSame($ringReference, $patchCopy->getPatch(1)->getRing(0)->getSpatialReference());
        static::assertSame($pointReference, $patchCopy->getPatch(1)->getRing(0)->getPoint(0)->getSpatialReference());
        static::assertNotSame($source->getPatch(1), $pointCopy->getPatch(1));
        static::assertNotSame($source->getPatch(1)->getRing(0), $ringCopy->getPatch(1)->getRing(0));
        static::assertNotSame($source->getPatch(1)->getRing(0)->getPoint(0), $patchCopy->getPatch(1)->getRing(0)->getPoint(0));
        static::assertSame([[[[0, 0, 0, 7], [5, 0, 0, 7], [0, 4, 0, 7], [0, 0, 0, 7]]], [[[0, 0, 0, 7], [4, 0, 0, 7], [0, 4, 0, 7], [0, 0, 0, 7]]]], $pointCopy->toArray());
        static::assertSame([[[[0, 0, 0, 7], [5, 0, 0, 7], [0, 4, 0, 7], [0, 0, 0, 7]]], [[[0, 0, 0, 7], [4, 0, 0, 7], [0, 4, 0, 7], [0, 0, 0, 7]]]], $ringCopy->toArray());
        static::assertSame([[[[0, 0, 0, 7], [5, 0, 0, 7], [0, 4, 0, 7], [0, 0, 0, 7]]], [[[0, 0, 0, 7], [4, 0, 0, 7], [0, 4, 0, 7], [0, 0, 0, 7]]]], $patchCopy->toArray());
        static::assertSame([[[[0, 0, 0, 7], [4, 0, 0, 7], [0, 4, 0, 7], [0, 0, 0, 7]]], [[[0, 0, 0, 7], [4, 0, 0, 7], [0, 4, 0, 7], [0, 0, 0, 7]]]], $source->toArray());
    }

    /** Xyzm geometry patches preserve independent polygon, ring and point references. */
    public function testXyzmGeometryPreservesPatchDescendants(): void
    {
        $reference = SpatialReference::epsg(4326);
        $patchReference = new SpatialReference(2154, 'LOCAL');
        $ringReference = new SpatialReference(99, 'RING');
        $pointReference = new SpatialReference(123, 'CUSTOM');
        $source = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\PolyhedralSurfaceImmutable([
            new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\PolygonImmutable([[[0, 0, 0, 7], [4, 0, 0, 7], [0, 4, 0, 7], [0, 0, 0, 7]]], $reference),
            new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\PolygonImmutable([
                new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\LineStringImmutable([
                    new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\PointImmutable(0, 0, 0, 7, $pointReference),
                    new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\PointImmutable(4, 0, 0, 7, $ringReference),
                    new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\PointImmutable(0, 4, 0, 7, $ringReference),
                    new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\PointImmutable(0, 0, 0, 7, $pointReference),
                ], $ringReference),
            ], $patchReference),
        ], $reference);

        $pointCopy = $source->withPoint(0, 0, 1, Coordinates::xyzm(5, 0, 0, 7));
        $ringCopy = $source->withRing(0, 0, [[0, 0, 0, 7], [5, 0, 0, 7], [0, 4, 0, 7], [0, 0, 0, 7]]);
        $patchCopy = $source->withPatch(0, [[[0, 0, 0, 7], [5, 0, 0, 7], [0, 4, 0, 7], [0, 0, 0, 7]]]);

        static::assertSame($reference, $pointCopy->getSpatialReference());
        static::assertSame($patchReference, $pointCopy->getPatch(1)->getSpatialReference());
        static::assertSame($ringReference, $pointCopy->getPatch(1)->getRing(0)->getSpatialReference());
        static::assertSame($pointReference, $pointCopy->getPatch(1)->getRing(0)->getPoint(0)->getSpatialReference());
        static::assertSame($pointReference, $ringCopy->getPatch(1)->getRing(0)->getPoint(0)->getSpatialReference());
        static::assertSame($patchReference, $patchCopy->getPatch(1)->getSpatialReference());
        static::assertSame($ringReference, $patchCopy->getPatch(1)->getRing(0)->getSpatialReference());
        static::assertSame($pointReference, $patchCopy->getPatch(1)->getRing(0)->getPoint(0)->getSpatialReference());
        static::assertNotSame($source->getPatch(1), $pointCopy->getPatch(1));
        static::assertNotSame($source->getPatch(1)->getRing(0), $ringCopy->getPatch(1)->getRing(0));
        static::assertNotSame($source->getPatch(1)->getRing(0)->getPoint(0), $patchCopy->getPatch(1)->getRing(0)->getPoint(0));
        static::assertSame([[[[0, 0, 0, 7], [5, 0, 0, 7], [0, 4, 0, 7], [0, 0, 0, 7]]], [[[0, 0, 0, 7], [4, 0, 0, 7], [0, 4, 0, 7], [0, 0, 0, 7]]]], $pointCopy->toArray());
        static::assertSame([[[[0, 0, 0, 7], [5, 0, 0, 7], [0, 4, 0, 7], [0, 0, 0, 7]]], [[[0, 0, 0, 7], [4, 0, 0, 7], [0, 4, 0, 7], [0, 0, 0, 7]]]], $ringCopy->toArray());
        static::assertSame([[[[0, 0, 0, 7], [5, 0, 0, 7], [0, 4, 0, 7], [0, 0, 0, 7]]], [[[0, 0, 0, 7], [4, 0, 0, 7], [0, 4, 0, 7], [0, 0, 0, 7]]]], $patchCopy->toArray());
        static::assertSame([[[[0, 0, 0, 7], [4, 0, 0, 7], [0, 4, 0, 7], [0, 0, 0, 7]]], [[[0, 0, 0, 7], [4, 0, 0, 7], [0, 4, 0, 7], [0, 0, 0, 7]]]], $source->toArray());
    }
}
