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

namespace LongitudeOne\SpatialTypes\Interfaces;

use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;

/** Mutable spatial contract; type-specific editing is introduced separately. */
interface MutableSpatialInterface extends SpatialInterface
{
    /** Change the declared family without transforming coordinates.
     * @param SpatialModelEnum $family Declared spatial family
     */
    public function setFamily(SpatialModelEnum $family): static;

    /** Change the declared reference without transforming coordinates.
     * @param SpatialReference $reference Declared reference
     */
    public function setSpatialReference(SpatialReference $reference): static;

    /** Change the declared SRID without transforming coordinates.
     * @param int $srid Declared reference identifier
     */
    public function setSrid(int $srid): static;
}
