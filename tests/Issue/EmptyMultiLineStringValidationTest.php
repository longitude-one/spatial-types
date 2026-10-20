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

use LongitudeOne\SpatialTypes\Exception\OutOfBoundsException;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineString;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiLineString;
use PHPUnit\Framework\TestCase;

/**
 * Issue #9: empty members retain reference and index behavior and reference identity.
 *
 * @internal
 *
 * @coversNothing
 */
class EmptyMultiLineStringValidationTest extends TestCase
{
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
