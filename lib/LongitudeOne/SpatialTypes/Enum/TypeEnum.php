<?php
/**
 * This file is part of the spatial project.
 *
 * PHP 8.1 | 8.2 | 8.3
 *
 * Copyright Alexandre Tranchant <alexandre.tranchant@gmail.com> 2024
 * Copyright Longitude One 2024
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

declare(strict_types=1);

namespace LongitudeOne\SpatialTypes\Enum;

/**
 * Types Enum.
 *
 * This enumeration is used to define the type of spatial instance.
 */
enum TypeEnum: string
{
    /**
     * Collection type is used to create GEOMETRY_COLLECTION and GEOGRAPHY_COLLECTION.
     */
    case COLLECTION = 'Collection';

    /**
     * Geometry type is used to create GEOMETRY and GEOGRAPHY LineStrings.
     */
    case LINESTRING = 'LineString';

    /**
     * MultiLineString type is used to create GEOMETRY and GEOGRAPHY MultiLineStrings.
     */
    case MULTILINESTRING = 'MultiLineString';

    /**
     * MultiPoint type is used to create GEOMETRY and GEOGRAPHY MultiPoints.
     */
    case MULTIPOINT = 'MultiPoint';

    /**
     * MultiPolygon type is used to create GEOMETRY and GEOGRAPHY MultiPolygons.
     */
    case MULTIPOLYGON = 'MultiPolygon';

    /**
     * Point type is used to create GEOMETRY and GEOGRAPHY Points.
     */
    case POINT = 'Point';

    /**
     * Polygon type is used to create GEOMETRY and GEOGRAPHY Polygons.
     */
    case POLYGON = 'Polygon';
}
