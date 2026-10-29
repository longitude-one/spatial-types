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
use LongitudeOne\SpatialTypes\Interfaces\MutableMultiLineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutablePointInterface;

/** Mutable MultiLineString members without geometric validation.
 * @internal
 *
 * @extends AbstractMutableCollection<MutableLineStringInterface, array<array{0: null|float|int|string, 1: null|float|int|string, 2?: null|float|int, 3?: null|float|int}|\LongitudeOne\SpatialTypes\Interfaces\MutablePointInterface>>
 */
abstract class AbstractMultiLineString extends AbstractMutableCollection implements MutableMultiLineStringInterface
{
    /** @return list<MutableLineStringInterface> */
    public function getLineStrings(): array
    {
        return $this->getElements();
    }

    /** Return the fixed spatial type. */
    public function getType(): GeometryTypeEnum
    {
        return GeometryTypeEnum::MULTILINESTRING;
    }

    /** @return (null|float|int)[][][] */
    public function toArray(): array
    {
        return array_map(static fn (MutableLineStringInterface $element): array => $element->toArray(), $this->getElements());
    }

    /** @param array<array{0: null|float|int|string, 1: null|float|int|string, 2?: null|float|int, 3?: null|float|int}|MutablePointInterface>|MutableLineStringInterface $element Mutable member or coordinate array */
    protected function hydrateElement(mixed $element): MutableLineStringInterface
    {
        if (is_array($element)) {
            $element = match ($this->getDimension()) {
                CoordinateDimensionEnum::XY => new Dimension2\LineString($element, $this->spatialReference, $this->family),
                CoordinateDimensionEnum::XYZ => new Dimension3z\LineString($element, $this->spatialReference, $this->family),
                CoordinateDimensionEnum::XYM => new Dimension3m\LineString($element, $this->spatialReference, $this->family),
                CoordinateDimensionEnum::XYZM => new Dimension4zm\LineString($element, $this->spatialReference, $this->family),
            };
        }
        if (!$element instanceof MutableLineStringInterface) {
            throw new InvalidValueException('Mutable MultiLineString members must be mutable LineString objects or coordinate arrays.');
        }
        MutableComponentGuard::check($element);

        return $element;
    }
}
