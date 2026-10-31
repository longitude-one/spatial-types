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
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\MultiPolygonImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\PolygonImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\MultiPolygon;
use LongitudeOne\SpatialTypes\Types\Dimension2\Polygon;
use PHPUnit\Framework\TestCase;

/** Approved all-EMPTY collection classification from Story #65.
 * @internal
 *
 * @coversNothing
 */
final class AllEmptyMultiPolygonTest extends TestCase
{
    /** Zero membership and all-EMPTY membership differ in storage, not emptiness. */
    public function testDimension2GeographyImmutableEmptyAndMixedMembers(): void
    {
        $first = new PolygonImmutable([], 4326);
        $second = new PolygonImmutable([], 4326);
        $populated = new PolygonImmutable([[[0, 0], [2, 0], [0, 2], [0, 0]]], 4326);
        $zero = new MultiPolygonImmutable([], 4326);
        $all = new MultiPolygonImmutable([$first, $second], 4326);
        $mixed = new MultiPolygonImmutable([$first, $populated, $second], 4326);

        self::assertSame([], $zero->getElements());
        self::assertCount(2, $all->getElements());
        self::assertSame([$first, $second], $all->getElements());
        self::assertSame([[], []], $all->toArray());
        self::assertCount(3, $mixed->getElements());
        self::assertSame([$first, $populated, $second], $mixed->getElements());
        self::assertSame([[], [[[0, 0], [2, 0], [0, 2], [0, 0]]], []], $mixed->toArray());
        self::assertSame(CoordinateDimensionEnum::XY, $all->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $all->getFamily());
        self::assertSame(4326, $all->getSrid());
        self::assertSame(4326, $mixed->getSrid());
    }

    /** Zero membership and all-EMPTY membership differ in storage, not emptiness. */
    public function testDimension2GeographyMutableEmptyAndMixedMembers(): void
    {
        $first = new Polygon([], 4326, SpatialModelEnum::GEOGRAPHY);
        $second = new Polygon([], 4326, SpatialModelEnum::GEOGRAPHY);
        $populated = new Polygon([[[0, 0], [2, 0], [0, 2], [0, 0]]], 4326, SpatialModelEnum::GEOGRAPHY);
        $zero = new MultiPolygon([], 4326, SpatialModelEnum::GEOGRAPHY);
        $all = new MultiPolygon([$first, $second], 4326, SpatialModelEnum::GEOGRAPHY);
        $mixed = new MultiPolygon([$first, $populated, $second], 4326, SpatialModelEnum::GEOGRAPHY);

        self::assertSame([], $zero->getElements());
        self::assertCount(2, $all->getElements());
        self::assertSame([$first, $second], $all->getElements());
        self::assertSame([[], []], $all->toArray());
        self::assertCount(3, $mixed->getElements());
        self::assertSame([$first, $populated, $second], $mixed->getElements());
        self::assertSame([[], [[[0, 0], [2, 0], [0, 2], [0, 0]]], []], $mixed->toArray());
        self::assertSame(CoordinateDimensionEnum::XY, $all->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $all->getFamily());
        self::assertSame(4326, $all->getSrid());
        self::assertSame(4326, $mixed->getSrid());
    }

    /** Zero membership and all-EMPTY membership differ in storage, not emptiness. */
    public function testDimension2GeometryImmutableEmptyAndMixedMembers(): void
    {
        $first = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PolygonImmutable([], 4326);
        $second = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PolygonImmutable([], 4326);
        $populated = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PolygonImmutable([[[0, 0], [2, 0], [0, 2], [0, 0]]], 4326);
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiPolygonImmutable([], 4326);
        $all = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiPolygonImmutable([$first, $second], 4326);
        $mixed = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiPolygonImmutable([$first, $populated, $second], 4326);

        self::assertSame([], $zero->getElements());
        self::assertCount(2, $all->getElements());
        self::assertSame([$first, $second], $all->getElements());
        self::assertSame([[], []], $all->toArray());
        self::assertCount(3, $mixed->getElements());
        self::assertSame([$first, $populated, $second], $mixed->getElements());
        self::assertSame([[], [[[0, 0], [2, 0], [0, 2], [0, 0]]], []], $mixed->toArray());
        self::assertSame(CoordinateDimensionEnum::XY, $all->getDimension());
        self::assertSame(SpatialModelEnum::GEOMETRY, $all->getFamily());
        self::assertSame(4326, $all->getSrid());
        self::assertSame(4326, $mixed->getSrid());
    }

