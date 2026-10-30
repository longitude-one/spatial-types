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

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\GeographyCollectionImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\PointImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\GeometryCollectionImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Point;
use LongitudeOne\SpatialTypes\Types\Dimension2\SpatialCollection;
use PHPUnit\Framework\TestCase;

/** Approved all-EMPTY collection classification from Story #65.
 * @internal
 *
 * @coversNothing
 */
final class AllEmptyGeometryCollectionTest extends TestCase
{
    /** Zero membership and all-EMPTY membership differ in storage, not emptiness. */
    public function testDimension2GeographyImmutableEmptyAndMixedMembers(): void
    {
        $first = new PointImmutable(null, null, 4326);
        $second = new PointImmutable(null, null, 4326);
        $populated = new PointImmutable(0, 0, 4326);
        $zero = new GeographyCollectionImmutable(4326, []);
        $all = new GeographyCollectionImmutable(4326, [$first, $second]);
        $mixed = new GeographyCollectionImmutable(4326, [$first, $populated, $second]);

        self::assertTrue($zero->isEmpty());
        self::assertSame([], $zero->getElements());
        self::assertTrue((new GeographyCollectionImmutable(4326, [$first]))->isEmpty());
        self::assertFalse((new GeographyCollectionImmutable(4326, [$populated, $first]))->isEmpty());
        self::assertFalse((new GeographyCollectionImmutable(4326, [$first, $populated]))->isEmpty());
        self::assertTrue($all->isEmpty());
        self::assertCount(2, $all->getElements());
        self::assertSame([$first, $second], $all->getElements());
        self::assertSame([[], []], $all->toArray());
        self::assertFalse($mixed->isEmpty());
        self::assertCount(3, $mixed->getElements());
        self::assertSame([$first, $populated, $second], $mixed->getElements());
        self::assertSame([[], [0, 0], []], $mixed->toArray());
        self::assertSame(CoordinateDimensionEnum::XY, $all->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $all->getFamily());
        self::assertSame(4326, $all->getSrid());
        self::assertSame(4326, $mixed->getSrid());
    }

    /** Zero membership and all-EMPTY membership differ in storage, not emptiness. */
    public function testDimension2GeographyMutableEmptyAndMixedMembers(): void
    {
        $first = new Point(null, null, 4326, SpatialModelEnum::GEOGRAPHY);
        $second = new Point(null, null, 4326, SpatialModelEnum::GEOGRAPHY);
        $populated = new Point(0, 0, 4326, SpatialModelEnum::GEOGRAPHY);
        $zero = new SpatialCollection([], 4326, SpatialModelEnum::GEOGRAPHY);
        $all = new SpatialCollection([$first, $second], 4326, SpatialModelEnum::GEOGRAPHY);
        $mixed = new SpatialCollection([$first, $populated, $second], 4326, SpatialModelEnum::GEOGRAPHY);

        self::assertTrue($zero->isEmpty());
        self::assertSame([], $zero->getElements());
        self::assertTrue((new SpatialCollection([$first], 4326, SpatialModelEnum::GEOGRAPHY))->isEmpty());
        self::assertFalse((new SpatialCollection([$populated, $first], 4326, SpatialModelEnum::GEOGRAPHY))->isEmpty());
        self::assertFalse((new SpatialCollection([$first, $populated], 4326, SpatialModelEnum::GEOGRAPHY))->isEmpty());
        self::assertTrue($all->isEmpty());
        self::assertCount(2, $all->getElements());
        self::assertSame([$first, $second], $all->getElements());
        self::assertSame([[], []], $all->toArray());
        self::assertFalse($mixed->isEmpty());
        self::assertCount(3, $mixed->getElements());
        self::assertSame([$first, $populated, $second], $mixed->getElements());
        self::assertSame([[], [0, 0], []], $mixed->toArray());
        self::assertSame(CoordinateDimensionEnum::XY, $all->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $all->getFamily());
        self::assertSame(4326, $all->getSrid());
        self::assertSame(4326, $mixed->getSrid());
    }

