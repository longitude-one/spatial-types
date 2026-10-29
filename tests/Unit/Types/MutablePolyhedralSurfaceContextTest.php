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

use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Types\Dimension3z\LineString;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Point;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Polygon;
use LongitudeOne\SpatialTypes\Types\Dimension3z\PolyhedralSurface;
use PHPUnit\Framework\TestCase;

/** Recursive surface context preserves shared component identity and coordinates.
 * @internal
 *
 * @coversNothing
 */
class MutablePolyhedralSurfaceContextTest extends TestCase
{
    /** Declared context edits retain coordinates through every patch, ring and point. */
    public function testFamilyLocalOnly(): void
    {
        $initial = SpatialReference::epsg(4326);
        $point = new Point(0, null, 8, $initial);
        $firstRing = new LineString([$point], $initial);
        $secondRing = new LineString([$point], $initial);
        $first = new Polygon([$firstRing], $initial);
        $second = new Polygon([$secondRing], $initial);
        $surface = new PolyhedralSurface([$first, $second], $initial);
        $owner = new PolyhedralSurface([$first], $initial);
        static::assertSame($surface, $surface->setFamily(SpatialModelEnum::GEOGRAPHY, false));
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $surface->getFamily());
        static::assertSame(SpatialModelEnum::GEOMETRY, $first->getFamily());
        static::assertSame(SpatialModelEnum::GEOMETRY, $second->getFamily());
        static::assertSame(SpatialModelEnum::GEOMETRY, $firstRing->getFamily());
        static::assertSame(SpatialModelEnum::GEOMETRY, $secondRing->getFamily());
        static::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        static::assertSame(SpatialModelEnum::GEOMETRY, $owner->getFamily());
        static::assertSame($first, $surface->getPatch(0));
        static::assertSame($first, $owner->getPatch(0));
        static::assertSame($point, $second->getRing(0)->getPoint(0));
        static::assertSame([[[[0, null, 8]]], [[[0, null, 8]]]], $surface->toArray());
        static::assertSame([[[[0, null, 8]]]], $owner->toArray());
    }

    /** Declared context edits retain coordinates through every patch, ring and point. */
    public function testFamilyPropagation(): void
    {
        $initial = SpatialReference::epsg(4326);
        $point = new Point(0, null, 8, $initial);
        $firstRing = new LineString([$point], $initial);
        $secondRing = new LineString([$point], $initial);
        $first = new Polygon([$firstRing], $initial);
        $second = new Polygon([$secondRing], $initial);
        $surface = new PolyhedralSurface([$first, $second], $initial);
        $owner = new PolyhedralSurface([$first], $initial);
        static::assertSame($surface, $surface->setFamily(SpatialModelEnum::GEOGRAPHY));
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $surface->getFamily());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $first->getFamily());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $second->getFamily());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $firstRing->getFamily());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $secondRing->getFamily());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame(SpatialModelEnum::GEOMETRY, $owner->getFamily());
        static::assertSame($first, $surface->getPatch(0));
        static::assertSame($first, $owner->getPatch(0));
        static::assertSame($point, $second->getRing(0)->getPoint(0));
        static::assertSame([[[[0, null, 8]]], [[[0, null, 8]]]], $surface->toArray());
        static::assertSame([[[[0, null, 8]]]], $owner->toArray());
    }

    /** Declared context edits retain coordinates through every patch, ring and point. */
    public function testReferenceLocalOnly(): void
    {
        $initial = SpatialReference::epsg(4326);
        $reference = SpatialReference::epsg(2154);
        $point = new Point(0, null, 8, $initial);
        $firstRing = new LineString([$point], $initial);
        $secondRing = new LineString([$point], $initial);
        $first = new Polygon([$firstRing], $initial);
        $second = new Polygon([$secondRing], $initial);
        $surface = new PolyhedralSurface([$first, $second], $initial);
        $owner = new PolyhedralSurface([$first], $initial);
        static::assertSame($surface, $surface->setSpatialReference($reference, false));
        static::assertSame($reference, $surface->getSpatialReference());
        static::assertSame($initial, $first->getSpatialReference());
        static::assertSame($initial, $second->getSpatialReference());
        static::assertSame($initial, $firstRing->getSpatialReference());
        static::assertSame($initial, $secondRing->getSpatialReference());
        static::assertSame($initial, $point->getSpatialReference());
        static::assertSame($initial, $owner->getSpatialReference());
        static::assertSame($first, $surface->getPatch(0));
        static::assertSame($first, $owner->getPatch(0));
        static::assertSame($point, $second->getRing(0)->getPoint(0));
        static::assertSame([[[[0, null, 8]]], [[[0, null, 8]]]], $surface->toArray());
        static::assertSame([[[[0, null, 8]]]], $owner->toArray());
    }

    /** Declared context edits retain coordinates through every patch, ring and point. */
    public function testReferencePropagation(): void
    {
        $initial = SpatialReference::epsg(4326);
        $reference = SpatialReference::epsg(2154);
        $point = new Point(0, null, 8, $initial);
        $firstRing = new LineString([$point], $initial);
        $secondRing = new LineString([$point], $initial);
        $first = new Polygon([$firstRing], $initial);
        $second = new Polygon([$secondRing], $initial);
        $surface = new PolyhedralSurface([$first, $second], $initial);
        $owner = new PolyhedralSurface([$first], $initial);
        static::assertSame($surface, $surface->setSpatialReference($reference));
        static::assertSame($reference, $surface->getSpatialReference());
        static::assertSame($reference, $first->getSpatialReference());
        static::assertSame($reference, $second->getSpatialReference());
        static::assertSame($reference, $firstRing->getSpatialReference());
        static::assertSame($reference, $secondRing->getSpatialReference());
        static::assertSame($reference, $point->getSpatialReference());
        static::assertSame($initial, $owner->getSpatialReference());
        static::assertSame($first, $surface->getPatch(0));
        static::assertSame($first, $owner->getPatch(0));
        static::assertSame($point, $second->getRing(0)->getPoint(0));
        static::assertSame([[[[0, null, 8]]], [[[0, null, 8]]]], $surface->toArray());
        static::assertSame([[[[0, null, 8]]]], $owner->toArray());
    }

    /** Declared context edits retain coordinates through every patch, ring and point. */
    public function testSridLocalOnly(): void
    {
        $initial = SpatialReference::epsg(4326);
        $point = new Point(0, null, 8, $initial);
        $firstRing = new LineString([$point], $initial);
        $secondRing = new LineString([$point], $initial);
        $first = new Polygon([$firstRing], $initial);
        $second = new Polygon([$secondRing], $initial);
        $surface = new PolyhedralSurface([$first, $second], $initial);
        $owner = new PolyhedralSurface([$first], $initial);
        static::assertSame($surface, $surface->setSrid(2154, false));
        static::assertSame(2154, $surface->getSrid());
        static::assertSame(4326, $first->getSrid());
        static::assertSame(4326, $second->getSrid());
        static::assertSame(4326, $firstRing->getSrid());
        static::assertSame(4326, $secondRing->getSrid());
        static::assertSame(4326, $point->getSrid());
        static::assertSame(4326, $owner->getSrid());
        static::assertSame($first, $surface->getPatch(0));
        static::assertSame($first, $owner->getPatch(0));
        static::assertSame($point, $second->getRing(0)->getPoint(0));
        static::assertSame([[[[0, null, 8]]], [[[0, null, 8]]]], $surface->toArray());
        static::assertSame([[[[0, null, 8]]]], $owner->toArray());
    }

    /** Declared context edits retain coordinates through every patch, ring and point. */
    public function testSridPropagation(): void
    {
        $initial = SpatialReference::epsg(4326);
        $point = new Point(0, null, 8, $initial);
        $firstRing = new LineString([$point], $initial);
        $secondRing = new LineString([$point], $initial);
        $first = new Polygon([$firstRing], $initial);
        $second = new Polygon([$secondRing], $initial);
        $surface = new PolyhedralSurface([$first, $second], $initial);
        $owner = new PolyhedralSurface([$first], $initial);
        static::assertSame($surface, $surface->setSrid(2154));
        static::assertSame(2154, $surface->getSrid());
        static::assertSame(2154, $first->getSrid());
        static::assertSame(2154, $second->getSrid());
        static::assertSame(2154, $firstRing->getSrid());
        static::assertSame(2154, $secondRing->getSrid());
        static::assertSame(2154, $point->getSrid());
        static::assertSame(4326, $owner->getSrid());
        static::assertSame($first, $surface->getPatch(0));
        static::assertSame($first, $owner->getPatch(0));
        static::assertSame($point, $second->getRing(0)->getPoint(0));
        static::assertSame([[[[0, null, 8]]], [[[0, null, 8]]]], $surface->toArray());
        static::assertSame([[[[0, null, 8]]]], $owner->toArray());
    }
}