    /** Zero membership and all-EMPTY membership differ in storage, not emptiness. */
    public function testDimension2GeometryMutableEmptyAndMixedMembers(): void
    {
        $first = new Polygon([], 4326, SpatialModelEnum::GEOMETRY);
        $second = new Polygon([], 4326, SpatialModelEnum::GEOMETRY);
        $populated = new Polygon([[[0, 0], [2, 0], [0, 2], [0, 0]]], 4326, SpatialModelEnum::GEOMETRY);
        $zero = new MultiPolygon([], 4326, SpatialModelEnum::GEOMETRY);
        $all = new MultiPolygon([$first, $second], 4326, SpatialModelEnum::GEOMETRY);
        $mixed = new MultiPolygon([$first, $populated, $second], 4326, SpatialModelEnum::GEOMETRY);

        self::assertSame([], $zero->getElements());
        self::assertCount(2, $all->getElements());
        self::assertSame([$first, $second], $all->getElements());
        self::assertSame([[], []], $all->toArray());
        self::assertCount(3, $mixed->getElements());
        self::assertSame([$first, $populated, $second], $mixed->getElements());
        self::assertSame([[], [[[0, 0], [2, 0], [0, 2], [0, 0]]], []], $mixed->toArray());
        self::assertSame(CoordinateDimensionEnum::XY, $all->getDimension());
        self::assertSame(SpatialModelEnum::GEOMETRY, $all->getFamily());
        self::assertSame(4326, $all->getSrid());
        self::assertSame(4326, $mixed->getSrid());
    }

    /** Zero membership and all-EMPTY membership differ in storage, not emptiness. */
    public function testDimension3mGeographyImmutableEmptyAndMixedMembers(): void
    {
        $first = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\PolygonImmutable([], 4326);
        $second = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\PolygonImmutable([], 4326);
        $populated = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\PolygonImmutable([[[0, 0, 1], [2, 0, 2], [0, 2, 3], [0, 0, 1]]], 4326);
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\MultiPolygonImmutable([], 4326);
        $all = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\MultiPolygonImmutable([$first, $second], 4326);
        $mixed = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\MultiPolygonImmutable([$first, $populated, $second], 4326);

        self::assertSame([], $zero->getElements());
        self::assertCount(2, $all->getElements());
        self::assertSame([$first, $second], $all->getElements());
        self::assertSame([[], []], $all->toArray());
        self::assertCount(3, $mixed->getElements());
        self::assertSame([$first, $populated, $second], $mixed->getElements());
        self::assertSame([[], [[[0, 0, 1], [2, 0, 2], [0, 2, 3], [0, 0, 1]]], []], $mixed->toArray());
        self::assertSame(CoordinateDimensionEnum::XYM, $all->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $all->getFamily());
        self::assertSame(4326, $all->getSrid());
        self::assertSame(4326, $mixed->getSrid());
    }

