<?php
/**
 * This file is part of the spatial project.
 *
 * PHP 8.1 | 8.2 | 8.3
 *
 * Copyright Alexandre Tranchant <alexandre.tranchant@gmail.com> 2024
 * Copyright Longitude One 2024
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

declare(strict_types=1);

namespace LongitudeOne\SpatialTypes\Tests\Unit\Types\Geography;

use LongitudeOne\SpatialTypes\Enum\FamilyEnum;
use LongitudeOne\SpatialTypes\Enum\TypeEnum;
use LongitudeOne\SpatialTypes\Exception\BadMethodCallException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Types\Geography\Point;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Unit test of geographic point.
 *
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractPoint
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractSpatialType
 * @covers \LongitudeOne\SpatialTypes\Types\Geography\Point
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
        $this->point = new Point('1.1W', '45.1N');
    }

    /**
     * Provide invalid coordinates.
     *
     * @return \Generator<string, array{0: float|int|string, 1: float|int|string, 2: string}, null, void>
     */
    public static function provideInvalidCoordinates(): \Generator
    {
        yield 'West Longitude greater than 180' => ['181W', '0N', 'Out of range longitude value, longitude must be between -180 and 180, got "181W".'];

        yield 'String Longitude greater than 180' => ['181', '0N', 'Out of range longitude value, longitude must be between -180 and 180, got "181".'];

        yield 'Integer Longitude greater than 180' => [181, '0N', 'Out of range longitude value, longitude must be between -180 and 180, got "181".'];

        yield 'Float Longitude greater than 180' => [181.1, '0N', 'Out of range longitude value, longitude must be between -180 and 180, got "181.1".'];

        yield 'West Latitude greater than 90' => [0, '91N', 'Out of range latitude value, latitude must be between -90 and 90, got "91N".'];

        yield 'String Latitude greater than 90' => [0, '91', 'Out of range latitude value, latitude must be between -90 and 90, got "91".'];

        yield 'Integer Latitude greater than 90' => [0, 91, 'Out of range latitude value, latitude must be between -90 and 90, got "91".'];

        yield 'Float Latitude greater than 90' => [0, 91.1, 'Out of range latitude value, latitude must be between -90 and 90, got "91.1".'];

        yield 'Minutes greater than 60' => ['79:60:55.832W', '100N', 'Out of range minute value, minute must be between 0 and 59, got "79:60:55.832W".'];

        yield 'Secondes greater than 60' => ['79:55:60.832W', '100N', 'Out of range second value, second must be between 0 and 59, got "79:55:60.832W".'];

        yield 'Invalid coordinate value' => ['180W', '85N 60', 'Invalid coordinate value, got "85N 60".'];

        yield 'Invalid array value' => ['180W 85N', '160W 85S', 'Invalid coordinate value, coordinate cannot be an array.'];
    }

    /**
     * Test the family getter.
     */
    public function testGetFamily(): void
    {
        static::assertSame(FamilyEnum::GEOGRAPHY, $this->point->getFamily());
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
        self::expectExceptionMessage('The method "LongitudeOne\SpatialTypes\Types\Geography\Point::getM" cannot be called with a spatial object with dimensions "XY".');
        $this->point->getM();
    }

    /**
     * Test the type getter.
     */
    public function testGetType(): void
    {
        static::assertSame(TypeEnum::POINT->value, $this->point->getType());
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
        self::expectException(BadMethodCallException::class);
        self::expectExceptionMessage('The method "LongitudeOne\SpatialTypes\Types\Geography\Point::getZ" cannot be called with a spatial object with dimensions "XY".');
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
        static::assertFalse($this->point->hasZ());
    }

    /**
     * Test the json serialize.
     */
    public function testJsonSerialize(): void
    {
        static::assertSame('{"type":"Point","coordinates":[-1.1,45.1],"srid":null}', json_encode($this->point));
        $this->point->setSrid(4326);
        static::assertSame('{"type":"Point","coordinates":[-1.1,45.1],"srid":4326}', json_encode($this->point));
    }

    /**
     * Test that out of range values throw an exception.
     *
     * @param float|int|string $longitude       the longitude
     * @param float|int|string $latitude        the latitude
     * @param string           $expectedMessage the expected exception message
     *
     * @throws InvalidValueException It shall happen
     */
    #[DataProvider('provideInvalidCoordinates')]
    public function testOutOfRange(float|int|string $longitude, float|int|string $latitude, string $expectedMessage): void
    {
        self::expectException(InvalidValueException::class);
        self::expectExceptionMessage($expectedMessage);
        new Point($longitude, $latitude);
    }

    /**
     * Test the X setter.
     */
    public function testSetX(): void
    {
        static::assertSame($this->point, $this->point->setX('1.1W'));
        static::assertSame(-1.1, $this->point->getX());
    }

    /**
     * Test the Y getter.
     */
    public function testSetY(): void
    {
        static::assertSame($this->point, $this->point->setY('45.1N'));
        static::assertSame(45.1, $this->point->getY());
    }

    /**
     * Test the toArray method.
     */
    public function testToArray(): void
    {
        static::assertSame([-1.1, 45.1], $this->point->toArray());
    }
}
