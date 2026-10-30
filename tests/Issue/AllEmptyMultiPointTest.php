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
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\MultiPointImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\PointImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\MultiPoint;
use LongitudeOne\SpatialTypes\Types\Dimension2\Point;
use PHPUnit\Framework\TestCase;

/** Approved all-EMPTY collection classification from Story #65.
 * @internal
 *
 * @coversNothing
 */
final class AllEmptyMultiPointTest extends TestCase
{
    /** Zero membership and all-EMPTY membership differ in storage, not emptiness. */
    public function testDimension2GeographyImmutableEmptyAndMixedMembers(): void
    {
        $first = new PointImmutable(null, null, 4326);
        $second = new PointImmutable(null, null, 4326);
        $populated = new PointImmutable(0, 0, 4326);
        $zero = new MultiPointImmutable([], 4326);
        $all = new MultiPointImmutable([$first, $second], 4326);
        $mixed = new MultiPointImmutable([$first, $populated, $second], 4326);

        self::assertTrue($zero->isEmpty());
        self::assertSame([], $zero->getElements());
        self::assertTrue((new MultiPointImmutable([$first], 4326))->isEmpty());
        self::assertFalse((new MultiPointImmutable([$populated, $first], 4326))->isEmpty());
        self::assertFalse((new MultiPointImmutable([$first, $populated], 4326))->isEmpty());
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
        $zero = new MultiPoint([], 4326, SpatialModelEnum::GEOGRAPHY);
        $all = new MultiPoint([$first, $second], 4326, SpatialModelEnum::GEOGRAPHY);
        $mixed = new MultiPoint([$first, $populated, $second], 4326, SpatialModelEnum::GEOGRAPHY);

        self::assertTrue($zero->isEmpty());
        self::assertSame([], $zero->getElements());
        self::assertTrue((new MultiPoint([$first], 4326, SpatialModelEnum::GEOGRAPHY))->isEmpty());
        self::assertFalse((new MultiPoint([$populated, $first], 4326, SpatialModelEnum::GEOGRAPHY))->isEmpty());
        self::assertFalse((new MultiPoint([$first, $populated], 4326, SpatialModelEnum::GEOGRAPHY))->isEmpty());
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
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiPointImmutable([], 4326);
        $all = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiPointImmutable([$first, $second], 4326);
        $mixed = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiPointImmutable([$first, $populated, $second], 4326);

        self::assertTrue($zero->isEmpty());
        self::assertSame([], $zero->getElements());
        self::assertTrue((new \LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiPointImmutable([$first], 4326))->isEmpty());
        self::assertFalse((new \LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiPointImmutable([$populated, $first], 4326))->isEmpty());
        self::assertFalse((new \LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiPointImmutable([$first, $populated], 4326))->isEmpty());
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
        $zero = new MultiPoint([], 4326, SpatialModelEnum::GEOMETRY);
        $all = new MultiPoint([$first, $second], 4326, SpatialModelEnum::GEOMETRY);
        $mixed = new MultiPoint([$first, $populated, $second], 4326, SpatialModelEnum::GEOMETRY);

        self::assertTrue($zero->isEmpty());
        self::assertSame([], $zero->getElements());
        self::assertTrue((new MultiPoint([$first], 4326, SpatialModelEnum::GEOMETRY))->isEmpty());
        self::assertFalse((new MultiPoint([$populated, $first], 4326, SpatialModelEnum::GEOMETRY))->isEmpty());
        self::assertFalse((new MultiPoint([$first, $populated], 4326, SpatialModelEnum::GEOMETRY))->isEmpty());
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
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\MultiPointImmutable([], 4326);
        $all = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\MultiPointImmutable([$first, $second], 4326);
        $mixed = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\MultiPointImmutable([$first, $populated, $second], 4326);

        self::assertTrue($zero->isEmpty());
        self::assertSame([], $zero->getElements());
        self::assertTrue((new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\MultiPointImmutable([$first], 4326))->isEmpty());
        self::assertFalse((new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\MultiPointImmutable([$populated, $first], 4326))->isEmpty());
        self::assertFalse((new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\MultiPointImmutable([$first, $populated], 4326))->isEmpty());
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
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension3m\MultiPoint([], 4326, SpatialModelEnum::GEOGRAPHY);
        $all = new \LongitudeOne\SpatialTypes\Types\Dimension3m\MultiPoint([$first, $second], 4326, SpatialModelEnum::GEOGRAPHY);
        $mixed = new \LongitudeOne\SpatialTypes\Types\Dimension3m\MultiPoint([$first, $populated, $second], 4326, SpatialModelEnum::GEOGRAPHY);

        self::assertTrue($zero->isEmpty());
        self::assertSame([], $zero->getElements());
        self::assertTrue((new \LongitudeOne\SpatialTypes\Types\Dimension3m\MultiPoint([$first], 4326, SpatialModelEnum::GEOGRAPHY))->isEmpty());
        self::assertFalse((new \LongitudeOne\SpatialTypes\Types\Dimension3m\MultiPoint([$populated, $first], 4326, SpatialModelEnum::GEOGRAPHY))->isEmpty());
        self::assertFalse((new \LongitudeOne\SpatialTypes\Types\Dimension3m\MultiPoint([$first, $populated], 4326, SpatialModelEnum::GEOGRAPHY))->isEmpty());
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
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\MultiPointImmutable([], 4326);
        $all = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\MultiPointImmutable([$first, $second], 4326);
        $mixed = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\MultiPointImmutable([$first, $populated, $second], 4326);

        self::assertTrue($zero->isEmpty());
        self::assertSame([], $zero->getElements());
        self::assertTrue((new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\MultiPointImmutable([$first], 4326))->isEmpty());
        self::assertFalse((new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\MultiPointImmutable([$populated, $first], 4326))->isEmpty());
        self::assertFalse((new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\MultiPointImmutable([$first, $populated], 4326))->isEmpty());
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
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension3m\MultiPoint([], 4326, SpatialModelEnum::GEOMETRY);
        $all = new \LongitudeOne\SpatialTypes\Types\Dimension3m\MultiPoint([$first, $second], 4326, SpatialModelEnum::GEOMETRY);
        $mixed = new \LongitudeOne\SpatialTypes\Types\Dimension3m\MultiPoint([$first, $populated, $second], 4326, SpatialModelEnum::GEOMETRY);

        self::assertTrue($zero->isEmpty());
        self::assertSame([], $zero->getElements());
        self::assertTrue((new \LongitudeOne\SpatialTypes\Types\Dimension3m\MultiPoint([$first], 4326, SpatialModelEnum::GEOMETRY))->isEmpty());
        self::assertFalse((new \LongitudeOne\SpatialTypes\Types\Dimension3m\MultiPoint([$populated, $first], 4326, SpatialModelEnum::GEOMETRY))->isEmpty());
        self::assertFalse((new \LongitudeOne\SpatialTypes\Types\Dimension3m\MultiPoint([$first, $populated], 4326, SpatialModelEnum::GEOMETRY))->isEmpty());
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
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\MultiPointImmutable([], 4326);
        $all = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\MultiPointImmutable([$first, $second], 4326);
        $mixed = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\MultiPointImmutable([$first, $populated, $second], 4326);

        self::assertTrue($zero->isEmpty());
        self::assertSame([], $zero->getElements());
        self::assertTrue((new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\MultiPointImmutable([$first], 4326))->isEmpty());
        self::assertFalse((new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\MultiPointImmutable([$populated, $first], 4326))->isEmpty());
        self::assertFalse((new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\MultiPointImmutable([$first, $populated], 4326))->isEmpty());
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
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension3z\MultiPoint([], 4326, SpatialModelEnum::GEOGRAPHY);
        $all = new \LongitudeOne\SpatialTypes\Types\Dimension3z\MultiPoint([$first, $second], 4326, SpatialModelEnum::GEOGRAPHY);
        $mixed = new \LongitudeOne\SpatialTypes\Types\Dimension3z\MultiPoint([$first, $populated, $second], 4326, SpatialModelEnum::GEOGRAPHY);

        self::assertTrue($zero->isEmpty());
        self::assertSame([], $zero->getElements());
        self::assertTrue((new \LongitudeOne\SpatialTypes\Types\Dimension3z\MultiPoint([$first], 4326, SpatialModelEnum::GEOGRAPHY))->isEmpty());
        self::assertFalse((new \LongitudeOne\SpatialTypes\Types\Dimension3z\MultiPoint([$populated, $first], 4326, SpatialModelEnum::GEOGRAPHY))->isEmpty());
        self::assertFalse((new \LongitudeOne\SpatialTypes\Types\Dimension3z\MultiPoint([$first, $populated], 4326, SpatialModelEnum::GEOGRAPHY))->isEmpty());
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
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\MultiPointImmutable([], 4326);
        $all = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\MultiPointImmutable([$first, $second], 4326);
        $mixed = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\MultiPointImmutable([$first, $populated, $second], 4326);

        self::assertTrue($zero->isEmpty());
        self::assertSame([], $zero->getElements());
        self::assertTrue((new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\MultiPointImmutable([$first], 4326))->isEmpty());
        self::assertFalse((new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\MultiPointImmutable([$populated, $first], 4326))->isEmpty());
        self::assertFalse((new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\MultiPointImmutable([$first, $populated], 4326))->isEmpty());
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
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension3z\MultiPoint([], 4326, SpatialModelEnum::GEOMETRY);
        $all = new \LongitudeOne\SpatialTypes\Types\Dimension3z\MultiPoint([$first, $second], 4326, SpatialModelEnum::GEOMETRY);
        $mixed = new \LongitudeOne\SpatialTypes\Types\Dimension3z\MultiPoint([$first, $populated, $second], 4326, SpatialModelEnum::GEOMETRY);

        self::assertTrue($zero->isEmpty());
        self::assertSame([], $zero->getElements());
        self::assertTrue((new \LongitudeOne\SpatialTypes\Types\Dimension3z\MultiPoint([$first], 4326, SpatialModelEnum::GEOMETRY))->isEmpty());
        self::assertFalse((new \LongitudeOne\SpatialTypes\Types\Dimension3z\MultiPoint([$populated, $first], 4326, SpatialModelEnum::GEOMETRY))->isEmpty());
        self::assertFalse((new \LongitudeOne\SpatialTypes\Types\Dimension3z\MultiPoint([$first, $populated], 4326, SpatialModelEnum::GEOMETRY))->isEmpty());
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
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\MultiPointImmutable([], 4326);
        $all = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\MultiPointImmutable([$first, $second], 4326);
        $mixed = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\MultiPointImmutable([$first, $populated, $second], 4326);

        self::assertTrue($zero->isEmpty());
        self::assertSame([], $zero->getElements());
        self::assertTrue((new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\MultiPointImmutable([$first], 4326))->isEmpty());
        self::assertFalse((new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\MultiPointImmutable([$populated, $first], 4326))->isEmpty());
        self::assertFalse((new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\MultiPointImmutable([$first, $populated], 4326))->isEmpty());
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
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\MultiPoint([], 4326, SpatialModelEnum::GEOGRAPHY);
        $all = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\MultiPoint([$first, $second], 4326, SpatialModelEnum::GEOGRAPHY);
        $mixed = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\MultiPoint([$first, $populated, $second], 4326, SpatialModelEnum::GEOGRAPHY);

        self::assertTrue($zero->isEmpty());
        self::assertSame([], $zero->getElements());
        self::assertTrue((new \LongitudeOne\SpatialTypes\Types\Dimension4zm\MultiPoint([$first], 4326, SpatialModelEnum::GEOGRAPHY))->isEmpty());
        self::assertFalse((new \LongitudeOne\SpatialTypes\Types\Dimension4zm\MultiPoint([$populated, $first], 4326, SpatialModelEnum::GEOGRAPHY))->isEmpty());
        self::assertFalse((new \LongitudeOne\SpatialTypes\Types\Dimension4zm\MultiPoint([$first, $populated], 4326, SpatialModelEnum::GEOGRAPHY))->isEmpty());
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
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\MultiPointImmutable([], 4326);
        $all = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\MultiPointImmutable([$first, $second], 4326);
        $mixed = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\MultiPointImmutable([$first, $populated, $second], 4326);

        self::assertTrue($zero->isEmpty());
        self::assertSame([], $zero->getElements());
        self::assertTrue((new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\MultiPointImmutable([$first], 4326))->isEmpty());
        self::assertFalse((new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\MultiPointImmutable([$populated, $first], 4326))->isEmpty());
        self::assertFalse((new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\MultiPointImmutable([$first, $populated], 4326))->isEmpty());
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
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\MultiPoint([], 4326, SpatialModelEnum::GEOMETRY);
        $all = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\MultiPoint([$first, $second], 4326, SpatialModelEnum::GEOMETRY);
        $mixed = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\MultiPoint([$first, $populated, $second], 4326, SpatialModelEnum::GEOMETRY);

        self::assertTrue($zero->isEmpty());
        self::assertSame([], $zero->getElements());
        self::assertTrue((new \LongitudeOne\SpatialTypes\Types\Dimension4zm\MultiPoint([$first], 4326, SpatialModelEnum::GEOMETRY))->isEmpty());
        self::assertFalse((new \LongitudeOne\SpatialTypes\Types\Dimension4zm\MultiPoint([$populated, $first], 4326, SpatialModelEnum::GEOMETRY))->isEmpty());
        self::assertFalse((new \LongitudeOne\SpatialTypes\Types\Dimension4zm\MultiPoint([$first, $populated], 4326, SpatialModelEnum::GEOMETRY))->isEmpty());
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
