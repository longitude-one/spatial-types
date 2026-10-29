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

use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\SpatialTypes\Interfaces\MutableLineStringInterface;

/** Mutable line-string operations without geometric validation.
 * @internal
 */
abstract class AbstractLineString extends AbstractPointSequence implements MutableLineStringInterface
{
    use PointSequenceEditingTrait;

    /** Return the fixed spatial type. */
    public function getType(): GeometryTypeEnum
    {
        return GeometryTypeEnum::LINESTRING;
    }

    /** Whether a line has equal endpoints. */
    public function isClosed(): bool
    {
        return $this->isLine() && parent::isClosed();
    }

    /** Whether a line has at least two defining points. */
    public function isLine(): bool
    {
        return count($this->points) >= 2;
    }
}