    /** Zero membership and all-EMPTY membership differ in storage, not emptiness. */
    public function testDimension2GeometryImmutableEmptyAndMixedMembers(): void
    {
        $first = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PointImmutable(null, null, 4326);
        $second = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PointImmutable(null, null, 4326);
        $populated = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PointImmutable(0, 0, 4326);
        $zero = new GeometryCollectionImmutable(4326, []);
        $all = new GeometryCollectionImmutable(4326, [$first, $second]);
        $mixed = new GeometryCollectionImmutable(4326, [$first, $populated, $second]);

        self::assertTrue($zero->isEmpty());
        self::assertSame([], $zero->getElements());
        self::assertTrue((new GeometryCollectionImmutable(4326, [$first]))->isEmpty());
        self::assertFalse((new GeometryCollectionImmutable(4326, [$populated, $first]))->isEmpty());
        self::assertFalse((new GeometryCollectionImmutable(4326, [$first, $populated]))->isEmpty());
        self::assertTrue($all->isEmpty());
        self::assertCount(2, $all->getElements());
        self::assertSame([$first, $second], $all->getElements());
        self::assertSame([[], []], $all->toArray());
        self::assertFalse($mixed->isEmpty());
        self::assertCount(3, $mixed->getElements());
        self::assertSame([$first, $populated, $second], $mixed->getElements());
        self::assertSame([[], [0, 0], []], $mixed->toArray());
        self::assertSame(CoordinateDimensionEnum::XY, $all->getDimension());
        self::assertSame(SpatialModelEnum::GEOMETRY, $all->getFamily());
        self::assertSame(4326, $all->getSrid());
        self::assertSame(4326, $mixed->getSrid());
    }

    /** Zero membership and all-EMPTY membership differ in storage, not emptiness. */
    public function testDimension2GeometryMutableEmptyAndMixedMembers(): void
    {
        $first = new Point(null, null, 4326, SpatialModelEnum::GEOMETRY);
        $second = new Point(null, null, 4326, SpatialModelEnum::GEOMETRY);
        $populated = new Point(0, 0, 4326, SpatialModelEnum::GEOMETRY);
        $zero = new SpatialCollection([], 4326, SpatialModelEnum::GEOMETRY);
        $all = new SpatialCollection([$first, $second], 4326, SpatialModelEnum::GEOMETRY);
        $mixed = new SpatialCollection([$first, $populated, $second], 4326, SpatialModelEnum::GEOMETRY);

        self::assertTrue($zero->isEmpty());
        self::assertSame([], $zero->getElements());
        self::assertTrue((new SpatialCollection([$first], 4326, SpatialModelEnum::GEOMETRY))->isEmpty());
        self::assertFalse((new SpatialCollection([$populated, $first], 4326, SpatialModelEnum::GEOMETRY))->isEmpty());
        self::assertFalse((new SpatialCollection([$first, $populated], 4326, SpatialModelEnum::GEOMETRY))->isEmpty());
        self::assertTrue($all->isEmpty());
        self::assertCount(2, $all->getElements());
        self::assertSame([$first, $second], $all->getElements());
        self::assertSame([[], []], $all->toArray());
        self::assertFalse($mixed->isEmpty());
        self::assertCount(3, $mixed->getElements());
        self::assertSame([$first, $populated, $second], $mixed->getElements());
        self::assertSame([[], [0, 0], []], $mixed->toArray());
        self::assertSame(CoordinateDimensionEnum::XY, $all->getDimension());
        self::assertSame(SpatialModelEnum::GEOMETRY, $all->getFamily());
        self::assertSame(4326, $all->getSrid());
        self::assertSame(4326, $mixed->getSrid());
    }

