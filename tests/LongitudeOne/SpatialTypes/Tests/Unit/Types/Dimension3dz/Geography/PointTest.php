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

namespace LongitudeOne\SpatialTypes\Tests\Unit\Types\Dimension3z\Geography;

use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Enum\TypeEnum;
use LongitudeOne\SpatialTypes\Exception\BadMethodCallException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\Point;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Unit test of geographic point.
 *
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractPoint
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractSpatialType
 * @covers \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\Point
 */
class PointTest extends TestCase
{
    /**
     * Point instance.
     */
    private Point $point;

    /**
     * Set up the point instance.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->point = new Point('1.1W', '45.1N', 42.195);
    }

    /**
     * Test latitude construction.
     */
    public function testConstructsLatitude(): void
    {
        static::assertSame(45.1, (new Point(-1.1, '45.1N', 42.195))->getY());
    }

    /**
     * Test longitude construction.
     */
    public function testConstructsLongitude(): void
    {
        static::assertSame(-1.1, (new Point('1.1W', 45.1, 42.195))->getX());
    }

    /**
     * Test the family getter.
     */
    public function testGetFamily(): void
    {
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $this->point->getFamily());
    }

    /**
     * Test the latitude getter.
     */
    public function testGetLatitude(): void
    {
        static::assertSame(45.1, $this->point->getLatitude());
    }

    /**
     * Test the longitude getter.
     */
    public function testGetLongitude(): void
    {
        static::assertSame(-1.1, $this->point->getLongitude());
    }

    /**
     * Test the elevation, the M getter.
     */
    public function testGetM(): void
    {
        self::expectException(BadMethodCallException::class);
        self::expectExceptionMessageIsOrContains('The method "LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\Point::getM" cannot be called with a spatial object with dimensions "XYZ".');
        $this->point->getM();
    }

    /**
     * Test the type getter.
     */
    public function testGetType(): void
    {
        static::assertSame(TypeEnum::POINT, $this->point->getType());
    }

    /**
     * Test the X getter.
     */
    public function testGetX(): void
    {
        static::assertSame(-1.1, $this->point->getX());
    }

    /**
     * Test the Y getter.
     */
    public function testGetY(): void
    {
        static::assertSame(45.1, $this->point->getY());
    }

    /**
     * Test the Z getter.
     */
    public function testGetZ(): void
    {
        static::assertSame(42.195, $this->point->getZ());
        $this->point->getZ();
    }

    /**
     * Test the has M method.
     */
    public function testHasM(): void
    {
        static::assertFalse($this->point->hasM());
    }

    /**
     * Test the has Z method.
     */
    public function testHasZ(): void
    {
        static::assertTrue($this->point->hasZ());
    }

    /**
     * Test the json serialize.
     */
    public function testJsonSerialize(): void
    {
        static::assertSame('{"type":"Point","coordinates":[-1.1,45.1,42.195],"srid":0}', json_encode($this->point));
        $this->point = new Point(-1.1, 45.1, 42.195, 4326);
        static::assertSame('{"type":"Point","coordinates":[-1.1,45.1,42.195],"srid":4326}', json_encode($this->point));
    }

    /**
     * Test that out of range values throw an exception.
     *
     * @param float|int|string $longitude       the longitude
     * @param float|int|string $latitude        the latitude
     * @param float|int        $elevation       the elevation
     * @param string           $expectedMessage the expected exception message
     *
     * @throws InvalidValueException It shall happen
     */
    #[DataProvider('provideInvalidCoordinates')]
    public function testOutOfRange(float|int|string $longitude, float|int|string $latitude, float|int $elevation, string $expectedMessage): void
    {
        self::expectException(InvalidValueException::class);
        self::expectExceptionMessageIsOrContains($expectedMessage);
        new Point($longitude, $latitude, $elevation);
    }

    /**
     * Provide invalid coordinates.
     *
     * @return \Generator<string, array{0: float|int|string, 1: float|int|string, 2: float|int, 3: string}, null, void>
     */
    public static function provideInvalidCoordinates(): \Generator
    {
        yield 'West Longitude greater than 180' => ['181W', '0N', 0, 'Out of range longitude value, longitude must be between -180 and 180, got "181W".'];

        yield 'String Longitude greater than 180' => ['181', '0N', 1, 'Out of range longitude value, longitude must be between -180 and 180, got "181".'];

        yield 'Integer Longitude greater than 180' => [181, '0N', 2, 'Out of range longitude value, longitude must be between -180 and 180, got "181".'];

        yield 'Float Longitude greater than 180' => [181.1, '0N', -1, 'Out of range longitude value, longitude must be between -180 and 180, got "181.1".'];

        yield 'West Latitude greater than 90' => [0, '91N', 0.1, 'Out of range latitude value, latitude must be between -90 and 90, got "91N".'];

        yield 'String Latitude greater than 90' => [0, '91', 0.2, 'Out of range latitude value, latitude must be between -90 and 90, got "91".'];

        yield 'Integer Latitude greater than 90' => [0, 91, 0, 'Out of range latitude value, latitude must be between -90 and 90, got "91".'];

        yield 'Float Latitude greater than 90' => [0, 91.1, 0, 'Out of range latitude value, latitude must be between -90 and 90, got "91.1".'];

        yield 'Minutes greater than 60' => ['79:60:55.832W', '100N', 1, 'Out of range minute value, minute must be between 0 and 59, got "79:60:55.832W".'];

        yield 'Secondes greater than 60' => ['79:55:60.832W', '100N', 2, 'Out of range second value, second must be between 0 and 59, got "79:55:60.832W".'];

        yield 'Invalid coordinate value' => ['180W', '85N 60', -0.2, 'Invalid coordinate value, got "85N 60".'];

        yield 'Invalid array value' => ['180W 85N', '160W 85S', 0, 'Invalid coordinate value, coordinate cannot be an array.'];
    }

    /**
     * Test the toArray method.
     */
    public function testToArray(): void
    {
        static::assertSame([-1.1, 45.1, 42.195], $this->point->toArray());
    }
}
