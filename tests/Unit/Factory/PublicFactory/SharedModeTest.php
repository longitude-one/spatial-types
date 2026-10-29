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

namespace LongitudeOne\SpatialTypes\Tests\Unit\Factory\PublicFactory;

use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Factory\Dimension2\Geometry\PointFactory;
use LongitudeOne\SpatialTypes\Factory\Dimension3m\Geography\LineStringFactory;
use LongitudeOne\SpatialTypes\Factory\SpatialFactory;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PointImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Point;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\LineStringImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension3m\LineString;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;

/** Shared selection and process-default contract.
 * @internal
 *
 * @coversNothing
 */
final class SharedModeTest extends TestCase
{
    /** Restore the shared mode for subsequent tests. */
    protected function tearDown(): void
    {
        SpatialFactory::immutable();
        parent::tearDown();
    }

    /** Direct constructors retain their representation while factory mode is mutable. */
    public function testDirectImmutableConstructionIgnoresSharedFactoryMode(): void
    {
        SpatialFactory::mutable();
        $point = new PointImmutable(0, null, 4326);
        $line = new LineStringImmutable([[0, null, 7]], 4326);

        self::assertSame([0, null], $point->toArray());
        self::assertSame([[0, null, 7]], $line->toArray());
        self::assertInstanceOf(\LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\PointImmutable::class, $line->getPoint(0));
        self::assertTrue(SpatialFactory::isMutable());
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    /** Fresh Process Defaults To Immutable Without Selecting AMode. */
    public function testFreshProcessUsesImmutableDefault(): void
    {
        self::assertFalse(SpatialFactory::isMutable());
        self::assertInstanceOf(PointImmutable::class, PointFactory::create());
        self::assertInstanceOf(LineStringImmutable::class, LineStringFactory::create([]));
    }

    /** Mode Commands Are Idempotent. */
    public function testModeCommandsAreIdempotent(): void
    {
        SpatialFactory::mutable();
        SpatialFactory::mutable();
        self::assertTrue(SpatialFactory::isMutable());
        SpatialFactory::immutable();
        SpatialFactory::immutable();
        self::assertFalse(SpatialFactory::isMutable());
    }

    /** Selection Is Shared Across Factories And Changes Only Later Creations. */
    public function testSelectionIsSharedAcrossFactoriesAndChangesOnlyLaterCreations(): void
    {
        SpatialFactory::immutable();
        self::assertFalse(SpatialFactory::isMutable());
        $first = PointFactory::create(0, null, 2154);
        SpatialFactory::mutable();
        self::assertTrue(SpatialFactory::isMutable());
        $second = PointFactory::create(1, 2, 4326);
        $line = LineStringFactory::create([[3, 4, 5]], 4326);
        SpatialFactory::immutable();
        self::assertFalse(SpatialFactory::isMutable());
        $third = PointFactory::create(6, 7, 3857);
        $immutableLine = LineStringFactory::create([[8, 9, 10]], 3857);

        self::assertInstanceOf(PointImmutable::class, $first);
        self::assertInstanceOf(Point::class, $second);
        self::assertInstanceOf(LineString::class, $line);
        self::assertInstanceOf(PointImmutable::class, $third);
        self::assertInstanceOf(LineStringImmutable::class, $immutableLine);
        $second->setX(20);
        self::assertSame([0, null], $first->toArray());
        self::assertSame(2154, $first->getSrid());
        self::assertSame([20, 2], $second->toArray());
        self::assertSame([[3, 4, 5]], $line->toArray());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $line->getFamily());
        self::assertSame([6, 7], $third->toArray());
        self::assertSame([[8, 9, 10]], $immutableLine->toArray());
    }
}