    /** Zero membership and all-EMPTY membership differ in storage, not emptiness. */
    public function testDimension3mGeographyImmutableEmptyAndMixedMembers(): void
    {
        $first = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\PointImmutable(null, null, null, 4326);
        $second = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\PointImmutable(null, null, null, 4326);
        $populated = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\PointImmutable(0, 0, 1, 4326);
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\GeographyCollectionImmutable(4326, []);
        $all = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\GeographyCollectionImmutable(4326, [$first, $second]);
        $mixed = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\GeographyCollectionImmutable(4326, [$first, $populated, $second]);

        self::assertTrue($zero->isEmpty());
        self::assertSame([], $zero->getElements());
        self::assertTrue((new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\GeographyCollectionImmutable(4326, [$first]))->isEmpty());
        self::assertFalse((new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\GeographyCollectionImmutable(4326, [$populated, $first]))->isEmpty());
        self::assertFalse((new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\GeographyCollectionImmutable(4326, [$first, $populated]))->isEmpty());
        self::assertTrue($all->isEmpty());
        self::assertCount(2, $all->getElements());
        self::assertSame([$first, $second], $all->getElements());
        self::assertSame([[], []], $all->toArray());
        self::assertFalse($mixed->isEmpty());
        self::assertCount(3, $mixed->getElements());
        self::assertSame([$first, $populated, $second], $mixed->getElements());
        self::assertSame([[], [0, 0, 1], []], $mixed->toArray());
        self::assertSame(CoordinateDimensionEnum::XYM, $all->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $all->getFamily());
        self::assertSame(4326, $all->getSrid());
        self::assertSame(4326, $mixed->getSrid());
    }

    /** Zero membership and all-EMPTY membership differ in storage, not emptiness. */
    public function testDimension3mGeographyMutableEmptyAndMixedMembers(): void
    {
        $first = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Point(null, null, null, 4326, SpatialModelEnum::GEOGRAPHY);
        $second = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Point(null, null, null, 4326, SpatialModelEnum::GEOGRAPHY);
        $populated = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Point(0, 0, 1, 4326, SpatialModelEnum::GEOGRAPHY);
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension3m\SpatialCollection([], 4326, SpatialModelEnum::GEOGRAPHY);
        $all = new \LongitudeOne\SpatialTypes\Types\Dimension3m\SpatialCollection([$first, $second], 4326, SpatialModelEnum::GEOGRAPHY);
        $mixed = new \LongitudeOne\SpatialTypes\Types\Dimension3m\SpatialCollection([$first, $populated, $second], 4326, SpatialModelEnum::GEOGRAPHY);

        self::assertTrue($zero->isEmpty());
        self::assertSame([], $zero->getElements());
        self::assertTrue((new \LongitudeOne\SpatialTypes\Types\Dimension3m\SpatialCollection([$first], 4326, SpatialModelEnum::GEOGRAPHY))->isEmpty());
        self::assertFalse((new \LongitudeOne\SpatialTypes\Types\Dimension3m\SpatialCollection([$populated, $first], 4326, SpatialModelEnum::GEOGRAPHY))->isEmpty());
        self::assertFalse((new \LongitudeOne\SpatialTypes\Types\Dimension3m\SpatialCollection([$first, $populated], 4326, SpatialModelEnum::GEOGRAPHY))->isEmpty());
        self::assertTrue($all->isEmpty());
        self::assertCount(2, $all->getElements());
        self::assertSame([$first, $second], $all->getElements());
        self::assertSame([[], []], $all->toArray());
        self::assertFalse($mixed->isEmpty());
        self::assertCount(3, $mixed->getElements());
        self::assertSame([$first, $populated, $second], $mixed->getElements());
        self::assertSame([[], [0, 0, 1], []], $mixed->toArray());
        self::assertSame(CoordinateDimensionEnum::XYM, $all->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $all->getFamily());
        self::assertSame(4326, $all->getSrid());
        self::assertSame(4326, $mixed->getSrid());
    }

