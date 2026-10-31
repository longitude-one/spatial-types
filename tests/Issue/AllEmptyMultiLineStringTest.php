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
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\LineStringImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\MultiLineStringImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\LineString;
use LongitudeOne\SpatialTypes\Types\Dimension2\MultiLineString;
use PHPUnit\Framework\TestCase;

/** Approved all-EMPTY collection classification from Story #65.
 * @internal
 *
 * @coversNothing
 */
final class AllEmptyMultiLineStringTest extends TestCase
{
    /** Zero membership and all-EMPTY membership differ in storage, not emptiness. */
    public function testDimension2GeographyImmutableEmptyAndMixedMembers(): void
    {
        $first = new LineStringImmutable([], 4326);
        $second = new LineStringImmutable([], 4326);
        $populated = new LineStringImmutable([[0, 0], [2, 0]], 4326);
        $zero = new MultiLineStringImmutable([], 4326);
        $all = new MultiLineStringImmutable([$first, $second], 4326);
        $mixed = new MultiLineStringImmutable([$first, $populated, $second], 4326);

        self::assertSame([], $zero->getElements());
        self::assertCount(2, $all->getElements());
        self::assertSame([$first, $second], $all->getElements());
        self::assertSame([[], []], $all->toArray());
        self::assertCount(3, $mixed->getElements());
        self::assertSame([$first, $populated, $second], $mixed->getElements());
        self::assertSame([[], [[0, 0], [2, 0]], []], $mixed->toArray());
        self::assertSame(CoordinateDimensionEnum::XY, $all->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $all->getFamily());
        self::assertSame(4326, $all->getSrid());
        self::assertSame(4326, $mixed->getSrid());
    }

    /** Zero membership and all-EMPTY membership differ in storage, not emptiness. */
    public function testDimension2GeographyMutableEmptyAndMixedMembers(): void
    {
        $first = new LineString([], 4326, SpatialModelEnum::GEOGRAPHY);
        $second = new LineString([], 4326, SpatialModelEnum::GEOGRAPHY);
        $populated = new LineString([[0, 0], [2, 0]], 4326, SpatialModelEnum::GEOGRAPHY);
        $zero = new MultiLineString([], 4326, SpatialModelEnum::GEOGRAPHY);
        $all = new MultiLineString([$first, $second], 4326, SpatialModelEnum::GEOGRAPHY);
        $mixed = new MultiLineString([$first, $populated, $second], 4326, SpatialModelEnum::GEOGRAPHY);

        self::assertSame([], $zero->getElements());
        self::assertCount(2, $all->getElements());
        self::assertSame([$first, $second], $all->getElements());
        self::assertSame([[], []], $all->toArray());
        self::assertCount(3, $mixed->getElements());
        self::assertSame([$first, $populated, $second], $mixed->getElements());
        self::assertSame([[], [[0, 0], [2, 0]], []], $mixed->toArray());
        self::assertSame(CoordinateDimensionEnum::XY, $all->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $all->getFamily());
        self::assertSame(4326, $all->getSrid());
        self::assertSame(4326, $mixed->getSrid());
    }

    /** Zero membership and all-EMPTY membership differ in storage, not emptiness. */
    public function testDimension2GeometryImmutableEmptyAndMixedMembers(): void
    {
        $first = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineStringImmutable([], 4326);
        $second = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineStringImmutable([], 4326);
        $populated = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineStringImmutable([[0, 0], [2, 0]], 4326);
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiLineStringImmutable([], 4326);
        $all = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiLineStringImmutable([$first, $second], 4326);
        $mixed = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiLineStringImmutable([$first, $populated, $second], 4326);

        self::assertSame([], $zero->getElements());
        self::assertCount(2, $all->getElements());
        self::assertSame([$first, $second], $all->getElements());
        self::assertSame([[], []], $all->toArray());
        self::assertCount(3, $mixed->getElements());
        self::assertSame([$first, $populated, $second], $mixed->getElements());
        self::assertSame([[], [[0, 0], [2, 0]], []], $mixed->toArray());
        self::assertSame(CoordinateDimensionEnum::XY, $all->getDimension());
        self::assertSame(SpatialModelEnum::GEOMETRY, $all->getFamily());
        self::assertSame(4326, $all->getSrid());
        self::assertSame(4326, $mixed->getSrid());
    }