    /** Zero membership and all-EMPTY membership differ in storage, not emptiness. */
    public function testDimension3mGeographyMutableEmptyAndMixedMembers(): void
    {
        $first = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Polygon([], 4326, SpatialModelEnum::GEOGRAPHY);
        $second = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Polygon([], 4326, SpatialModelEnum::GEOGRAPHY);
        $populated = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Polygon([[[0, 0, 1], [2, 0, 2], [0, 2, 3], [0, 0, 1]]], 4326, SpatialModelEnum::GEOGRAPHY);
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension3m\MultiPolygon([], 4326, SpatialModelEnum::GEOGRAPHY);
        $all = new \LongitudeOne\SpatialTypes\Types\Dimension3m\MultiPolygon([$first, $second], 4326, SpatialModelEnum::GEOGRAPHY);
        $mixed = new \LongitudeOne\SpatialTypes\Types\Dimension3m\MultiPolygon([$first, $populated, $second], 4326, SpatialModelEnum::GEOGRAPHY);

        self::assertSame([], $zero->getElements());
        self::assertCount(2, $all->getElements());
        self::assertSame([$first, $second], $all->getElements());
        self::assertSame([[], []], $all->toArray());
        self::assertCount(3, $mixed->getElements());
        self::assertSame([$first, $populated, $second], $mixed->getElements());
        self::assertSame([[], [[[0, 0, 1], [2, 0, 2], [0, 2, 3], [0, 0, 1]]], []], $mixed->toArray());
        self::assertSame(CoordinateDimensionEnum::XYM, $all->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $all->getFamily());
        self::assertSame(4326, $all->getSrid());
        self::assertSame(4326, $mixed->getSrid());
    }

    /** Zero membership and all-EMPTY membership differ in storage, not emptiness. */
    public function testDimension3mGeometryImmutableEmptyAndMixedMembers(): void
    {
        $first = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\PolygonImmutable([], 4326);
        $second = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\PolygonImmutable([], 4326);
        $populated = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\PolygonImmutable([[[0, 0, 1], [2, 0, 2], [0, 2, 3], [0, 0, 1]]], 4326);
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\MultiPolygonImmutable([], 4326);
        $all = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\MultiPolygonImmutable([$first, $second], 4326);
        $mixed = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\MultiPolygonImmutable([$first, $populated, $second], 4326);

        self::assertSame([], $zero->getElements());
        self::assertCount(2, $all->getElements());
        self::assertSame([$first, $second], $all->getElements());
        self::assertSame([[], []], $all->toArray());
        self::assertCount(3, $mixed->getElements());
        self::assertSame([$first, $populated, $second], $mixed->getElements());
        self::assertSame([[], [[[0, 0, 1], [2, 0, 2], [0, 2, 3], [0, 0, 1]]], []], $mixed->toArray());
        self::assertSame(CoordinateDimensionEnum::XYM, $all->getDimension());
        self::assertSame(SpatialModelEnum::GEOMETRY, $all->getFamily());
        self::assertSame(4326, $all->getSrid());
        self::assertSame(4326, $mixed->getSrid());
    }

    /** Zero membership and all-EMPTY membership differ in storage, not emptiness. */
    public function testDimension3mGeometryMutableEmptyAndMixedMembers(): void
    {
        $first = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Polygon([], 4326, SpatialModelEnum::GEOMETRY);
        $second = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Polygon([], 4326, SpatialModelEnum::GEOMETRY);
        $populated = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Polygon([[[0, 0, 1], [2, 0, 2], [0, 2, 3], [0, 0, 1]]], 4326, SpatialModelEnum::GEOMETRY);
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension3m\MultiPolygon([], 4326, SpatialModelEnum::GEOMETRY);
        $all = new \LongitudeOne\SpatialTypes\Types\Dimension3m\MultiPolygon([$first, $second], 4326, SpatialModelEnum::GEOMETRY);
        $mixed = new \LongitudeOne\SpatialTypes\Types\Dimension3m\MultiPolygon([$first, $populated, $second], 4326, SpatialModelEnum::GEOMETRY);

        self::assertSame([], $zero->getElements());
        self::assertCount(2, $all->getElements());
        self::assertSame([$first, $second], $all->getElements());
        self::assertSame([[], []], $all->toArray());
        self::assertCount(3, $mixed->getElements());
        self::assertSame([$first, $populated, $second], $mixed->getElements());
        self::assertSame([[], [[[0, 0, 1], [2, 0, 2], [0, 2, 3], [0, 0, 1]]], []], $mixed->toArray());
        self::assertSame(CoordinateDimensionEnum::XYM, $all->getDimension());
        self::assertSame(SpatialModelEnum::GEOMETRY, $all->getFamily());
        self::assertSame(4326, $all->getSrid());
        self::assertSame(4326, $mixed->getSrid());
    }