    /** Zero membership and all-EMPTY membership differ in storage, not emptiness. */
    public function testDimension3mGeometryImmutableEmptyAndMixedMembers(): void
    {
        $first = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\PointImmutable(null, null, null, 4326);
        $second = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\PointImmutable(null, null, null, 4326);
        $populated = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\PointImmutable(0, 0, 1, 4326);
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\GeometryCollectionImmutable(4326, []);
        $all = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\GeometryCollectionImmutable(4326, [$first, $second]);
        $mixed = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\GeometryCollectionImmutable(4326, [$first, $populated, $second]);

        self::assertTrue($zero->isEmpty());
        self::assertSame([], $zero->getElements());
        self::assertTrue((new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\GeometryCollectionImmutable(4326, [$first]))->isEmpty());
        self::assertFalse((new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\GeometryCollectionImmutable(4326, [$populated, $first]))->isEmpty());
        self::assertFalse((new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\GeometryCollectionImmutable(4326, [$first, $populated]))->isEmpty());
        self::assertTrue($all->isEmpty());
        self::assertCount(2, $all->getElements());
        self::assertSame([$first, $second], $all->getElements());
        self::assertSame([[], []], $all->toArray());
        self::assertFalse($mixed->isEmpty());
        self::assertCount(3, $mixed->getElements());
        self::assertSame([$first, $populated, $second], $mixed->getElements());
        self::assertSame([[], [0, 0, 1], []], $mixed->toArray());
        self::assertSame(CoordinateDimensionEnum::XYM, $all->getDimension());
        self::assertSame(SpatialModelEnum::GEOMETRY, $all->getFamily());
        self::assertSame(4326, $all->getSrid());
        self::assertSame(4326, $mixed->getSrid());
    }

    /** Zero membership and all-EMPTY membership differ in storage, not emptiness. */
    public function testDimension3mGeometryMutableEmptyAndMixedMembers(): void
    {
        $first = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Point(null, null, null, 4326, SpatialModelEnum::GEOMETRY);
        $second = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Point(null, null, null, 4326, SpatialModelEnum::GEOMETRY);
        $populated = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Point(0, 0, 1, 4326, SpatialModelEnum::GEOMETRY);
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension3m\SpatialCollection([], 4326, SpatialModelEnum::GEOMETRY);
        $all = new \LongitudeOne\SpatialTypes\Types\Dimension3m\SpatialCollection([$first, $second], 4326, SpatialModelEnum::GEOMETRY);
        $mixed = new \LongitudeOne\SpatialTypes\Types\Dimension3m\SpatialCollection([$first, $populated, $second], 4326, SpatialModelEnum::GEOMETRY);

        self::assertTrue($zero->isEmpty());
        self::assertSame([], $zero->getElements());
        self::assertTrue((new \LongitudeOne\SpatialTypes\Types\Dimension3m\SpatialCollection([$first], 4326, SpatialModelEnum::GEOMETRY))->isEmpty());
        self::assertFalse((new \LongitudeOne\SpatialTypes\Types\Dimension3m\SpatialCollection([$populated, $first], 4326, SpatialModelEnum::GEOMETRY))->isEmpty());
        self::assertFalse((new \LongitudeOne\SpatialTypes\Types\Dimension3m\SpatialCollection([$first, $populated], 4326, SpatialModelEnum::GEOMETRY))->isEmpty());
        self::assertTrue($all->isEmpty());
        self::assertCount(2, $all->getElements());
        self::assertSame([$first, $second], $all->getElements());
        self::assertSame([[], []], $all->toArray());
        self::assertFalse($mixed->isEmpty());
        self::assertCount(3, $mixed->getElements());
        self::assertSame([$first, $populated, $second], $mixed->getElements());
        self::assertSame([[], [0, 0, 1], []], $mixed->toArray());
        self::assertSame(CoordinateDimensionEnum::XYM, $all->getDimension());
        self::assertSame(SpatialModelEnum::GEOMETRY, $all->getFamily());
        self::assertSame(4326, $all->getSrid());
        self::assertSame(4326, $mixed->getSrid());
    }

