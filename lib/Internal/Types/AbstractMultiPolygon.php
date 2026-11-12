<?php

/*
 * This file is part of the LongitudeOne Spatial project.
 *
 * Copyright (c) 2026 Alexandre Tranchant <alexandre.tranchant@gmail.com>
 *
 * See LICENSE for copyright and license information.
 *
 */

declare(strict_types=1);

namespace LongitudeOne\SpatialTypes\Internal\Types;

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Interfaces\MutableLineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutableMultiPolygonInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutablePointInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutablePolygonInterface;
use LongitudeOne\SpatialTypes\Interfaces\SpatialInterface;
use LongitudeOne\SpatialTypes\Types\Dimension2;
use LongitudeOne\SpatialTypes\Types\Dimension3m;
use LongitudeOne\SpatialTypes\Types\Dimension3z;
use LongitudeOne\SpatialTypes\Types\Dimension4zm;

/** Mutable MultiPolygon members without geometric validation.
 * @internal
 *
 * @extends AbstractMutableCollection<MutablePolygonInterface, array<array<array{0: float|int|string|null, 1: float|int|string|null, 2?: float|int|null, 3?: float|int|null}|\LongitudeOne\SpatialTypes\Interfaces\MutablePointInterface>|\LongitudeOne\SpatialTypes\Interfaces\MutableLineStringInterface>>
 */
abstract class AbstractMultiPolygon extends AbstractMutableCollection implements MutableMultiPolygonInterface
{
    /** @return list<MutablePolygonInterface> */
    public function getPolygons(): array
    {
        return $this->getElements();
    }

    /** Return the fixed spatial type. */
    public function getType(): GeometryTypeEnum
    {
        return GeometryTypeEnum::MULTIPOLYGON;
    }

    /** @return (float|int|null)[][][][] */
    public function toArray(): array
    {
        return array_map(static fn (MutablePolygonInterface $element): array => $element->toArray(), $this->getElements());
    }

    /** @param array<array<array{0: float|int|string|null, 1: float|int|string|null, 2?: float|int|null, 3?: float|int|null}|MutablePointInterface>|MutableLineStringInterface>|SpatialInterface $element Mutable member or coordinate array to validate */
    protected function hydrateElement(mixed $element): MutablePolygonInterface
    {
        if (\is_array($element)) {
            $element = match ($this->getDimension()) {
                CoordinateDimensionEnum::XY => new Dimension2\Polygon($element, $this->spatialReference, $this->family),
                CoordinateDimensionEnum::XYZ => new Dimension3z\Polygon($element, $this->spatialReference, $this->family),
                CoordinateDimensionEnum::XYM => new Dimension3m\Polygon($element, $this->spatialReference, $this->family),
                CoordinateDimensionEnum::XYZM => new Dimension4zm\Polygon($element, $this->spatialReference, $this->family),
            };
        }
        if (!$element instanceof MutablePolygonInterface) {
            throw new InvalidValueException('Mutable MultiPolygon members must be mutable Polygon objects or coordinate arrays.');
        }
        MutableComponentGuard::check($element, $this);

        return $element;
    }
}
