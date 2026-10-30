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

namespace LongitudeOne\SpatialTypes\Types;

use LongitudeOne\Core\Diagnostic\DiagnosticValueFormatter;
use LongitudeOne\GeoParser\Exception\RangeException as GeoParserRangeException;
use LongitudeOne\GeoParser\Exception\UnexpectedValueException;
use LongitudeOne\GeoParser\Parser;
use LongitudeOne\SpatialTypes\Exception\BadMethodCallException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\RangeException;
use LongitudeOne\SpatialTypes\Interfaces\PointInterface;
use LongitudeOne\SpatialTypes\Value\Coordinates;

/** Shared point reads, initialization and coordinate parsing.
 * @internal
 */
trait PointBehaviorTrait
{
    /**
     * The X coordinate or the longitude.
     */
    protected float|int|null $x = null;

    /**
     * The Y coordinate or the latitude.
     */
    protected float|int|null $y = null;

    /** Whether this point corresponds to the empty set. */
    private bool $empty = false;

    /**
     * Compare a mixed numeric pair without rounding the integer to binary64.
     *
     * @param int   $integer  the integer ordinate
     * @param float $floating the floating-point ordinate
     */
    private static function integerEqualsFloat(int $integer, float $floating): bool
    {
        // The exclusive upper bound is exact even when PHP_INT_MAX rounds up as a float.
        return $floating >= PHP_INT_MIN
            && $floating < -(float) PHP_INT_MIN
            && floor($floating) === $floating
            && (int) $floating === $integer;
    }

    /**
     * Compare numeric ordinates without coercing missing values to zero.
     *
     * @param null|float|int $first  the first ordinate
     * @param null|float|int $second the second ordinate
     */
    private static function ordinatesEqual(float|int|null $first, float|int|null $second): bool
    {
        if (is_int($first) && is_float($second)) {
            return self::integerEqualsFloat($first, $second);
        }

        if (is_float($first) && is_int($second)) {
            return self::integerEqualsFloat($second, $first);
        }

        return $first === $second;
    }

    /**
     * Is the current point equal to another point?
     *
     * @param PointInterface $point the point to compare
     */
    public function equalsTo(PointInterface $point): bool
    {
        if (!($point instanceof static && $point->getSrid() === $this->getSrid() && $point->getFamily() === $this->getFamily() && $point->getDimension() === $this->getDimension())) {
            return false;
        }

        if ($this->isEmpty() || $point->isEmpty()) {
            return $this->isEmpty() && $point->isEmpty();
        }

        if (!self::ordinatesEqual($point->getX(), $this->getX()) || !self::ordinatesEqual($point->getY(), $this->getY())) {
            return false;
        }

        if ($point->hasM() && $this->hasM() && !self::ordinatesEqual($this->getM(), $point->getM())) {
            return false;
        }

        if ($point->hasZ() && $this->hasZ() && !self::ordinatesEqual($this->getZ(), $point->getZ())) {
            return false;
        }

        return true;
    }

    /**
     * Return the normalized coordinates of this point.
     */
    public function getCoordinates(): ?Coordinates
    {
        if ($this->isEmpty()) {
            return null;
        }

        return new Coordinates(
            $this->getDimension(),
            $this->x,
            $this->y,
            $this->hasZ() ? $this->getZ() : null,
            $this->hasM() ? $this->getM() : null
        );
    }

    /**
     * Latitude getter.
     */
    public function getLatitude(): float|int|null
    {
        return $this->getY();
    }

    /**
     * Longitude getter.
     */
    public function getLongitude(): float|int|null
    {
        return $this->getX();
    }

    /**
     * X getter. (Longitude getter).
     */
    public function getX(): float|int|null
    {
        return $this->isEmpty() ? null : $this->x;
    }

    /**
     * Y getter. Latitude getter.
     */
    public function getY(): float|int|null
    {
        return $this->isEmpty() ? null : $this->y;
    }

    /**
     * Does this point correspond to the empty set?
     */
    public function isEmpty(): bool
    {
        return $this->empty;
    }

