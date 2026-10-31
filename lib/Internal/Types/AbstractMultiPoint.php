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

namespace LongitudeOne\SpatialTypes\Internal\Types;

use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Factory\SpatialContext;
use LongitudeOne\SpatialTypes\Interfaces\MutableMultiPointInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutablePointInterface;
use LongitudeOne\SpatialTypes\Types\MutableComponentGuard;
use LongitudeOne\SpatialTypes\Types\MutablePointHydrator;

/** Mutable MultiPoint members without geometric validation.
 * @internal
 *
 * @extends AbstractMutableCollection<MutablePointInterface, array{0: null|float|int|string, 1: null|float|int|string, 2?: null|float|int, 3?: null|float|int}>
 */
abstract class AbstractMultiPoint extends AbstractMutableCollection implements MutableMultiPointInterface
{
    /** @return list<MutablePointInterface> */
    public function getPoints(): array
    {
        return $this->getElements();
    }

    /** Return the fixed spatial type. */
    public function getType(): GeometryTypeEnum
    {
        return GeometryTypeEnum::MULTIPOINT;
    }

    /** @return (null|float|int)[][] */
    public function toArray(): array
    {
        return array_map(static fn (MutablePointInterface $element): array => $element->toArray(), $this->getElements());
    }

    /** @param array{0: null|float|int|string, 1: null|float|int|string, 2?: null|float|int, 3?: null|float|int}|MutablePointInterface $element Mutable member or coordinate array */
    protected function hydrateElement(mixed $element): MutablePointInterface
    {
        if (is_array($element)) {
            $element = MutablePointHydrator::hydrate($element, new SpatialContext($this->spatialReference, $this->family, $this->getDimension()));
        }
        if (!$element instanceof MutablePointInterface) {
            throw new InvalidValueException('Mutable MultiPoint members must be mutable Point objects or coordinate arrays.');
        }
        MutableComponentGuard::check($element, $this);

        return $element;
    }
}