    /** Zero membership and all-EMPTY membership differ in storage, not emptiness. */
    public function testDimension2GeometryMutableEmptyAndMixedMembers(): void
    {
        $first = new LineString([], 4326, SpatialModelEnum::GEOMETRY);
        $second = new LineString([], 4326, SpatialModelEnum::GEOMETRY);
        $populated = new LineString([[0, 0], [2, 0]], 4326, SpatialModelEnum::GEOMETRY);
        $zero = new MultiLineString([], 4326, SpatialModelEnum::GEOMETRY);
        $all = new MultiLineString([$first, $second], 4326, SpatialModelEnum::GEOMETRY);
        $mixed = new MultiLineString([$first, $populated, $second], 4326, SpatialModelEnum::GEOMETRY);

        self::assertSame([], $zero->getElements());
        self::assertCount(2, $all->getElements());
        self::assertSame([$first, $second], $all->getElements());
        self::assertSame([[], []], $all->toArray());
        self::assertCount(3, $mixed->getElements());
        self::assertSame([$first, $populated, $second], $mixed->getElements());
        self::assertSame([[], [[0, 0], [2, 0]], []], $mixed->toArray());
        self::assertSame(CoordinateDimensionEnum::XY, $all->getDimension());
        self::assertSame(SpatialModelEnum::GEOMETRY, $all->getFamily());
        self::assertSame(4326, $all->getSrid());
        self::assertSame(4326, $mixed->getSrid());
    }

    /** Zero membership and all-EMPTY membership differ in storage, not emptiness. */
    public function testDimension3mGeographyImmutableEmptyAndMixedMembers(): void
    {
        $first = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\LineStringImmutable([], 4326);
        $second = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\LineStringImmutable([], 4326);
        $populated = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\LineStringImmutable([[0, 0, 1], [2, 0, 2]], 4326);
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\MultiLineStringImmutable([], 4326);
        $all = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\MultiLineStringImmutable([$first, $second], 4326);
        $mixed = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\MultiLineStringImmutable([$first, $populated, $second], 4326);

        self::assertSame([], $zero->getElements());
        self::assertCount(2, $all->getElements());
        self::assertSame([$first, $second], $all->getElements());
        self::assertSame([[], []], $all->toArray());
        self::assertCount(3, $mixed->getElements());
        self::assertSame([$first, $populated, $second], $mixed->getElements());
        self::assertSame([[], [[0, 0, 1], [2, 0, 2]], []], $mixed->toArray());
        self::assertSame(CoordinateDimensionEnum::XYM, $all->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $all->getFamily());
        self::assertSame(4326, $all->getSrid());
        self::assertSame(4326, $mixed->getSrid());
    }

