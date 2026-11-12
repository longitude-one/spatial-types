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
use LongitudeOne\SpatialTypes\Types\Dimension2\LineString;
use LongitudeOne\SpatialTypes\Types\Dimension2\Point;
use LongitudeOne\SpatialTypes\Types\Dimension2\Polygon;
use PHPUnit\Framework\TestCase;

/** Recursive polygon context and shared ownership.
 * @internal
 *
 * @coversNothing
 */
class MutablePolygonContextTest extends TestCase
{
    /** Declared context preserves coordinates and shared component identity. */
    public function testFamilyLocalOnly(): void
    {
        $initial = SpatialReference::epsg(4326);
        $point = new Point(0, null, $initial);
        $line = new LineString([$point], $initial);
        $arc = new LineString([$point], $initial);
        $compound = new Polygon([$line, $arc], $initial);
        $owner = new Polygon([$line], $initial);
        self::assertSame($compound, $compound->setFamily(SpatialModelEnum::GEOGRAPHY, false));
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $compound->getFamily());
        self::assertSame(SpatialModelEnum::GEOMETRY, $line->getFamily());
        self::assertSame(SpatialModelEnum::GEOMETRY, $arc->getFamily());
        self::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        self::assertSame(SpatialModelEnum::GEOMETRY, $owner->getRing(0)->getFamily());
        self::assertSame(SpatialModelEnum::GEOMETRY, $owner->getFamily());
        self::assertSame($line, $compound->getRing(0));
        self::assertSame($point, $arc->getPoint(0));
        self::assertSame([[[0, null]], [[0, null]]], $compound->toArray());
    }

    /** Declared context preserves coordinates and shared component identity. */
    public function testFamilyPropagation(): void
    {
        $initial = SpatialReference::epsg(4326);
        $point = new Point(0, null, $initial);
        $line = new LineString([$point], $initial);
        $arc = new LineString([$point], $initial);
        $compound = new Polygon([$line, $arc], $initial);
        $owner = new Polygon([$line], $initial);
        self::assertSame($compound, $compound->setFamily(SpatialModelEnum::GEOGRAPHY));
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $compound->getFamily());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $line->getFamily());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $arc->getFamily());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $owner->getRing(0)->getFamily());
        self::assertSame(SpatialModelEnum::GEOMETRY, $owner->getFamily());
        self::assertSame($line, $compound->getRing(0));
        self::assertSame($point, $arc->getPoint(0));
        self::assertSame([[[0, null]], [[0, null]]], $compound->toArray());
    }

    /** Declared context preserves coordinates and shared component identity. */
    public function testReferenceLocalOnly(): void
    {
        $initial = SpatialReference::epsg(4326);
        $reference = SpatialReference::epsg(2154);
        $point = new Point(0, null, $initial);
        $line = new LineString([$point], $initial);
        $arc = new LineString([$point], $initial);
        $compound = new Polygon([$line, $arc], $initial);
        $owner = new Polygon([$line], $initial);
        self::assertSame($compound, $compound->setSpatialReference($reference, false));
        self::assertSame($reference, $compound->getSpatialReference());
        self::assertSame($initial, $line->getSpatialReference());
        self::assertSame($initial, $arc->getSpatialReference());
        self::assertSame($initial, $point->getSpatialReference());
        self::assertSame($initial, $owner->getRing(0)->getSpatialReference());
        self::assertSame($initial, $owner->getSpatialReference());
        self::assertSame($line, $compound->getRing(0));
        self::assertSame($point, $arc->getPoint(0));
        self::assertSame([[[0, null]], [[0, null]]], $compound->toArray());
    }

    /** Declared context preserves coordinates and shared component identity. */
    public function testReferencePropagation(): void
    {
        $initial = SpatialReference::epsg(4326);
        $reference = SpatialReference::epsg(2154);
        $point = new Point(0, null, $initial);
        $line = new LineString([$point], $initial);
        $arc = new LineString([$point], $initial);
        $compound = new Polygon([$line, $arc], $initial);
        $owner = new Polygon([$line], $initial);
        self::assertSame($compound, $compound->setSpatialReference($reference));
        self::assertSame($reference, $compound->getSpatialReference());
        self::assertSame($reference, $line->getSpatialReference());
        self::assertSame($reference, $arc->getSpatialReference());
        self::assertSame($reference, $point->getSpatialReference());
        self::assertSame($reference, $owner->getRing(0)->getSpatialReference());
        self::assertSame($initial, $owner->getSpatialReference());
        self::assertSame($line, $compound->getRing(0));
        self::assertSame($point, $arc->getPoint(0));
        self::assertSame([[[0, null]], [[0, null]]], $compound->toArray());
    }

    /** Declared context preserves coordinates and shared component identity. */
    public function testSridLocalOnly(): void
    {
        $initial = SpatialReference::epsg(4326);
        $point = new Point(0, null, $initial);
        $line = new LineString([$point], $initial);
        $arc = new LineString([$point], $initial);
        $compound = new Polygon([$line, $arc], $initial);
        $owner = new Polygon([$line], $initial);
        self::assertSame($compound, $compound->setSrid(3857, false));
        self::assertSame(3857, $compound->getSrid());
        self::assertSame(4326, $line->getSrid());
        self::assertSame(4326, $arc->getSrid());
        self::assertSame(4326, $point->getSrid());
        self::assertSame(4326, $owner->getRing(0)->getSrid());
        self::assertSame(4326, $owner->getSrid());
        self::assertSame($line, $compound->getRing(0));
        self::assertSame($point, $arc->getPoint(0));
        self::assertSame([[[0, null]], [[0, null]]], $compound->toArray());
    }

    /** Declared context preserves coordinates and shared component identity. */
    public function testSridPropagation(): void
    {
        $initial = SpatialReference::epsg(4326);
        $point = new Point(0, null, $initial);
        $line = new LineString([$point], $initial);
        $arc = new LineString([$point], $initial);
        $compound = new Polygon([$line, $arc], $initial);
        $owner = new Polygon([$line], $initial);
        self::assertSame($compound, $compound->setSrid(3857));
        self::assertSame(3857, $compound->getSrid());
        self::assertSame(3857, $line->getSrid());
        self::assertSame(3857, $arc->getSrid());
        self::assertSame(3857, $point->getSrid());
        self::assertSame(3857, $owner->getRing(0)->getSrid());
        self::assertSame(4326, $owner->getSrid());
        self::assertSame($line, $compound->getRing(0));
        self::assertSame($point, $arc->getPoint(0));
        self::assertSame([[[0, null]], [[0, null]]], $compound->toArray());
    }
}
