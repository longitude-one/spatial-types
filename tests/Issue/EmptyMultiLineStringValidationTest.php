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

namespace LongitudeOne\SpatialTypes\Tests\Issue;

use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Exception\InvalidFamilyException;
use LongitudeOne\SpatialTypes\Exception\InvalidSridException;
use LongitudeOne\SpatialTypes\Exception\OutOfBoundsException;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineString;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiLineString;
use PHPUnit\Framework\TestCase;

/**
 * Issue #9: empty members retain validation and reference identity.
 *
 * @internal
 *
 * @coversNothing
 */
class EmptyMultiLineStringValidationTest extends TestCase
{
    /** Empty geography members must match the receiving dimension. */
    public function testDimension2GeographyRejectsWrongDimension(): void
    {
        $this->expectException(InvalidDimensionException::class);

        new \LongitudeOne\SpatialTypes\Types\Dimension2\Geography\MultiLineString([new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\LineString([], 4326)], 4326);
    }

    /** Empty geography members must match the receiving family. */
    public function testDimension2GeographyRejectsWrongFamily(): void
    {
        $this->expectException(InvalidFamilyException::class);

        new \LongitudeOne\SpatialTypes\Types\Dimension2\Geography\MultiLineString([new LineString([], 4326)], 4326);
    }

    /** Empty geography members must match the receiving srid. */
    public function testDimension2GeographyRejectsWrongSrid(): void
    {
        $this->expectException(InvalidSridException::class);

        new \LongitudeOne\SpatialTypes\Types\Dimension2\Geography\MultiLineString([new \LongitudeOne\SpatialTypes\Types\Dimension2\Geography\LineString([], 2154)], 4326);
    }

    /** Empty geometry members must match the receiving dimension. */
    public function testDimension2GeometryRejectsWrongDimension(): void
    {
        $this->expectException(InvalidDimensionException::class);

        new MultiLineString([new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\LineString([], 4326)], 4326);
    }

    /** Empty geometry members must match the receiving family. */
    public function testDimension2GeometryRejectsWrongFamily(): void
    {
        $this->expectException(InvalidFamilyException::class);

        new MultiLineString([new \LongitudeOne\SpatialTypes\Types\Dimension2\Geography\LineString([], 4326)], 4326);
    }

    /** Empty geometry members must match the receiving srid. */
    public function testDimension2GeometryRejectsWrongSrid(): void
    {
        $this->expectException(InvalidSridException::class);

        new MultiLineString([new LineString([], 2154)], 4326);
    }

    /** Empty geography members must match the receiving dimension. */
    public function testDimension3mGeographyRejectsWrongDimension(): void
    {
        $this->expectException(InvalidDimensionException::class);

        new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\MultiLineString([new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\LineString([], 4326)], 4326);
    }

    /** Empty geography members must match the receiving family. */
    public function testDimension3mGeographyRejectsWrongFamily(): void
    {
        $this->expectException(InvalidFamilyException::class);

        new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\MultiLineString([new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\LineString([], 4326)], 4326);
    }

    /** Empty geography members must match the receiving srid. */
    public function testDimension3mGeographyRejectsWrongSrid(): void
    {
        $this->expectException(InvalidSridException::class);

        new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\MultiLineString([new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\LineString([], 2154)], 4326);
    }

    /** Empty geometry members must match the receiving dimension. */
    public function testDimension3mGeometryRejectsWrongDimension(): void
    {
        $this->expectException(InvalidDimensionException::class);

        new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\MultiLineString([new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\LineString([], 4326)], 4326);
    }

    /** Empty geometry members must match the receiving family. */
    public function testDimension3mGeometryRejectsWrongFamily(): void
    {
        $this->expectException(InvalidFamilyException::class);

        new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\MultiLineString([new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\LineString([], 4326)], 4326);
    }

    /** Empty geometry members must match the receiving srid. */
    public function testDimension3mGeometryRejectsWrongSrid(): void
    {
        $this->expectException(InvalidSridException::class);

        new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\MultiLineString([new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\LineString([], 2154)], 4326);
    }

    /** Empty geography members must match the receiving dimension. */
    public function testDimension3zGeographyRejectsWrongDimension(): void
    {
        $this->expectException(InvalidDimensionException::class);

        new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\MultiLineString([new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\LineString([], 4326)], 4326);
    }

    /** Empty geography members must match the receiving family. */
    public function testDimension3zGeographyRejectsWrongFamily(): void
    {
        $this->expectException(InvalidFamilyException::class);

        new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\MultiLineString([new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\LineString([], 4326)], 4326);
    }

    /** Empty geography members must match the receiving srid. */
    public function testDimension3zGeographyRejectsWrongSrid(): void
    {
        $this->expectException(InvalidSridException::class);

        new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\MultiLineString([new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\LineString([], 2154)], 4326);
    }