    /** Zero membership and all-EMPTY membership differ in storage, not emptiness. */
    public function testDimension3zGeographyImmutableEmptyAndMixedMembers(): void
    {
        $first = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\PointImmutable(null, null, null, 4326);
        $second = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\PointImmutable(null, null, null, 4326);
        $populated = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\PointImmutable(0, 0, 1, 4326);
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\GeographyCollectionImmutable(4326, []);
        $all = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\GeographyCollectionImmutable(4326, [$first, $second]);
        $mixed = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\GeographyCollectionImmutable(4326, [$first, $populated, $second]);

        self::assertTrue($zero->isEmpty());
        self::assertSame([], $zero->getElements());
        self::assertTrue((new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\GeographyCollectionImmutable(4326, [$first]))->isEmpty());
        self::assertFalse((new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\GeographyCollectionImmutable(4326, [$populated, $first]))->isEmpty());
        self::assertFalse((new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\GeographyCollectionImmutable(4326, [$first, $populated]))->isEmpty());
        self::assertTrue($all->isEmpty());
        self::assertCount(2, $all->getElements());
        self::assertSame([$first, $second], $all->getElements());
        self::assertSame([[], []], $all->toArray());
        self::assertFalse($mixed->isEmpty());
        self::assertCount(3, $mixed->getElements());
        self::assertSame([$first, $populated, $second], $mixed->getElements());
        self::assertSame([[], [0, 0, 1], []], $mixed->toArray());
        self::assertSame(CoordinateDimensionEnum::XYZ, $all->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $all->getFamily());
        self::assertSame(4326, $all->getSrid());
        self::assertSame(4326, $mixed->getSrid());
    }

    /** Zero membership and all-EMPTY membership differ in storage, not emptiness. */
    public function testDimension3zGeographyMutableEmptyAndMixedMembers(): void
    {
        $first = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Point(null, null, null, 4326, SpatialModelEnum::GEOGRAPHY);
        $second = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Point(null, null, null, 4326, SpatialModelEnum::GEOGRAPHY);
        $populated = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Point(0, 0, 1, 4326, SpatialModelEnum::GEOGRAPHY);
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension3z\SpatialCollection([], 4326, SpatialModelEnum::GEOGRAPHY);
        $all = new \LongitudeOne\SpatialTypes\Types\Dimension3z\SpatialCollection([$first, $second], 4326, SpatialModelEnum::GEOGRAPHY);
        $mixed = new \LongitudeOne\SpatialTypes\Types\Dimension3z\SpatialCollection([$first, $populated, $second], 4326, SpatialModelEnum::GEOGRAPHY);

        self::assertTrue($zero->isEmpty());
        self::assertSame([], $zero->getElements());
        self::assertTrue((new \LongitudeOne\SpatialTypes\Types\Dimension3z\SpatialCollection([$first], 4326, SpatialModelEnum::GEOGRAPHY))->isEmpty());
        self::assertFalse((new \LongitudeOne\SpatialTypes\Types\Dimension3z\SpatialCollection([$populated, $first], 4326, SpatialModelEnum::GEOGRAPHY))->isEmpty());
        self::assertFalse((new \LongitudeOne\SpatialTypes\Types\Dimension3z\SpatialCollection([$first, $populated], 4326, SpatialModelEnum::GEOGRAPHY))->isEmpty());
        self::assertTrue($all->isEmpty());
        self::assertCount(2, $all->getElements());
        self::assertSame([$first, $second], $all->getElements());
        self::assertSame([[], []], $all->toArray());
        self::assertFalse($mixed->isEmpty());
        self::assertCount(3, $mixed->getElements());
        self::assertSame([$first, $populated, $second], $mixed->getElements());
        self::assertSame([[], [0, 0, 1], []], $mixed->toArray());
        self::assertSame(CoordinateDimensionEnum::XYZ, $all->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $all->getFamily());
        self::assertSame(4326, $all->getSrid());
        self::assertSame(4326, $mixed->getSrid());
    }

