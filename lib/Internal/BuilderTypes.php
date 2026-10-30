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

namespace LongitudeOne\SpatialTypes\Internal;

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Interfaces\MutableSpatialInterface;
use LongitudeOne\SpatialTypes\Interfaces\SpatialInterface;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Types\ImmutableComponentGuard;
use ReflectionClass as ConstructorReflection;

/** @internal Constructor selection for the one public builder class. */
final class BuilderTypes
{
    /**
     * Create.
     *
     * @param string                  $type      type value
     * @param array                   $arguments arguments value
     * @param CoordinateDimensionEnum $dimension dimension value
     * @param SpatialModelEnum        $family    family value
     * @param SpatialReference        $reference reference value
     * @param bool                    $mutable   mutable value
     *
     * @phpstan-param array<mixed> $arguments
     * @phpstan-param 'CircularString'|'Collection'|'CompoundCurve'|'LineString'|'MultiLineString'|'MultiPoint'|'MultiPolygon'|'Point'|'Polygon'|'PolyhedralSurface'|'Triangle' $type
     * @phpstan-param list<mixed> $arguments
     */
    public static function create(string $type, array $arguments, CoordinateDimensionEnum $dimension, SpatialModelEnum $family, SpatialReference $reference, bool $mutable): SpatialInterface
    {
        if ('PolyhedralSurface' === $type && !in_array($dimension, [CoordinateDimensionEnum::XYZ, CoordinateDimensionEnum::XYZM], true)) {
            throw new InvalidDimensionException('PolyhedralSurface is supported only for XYZ and XYZM.');
        }

        self::checkComponents($arguments, $mutable);
        $class = self::className($type, $dimension, $family, $mutable);

        if (!class_exists($class)) {
            throw new InvalidDimensionException(sprintf('%s is unavailable in %s.', $type, $dimension->value));
        }

        $created = (new ConstructorReflection($class))->newInstanceArgs(self::constructorArguments($type, $arguments, $family, $reference, $mutable));
        if (!$created instanceof SpatialInterface) {
            throw new InvalidValueException('The selected class is not a spatial type.');
        }

        return $created;
    }

    /**
     * Reject components of the opposite representation before construction.
     *
     * @param array $arguments constructor input
     * @param bool  $mutable   representation
     *
     * @phpstan-param list<mixed> $arguments
     */
    private static function checkComponents(array $arguments, bool $mutable): void
    {
        if ($mutable) {
            self::checkMutableComponents($arguments);

            return;
        }
        self::checkImmutableComponents($arguments);
    }

    /** @param array<mixed, mixed> $values */
    /**
     * Check immutable components.
     *
     * @param array $values values value
     *
     * @phpstan-param array<mixed> $values
     */
    private static function checkImmutableComponents(array $values): void
    {
        foreach ($values as $value) {
            if ($value instanceof SpatialInterface) {
                ImmutableComponentGuard::check($value);
            } elseif (is_array($value)) {
                self::checkImmutableComponents($value);
            }
        }
    }

    /** @param array<mixed, mixed> $values */
    /**
     * Check mutable components.
     *
     * @param array $values values value
     *
     * @phpstan-param array<mixed> $values
     */
    private static function checkMutableComponents(array $values): void
    {
        foreach ($values as $value) {
            if ($value instanceof SpatialInterface && !$value instanceof MutableSpatialInterface) {
                throw new InvalidValueException('Mutable builder output requires mutable spatial components.');
            }
            if (is_array($value)) {
                self::checkMutableComponents($value);
            }
        }
    }

    /**
     * Select the concrete constructor without consulting global factory state.
     *
     * @param string                  $type      spatial type
     * @param CoordinateDimensionEnum $dimension coordinate layout
     * @param SpatialModelEnum        $family    spatial family
     * @param bool                    $mutable   representation
     */
    private static function className(string $type, CoordinateDimensionEnum $dimension, SpatialModelEnum $family, bool $mutable): string
    {
        $layout = match ($dimension) {
            CoordinateDimensionEnum::XY => 'Dimension2',
            CoordinateDimensionEnum::XYZ => 'Dimension3z',
            CoordinateDimensionEnum::XYM => 'Dimension3m',
            CoordinateDimensionEnum::XYZM => 'Dimension4zm',
        };
        $familyName = match ($family) {
            SpatialModelEnum::GEOMETRY => 'Geometry',
            SpatialModelEnum::GEOGRAPHY => 'Geography',
        };
        $immutableName = 'Collection' === $type ? $familyName.'CollectionImmutable' : $type.'Immutable';
        $mutableName = 'Collection' === $type ? 'SpatialCollection' : $type;

        return $mutable
            ? 'LongitudeOne\SpatialTypes\Types\\'.$layout.'\\'.$mutableName
            : 'LongitudeOne\SpatialTypes\Types\\'.$layout.'\\'.$familyName.'\\'.$immutableName;
    }

    /**
     * Adapt the immutable public argument order to mutable constructors.
     *
     * @param string           $type      spatial type
     * @param array            $arguments builder arguments
     * @param SpatialModelEnum $family    spatial family
     * @param SpatialReference $reference declared reference
     * @param bool             $mutable   representation
     *
     * @phpstan-param list<mixed> $arguments
     *
     * @return list<mixed>
     */
    private static function constructorArguments(string $type, array $arguments, SpatialModelEnum $family, SpatialReference $reference, bool $mutable): array
    {
        $result = [$arguments[0], $reference];
        if ('Collection' === $type && !$mutable) {
            $result = [$reference, $arguments[0]];
        }
        if ('Point' === $type) {
            $result = [...$arguments, $reference];
        }
        if ($mutable) {
            $result[] = $family;
        }

        return $result;
    }
}