    /** Zero membership and all-EMPTY membership differ in storage, not emptiness. */
    public function testDimension3zGeographyImmutableEmptyAndMixedMembers(): void
    {
        $first = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\PolygonImmutable([], 4326);
        $second = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\PolygonImmutable([], 4326);
        $populated = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\PolygonImmutable([[[0, 0, 1], [2, 0, 1], [0, 2, 1], [0, 0, 1]]], 4326);
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\MultiPolygonImmutable([], 4326);
        $all = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\MultiPolygonImmutable([$first, $second], 4326);
        $mixed = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\MultiPolygonImmutable([$first, $populated, $second], 4326);

        self::assertSame([], $zero->getElements());
        self::assertCount(2, $all->getElements());
        self::assertSame([$first, $second], $all->getElements());
        self::assertSame([[], []], $all->toArray());
        self::assertCount(3, $mixed->getElements());
        self::assertSame([$first, $populated, $second], $mixed->getElements());
        self::assertSame([[], [[[0, 0, 1], [2, 0, 1], [0, 2, 1], [0, 0, 1]]], []], $mixed->toArray());
        self::assertSame(CoordinateDimensionEnum::XYZ, $all->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $all->getFamily());
        self::assertSame(4326, $all->getSrid());
        self::assertSame(4326, $mixed->getSrid());
    }

    /** Zero membership and all-EMPTY membership differ in storage, not emptiness. */
    public function testDimension3zGeographyMutableEmptyAndMixedMembers(): void
    {
        $first = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Polygon([], 4326, SpatialModelEnum::GEOGRAPHY);
        $second = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Polygon([], 4326, SpatialModelEnum::GEOGRAPHY);
        $populated = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Polygon([[[0, 0, 1], [2, 0, 1], [0, 2, 1], [0, 0, 1]]], 4326, SpatialModelEnum::GEOGRAPHY);
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension3z\MultiPolygon([], 4326, SpatialModelEnum::GEOGRAPHY);
        $all = new \LongitudeOne\SpatialTypes\Types\Dimension3z\MultiPolygon([$first, $second], 4326, SpatialModelEnum::GEOGRAPHY);
        $mixed = new \LongitudeOne\SpatialTypes\Types\Dimension3z\MultiPolygon([$first, $populated, $second], 4326, SpatialModelEnum::GEOGRAPHY);

        self::assertSame([], $zero->getElements());
        self::assertCount(2, $all->getElements());
        self::assertSame([$first, $second], $all->getElements());
        self::assertSame([[], []], $all->toArray());
        self::assertCount(3, $mixed->getElements());
        self::assertSame([$first, $populated, $second], $mixed->getElements());
        self::assertSame([[], [[[0, 0, 1], [2, 0, 1], [0, 2, 1], [0, 0, 1]]], []], $mixed->toArray());
        self::assertSame(CoordinateDimensionEnum::XYZ, $all->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $all->getFamily());
        self::assertSame(4326, $all->getSrid());
        self::assertSame(4326, $mixed->getSrid());
    }

    /** Zero membership and all-EMPTY membership differ in storage, not emptiness. */
    public function testDimension3zGeometryImmutableEmptyAndMixedMembers(): void
    {
        $first = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\PolygonImmutable([], 4326);
        $second = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\PolygonImmutable([], 4326);
        $populated = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\PolygonImmutable([[[0, 0, 1], [2, 0, 1], [0, 2, 1], [0, 0, 1]]], 4326);
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\MultiPolygonImmutable([], 4326);
        $all = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\MultiPolygonImmutable([$first, $second], 4326);
        $mixed = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\MultiPolygonImmutable([$first, $populated, $second], 4326);

        self::assertSame([], $zero->getElements());
        self::assertCount(2, $all->getElements());
        self::assertSame([$first, $second], $all->getElements());
        self::assertSame([[], []], $all->toArray());
        self::assertCount(3, $mixed->getElements());
        self::assertSame([$first, $populated, $second], $mixed->getElements());
        self::assertSame([[], [[[0, 0, 1], [2, 0, 1], [0, 2, 1], [0, 0, 1]]], []], $mixed->toArray());
        self::assertSame(CoordinateDimensionEnum::XYZ, $all->getDimension());
        self::assertSame(SpatialModelEnum::GEOMETRY, $all->getFamily());
        self::assertSame(4326, $all->getSrid());
        self::assertSame(4326, $mixed->getSrid());
    }

