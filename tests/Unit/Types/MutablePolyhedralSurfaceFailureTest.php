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

use LongitudeOne\SpatialTypes\Exception\OutOfBoundsException;
use LongitudeOne\SpatialTypes\Types\Dimension3z\LineString;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Point;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Polygon;
use LongitudeOne\SpatialTypes\Types\Dimension3z\PolyhedralSurface;
use PHPUnit\Framework\TestCase;

/** Technical input and indexed failures never partially modify shared descendants.
 * @internal
 *
 * @coversNothing
 */
class MutablePolyhedralSurfaceFailureTest extends TestCase
{
    /** Invalid zero-based addressing leaves every shared component unchanged. */
    public function testCountPatchAccessIsAtomic(): void
    {
        $point = new Point(0, null, 8, 4326);
        $ring = new LineString([$point], 4326);
        $patch = new Polygon([$ring], 4326);
        $surface = new PolyhedralSurface([$patch], 2154);
        $owner = new PolyhedralSurface([$patch]);
        $this->expectException(OutOfBoundsException::class);

        try {
            $surface->getPatch(1);
        } finally {
            static::assertSame([$patch], $surface->getPatches());
            static::assertSame($ring, $patch->getRing(0));
            static::assertSame($point, $ring->getPoint(0));
            static::assertSame([[[[0, null, 8]]]], $surface->toArray());
            static::assertSame([[[[0, null, 8]]]], $owner->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(4326, $ring->getSrid());
            static::assertSame(4326, $patch->getSrid());
            static::assertSame(2154, $surface->getSrid());
        }
    }

    /** Invalid zero-based addressing leaves every shared component unchanged. */
    public function testCountPatchRemovalIsAtomic(): void
    {
        $point = new Point(0, null, 8, 4326);
        $ring = new LineString([$point], 4326);
        $patch = new Polygon([$ring], 4326);
        $surface = new PolyhedralSurface([$patch], 2154);
        $owner = new PolyhedralSurface([$patch]);
        $this->expectException(OutOfBoundsException::class);

        try {
            $surface->removePatch(1);
        } finally {
            static::assertSame([$patch], $surface->getPatches());
            static::assertSame($ring, $patch->getRing(0));
            static::assertSame($point, $ring->getPoint(0));
            static::assertSame([[[[0, null, 8]]]], $surface->toArray());
            static::assertSame([[[[0, null, 8]]]], $owner->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(4326, $ring->getSrid());
            static::assertSame(4326, $patch->getSrid());
            static::assertSame(2154, $surface->getSrid());
        }
    }

    /** Invalid zero-based addressing leaves every shared component unchanged. */
    public function testCountPatchReplacementIsAtomic(): void
    {
        $point = new Point(0, null, 8, 4326);
        $ring = new LineString([$point], 4326);
        $patch = new Polygon([$ring], 4326);
        $surface = new PolyhedralSurface([$patch], 2154);
        $owner = new PolyhedralSurface([$patch]);
        $this->expectException(OutOfBoundsException::class);

        try {
            $surface->setPatch(1, $patch);
        } finally {
            static::assertSame([$patch], $surface->getPatches());
            static::assertSame($ring, $patch->getRing(0));
            static::assertSame($point, $ring->getPoint(0));
            static::assertSame([[[[0, null, 8]]]], $surface->toArray());
            static::assertSame([[[[0, null, 8]]]], $owner->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(4326, $ring->getSrid());
            static::assertSame(4326, $patch->getSrid());
            static::assertSame(2154, $surface->getSrid());
        }
    }

    /** Invalid zero-based addressing leaves every shared component unchanged. */
    public function testCountPointReplacementIsAtomic(): void
    {
        $point = new Point(0, null, 8, 4326);
        $ring = new LineString([$point], 4326);
        $patch = new Polygon([$ring], 4326);
        $surface = new PolyhedralSurface([$patch], 2154);
        $owner = new PolyhedralSurface([$patch]);
        $this->expectException(OutOfBoundsException::class);

        try {
            $surface->setPoint(0, 0, 1, $point);
        } finally {
            static::assertSame([$patch], $surface->getPatches());
            static::assertSame($ring, $patch->getRing(0));
            static::assertSame($point, $ring->getPoint(0));
            static::assertSame([[[[0, null, 8]]]], $surface->toArray());
            static::assertSame([[[[0, null, 8]]]], $owner->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(4326, $ring->getSrid());
            static::assertSame(4326, $patch->getSrid());
            static::assertSame(2154, $surface->getSrid());
        }
    }

    /** Invalid zero-based addressing leaves every shared component unchanged. */
    public function testCountRingRemovalIsAtomic(): void
    {
        $point = new Point(0, null, 8, 4326);
        $ring = new LineString([$point], 4326);
        $patch = new Polygon([$ring], 4326);
        $surface = new PolyhedralSurface([$patch], 2154);
        $owner = new PolyhedralSurface([$patch]);
        $this->expectException(OutOfBoundsException::class);

        try {
            $surface->removeRing(0, 1);
        } finally {
            static::assertSame([$patch], $surface->getPatches());
            static::assertSame($ring, $patch->getRing(0));
            static::assertSame($point, $ring->getPoint(0));
            static::assertSame([[[[0, null, 8]]]], $surface->toArray());
            static::assertSame([[[[0, null, 8]]]], $owner->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(4326, $ring->getSrid());
            static::assertSame(4326, $patch->getSrid());
            static::assertSame(2154, $surface->getSrid());
        }
    }

    /** Invalid zero-based addressing leaves every shared component unchanged. */
    public function testCountRingReplacementIsAtomic(): void
    {
        $point = new Point(0, null, 8, 4326);
        $ring = new LineString([$point], 4326);
        $patch = new Polygon([$ring], 4326);
        $surface = new PolyhedralSurface([$patch], 2154);
        $owner = new PolyhedralSurface([$patch]);
        $this->expectException(OutOfBoundsException::class);

        try {
            $surface->setRing(0, 1, $ring);
        } finally {
            static::assertSame([$patch], $surface->getPatches());
            static::assertSame($ring, $patch->getRing(0));
            static::assertSame($point, $ring->getPoint(0));
            static::assertSame([[[[0, null, 8]]]], $surface->toArray());
            static::assertSame([[[[0, null, 8]]]], $owner->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(4326, $ring->getSrid());
            static::assertSame(4326, $patch->getSrid());
            static::assertSame(2154, $surface->getSrid());
        }
    }

    /** Invalid zero-based addressing leaves every shared component unchanged. */
    public function testMissingPatchPointReplacementIsAtomic(): void
    {
        $point = new Point(0, null, 8, 4326);
        $ring = new LineString([$point], 4326);
        $patch = new Polygon([$ring], 4326);
        $surface = new PolyhedralSurface([$patch], 2154);
        $owner = new PolyhedralSurface([$patch]);
        $this->expectException(OutOfBoundsException::class);

        try {
            $surface->setPoint(1, 0, 0, $point);
        } finally {
            static::assertSame([$patch], $surface->getPatches());
            static::assertSame($ring, $patch->getRing(0));
            static::assertSame($point, $ring->getPoint(0));
            static::assertSame([[[[0, null, 8]]]], $surface->toArray());
            static::assertSame([[[[0, null, 8]]]], $owner->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(4326, $ring->getSrid());
            static::assertSame(4326, $patch->getSrid());
            static::assertSame(2154, $surface->getSrid());
        }
    }

    /** Invalid zero-based addressing leaves every shared component unchanged. */
    public function testMissingPatchRingAdditionIsAtomic(): void
    {
        $point = new Point(0, null, 8, 4326);
        $ring = new LineString([$point], 4326);
        $patch = new Polygon([$ring], 4326);
        $surface = new PolyhedralSurface([$patch], 2154);
        $owner = new PolyhedralSurface([$patch]);
        $this->expectException(OutOfBoundsException::class);

        try {
            $surface->addRing(1, $ring);
        } finally {
            static::assertSame([$patch], $surface->getPatches());
            static::assertSame($ring, $patch->getRing(0));
            static::assertSame($point, $ring->getPoint(0));
            static::assertSame([[[[0, null, 8]]]], $surface->toArray());
            static::assertSame([[[[0, null, 8]]]], $owner->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(4326, $ring->getSrid());
            static::assertSame(4326, $patch->getSrid());
            static::assertSame(2154, $surface->getSrid());
        }
    }

    /** Invalid zero-based addressing leaves every shared component unchanged. */
    public function testMissingPatchRingInsertionIsAtomic(): void
    {
        $point = new Point(0, null, 8, 4326);
        $ring = new LineString([$point], 4326);
        $patch = new Polygon([$ring], 4326);
        $surface = new PolyhedralSurface([$patch], 2154);
        $owner = new PolyhedralSurface([$patch]);
        $this->expectException(OutOfBoundsException::class);

        try {
            $surface->insertRing(1, 0, $ring);
        } finally {
            static::assertSame([$patch], $surface->getPatches());
            static::assertSame($ring, $patch->getRing(0));
            static::assertSame($point, $ring->getPoint(0));
            static::assertSame([[[[0, null, 8]]]], $surface->toArray());
            static::assertSame([[[[0, null, 8]]]], $owner->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(4326, $ring->getSrid());
            static::assertSame(4326, $patch->getSrid());
            static::assertSame(2154, $surface->getSrid());
        }
    }

    /** Invalid zero-based addressing leaves every shared component unchanged. */
    public function testMissingPatchRingRemovalIsAtomic(): void
    {
        $point = new Point(0, null, 8, 4326);
        $ring = new LineString([$point], 4326);
        $patch = new Polygon([$ring], 4326);
        $surface = new PolyhedralSurface([$patch], 2154);
        $owner = new PolyhedralSurface([$patch]);
        $this->expectException(OutOfBoundsException::class);

        try {
            $surface->removeRing(1, 0);
        } finally {
            static::assertSame([$patch], $surface->getPatches());
            static::assertSame($ring, $patch->getRing(0));
            static::assertSame($point, $ring->getPoint(0));
            static::assertSame([[[[0, null, 8]]]], $surface->toArray());
            static::assertSame([[[[0, null, 8]]]], $owner->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(4326, $ring->getSrid());
            static::assertSame(4326, $patch->getSrid());
            static::assertSame(2154, $surface->getSrid());
        }
    }

    /** Invalid zero-based addressing leaves every shared component unchanged. */
    public function testMissingPatchRingReplacementIsAtomic(): void
    {
        $point = new Point(0, null, 8, 4326);
        $ring = new LineString([$point], 4326);
        $patch = new Polygon([$ring], 4326);
        $surface = new PolyhedralSurface([$patch], 2154);
        $owner = new PolyhedralSurface([$patch]);
        $this->expectException(OutOfBoundsException::class);

        try {
            $surface->setRing(1, 0, $ring);
        } finally {
            static::assertSame([$patch], $surface->getPatches());
            static::assertSame($ring, $patch->getRing(0));
            static::assertSame($point, $ring->getPoint(0));
            static::assertSame([[[[0, null, 8]]]], $surface->toArray());
            static::assertSame([[[[0, null, 8]]]], $owner->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(4326, $ring->getSrid());
            static::assertSame(4326, $patch->getSrid());
            static::assertSame(2154, $surface->getSrid());
        }
    }

    /** Invalid zero-based addressing leaves every shared component unchanged. */
    public function testMissingRingPointReplacementIsAtomic(): void
    {
        $point = new Point(0, null, 8, 4326);
        $ring = new LineString([$point], 4326);
        $patch = new Polygon([$ring], 4326);
        $surface = new PolyhedralSurface([$patch], 2154);
        $owner = new PolyhedralSurface([$patch]);
        $this->expectException(OutOfBoundsException::class);

        try {
            $surface->setPoint(0, 1, 0, $point);
        } finally {
            static::assertSame([$patch], $surface->getPatches());
            static::assertSame($ring, $patch->getRing(0));
            static::assertSame($point, $ring->getPoint(0));
            static::assertSame([[[[0, null, 8]]]], $surface->toArray());
            static::assertSame([[[[0, null, 8]]]], $owner->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(4326, $ring->getSrid());
            static::assertSame(4326, $patch->getSrid());
            static::assertSame(2154, $surface->getSrid());
        }
    }

    /** Invalid zero-based addressing leaves every shared component unchanged. */
    public function testNegativePatchAccessIsAtomic(): void
    {
        $point = new Point(0, null, 8, 4326);
        $ring = new LineString([$point], 4326);
        $patch = new Polygon([$ring], 4326);
        $surface = new PolyhedralSurface([$patch], 2154);
        $owner = new PolyhedralSurface([$patch]);
        $this->expectException(OutOfBoundsException::class);

        try {
            $surface->getPatch(-1);
        } finally {
            static::assertSame([$patch], $surface->getPatches());
            static::assertSame($ring, $patch->getRing(0));
            static::assertSame($point, $ring->getPoint(0));
            static::assertSame([[[[0, null, 8]]]], $surface->toArray());
            static::assertSame([[[[0, null, 8]]]], $owner->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(4326, $ring->getSrid());
            static::assertSame(4326, $patch->getSrid());
            static::assertSame(2154, $surface->getSrid());
        }
    }

    /** Invalid zero-based addressing leaves every shared component unchanged. */
    public function testNegativePatchInsertionIsAtomic(): void
    {
        $point = new Point(0, null, 8, 4326);
        $ring = new LineString([$point], 4326);
        $patch = new Polygon([$ring], 4326);
        $surface = new PolyhedralSurface([$patch], 2154);
        $owner = new PolyhedralSurface([$patch]);
        $this->expectException(OutOfBoundsException::class);

        try {
            $surface->insertPatch(-1, $patch);
        } finally {
            static::assertSame([$patch], $surface->getPatches());
            static::assertSame($ring, $patch->getRing(0));
            static::assertSame($point, $ring->getPoint(0));
            static::assertSame([[[[0, null, 8]]]], $surface->toArray());
            static::assertSame([[[[0, null, 8]]]], $owner->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(4326, $ring->getSrid());
            static::assertSame(4326, $patch->getSrid());
            static::assertSame(2154, $surface->getSrid());
        }
    }

    /** Invalid zero-based addressing leaves every shared component unchanged. */
    public function testNegativePatchRemovalIsAtomic(): void
    {
        $point = new Point(0, null, 8, 4326);
        $ring = new LineString([$point], 4326);
        $patch = new Polygon([$ring], 4326);
        $surface = new PolyhedralSurface([$patch], 2154);
        $owner = new PolyhedralSurface([$patch]);
        $this->expectException(OutOfBoundsException::class);

        try {
            $surface->removePatch(-1);
        } finally {
            static::assertSame([$patch], $surface->getPatches());
            static::assertSame($ring, $patch->getRing(0));
            static::assertSame($point, $ring->getPoint(0));
            static::assertSame([[[[0, null, 8]]]], $surface->toArray());
            static::assertSame([[[[0, null, 8]]]], $owner->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(4326, $ring->getSrid());
            static::assertSame(4326, $patch->getSrid());
            static::assertSame(2154, $surface->getSrid());
        }
    }

    /** Invalid zero-based addressing leaves every shared component unchanged. */
    public function testNegativePatchReplacementIsAtomic(): void
    {
        $point = new Point(0, null, 8, 4326);
        $ring = new LineString([$point], 4326);
        $patch = new Polygon([$ring], 4326);
        $surface = new PolyhedralSurface([$patch], 2154);
        $owner = new PolyhedralSurface([$patch]);
        $this->expectException(OutOfBoundsException::class);

        try {
            $surface->setPatch(-1, $patch);
        } finally {
            static::assertSame([$patch], $surface->getPatches());
            static::assertSame($ring, $patch->getRing(0));
            static::assertSame($point, $ring->getPoint(0));
            static::assertSame([[[[0, null, 8]]]], $surface->toArray());
            static::assertSame([[[[0, null, 8]]]], $owner->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(4326, $ring->getSrid());
            static::assertSame(4326, $patch->getSrid());
            static::assertSame(2154, $surface->getSrid());
        }
    }

    /** Invalid zero-based addressing leaves every shared component unchanged. */
    public function testNegativePointReplacementIsAtomic(): void
    {
        $point = new Point(0, null, 8, 4326);
        $ring = new LineString([$point], 4326);
        $patch = new Polygon([$ring], 4326);
        $surface = new PolyhedralSurface([$patch], 2154);
        $owner = new PolyhedralSurface([$patch]);
        $this->expectException(OutOfBoundsException::class);

        try {
            $surface->setPoint(0, 0, -1, $point);
        } finally {
            static::assertSame([$patch], $surface->getPatches());
            static::assertSame($ring, $patch->getRing(0));
            static::assertSame($point, $ring->getPoint(0));
            static::assertSame([[[[0, null, 8]]]], $surface->toArray());
            static::assertSame([[[[0, null, 8]]]], $owner->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(4326, $ring->getSrid());
            static::assertSame(4326, $patch->getSrid());
            static::assertSame(2154, $surface->getSrid());
        }
    }

    /** Invalid zero-based addressing leaves every shared component unchanged. */
    public function testNegativeRingInsertionIsAtomic(): void
    {
        $point = new Point(0, null, 8, 4326);
        $ring = new LineString([$point], 4326);
        $patch = new Polygon([$ring], 4326);
        $surface = new PolyhedralSurface([$patch], 2154);
        $owner = new PolyhedralSurface([$patch]);
        $this->expectException(OutOfBoundsException::class);

        try {
            $surface->insertRing(0, -1, $ring);
        } finally {
            static::assertSame([$patch], $surface->getPatches());
            static::assertSame($ring, $patch->getRing(0));
            static::assertSame($point, $ring->getPoint(0));
            static::assertSame([[[[0, null, 8]]]], $surface->toArray());
            static::assertSame([[[[0, null, 8]]]], $owner->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(4326, $ring->getSrid());
            static::assertSame(4326, $patch->getSrid());
            static::assertSame(2154, $surface->getSrid());
        }
    }

    /** Invalid zero-based addressing leaves every shared component unchanged. */
    public function testNegativeRingRemovalIsAtomic(): void
    {
        $point = new Point(0, null, 8, 4326);
        $ring = new LineString([$point], 4326);
        $patch = new Polygon([$ring], 4326);
        $surface = new PolyhedralSurface([$patch], 2154);
        $owner = new PolyhedralSurface([$patch]);
        $this->expectException(OutOfBoundsException::class);

        try {
            $surface->removeRing(0, -1);
        } finally {
            static::assertSame([$patch], $surface->getPatches());
            static::assertSame($ring, $patch->getRing(0));
            static::assertSame($point, $ring->getPoint(0));
            static::assertSame([[[[0, null, 8]]]], $surface->toArray());
            static::assertSame([[[[0, null, 8]]]], $owner->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(4326, $ring->getSrid());
            static::assertSame(4326, $patch->getSrid());
            static::assertSame(2154, $surface->getSrid());
        }
    }

    /** Invalid zero-based addressing leaves every shared component unchanged. */
    public function testNegativeRingReplacementIsAtomic(): void
    {
        $point = new Point(0, null, 8, 4326);
        $ring = new LineString([$point], 4326);
        $patch = new Polygon([$ring], 4326);
        $surface = new PolyhedralSurface([$patch], 2154);
        $owner = new PolyhedralSurface([$patch]);
        $this->expectException(OutOfBoundsException::class);

        try {
            $surface->setRing(0, -1, $ring);
        } finally {
            static::assertSame([$patch], $surface->getPatches());
            static::assertSame($ring, $patch->getRing(0));
            static::assertSame($point, $ring->getPoint(0));
            static::assertSame([[[[0, null, 8]]]], $surface->toArray());
            static::assertSame([[[[0, null, 8]]]], $owner->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(4326, $ring->getSrid());
            static::assertSame(4326, $patch->getSrid());
            static::assertSame(2154, $surface->getSrid());
        }
    }

    /** Invalid zero-based addressing leaves every shared component unchanged. */
    public function testOversizedPatchInsertionIsAtomic(): void
    {
        $point = new Point(0, null, 8, 4326);
        $ring = new LineString([$point], 4326);
        $patch = new Polygon([$ring], 4326);
        $surface = new PolyhedralSurface([$patch], 2154);
        $owner = new PolyhedralSurface([$patch]);
        $this->expectException(OutOfBoundsException::class);

        try {
            $surface->insertPatch(2, $patch);
        } finally {
            static::assertSame([$patch], $surface->getPatches());
            static::assertSame($ring, $patch->getRing(0));
            static::assertSame($point, $ring->getPoint(0));
            static::assertSame([[[[0, null, 8]]]], $surface->toArray());
            static::assertSame([[[[0, null, 8]]]], $owner->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(4326, $ring->getSrid());
            static::assertSame(4326, $patch->getSrid());
            static::assertSame(2154, $surface->getSrid());
        }
    }

    /** Invalid zero-based addressing leaves every shared component unchanged. */
    public function testOversizedRingInsertionIsAtomic(): void
    {
        $point = new Point(0, null, 8, 4326);
        $ring = new LineString([$point], 4326);
        $patch = new Polygon([$ring], 4326);
        $surface = new PolyhedralSurface([$patch], 2154);
        $owner = new PolyhedralSurface([$patch]);
        $this->expectException(OutOfBoundsException::class);

        try {
            $surface->insertRing(0, 2, $ring);
        } finally {
            static::assertSame([$patch], $surface->getPatches());
            static::assertSame($ring, $patch->getRing(0));
            static::assertSame($point, $ring->getPoint(0));
            static::assertSame([[[[0, null, 8]]]], $surface->toArray());
            static::assertSame([[[[0, null, 8]]]], $owner->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(4326, $ring->getSrid());
            static::assertSame(4326, $patch->getSrid());
            static::assertSame(2154, $surface->getSrid());
        }
    }
}
