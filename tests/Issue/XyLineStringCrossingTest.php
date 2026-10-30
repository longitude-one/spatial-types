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

use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineStringImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PointImmutable;
use PHPUnit\Framework\TestCase;

/** Approved complete XY interior-crossing regression from audit R2.
 * @internal
 *
 * @coversNothing
 */
final class XyLineStringCrossingTest extends TestCase
{
    /** Existing duplicate endpoint equality remains a separate unresolved contract. */
    public function testDistinctClosingEndpointResultIsPreserved(): void
    {
        $ring = new LineStringImmutable([[0, 0], [2, 0], [2, 2], [0, 2], [0, 0]]);

        self::assertTrue($ring->isClosed());
        self::assertFalse($ring->isSimple());
    }

    /** Reproduce the exact crossing graph, preserving its shared points. */
    public function testExactAuditCrossing(): void
    {
        $first = new PointImmutable(0, 0);
        $last = new PointImmutable(2, 0);
        $line = new LineStringImmutable([$first, new PointImmutable(2, 2), new PointImmutable(0, 2), $last]);

        self::assertFalse($line->isSimple());
        self::assertSame([[0, 0], [2, 2], [0, 2], [2, 0]], $line->toArray());
        self::assertSame($first, $line->getStartPoint());
        self::assertSame($last, $line->getEndPoint());
        self::assertSame([0, 0], $first->toArray());
        self::assertSame([2, 0], $last->toArray());
    }

    /** Existing duplicate interior positions remain non-simple. */
    public function testExistingDuplicatePositionResultIsPreserved(): void
    {
        $line = new LineStringImmutable([[0, 0], [1, 1], [0, 0], [2, 2]]);

        self::assertFalse($line->isSimple());
    }

    /** The same approved graph is non-simple in the Geography family. */
    public function testGeographyCrossing(): void
    {
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geography\LineStringImmutable([[0, 0], [2, 2], [0, 2], [2, 0]], 4326);

        self::assertFalse($line->isSimple());
        self::assertSame([[0, 0], [2, 2], [0, 2], [2, 0]], $line->toArray());
        self::assertSame(4326, $line->getSrid());
    }

    /** Higher-dimensional crossings remain pending the R3 mapping decision. */
    public function testHigherDimensionalResultsArePreserved(): void
    {
        $xyz = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\LineStringImmutable([[0, 0, 0], [2, 2, 0], [0, 2, 0], [2, 0, 0]]);
        $xym = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\LineStringImmutable([[0, 0, 1], [2, 2, 2], [0, 2, 3], [2, 0, 4]]);
        $xyzm = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\LineStringImmutable([[0, 0, 0, 1], [2, 2, 0, 2], [0, 2, 0, 3], [2, 0, 0, 4]]);

        self::assertTrue($xyz->isSimple());
        self::assertTrue($xym->isSimple());
        self::assertTrue($xyzm->isSimple());
    }

    /** A coordinate replacement is observed without changing its source. */
    public function testImmutableReplacementCrossing(): void
    {
        $source = new LineStringImmutable([[0, 0], [2, 0], [2, 2]], 4326);
        $crossing = $source->withArrayOfCoordinates([[0, 0], [2, 2], [0, 2], [2, 0]]);

        self::assertTrue($source->isSimple());
        self::assertFalse($crossing->isSimple());
        self::assertSame([[0, 0], [2, 0], [2, 2]], $source->toArray());
        self::assertSame(4326, $crossing->getSrid());
    }

    /** Cross-family or reference-inconsistent members are still representable. */
    public function testInconsistentContextResultIsPreserved(): void
    {
        $line = new LineStringImmutable([
            new PointImmutable(0, 0),
            new PointImmutable(2, 2),
            new PointImmutable(0, 2),
            new \LongitudeOne\SpatialTypes\Types\Dimension2\Geography\PointImmutable(2, 0, 4326),
        ]);

        self::assertTrue($line->isSimple());
        $endpoint = $line->getEndPoint();
        self::assertNotNull($endpoint);
        self::assertSame(4326, $endpoint->getSrid());
    }

    /** Non-finite coordinate predicates remain outside the approved correction. */
    public function testNonFinitePositionResultIsPreserved(): void
    {
        $line = new LineStringImmutable([[0, 0], [2, 2], [0, 2], [2, 0], [INF, 3]]);

        self::assertTrue($line->isSimple());
    }

    /** Complete non-crossing XY paths retain their former result. */
    public function testOrdinaryNonCrossingPaths(): void
    {
        $empty = new LineStringImmutable([]);
        $line = new LineStringImmutable([[0, 0], [2, 2]]);
        $path = new LineStringImmutable([[0, 0], [2, 0], [2, 2], [0, 2]]);
        $collinear = new LineStringImmutable([[0, 0], [1, 1], [2, 2]]);

        self::assertTrue($empty->isSimple());
        self::assertTrue($line->isSimple());
        self::assertTrue($path->isSimple());
        self::assertTrue($collinear->isSimple());
    }

    /** Partial coordinate predicates remain unchanged pending R3. */
    public function testPartialPositionResultIsPreserved(): void
    {
        $line = new LineStringImmutable([[0, 0], [2, 2], [0, 2], [2, 0], [null, 3]]);

        self::assertTrue($line->isSimple());
    }

    /** Reversing an interior crossing still reports non-simple. */
    public function testReversedCrossing(): void
    {
        $line = new LineStringImmutable([[2, 0], [0, 2], [2, 2], [0, 0]]);

        self::assertFalse($line->isSimple());
        self::assertSame([[2, 0], [0, 2], [2, 2], [0, 0]], $line->toArray());
    }

    /** A permitted closing endpoint contact alone is not an interior crossing. */
    public function testSharedClosingEndpoint(): void
    {
        $endpoint = new PointImmutable(0, 0);
        $ring = new LineStringImmutable([$endpoint, new PointImmutable(2, 0), new PointImmutable(2, 2), new PointImmutable(0, 2), $endpoint]);

        self::assertTrue($ring->isClosed());
        self::assertTrue($ring->isSimple());
        self::assertSame($endpoint, $ring->getStartPoint());
        self::assertSame($endpoint, $ring->getEndPoint());
        self::assertSame([[0, 0], [2, 0], [2, 2], [0, 2], [0, 0]], $ring->toArray());
    }
}