    /** Zero membership and all-EMPTY membership differ in storage, not emptiness. */
    public function testDimension3zGeometryMutableEmptyAndMixedMembers(): void
    {
        $first = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Polygon([], 4326, SpatialModelEnum::GEOMETRY);
        $second = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Polygon([], 4326, SpatialModelEnum::GEOMETRY);
        $populated = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Polygon([[[0, 0, 1], [2, 0, 1], [0, 2, 1], [0, 0, 1]]], 4326, SpatialModelEnum::GEOMETRY);
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension3z\MultiPolygon([], 4326, SpatialModelEnum::GEOMETRY);
        $all = new \LongitudeOne\SpatialTypes\Types\Dimension3z\MultiPolygon([$first, $second], 4326, SpatialModelEnum::GEOMETRY);
        $mixed = new \LongitudeOne\SpatialTypes\Types\Dimension3z\MultiPolygon([$first, $populated, $second], 4326, SpatialModelEnum::GEOMETRY);

        self::assertSame([], $zero->getElements());
        self::assertCount(2, $all->getElements());
        self::assertSame([$first, $second], $all->getElements());
        self::assertSame([[], []], $all->toArray());
        self::assertCount(3, $mixed->getElements());
        self::assertSame([$first, $populated, $second], $mixed->getElements());
        self::assertSame([[], [[[0, 0, 1], [2, 0, 1], [0, 2, 1], [0, 0, 1]]], []], $mixed->toArray());
        self::assertSame(CoordinateDimensionEnum::XYZ, $all->getDimension());
        self::assertSame(SpatialModelEnum::GEOMETRY, $all->getFamily());
        self::assertSame(4326, $all->getSrid());
        self::assertSame(4326, $mixed->getSrid());
    }

    /** Zero membership and all-EMPTY membership differ in storage, not emptiness. */
    public function testDimension4zmGeographyImmutableEmptyAndMixedMembers(): void
    {
        $first = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\PolygonImmutable([], 4326);
        $second = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\PolygonImmutable([], 4326);
        $populated = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\PolygonImmutable([[[0, 0, 1, 1], [2, 0, 1, 2], [0, 2, 1, 3], [0, 0, 1, 1]]], 4326);
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\MultiPolygonImmutable([], 4326);
        $all = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\MultiPolygonImmutable([$first, $second], 4326);
        $mixed = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\MultiPolygonImmutable([$first, $populated, $second], 4326);

        self::assertSame([], $zero->getElements());
        self::assertCount(2, $all->getElements());
        self::assertSame([$first, $second], $all->getElements());
        self::assertSame([[], []], $all->toArray());
        self::assertCount(3, $mixed->getElements());
        self::assertSame([$first, $populated, $second], $mixed->getElements());
        self::assertSame([[], [[[0, 0, 1, 1], [2, 0, 1, 2], [0, 2, 1, 3], [0, 0, 1, 1]]], []], $mixed->toArray());
        self::assertSame(CoordinateDimensionEnum::XYZM, $all->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $all->getFamily());
        self::assertSame(4326, $all->getSrid());
        self::assertSame(4326, $mixed->getSrid());
    }