    /** Empty geometry members must match the receiving dimension. */
    public function testDimension3zGeometryRejectsWrongDimension(): void
    {
        $this->expectException(InvalidDimensionException::class);

        new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\MultiLineString([new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\LineString([], 4326)], 4326);
    }

    /** Empty geometry members must match the receiving family. */
    public function testDimension3zGeometryRejectsWrongFamily(): void
    {
        $this->expectException(InvalidFamilyException::class);

        new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\MultiLineString([new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\LineString([], 4326)], 4326);
    }

    /** Empty geometry members must match the receiving srid. */
    public function testDimension3zGeometryRejectsWrongSrid(): void
    {
        $this->expectException(InvalidSridException::class);

        new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\MultiLineString([new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\LineString([], 2154)], 4326);
    }

    /** Empty geography members must match the receiving dimension. */
    public function testDimension4zmGeographyRejectsWrongDimension(): void
    {
        $this->expectException(InvalidDimensionException::class);

        new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\MultiLineString([new \LongitudeOne\SpatialTypes\Types\Dimension2\Geography\LineString([], 4326)], 4326);
    }

    /** Empty geography members must match the receiving family. */
    public function testDimension4zmGeographyRejectsWrongFamily(): void
    {
        $this->expectException(InvalidFamilyException::class);

        new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\MultiLineString([new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\LineString([], 4326)], 4326);
    }

    /** Empty geography members must match the receiving srid. */
    public function testDimension4zmGeographyRejectsWrongSrid(): void
    {
        $this->expectException(InvalidSridException::class);

        new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\MultiLineString([new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\LineString([], 2154)], 4326);
    }

    /** Empty geometry members must match the receiving dimension. */
    public function testDimension4zmGeometryRejectsWrongDimension(): void
    {
        $this->expectException(InvalidDimensionException::class);

        new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\MultiLineString([new LineString([], 4326)], 4326);
    }

    /** Empty geometry members must match the receiving family. */
    public function testDimension4zmGeometryRejectsWrongFamily(): void
    {
        $this->expectException(InvalidFamilyException::class);

        new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\MultiLineString([new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\LineString([], 4326)], 4326);
    }

    /** Empty geometry members must match the receiving srid. */
    public function testDimension4zmGeometryRejectsWrongSrid(): void
    {
        $this->expectException(InvalidSridException::class);

        new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\MultiLineString([new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\LineString([], 2154)], 4326);
    }

    /** An empty member still has a complete reference identity. */
    public function testEmptyMemberWithDifferentAuthorityIsRejected(): void
    {
        $this->expectException(InvalidSridException::class);

        new MultiLineString([new LineString([], SpatialReference::epsg(4326))], 4326);
    }

    /** The unnamed reference remains a concrete reference for empty members. */
    public function testEmptyMemberWithZeroSridIsRejected(): void
    {
        $this->expectException(InvalidSridException::class);

        new MultiLineString([new LineString([])], 4326);
    }

    /** A zero-member collection and a single empty member survive reference copies. */
    public function testReferenceCopiesDistinguishZeroAndOneMember(): void
    {
        $zero = new MultiLineString([], 4326);
        $single = new MultiLineString([new LineString([], 4326)], 4326);
        $reference = SpatialReference::epsg(2154);

        static::assertSame([], $zero->withSpatialReference($reference)->getElements());
        static::assertSame([], $zero->withSrid(3857)->toArray());
        static::assertSame([[]], $single->withSpatialReference($reference)->toArray());
        static::assertCount(1, $single->withSrid(3857)->getElements());
        static::assertSame(4326, $single->getLineString(0)->getSrid());
    }

    /** Empty members keep their authority when another member is replaced. */
    public function testReplacementPreservesReferenceAuthority(): void
    {
        $reference = SpatialReference::epsg(4326);
        $original = new MultiLineString([[], [[1, 2], [3, 4]], []], $reference);
        $replacement = $original->withLineString(1, []);

        static::assertSame([[], [], []], $replacement->toArray());
        static::assertSame([[], [[1, 2], [3, 4]], []], $original->toArray());
        static::assertSame($reference, $replacement->getSpatialReference());
        static::assertSame($reference, $replacement->getLineString(0)->getSpatialReference());
        static::assertSame($reference, $replacement->getLineString(1)->getSpatialReference());
        static::assertSame($reference, $replacement->getLineString(2)->getSpatialReference());
        static::assertNotSame($original->getLineString(0), $replacement->getLineString(0));
        static::assertNotSame($original->getLineString(2), $replacement->getLineString(2));
    }

    /** There is no indexed member in a zero-member collection. */
    public function testZeroMemberAccessIsRejected(): void
    {
        $zero = new MultiLineString([]);
        $this->expectException(OutOfBoundsException::class);

        $zero->getLineString(0);
    }

    /** Replacing a member cannot append to a zero-member collection. */
    public function testZeroMemberReplacementIsRejected(): void
    {
        $zero = new MultiLineString([]);
        $this->expectException(OutOfBoundsException::class);

        $zero->withLineString(0, []);
    }
}