    /**
     * Use the longitude-one/geo-parser to parse a coordinate.
     *
     * @param string $coordinate the coordinate to parse
     *
     * @throws InvalidValueException when coordinate is invalid
     */
    protected function geoParse(string $coordinate): float|int
    {
        try {
            $parser = new Parser($coordinate);

            $parsedCoordinate = $parser->parse();
        } catch (GeoParserRangeException $e) {
            $messages = [
                GeoParserRangeException::LATITUDE_OUT_OF_RANGE => sprintf(InvalidValueException::OUT_OF_RANGE_LATITUDE, DiagnosticValueFormatter::format((string) $coordinate)),
                GeoParserRangeException::LONGITUDE_OUT_OF_RANGE => sprintf(InvalidValueException::OUT_OF_RANGE_LONGITUDE, DiagnosticValueFormatter::format((string) $coordinate)),
                GeoParserRangeException::MINUTES_OUT_OF_RANGE => sprintf(InvalidValueException::OUT_OF_RANGE_MINUTE, DiagnosticValueFormatter::format((string) $coordinate)),
                GeoParserRangeException::SECONDS_OUT_OF_RANGE => sprintf(InvalidValueException::OUT_OF_RANGE_SECOND, DiagnosticValueFormatter::format((string) $coordinate)),
            ];
            $message = $messages[$e->getCode()] ?? DiagnosticValueFormatter::format($e->getMessage());

            throw new InvalidValueException($message, $e->getCode(), $e);
        } catch (UnexpectedValueException $e) {
            throw new InvalidValueException(sprintf('Invalid coordinate value, got "%s".', DiagnosticValueFormatter::format((string) $coordinate)), $e->getCode(), $e);
        }

        if (is_array($parsedCoordinate)) {
            throw new InvalidValueException('Invalid coordinate value, coordinate cannot be an array.');
        }

        return $parsedCoordinate;
    }

    /**
     * Determine whether all coordinates describe an empty point.
     *
     * A point is empty only when every ordinate in its coordinate layout is
     * null. Partially supplied coordinates are preserved without validation.
     *
     * @param (null|float|int|string) ...$coordinates Point ordinates
     */
    final protected function hasOnlyNullCoordinates(float|int|string|null ...$coordinates): bool
    {
        $this->empty = count($coordinates) === count(array_filter($coordinates, static fn (float|int|string|null $coordinate): bool => null === $coordinate));

        return $this->empty;
    }

    /**
     * Set the M coordinate on a point that supports a measure ordinate.
     *
     * @param null|float|int $m M coordinate or measure
     *
     * @throws BadMethodCallException when the point has no M ordinate
     */
    protected function initializeM(float|int|null $m): static
    {
        // @codeCoverageIgnoreStart
        throw new BadMethodCallException(sprintf('The M ordinate "%s" cannot be assigned to a point with the %s dimension.', $m, $this->getDimension()->value));
        // @codeCoverageIgnoreEnd
    }

    /**
     * X setter. (Latitude setter).
     *
     * @param null|float|int|string $x the new X
     *
     * @throws InvalidValueException when x is not valid
     */
    protected function initializeX(float|int|string|null $x): static
    {
        $this->x = $this->setCartesianCoordinate($x);

        return $this;
    }

    /**
     * Y setter. Longitude Setter.
     *
     * @param null|float|int|string $y the new Y value
     *
     * @throws InvalidValueException when Y is invalid, not in valid range
     */
    protected function initializeY(float|int|string|null $y): static
    {
        $this->y = $this->setCartesianCoordinate($y);

        return $this;
    }

    /**
     * Set the Z coordinate on a point that supports an elevation ordinate.
     *
     * @param null|float|int $z Z coordinate or elevation
     *
     * @throws BadMethodCallException when the point has no Z ordinate
     */
    protected function initializeZ(float|int|null $z): static
    {
        // @codeCoverageIgnoreStart
        throw new BadMethodCallException(sprintf('The Z ordinate "%s" cannot be assigned to a point with the %s dimension.', $z, $this->getDimension()->value));
        // @codeCoverageIgnoreEnd
    }

    /**
     * Set a cartesian coordinate.
     * Abscissa or ordinate.
     *
     * @param null|float|int|string $coordinate the coordinate to set
     *
     * @throws InvalidValueException when coordinate is invalid, RangeException is never thrown
     */
    protected function setCartesianCoordinate(float|int|string|null $coordinate): float|int|null
    {
        if (null === $coordinate || is_integer($coordinate) || is_float($coordinate)) {
            // We don't check the range of the value.
            return $coordinate;
        }

        // $y is a string, let's use the geo-parser.
        return $this->geoParse($coordinate);
    }

    /**
     * Convert point into an array of coordinates.
     * SRID is NOT exported.
     *
     * The array contains coordinate values only; it does not contain spatial object instances.
     */
    abstract public function toArray(): array;
}