    /** Zero membership and all-EMPTY membership differ in storage, not emptiness. */
    public function testDimension3mGeographyMutableEmptyAndMixedMembers(): void
    {
        $first = new \LongitudeOne\SpatialTypes\Types\Dimension3m\LineString([], 4326, SpatialModelEnum::GEOGRAPHY);
        $second = new \LongitudeOne\SpatialTypes\Types\Dimension3m\LineString([], 4326, SpatialModelEnum::GEOGRAPHY);
        $populated = new \LongitudeOne\SpatialTypes\Types\Dimension3m\LineString([[0, 0, 1], [2, 0, 2]], 4326, SpatialModelEnum::GEOGRAPHY);
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension3m\MultiLineString([], 4326, SpatialModelEnum::GEOGRAPHY);
        $all = new \LongitudeOne\SpatialTypes\Types\Dimension3m\MultiLineString([$first, $second], 4326, SpatialModelEnum::GEOGRAPHY);
        $mixed = new \LongitudeOne\SpatialTypes\Types\Dimension3m\MultiLineString([$first, $populated, $second], 4326, SpatialModelEnum::GEOGRAPHY);

        self::assertSame([], $zero->getElements());
        self::assertCount(2, $all->getElements());
        self::assertSame([$first, $second], $all->getElements());
        self::assertSame([[], []], $all->toArray());
        self::assertCount(3, $mixed->getElements());
        self::assertSame([$first, $populated, $second], $mixed->getElements());
        self::assertSame([[], [[0, 0, 1], [2, 0, 2]], []], $mixed->toArray());
        self::assertSame(CoordinateDimensionEnum::XYM, $all->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $all->getFamily());
        self::assertSame(4326, $all->getSrid());
        self::assertSame(4326, $mixed->getSrid());
    }

    /** Zero membership and all-EMPTY membership differ in storage, not emptiness. */
    public function testDimension3mGeometryImmutableEmptyAndMixedMembers(): void
    {
        $first = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\LineStringImmutable([], 4326);
        $second = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\LineStringImmutable([], 4326);
        $populated = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\LineStringImmutable([[0, 0, 1], [2, 0, 2]], 4326);
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\MultiLineStringImmutable([], 4326);
        $all = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\MultiLineStringImmutable([$first, $second], 4326);
        $mixed = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\MultiLineStringImmutable([$first, $populated, $second], 4326);

        self::assertSame([], $zero->getElements());
        self::assertCount(2, $all->getElements());
        self::assertSame([$first, $second], $all->getElements());
        self::assertSame([[], []], $all->toArray());
        self::assertCount(3, $mixed->getElements());
        self::assertSame([$first, $populated, $second], $mixed->getElements());
        self::assertSame([[], [[0, 0, 1], [2, 0, 2]], []], $mixed->toArray());
        self::assertSame(CoordinateDimensionEnum::XYM, $all->getDimension());
        self::assertSame(SpatialModelEnum::GEOMETRY, $all->getFamily());
        self::assertSame(4326, $all->getSrid());
        self::assertSame(4326, $mixed->getSrid());
    }

    /** Zero membership and all-EMPTY membership differ in storage, not emptiness. */
    public function testDimension3mGeometryMutableEmptyAndMixedMembers(): void
    {
        $first = new \LongitudeOne\SpatialTypes\Types\Dimension3m\LineString([], 4326, SpatialModelEnum::GEOMETRY);
        $second = new \LongitudeOne\SpatialTypes\Types\Dimension3m\LineString([], 4326, SpatialModelEnum::GEOMETRY);
        $populated = new \LongitudeOne\SpatialTypes\Types\Dimension3m\LineString([[0, 0, 1], [2, 0, 2]], 4326, SpatialModelEnum::GEOMETRY);
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension3m\MultiLineString([], 4326, SpatialModelEnum::GEOMETRY);
        $all = new \LongitudeOne\SpatialTypes\Types\Dimension3m\MultiLineString([$first, $second], 4326, SpatialModelEnum::GEOMETRY);
        $mixed = new \LongitudeOne\SpatialTypes\Types\Dimension3m\MultiLineString([$first, $populated, $second], 4326, SpatialModelEnum::GEOMETRY);

        self::assertSame([], $zero->getElements());
        self::assertCount(2, $all->getElements());
        self::assertSame([$first, $second], $all->getElements());
        self::assertSame([[], []], $all->toArray());
        self::assertCount(3, $mixed->getElements());
        self::assertSame([$first, $populated, $second], $mixed->getElements());
        self::assertSame([[], [[0, 0, 1], [2, 0, 2]], []], $mixed->toArray());
        self::assertSame(CoordinateDimensionEnum::XYM, $all->getDimension());
        self::assertSame(SpatialModelEnum::GEOMETRY, $all->getFamily());
        self::assertSame(4326, $all->getSrid());
        self::assertSame(4326, $mixed->getSrid());
    }

