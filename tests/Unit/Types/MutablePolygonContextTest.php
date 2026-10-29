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
        static::assertSame($compound, $compound->setFamily(SpatialModelEnum::GEOGRAPHY, false));
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $compound->getFamily());
        static::assertSame(SpatialModelEnum::GEOMETRY, $line->getFamily());
        static::assertSame(SpatialModelEnum::GEOMETRY, $arc->getFamily());
        static::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        static::assertSame(SpatialModelEnum::GEOMETRY, $owner->getRing(0)->getFamily());
        static::assertSame(SpatialModelEnum::GEOMETRY, $owner->getFamily());
        static::assertSame($line, $compound->getRing(0));
        static::assertSame($point, $arc->getPoint(0));
        static::assertSame([[[0, null]], [[0, null]]], $compound->toArray());
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
        static::assertSame($compound, $compound->setFamily(SpatialModelEnum::GEOGRAPHY));
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $compound->getFamily());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $line->getFamily());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $arc->getFamily());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $owner->getRing(0)->getFamily());
        static::assertSame(SpatialModelEnum::GEOMETRY, $owner->getFamily());
        static::assertSame($line, $compound->getRing(0));
        static::assertSame($point, $arc->getPoint(0));
        static::assertSame([[[0, null]], [[0, null]]], $compound->toArray());
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
        static::assertSame($compound, $compound->setSpatialReference($reference, false));
        static::assertSame($reference, $compound->getSpatialReference());
        static::assertSame($initial, $line->getSpatialReference());
        static::assertSame($initial, $arc->getSpatialReference());
        static::assertSame($initial, $point->getSpatialReference());
        static::assertSame($initial, $owner->getRing(0)->getSpatialReference());
        static::assertSame($initial, $owner->getSpatialReference());
        static::assertSame($line, $compound->getRing(0));
        static::assertSame($point, $arc->getPoint(0));
        static::assertSame([[[0, null]], [[0, null]]], $compound->toArray());
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
        static::assertSame($compound, $compound->setSpatialReference($reference));
        static::assertSame($reference, $compound->getSpatialReference());
        static::assertSame($reference, $line->getSpatialReference());
        static::assertSame($reference, $arc->getSpatialReference());
        static::assertSame($reference, $point->getSpatialReference());
        static::assertSame($reference, $owner->getRing(0)->getSpatialReference());
        static::assertSame($initial, $owner->getSpatialReference());
        static::assertSame($line, $compound->getRing(0));
        static::assertSame($point, $arc->getPoint(0));
        static::assertSame([[[0, null]], [[0, null]]], $compound->toArray());
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
        static::assertSame($compound, $compound->setSrid(3857, false));
        static::assertSame(3857, $compound->getSrid());
        static::assertSame(4326, $line->getSrid());
        static::assertSame(4326, $arc->getSrid());
        static::assertSame(4326, $point->getSrid());
        static::assertSame(4326, $owner->getRing(0)->getSrid());
        static::assertSame(4326, $owner->getSrid());
        static::assertSame($line, $compound->getRing(0));
        static::assertSame($point, $arc->getPoint(0));
        static::assertSame([[[0, null]], [[0, null]]], $compound->toArray());
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
        static::assertSame($compound, $compound->setSrid(3857));
        static::assertSame(3857, $compound->getSrid());
        static::assertSame(3857, $line->getSrid());
        static::assertSame(3857, $arc->getSrid());
        static::assertSame(3857, $point->getSrid());
        static::assertSame(3857, $owner->getRing(0)->getSrid());
        static::assertSame(4326, $owner->getSrid());
        static::assertSame($line, $compound->getRing(0));
        static::assertSame($point, $arc->getPoint(0));
        static::assertSame([[[0, null]], [[0, null]]], $compound->toArray());
    }
}