    /** Zero membership and all-EMPTY membership differ in storage, not emptiness. */
    public function testDimension4zmGeographyMutableEmptyAndMixedMembers(): void
    {
        $first = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Polygon([], 4326, SpatialModelEnum::GEOGRAPHY);
        $second = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Polygon([], 4326, SpatialModelEnum::GEOGRAPHY);
        $populated = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Polygon([[[0, 0, 1, 1], [2, 0, 1, 2], [0, 2, 1, 3], [0, 0, 1, 1]]], 4326, SpatialModelEnum::GEOGRAPHY);
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\MultiPolygon([], 4326, SpatialModelEnum::GEOGRAPHY);
        $all = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\MultiPolygon([$first, $second], 4326, SpatialModelEnum::GEOGRAPHY);
        $mixed = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\MultiPolygon([$first, $populated, $second], 4326, SpatialModelEnum::GEOGRAPHY);

        self::assertSame([], $zero->getElements());
        self::assertCount(2, $all->getElements());
        self::assertSame([$first, $second], $all->getElements());
        self::assertSame([[], []], $all->toArray());
        self::assertCount(3, $mixed->getElements());
        self::assertSame([$first, $populated, $second], $mixed->getElements());
        self::assertSame([[], [[[0, 0, 1, 1], [2, 0, 1, 2], [0, 2, 1, 3], [0, 0, 1, 1]]], []], $mixed->toArray());
        self::assertSame(CoordinateDimensionEnum::XYZM, $all->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $all->getFamily());
        self::assertSame(4326, $all->getSrid());
        self::assertSame(4326, $mixed->getSrid());
    }

    /** Zero membership and all-EMPTY membership differ in storage, not emptiness. */
    public function testDimension4zmGeometryImmutableEmptyAndMixedMembers(): void
    {
        $first = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\PolygonImmutable([], 4326);
        $second = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\PolygonImmutable([], 4326);
        $populated = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\PolygonImmutable([[[0, 0, 1, 1], [2, 0, 1, 2], [0, 2, 1, 3], [0, 0, 1, 1]]], 4326);
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\MultiPolygonImmutable([], 4326);
        $all = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\MultiPolygonImmutable([$first, $second], 4326);
        $mixed = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\MultiPolygonImmutable([$first, $populated, $second], 4326);

        self::assertSame([], $zero->getElements());
        self::assertCount(2, $all->getElements());
        self::assertSame([$first, $second], $all->getElements());
        self::assertSame([[], []], $all->toArray());
        self::assertCount(3, $mixed->getElements());
        self::assertSame([$first, $populated, $second], $mixed->getElements());
        self::assertSame([[], [[[0, 0, 1, 1], [2, 0, 1, 2], [0, 2, 1, 3], [0, 0, 1, 1]]], []], $mixed->toArray());
        self::assertSame(CoordinateDimensionEnum::XYZM, $all->getDimension());
        self::assertSame(SpatialModelEnum::GEOMETRY, $all->getFamily());
        self::assertSame(4326, $all->getSrid());
        self::assertSame(4326, $mixed->getSrid());
    }

    /** Zero membership and all-EMPTY membership differ in storage, not emptiness. */
    public function testDimension4zmGeometryMutableEmptyAndMixedMembers(): void
    {
        $first = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Polygon([], 4326, SpatialModelEnum::GEOMETRY);
        $second = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Polygon([], 4326, SpatialModelEnum::GEOMETRY);
        $populated = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Polygon([[[0, 0, 1, 1], [2, 0, 1, 2], [0, 2, 1, 3], [0, 0, 1, 1]]], 4326, SpatialModelEnum::GEOMETRY);
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\MultiPolygon([], 4326, SpatialModelEnum::GEOMETRY);
        $all = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\MultiPolygon([$first, $second], 4326, SpatialModelEnum::GEOMETRY);
        $mixed = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\MultiPolygon([$first, $populated, $second], 4326, SpatialModelEnum::GEOMETRY);

        self::assertSame([], $zero->getElements());
        self::assertCount(2, $all->getElements());
        self::assertSame([$first, $second], $all->getElements());
        self::assertSame([[], []], $all->toArray());
        self::assertCount(3, $mixed->getElements());
        self::assertSame([$first, $populated, $second], $mixed->getElements());
        self::assertSame([[], [[[0, 0, 1, 1], [2, 0, 1, 2], [0, 2, 1, 3], [0, 0, 1, 1]]], []], $mixed->toArray());
        self::assertSame(CoordinateDimensionEnum::XYZM, $all->getDimension());
        self::assertSame(SpatialModelEnum::GEOMETRY, $all->getFamily());
        self::assertSame(4326, $all->getSrid());
        self::assertSame(4326, $mixed->getSrid());
    }
}