    /** Zero membership and all-EMPTY membership differ in storage, not emptiness. */
    public function testDimension3zGeographyImmutableEmptyAndMixedMembers(): void
    {
        $first = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\LineStringImmutable([], 4326);
        $second = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\LineStringImmutable([], 4326);
        $populated = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\LineStringImmutable([[0, 0, 1], [2, 0, 1]], 4326);
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\MultiLineStringImmutable([], 4326);
        $all = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\MultiLineStringImmutable([$first, $second], 4326);
        $mixed = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\MultiLineStringImmutable([$first, $populated, $second], 4326);

        self::assertSame([], $zero->getElements());
        self::assertCount(2, $all->getElements());
        self::assertSame([$first, $second], $all->getElements());
        self::assertSame([[], []], $all->toArray());
        self::assertCount(3, $mixed->getElements());
        self::assertSame([$first, $populated, $second], $mixed->getElements());
        self::assertSame([[], [[0, 0, 1], [2, 0, 1]], []], $mixed->toArray());
        self::assertSame(CoordinateDimensionEnum::XYZ, $all->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $all->getFamily());
        self::assertSame(4326, $all->getSrid());
        self::assertSame(4326, $mixed->getSrid());
    }

    /** Zero membership and all-EMPTY membership differ in storage, not emptiness. */
    public function testDimension3zGeographyMutableEmptyAndMixedMembers(): void
    {
        $first = new \LongitudeOne\SpatialTypes\Types\Dimension3z\LineString([], 4326, SpatialModelEnum::GEOGRAPHY);
        $second = new \LongitudeOne\SpatialTypes\Types\Dimension3z\LineString([], 4326, SpatialModelEnum::GEOGRAPHY);
        $populated = new \LongitudeOne\SpatialTypes\Types\Dimension3z\LineString([[0, 0, 1], [2, 0, 1]], 4326, SpatialModelEnum::GEOGRAPHY);
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension3z\MultiLineString([], 4326, SpatialModelEnum::GEOGRAPHY);
        $all = new \LongitudeOne\SpatialTypes\Types\Dimension3z\MultiLineString([$first, $second], 4326, SpatialModelEnum::GEOGRAPHY);
        $mixed = new \LongitudeOne\SpatialTypes\Types\Dimension3z\MultiLineString([$first, $populated, $second], 4326, SpatialModelEnum::GEOGRAPHY);

        self::assertSame([], $zero->getElements());
        self::assertCount(2, $all->getElements());
        self::assertSame([$first, $second], $all->getElements());
        self::assertSame([[], []], $all->toArray());
        self::assertCount(3, $mixed->getElements());
        self::assertSame([$first, $populated, $second], $mixed->getElements());
        self::assertSame([[], [[0, 0, 1], [2, 0, 1]], []], $mixed->toArray());
        self::assertSame(CoordinateDimensionEnum::XYZ, $all->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $all->getFamily());
        self::assertSame(4326, $all->getSrid());
        self::assertSame(4326, $mixed->getSrid());
    }

    /** Zero membership and all-EMPTY membership differ in storage, not emptiness. */
    public function testDimension3zGeometryImmutableEmptyAndMixedMembers(): void
    {
        $first = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\LineStringImmutable([], 4326);
        $second = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\LineStringImmutable([], 4326);
        $populated = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\LineStringImmutable([[0, 0, 1], [2, 0, 1]], 4326);
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\MultiLineStringImmutable([], 4326);
        $all = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\MultiLineStringImmutable([$first, $second], 4326);
        $mixed = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\MultiLineStringImmutable([$first, $populated, $second], 4326);

        self::assertSame([], $zero->getElements());
        self::assertCount(2, $all->getElements());
        self::assertSame([$first, $second], $all->getElements());
        self::assertSame([[], []], $all->toArray());
        self::assertCount(3, $mixed->getElements());
        self::assertSame([$first, $populated, $second], $mixed->getElements());
        self::assertSame([[], [[0, 0, 1], [2, 0, 1]], []], $mixed->toArray());
        self::assertSame(CoordinateDimensionEnum::XYZ, $all->getDimension());
        self::assertSame(SpatialModelEnum::GEOMETRY, $all->getFamily());
        self::assertSame(4326, $all->getSrid());
        self::assertSame(4326, $mixed->getSrid());
    }

