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
use LongitudeOne\SpatialTypes\Interfaces\MutableSpatialInterface;

/** Family-independent mutable spatial collection.
 * @internal
 *
 * @extends AbstractMutableCollection<MutableSpatialInterface, never>
 */
abstract class AbstractSpatialCollection extends AbstractMutableCollection
{
    /** Return the fixed collection type. */
    public function getType(): GeometryTypeEnum
    {
        return GeometryTypeEnum::GEOMETRYCOLLECTION;
    }

    /** Return ordered member representations, preserving arbitrary collection nesting.
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return array_map(static fn (MutableSpatialInterface $element): array => $element->toArray(), $this->getElements());
    }

    /** @param mixed $element Mutable spatial object retained without copying */
    protected function hydrateElement(mixed $element): MutableSpatialInterface
    {
        if (!$element instanceof MutableSpatialInterface) {
            throw new InvalidValueException('Mutable spatial collections require mutable spatial objects.');
        }
        MutableComponentGuard::check($element, $this);

        return $element;
    }
}