    /** Zero membership and all-EMPTY membership differ in storage, not emptiness. */
    public function testDimension3zGeometryImmutableEmptyAndMixedMembers(): void
    {
        $first = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\PointImmutable(null, null, null, 4326);
        $second = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\PointImmutable(null, null, null, 4326);
        $populated = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\PointImmutable(0, 0, 1, 4326);
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\GeometryCollectionImmutable(4326, []);
        $all = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\GeometryCollectionImmutable(4326, [$first, $second]);
        $mixed = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\GeometryCollectionImmutable(4326, [$first, $populated, $second]);

        self::assertTrue($zero->isEmpty());
        self::assertSame([], $zero->getElements());
        self::assertTrue((new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\GeometryCollectionImmutable(4326, [$first]))->isEmpty());
        self::assertFalse((new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\GeometryCollectionImmutable(4326, [$populated, $first]))->isEmpty());
        self::assertFalse((new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\GeometryCollectionImmutable(4326, [$first, $populated]))->isEmpty());
        self::assertTrue($all->isEmpty());
        self::assertCount(2, $all->getElements());
        self::assertSame([$first, $second], $all->getElements());
        self::assertSame([[], []], $all->toArray());
        self::assertFalse($mixed->isEmpty());
        self::assertCount(3, $mixed->getElements());
        self::assertSame([$first, $populated, $second], $mixed->getElements());
        self::assertSame([[], [0, 0, 1], []], $mixed->toArray());
        self::assertSame(CoordinateDimensionEnum::XYZ, $all->getDimension());
        self::assertSame(SpatialModelEnum::GEOMETRY, $all->getFamily());
        self::assertSame(4326, $all->getSrid());
        self::assertSame(4326, $mixed->getSrid());
    }

    /** Zero membership and all-EMPTY membership differ in storage, not emptiness. */
    public function testDimension3zGeometryMutableEmptyAndMixedMembers(): void
    {
        $first = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Point(null, null, null, 4326, SpatialModelEnum::GEOMETRY);
        $second = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Point(null, null, null, 4326, SpatialModelEnum::GEOMETRY);
        $populated = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Point(0, 0, 1, 4326, SpatialModelEnum::GEOMETRY);
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension3z\SpatialCollection([], 4326, SpatialModelEnum::GEOMETRY);
        $all = new \LongitudeOne\SpatialTypes\Types\Dimension3z\SpatialCollection([$first, $second], 4326, SpatialModelEnum::GEOMETRY);
        $mixed = new \LongitudeOne\SpatialTypes\Types\Dimension3z\SpatialCollection([$first, $populated, $second], 4326, SpatialModelEnum::GEOMETRY);

        self::assertTrue($zero->isEmpty());
        self::assertSame([], $zero->getElements());
        self::assertTrue((new \LongitudeOne\SpatialTypes\Types\Dimension3z\SpatialCollection([$first], 4326, SpatialModelEnum::GEOMETRY))->isEmpty());
        self::assertFalse((new \LongitudeOne\SpatialTypes\Types\Dimension3z\SpatialCollection([$populated, $first], 4326, SpatialModelEnum::GEOMETRY))->isEmpty());
        self::assertFalse((new \LongitudeOne\SpatialTypes\Types\Dimension3z\SpatialCollection([$first, $populated], 4326, SpatialModelEnum::GEOMETRY))->isEmpty());
        self::assertTrue($all->isEmpty());
        self::assertCount(2, $all->getElements());
        self::assertSame([$first, $second], $all->getElements());
        self::assertSame([[], []], $all->toArray());
        self::assertFalse($mixed->isEmpty());
        self::assertCount(3, $mixed->getElements());
        self::assertSame([$first, $populated, $second], $mixed->getElements());
        self::assertSame([[], [0, 0, 1], []], $mixed->toArray());
        self::assertSame(CoordinateDimensionEnum::XYZ, $all->getDimension());
        self::assertSame(SpatialModelEnum::GEOMETRY, $all->getFamily());
        self::assertSame(4326, $all->getSrid());
        self::assertSame(4326, $mixed->getSrid());
    }