    /** Zero membership and all-EMPTY membership differ in storage, not emptiness. */
    public function testDimension3zGeometryMutableEmptyAndMixedMembers(): void
    {
        $first = new \LongitudeOne\SpatialTypes\Types\Dimension3z\LineString([], 4326, SpatialModelEnum::GEOMETRY);
        $second = new \LongitudeOne\SpatialTypes\Types\Dimension3z\LineString([], 4326, SpatialModelEnum::GEOMETRY);
        $populated = new \LongitudeOne\SpatialTypes\Types\Dimension3z\LineString([[0, 0, 1], [2, 0, 1]], 4326, SpatialModelEnum::GEOMETRY);
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension3z\MultiLineString([], 4326, SpatialModelEnum::GEOMETRY);
        $all = new \LongitudeOne\SpatialTypes\Types\Dimension3z\MultiLineString([$first, $second], 4326, SpatialModelEnum::GEOMETRY);
        $mixed = new \LongitudeOne\SpatialTypes\Types\Dimension3z\MultiLineString([$first, $populated, $second], 4326, SpatialModelEnum::GEOMETRY);

        self::assertSame([], $zero->getElements());
        self::assertCount(2, $all->getElements());
        self::assertSame([$first, $second], $all->getElements());
        self::assertSame([[], []], $all->toArray());
        self::assertCount(3, $mixed->getElements());
        self::assertSame([$first, $populated, $second], $mixed->getElements());
        self::assertSame([[], [[0, 0, 1], [2, 0, 1]], []], $mixed->toArray());
        self::assertSame(CoordinateDimensionEnum::XYZ, $all->getDimension());
        self::assertSame(SpatialModelEnum::GEOMETRY, $all->getFamily());
        self::assertSame(4326, $all->getSrid());
        self::assertSame(4326, $mixed->getSrid());
    }

    /** Zero membership and all-EMPTY membership differ in storage, not emptiness. */
    public function testDimension4zmGeographyImmutableEmptyAndMixedMembers(): void
    {
        $first = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\LineStringImmutable([], 4326);
        $second = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\LineStringImmutable([], 4326);
        $populated = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\LineStringImmutable([[0, 0, 1, 1], [2, 0, 1, 2]], 4326);
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\MultiLineStringImmutable([], 4326);
        $all = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\MultiLineStringImmutable([$first, $second], 4326);
        $mixed = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\MultiLineStringImmutable([$first, $populated, $second], 4326);

        self::assertSame([], $zero->getElements());
        self::assertCount(2, $all->getElements());
        self::assertSame([$first, $second], $all->getElements());
        self::assertSame([[], []], $all->toArray());
        self::assertCount(3, $mixed->getElements());
        self::assertSame([$first, $populated, $second], $mixed->getElements());
        self::assertSame([[], [[0, 0, 1, 1], [2, 0, 1, 2]], []], $mixed->toArray());
        self::assertSame(CoordinateDimensionEnum::XYZM, $all->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $all->getFamily());
        self::assertSame(4326, $all->getSrid());
        self::assertSame(4326, $mixed->getSrid());
    }

