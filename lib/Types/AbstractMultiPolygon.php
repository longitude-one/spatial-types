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

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Interfaces\MutableLineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutableMultiPolygonInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutablePointInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutablePolygonInterface;

/** Mutable MultiPolygon members without geometric validation.
 * @internal
 *
 * @extends AbstractMutableCollection<MutablePolygonInterface, array<array<array{0: null|float|int|string, 1: null|float|int|string, 2?: null|float|int, 3?: null|float|int}|\LongitudeOne\SpatialTypes\Interfaces\MutablePointInterface>|\LongitudeOne\SpatialTypes\Interfaces\MutableLineStringInterface>>
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

    /** @return (null|float|int)[][][][] */
    public function toArray(): array
    {
        return array_map(static fn (MutablePolygonInterface $element): array => $element->toArray(), $this->getElements());
    }

    /** @param array<array<array{0: null|float|int|string, 1: null|float|int|string, 2?: null|float|int, 3?: null|float|int}|MutablePointInterface>|MutableLineStringInterface>|MutablePolygonInterface $element Mutable member or coordinate array */
    protected function hydrateElement(mixed $element): MutablePolygonInterface
    {
        if (is_array($element)) {
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