    /** Zero membership and all-EMPTY membership differ in storage, not emptiness. */
    public function testDimension4zmGeographyImmutableEmptyAndMixedMembers(): void
    {
        $first = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\PointImmutable(null, null, null, null, 4326);
        $second = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\PointImmutable(null, null, null, null, 4326);
        $populated = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\PointImmutable(0, 0, 1, 1, 4326);
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\GeographyCollectionImmutable(4326, []);
        $all = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\GeographyCollectionImmutable(4326, [$first, $second]);
        $mixed = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\GeographyCollectionImmutable(4326, [$first, $populated, $second]);

        self::assertTrue($zero->isEmpty());
        self::assertSame([], $zero->getElements());
        self::assertTrue((new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\GeographyCollectionImmutable(4326, [$first]))->isEmpty());
        self::assertFalse((new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\GeographyCollectionImmutable(4326, [$populated, $first]))->isEmpty());
        self::assertFalse((new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\GeographyCollectionImmutable(4326, [$first, $populated]))->isEmpty());
        self::assertTrue($all->isEmpty());
        self::assertCount(2, $all->getElements());
        self::assertSame([$first, $second], $all->getElements());
        self::assertSame([[], []], $all->toArray());
        self::assertFalse($mixed->isEmpty());
        self::assertCount(3, $mixed->getElements());
        self::assertSame([$first, $populated, $second], $mixed->getElements());
        self::assertSame([[], [0, 0, 1, 1], []], $mixed->toArray());
        self::assertSame(CoordinateDimensionEnum::XYZM, $all->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $all->getFamily());
        self::assertSame(4326, $all->getSrid());
        self::assertSame(4326, $mixed->getSrid());
    }

    /** Zero membership and all-EMPTY membership differ in storage, not emptiness. */
    public function testDimension4zmGeographyMutableEmptyAndMixedMembers(): void
    {
        $first = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Point(null, null, null, null, 4326, SpatialModelEnum::GEOGRAPHY);
        $second = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Point(null, null, null, null, 4326, SpatialModelEnum::GEOGRAPHY);
        $populated = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Point(0, 0, 1, 1, 4326, SpatialModelEnum::GEOGRAPHY);
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\SpatialCollection([], 4326, SpatialModelEnum::GEOGRAPHY);
        $all = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\SpatialCollection([$first, $second], 4326, SpatialModelEnum::GEOGRAPHY);
        $mixed = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\SpatialCollection([$first, $populated, $second], 4326, SpatialModelEnum::GEOGRAPHY);

        self::assertTrue($zero->isEmpty());
        self::assertSame([], $zero->getElements());
        self::assertTrue((new \LongitudeOne\SpatialTypes\Types\Dimension4zm\SpatialCollection([$first], 4326, SpatialModelEnum::GEOGRAPHY))->isEmpty());
        self::assertFalse((new \LongitudeOne\SpatialTypes\Types\Dimension4zm\SpatialCollection([$populated, $first], 4326, SpatialModelEnum::GEOGRAPHY))->isEmpty());
        self::assertFalse((new \LongitudeOne\SpatialTypes\Types\Dimension4zm\SpatialCollection([$first, $populated], 4326, SpatialModelEnum::GEOGRAPHY))->isEmpty());
        self::assertTrue($all->isEmpty());
        self::assertCount(2, $all->getElements());
        self::assertSame([$first, $second], $all->getElements());
        self::assertSame([[], []], $all->toArray());
        self::assertFalse($mixed->isEmpty());
        self::assertCount(3, $mixed->getElements());
        self::assertSame([$first, $populated, $second], $mixed->getElements());
        self::assertSame([[], [0, 0, 1, 1], []], $mixed->toArray());
        self::assertSame(CoordinateDimensionEnum::XYZM, $all->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $all->getFamily());
        self::assertSame(4326, $all->getSrid());
        self::assertSame(4326, $mixed->getSrid());
    }

