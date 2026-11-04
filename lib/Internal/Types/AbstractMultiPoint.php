<?php
/**
 * This file is part of the spatial project.
 *
 * PHP 8.4 | 8.5
 *
 * Copyright Alexandre Tranchant <alexandre.tranchant@gmail.com> 2026
 * Copyright Longitude One 2026
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

declare(strict_types=1);

namespace LongitudeOne\SpatialTypes\Internal\Types;

use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Interfaces\MutableMultiPointInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutablePointInterface;
use LongitudeOne\SpatialTypes\Interfaces\SpatialInterface;
use LongitudeOne\SpatialTypes\Internal\Factory\SpatialContext;

/** Mutable MultiPoint members without geometric validation.
 * @internal
 *
 * @extends AbstractMutableCollection<MutablePointInterface, array{0: float|int|string|null, 1: float|int|string|null, 2?: float|int|null, 3?: float|int|null}>
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

    /** @return (float|int|null)[][] */
    public function toArray(): array
    {
        return array_map(static fn (MutablePointInterface $element): array => $element->toArray(), $this->getElements());
    }

    /** @param array{0: float|int|string|null, 1: float|int|string|null, 2?: float|int|null, 3?: float|int|null}|SpatialInterface $element Mutable member or coordinate array to validate */
    protected function hydrateElement(mixed $element): MutablePointInterface
    {
        if (\is_array($element)) {
            $element = MutablePointHydrator::hydrate($element, new SpatialContext($this->spatialReference, $this->family, $this->getDimension()));
        }
        if (!$element instanceof MutablePointInterface) {
            throw new InvalidValueException('Mutable MultiPoint members must be mutable Point objects or coordinate arrays.');
        }
        MutableComponentGuard::check($element, $this);

        return $element;
    }
}
