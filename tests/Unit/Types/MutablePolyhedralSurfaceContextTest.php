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
        self::assertSame($surface, $surface->setFamily(SpatialModelEnum::GEOGRAPHY, false));
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $surface->getFamily());
        self::assertSame(SpatialModelEnum::GEOMETRY, $first->getFamily());
        self::assertSame(SpatialModelEnum::GEOMETRY, $second->getFamily());
        self::assertSame(SpatialModelEnum::GEOMETRY, $firstRing->getFamily());
        self::assertSame(SpatialModelEnum::GEOMETRY, $secondRing->getFamily());
        self::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        self::assertSame(SpatialModelEnum::GEOMETRY, $owner->getFamily());
        self::assertSame($first, $surface->getPatch(0));
        self::assertSame($first, $owner->getPatch(0));
        self::assertSame($point, $second->getRing(0)->getPoint(0));
        self::assertSame([[[[0, null, 8]]], [[[0, null, 8]]]], $surface->toArray());
        self::assertSame([[[[0, null, 8]]]], $owner->toArray());
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
        self::assertSame($surface, $surface->setFamily(SpatialModelEnum::GEOGRAPHY));
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $surface->getFamily());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $first->getFamily());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $second->getFamily());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $firstRing->getFamily());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $secondRing->getFamily());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        self::assertSame(SpatialModelEnum::GEOMETRY, $owner->getFamily());
        self::assertSame($first, $surface->getPatch(0));
        self::assertSame($first, $owner->getPatch(0));
        self::assertSame($point, $second->getRing(0)->getPoint(0));
        self::assertSame([[[[0, null, 8]]], [[[0, null, 8]]]], $surface->toArray());
        self::assertSame([[[[0, null, 8]]]], $owner->toArray());
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
        self::assertSame($surface, $surface->setSpatialReference($reference, false));
        self::assertSame($reference, $surface->getSpatialReference());
        self::assertSame($initial, $first->getSpatialReference());
        self::assertSame($initial, $second->getSpatialReference());
        self::assertSame($initial, $firstRing->getSpatialReference());
        self::assertSame($initial, $secondRing->getSpatialReference());
        self::assertSame($initial, $point->getSpatialReference());
        self::assertSame($initial, $owner->getSpatialReference());
        self::assertSame($first, $surface->getPatch(0));
        self::assertSame($first, $owner->getPatch(0));
        self::assertSame($point, $second->getRing(0)->getPoint(0));
        self::assertSame([[[[0, null, 8]]], [[[0, null, 8]]]], $surface->toArray());
        self::assertSame([[[[0, null, 8]]]], $owner->toArray());
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
        self::assertSame($surface, $surface->setSpatialReference($reference));
        self::assertSame($reference, $surface->getSpatialReference());
        self::assertSame($reference, $first->getSpatialReference());
        self::assertSame($reference, $second->getSpatialReference());
        self::assertSame($reference, $firstRing->getSpatialReference());
        self::assertSame($reference, $secondRing->getSpatialReference());
        self::assertSame($reference, $point->getSpatialReference());
        self::assertSame($initial, $owner->getSpatialReference());
        self::assertSame($first, $surface->getPatch(0));
        self::assertSame($first, $owner->getPatch(0));
        self::assertSame($point, $second->getRing(0)->getPoint(0));
        self::assertSame([[[[0, null, 8]]], [[[0, null, 8]]]], $surface->toArray());
        self::assertSame([[[[0, null, 8]]]], $owner->toArray());
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
        self::assertSame($surface, $surface->setSrid(2154, false));
        self::assertSame(2154, $surface->getSrid());
        self::assertSame(4326, $first->getSrid());
        self::assertSame(4326, $second->getSrid());
        self::assertSame(4326, $firstRing->getSrid());
        self::assertSame(4326, $secondRing->getSrid());
        self::assertSame(4326, $point->getSrid());
        self::assertSame(4326, $owner->getSrid());
        self::assertSame($first, $surface->getPatch(0));
        self::assertSame($first, $owner->getPatch(0));
        self::assertSame($point, $second->getRing(0)->getPoint(0));
        self::assertSame([[[[0, null, 8]]], [[[0, null, 8]]]], $surface->toArray());
        self::assertSame([[[[0, null, 8]]]], $owner->toArray());
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
        self::assertSame($surface, $surface->setSrid(2154));
        self::assertSame(2154, $surface->getSrid());
        self::assertSame(2154, $first->getSrid());
        self::assertSame(2154, $second->getSrid());
        self::assertSame(2154, $firstRing->getSrid());
        self::assertSame(2154, $secondRing->getSrid());
        self::assertSame(2154, $point->getSrid());
        self::assertSame(4326, $owner->getSrid());
        self::assertSame($first, $surface->getPatch(0));
        self::assertSame($first, $owner->getPatch(0));
        self::assertSame($point, $second->getRing(0)->getPoint(0));
        self::assertSame([[[[0, null, 8]]], [[[0, null, 8]]]], $surface->toArray());
        self::assertSame([[[[0, null, 8]]]], $owner->toArray());
    }
}