    /** Zero membership and all-EMPTY membership differ in storage, not emptiness. */
    public function testDimension4zmGeometryImmutableEmptyAndMixedMembers(): void
    {
        $first = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\PointImmutable(null, null, null, null, 4326);
        $second = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\PointImmutable(null, null, null, null, 4326);
        $populated = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\PointImmutable(0, 0, 1, 1, 4326);
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\GeometryCollectionImmutable(4326, []);
        $all = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\GeometryCollectionImmutable(4326, [$first, $second]);
        $mixed = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\GeometryCollectionImmutable(4326, [$first, $populated, $second]);

        self::assertTrue($zero->isEmpty());
        self::assertSame([], $zero->getElements());
        self::assertTrue((new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\GeometryCollectionImmutable(4326, [$first]))->isEmpty());
        self::assertFalse((new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\GeometryCollectionImmutable(4326, [$populated, $first]))->isEmpty());
        self::assertFalse((new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\GeometryCollectionImmutable(4326, [$first, $populated]))->isEmpty());
        self::assertTrue($all->isEmpty());
        self::assertCount(2, $all->getElements());
        self::assertSame([$first, $second], $all->getElements());
        self::assertSame([[], []], $all->toArray());
        self::assertFalse($mixed->isEmpty());
        self::assertCount(3, $mixed->getElements());
        self::assertSame([$first, $populated, $second], $mixed->getElements());
        self::assertSame([[], [0, 0, 1, 1], []], $mixed->toArray());
        self::assertSame(CoordinateDimensionEnum::XYZM, $all->getDimension());
        self::assertSame(SpatialModelEnum::GEOMETRY, $all->getFamily());
        self::assertSame(4326, $all->getSrid());
        self::assertSame(4326, $mixed->getSrid());
    }

    /** Zero membership and all-EMPTY membership differ in storage, not emptiness. */
    public function testDimension4zmGeometryMutableEmptyAndMixedMembers(): void
    {
        $first = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Point(null, null, null, null, 4326, SpatialModelEnum::GEOMETRY);
        $second = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Point(null, null, null, null, 4326, SpatialModelEnum::GEOMETRY);
        $populated = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Point(0, 0, 1, 1, 4326, SpatialModelEnum::GEOMETRY);
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\SpatialCollection([], 4326, SpatialModelEnum::GEOMETRY);
        $all = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\SpatialCollection([$first, $second], 4326, SpatialModelEnum::GEOMETRY);
        $mixed = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\SpatialCollection([$first, $populated, $second], 4326, SpatialModelEnum::GEOMETRY);

        self::assertTrue($zero->isEmpty());
        self::assertSame([], $zero->getElements());
        self::assertTrue((new \LongitudeOne\SpatialTypes\Types\Dimension4zm\SpatialCollection([$first], 4326, SpatialModelEnum::GEOMETRY))->isEmpty());
        self::assertFalse((new \LongitudeOne\SpatialTypes\Types\Dimension4zm\SpatialCollection([$populated, $first], 4326, SpatialModelEnum::GEOMETRY))->isEmpty());
        self::assertFalse((new \LongitudeOne\SpatialTypes\Types\Dimension4zm\SpatialCollection([$first, $populated], 4326, SpatialModelEnum::GEOMETRY))->isEmpty());
        self::assertTrue($all->isEmpty());
        self::assertCount(2, $all->getElements());
        self::assertSame([$first, $second], $all->getElements());
        self::assertSame([[], []], $all->toArray());
        self::assertFalse($mixed->isEmpty());
        self::assertCount(3, $mixed->getElements());
        self::assertSame([$first, $populated, $second], $mixed->getElements());
        self::assertSame([[], [0, 0, 1, 1], []], $mixed->toArray());
        self::assertSame(CoordinateDimensionEnum::XYZM, $all->getDimension());
        self::assertSame(SpatialModelEnum::GEOMETRY, $all->getFamily());
        self::assertSame(4326, $all->getSrid());
        self::assertSame(4326, $mixed->getSrid());
    }
}