    /** Zero membership and all-EMPTY membership differ in storage, not emptiness. */
    public function testDimension4zmGeographyMutableEmptyAndMixedMembers(): void
    {
        $first = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\LineString([], 4326, SpatialModelEnum::GEOGRAPHY);
        $second = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\LineString([], 4326, SpatialModelEnum::GEOGRAPHY);
        $populated = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\LineString([[0, 0, 1, 1], [2, 0, 1, 2]], 4326, SpatialModelEnum::GEOGRAPHY);
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\MultiLineString([], 4326, SpatialModelEnum::GEOGRAPHY);
        $all = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\MultiLineString([$first, $second], 4326, SpatialModelEnum::GEOGRAPHY);
        $mixed = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\MultiLineString([$first, $populated, $second], 4326, SpatialModelEnum::GEOGRAPHY);

        self::assertSame([], $zero->getElements());
        self::assertCount(2, $all->getElements());
        self::assertSame([$first, $second], $all->getElements());
        self::assertSame([[], []], $all->toArray());
        self::assertCount(3, $mixed->getElements());
        self::assertSame([$first, $populated, $second], $mixed->getElements());
        self::assertSame([[], [[0, 0, 1, 1], [2, 0, 1, 2]], []], $mixed->toArray());
        self::assertSame(CoordinateDimensionEnum::XYZM, $all->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $all->getFamily());
        self::assertSame(4326, $all->getSrid());
        self::assertSame(4326, $mixed->getSrid());
    }

    /** Zero membership and all-EMPTY membership differ in storage, not emptiness. */
    public function testDimension4zmGeometryImmutableEmptyAndMixedMembers(): void
    {
        $first = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\LineStringImmutable([], 4326);
        $second = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\LineStringImmutable([], 4326);
        $populated = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\LineStringImmutable([[0, 0, 1, 1], [2, 0, 1, 2]], 4326);
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\MultiLineStringImmutable([], 4326);
        $all = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\MultiLineStringImmutable([$first, $second], 4326);
        $mixed = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\MultiLineStringImmutable([$first, $populated, $second], 4326);

        self::assertSame([], $zero->getElements());
        self::assertCount(2, $all->getElements());
        self::assertSame([$first, $second], $all->getElements());
        self::assertSame([[], []], $all->toArray());
        self::assertCount(3, $mixed->getElements());
        self::assertSame([$first, $populated, $second], $mixed->getElements());
        self::assertSame([[], [[0, 0, 1, 1], [2, 0, 1, 2]], []], $mixed->toArray());
        self::assertSame(CoordinateDimensionEnum::XYZM, $all->getDimension());
        self::assertSame(SpatialModelEnum::GEOMETRY, $all->getFamily());
        self::assertSame(4326, $all->getSrid());
        self::assertSame(4326, $mixed->getSrid());
    }

    /** Zero membership and all-EMPTY membership differ in storage, not emptiness. */
    public function testDimension4zmGeometryMutableEmptyAndMixedMembers(): void
    {
        $first = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\LineString([], 4326, SpatialModelEnum::GEOMETRY);
        $second = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\LineString([], 4326, SpatialModelEnum::GEOMETRY);
        $populated = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\LineString([[0, 0, 1, 1], [2, 0, 1, 2]], 4326, SpatialModelEnum::GEOMETRY);
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\MultiLineString([], 4326, SpatialModelEnum::GEOMETRY);
        $all = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\MultiLineString([$first, $second], 4326, SpatialModelEnum::GEOMETRY);
        $mixed = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\MultiLineString([$first, $populated, $second], 4326, SpatialModelEnum::GEOMETRY);

        self::assertSame([], $zero->getElements());
        self::assertCount(2, $all->getElements());
        self::assertSame([$first, $second], $all->getElements());
        self::assertSame([[], []], $all->toArray());
        self::assertCount(3, $mixed->getElements());
        self::assertSame([$first, $populated, $second], $mixed->getElements());
        self::assertSame([[], [[0, 0, 1, 1], [2, 0, 1, 2]], []], $mixed->toArray());
        self::assertSame(CoordinateDimensionEnum::XYZM, $all->getDimension());
        self::assertSame(SpatialModelEnum::GEOMETRY, $all->getFamily());
        self::assertSame(4326, $all->getSrid());
        self::assertSame(4326, $mixed->getSrid());
    }
}
