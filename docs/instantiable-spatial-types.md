# Instantiable spatial types

This reference documents the public, concrete spatial value types provided by
`longitude-one/spatial-types`. It covers their namespaces, direct constructors,
observable API, and mutation semantics.

## Standards and model

The library follows the spatial-object model established by the
[OGC Simple Features Access standard](https://www.ogc.org/standards/sfa/) and
the `ST_Geometry` hierarchy of SQL/MM Spatial (ISO/IEC 13249-3). In practice,
that model supplies the instantiable `Point`, `LineString`, `Polygon`,
`MultiPoint`, `MultiLineString`, `MultiPolygon`, and collection types.

### ISO/IEC 13249-3 geometry-type coverage

The following matrix covers the SQL/MM geometry hierarchy used by this
library. It distinguishes the standard's instantiable types from the concrete
types exposed by the library. SQL/MM's curve-capable types are listed even
though the library deliberately implements only the linear subset. The SQL/MM
type declarations are reproduced in the informative SQL/MM comparison in the
[OGC Simple Features Access specification](https://docs.ogc.org/is/06-104r4/06-104r4/pdf).

| ISO/IEC 13249-3 type | Instantiable in SQL/MM | Library representation | Coverage note |
| --- | --- | --- | --- |
| `ST_Geometry` | No | — | Abstract root type; `SpatialInterface` is the common PHP contract. |
| `ST_Point` | Yes | `Point` | Implemented for every family and coordinate layout. |
| `ST_Curve` | No | — | Abstract one-dimensional base type. |
| `ST_LineString` | Yes | `LineString` | Implemented for every family and coordinate layout. |
| `ST_CircularString` | Yes | — | Circular-arc curves are not implemented. |
| `ST_CompoundCurve` | Yes | — | Compositions of linear and circular curves are not implemented. |
| `ST_Surface` | No | — | Abstract two-dimensional base type. |
| `ST_CurvePolygon` | Yes | — | Curve-bounded polygons are not implemented. |
| `ST_Polygon` | Yes | `Polygon` | Implemented with `LineString` rings only. |
| `ST_GeomCollection` | Yes | `GeometryCollection` / `GeographyCollection` | Implemented as a heterogeneous collection that can contain other collections. |
| `ST_MultiPoint` | Yes | `MultiPoint` | Implemented. |
| `ST_MultiCurve` | Yes | — | Not implemented; it could contain any `ST_Curve` subtype. |
| `ST_MultiLineString` | Yes | `MultiLineString` | Implemented. |
| `ST_MultiSurface` | Yes | — | Not implemented; it could contain any `ST_Surface` subtype. |
| `ST_MultiPolygon` | Yes | `MultiPolygon` | Implemented. |

`ST_SpatialRefSys` is an SQL/MM spatial-reference-system metadata type rather
than a subtype of `ST_Geometry`; it is outside this value-type hierarchy. This
library identifies a reference with `Reference\SpatialReference`, which can
carry an authority and its integer identifier, without modelling a full
reference-system definition. `ST_PolyhedralSurface` is not part of the SQL/MM
base hierarchy covered by this matrix and is not implemented.

The `Geography` family is a library-level counterpart to the `Geometry` family;
it is not a separate `ST_Geography` branch in the SQL/MM hierarchy.

The library distinguishes two coordinate families:

- **Geometry** uses Cartesian `X, Y` coordinates. Numeric values are not range
  limited by this library.
- **Geography** uses geodetic `longitude, latitude` coordinates, still ordered
  as `X, Y`. Longitude must be in `[-180, 180]` and latitude in `[-90, 90]`.

The coordinate layouts are `XY`, `XYZ`, `XYM`, and `XYZM`; their tuple order is
always `X, Y[, Z][, M]`. `Z` is elevation and `M` is a measure. In particular,
an `XYM` tuple is `[x, y, m]`, whereas an `XYZ` tuple is `[x, y, z]`.

Every spatial object has a `SpatialReference`; `getSrid()` exposes its legacy
integer identifier. Constructors without a reference use identifier `0`, as
prescribed for SQL/MM constructors without an SRID. Identifier `0` is a real
unnamed reference in this model, not a compatibility wildcard: every member of
an aggregate must have exactly the aggregate's spatial reference.

See [Spatial reference systems](spatial-reference-systems.md) for the ISO/IEC
13249-3 rationale and examples of valid and invalid aggregate membership.

## Class catalogue

All classes in the following matrix are concrete and instantiable. The class
name is composed from the dimension, family, and type:

```php
LongitudeOne\SpatialTypes\Types\<dimension>\<family>\<type>
```

| Dimension | Namespace segment | Coordinates |
| --- | --- | --- |
| 2D | `Dimension2` | `X, Y` |
| 3D with elevation | `Dimension3z` | `X, Y, Z` |
| 3D with measure | `Dimension3m` | `X, Y, M` |
| 4D | `Dimension4zm` | `X, Y, Z, M` |

For each dimension, both `Geometry` and `Geography` provide:

| Type | Geometry class | Geography class |
| --- | --- | --- |
| Point | `…\Geometry\Point` | `…\Geography\Point` |
| Line string | `…\Geometry\LineString` | `…\Geography\LineString` |
| Polygon | `…\Geometry\Polygon` | `…\Geography\Polygon` |
| Multi-point | `…\Geometry\MultiPoint` | `…\Geography\MultiPoint` |
| Multi-line string | `…\Geometry\MultiLineString` | `…\Geography\MultiLineString` |
| Multi-polygon | `…\Geometry\MultiPolygon` | `…\Geography\MultiPolygon` |
| Heterogeneous collection | `…\Geometry\GeometryCollection` | `…\Geography\GeographyCollection` |

For example, a four-dimensional geographic polygon is
`LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\Polygon`.

`AbstractSpatialType` and the other `Abstract*` classes are internal base
classes, not part of the instantiable API.

## Direct construction

The selected namespace determines both the coordinate dimension and family.
The optional `$srid` argument accepts `int|SpatialReference` and defaults to
the unnamed reference identified by `0` in every constructor.

### Point

```php
new Point($x, $y, int|SpatialReference $srid = 0);                 // Dimension2
new Point($x, $y, $z, int|SpatialReference $srid = 0);             // Dimension3z
new Point($x, $y, $m, int|SpatialReference $srid = 0);             // Dimension3m
new Point($x, $y, $z, $m, int|SpatialReference $srid = 0);         // Dimension4zm
```

`$x` and `$y` accept `int`, `float`, or a coordinate string accepted by the
geo-parser. `$z` and `$m` accept `int|float`. For Geography, `$x` means
longitude and `$y` means latitude.

```php
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\Point;

$point = new Point(2.3522, 48.8566, 35, 4326); // longitude, latitude, elevation
```

### Immutable point coordinates

`LongitudeOne\SpatialTypes\Value\Coordinates` is a public immutable value
object for normalized numeric point coordinates. Its named constructors encode
the coordinate dimension: `Coordinates::xy()`, `Coordinates::xym()`,
`Coordinates::xyz()`, and `Coordinates::xyzm()`.

`PointInterface::getCoordinates()` returns this value object.
`PointInterface::withCoordinates(Coordinates $coordinates)` returns a new point
of the same concrete class and SRID. The supplied coordinates must have the
same dimension as the point; geographic points additionally validate longitude
and latitude ranges.

```php
use LongitudeOne\SpatialTypes\Value\Coordinates;

$higherPoint = $point->withCoordinates(Coordinates::xyz(2.3522, 48.8566, 42));
```

### Point-based types

```php
new LineString(array $points, int|SpatialReference $srid = 0);
new MultiPoint(array $points, int|SpatialReference $srid = 0);
```

`$points` may contain instances of `PointInterface` or coordinate tuples for
the selected dimension, for example `[[0, 0], [1, 1]]` for `XY` or
`[[0, 0, 12, 3], [1, 1, 15, 4]]` for `XYZM`.

```php
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineString;

$lineString = new LineString([[0, 0], [2, 1], [5, 1]], 3857);
```

### Ring-, line-, and polygon-based types

```php
new Polygon(array $rings, int|SpatialReference $srid = 0);
new MultiLineString(array $lineStrings, int|SpatialReference $srid = 0);
new MultiPolygon(array $polygons, int|SpatialReference $srid = 0);
```

- A polygon ring is a `LineStringInterface` or an array of point tuples. Every
  supplied ring must be closed.
- A multi-line-string element is a `LineStringInterface` or an array of point
  tuples.
- A multi-polygon element is a `PolygonInterface` or an array of rings.

```php
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\Polygon;

$polygon = new Polygon([
    [[0, 0], [4, 0], [4, 3], [0, 0]],
], 3857);
```

### Heterogeneous collections

```php
new GeometryCollection(int|SpatialReference $srid = 0, array $elements = []);
new GeographyCollection(int|SpatialReference $srid = 0, array $elements = []);
```

Collections accept their initial elements in their constructor. They accept any
non-collection spatial type with the same family and coordinate dimension.
Nested geometry/geography collections are rejected.

```php
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\GeometryCollection;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\Point;

$collection = new GeometryCollection(3857, [new Point(0, 0, 3857)]);
```

## Observing spatial values

All concrete types implement `SpatialInterface` and `JsonSerializable`.

| Method | Result |
| --- | --- |
| `getFamily(): FamilyEnum` | `FamilyEnum::GEOMETRY` or `FamilyEnum::GEOGRAPHY`. |
| `getType(): TypeEnum` | The OGC/SQL/MM type, such as `TypeEnum::POLYGON`. |
| `getSrid(): int` | The object's SRID. |
| `getSpatialReference(): SpatialReference` | The full reference identity, including its optional authority. |
| `hasZ(): bool` / `hasM(): bool` | Whether the selected coordinate layout has Z or M. |
| `hasSameDimension(SpatialInterface $other): bool` | Whether both values use the same Z/M layout. |
| `toArray(): array` | Nested coordinate arrays only; it omits type, family, and SRID. |
| `jsonSerialize(): array` | `['type' => string, 'coordinates' => array, 'srid' => int]`. |

There is intentionally no public `getDimension()` method. Use `hasZ()` and
`hasM()` to inspect the coordinate layout.

### Point getters

Every point provides `getX()` and `getY()`. `getLongitude()` is an alias for
`getX()`, and `getLatitude()` is an alias for `getY()`; those aliases are useful
for Geography values. `getZ()` is available only for `XYZ`/`XYZM` points and
`getM()` only for `XYM`/`XYZM` points. Calling an unavailable getter throws
`BadMethodCallException`.

`equalsTo(PointInterface $other): bool` compares the concrete point class,
family, SRID, coordinate layout, and all applicable ordinates. `toArray()`
returns one tuple in the layout's order.

### Aggregate getters and predicates

| Type | Element access | Predicates |
| --- | --- | --- |
| `LineString` | `getPoints()`, `getPoint($index)`, `getElements()` | `isEmpty()`, `isLine()`, `isClosed()` |
| `MultiPoint` | `getPoints()`, `getPoint($index)`, `getElements()` | `isEmpty()`, `isSimple()` |
| `Polygon` | `getRings()`, `getRing($index)`, `getElements()` | — |
| `MultiLineString` | `getLineStrings()`, `getLineString($index)`, `getElements()` | `isEmpty()` |
| `MultiPolygon` | `getPolygons()`, `getPolygon($index)`, `getElements()` | `isEmpty()` |
| `GeometryCollection` / `GeographyCollection` | `getElements()` | `isEmpty()`, `hasElement($spatial)` |

For point, ring, line-string, and polygon single-element accessors, negative
indexes count from the end (`-1` is the last element). An index is wrapped by
the element count; accessing an empty aggregate raises `OutOfBoundsException`.

In the current implementation, `isLine()` is true for a line string with at
least two points. `isClosed()` requires a line and equal first/last points.
Use the Symfony constraint `Validator\\Constraints\\Ring` to validate a
linear ring: it requires at least four points and equal first and last points.
No simplicity or polygon topology validation is performed yet.

## Immutability contract

Every spatial type is immutable through the public API: construction sets its
ordinates, SRID, and aggregate membership, and no public mutator exists.
`withCoordinates(Coordinates $coordinates): static` returns a point with
replacement coordinates of the same dimension; `withSpatialReference(SpatialReference $reference): static`
returns one with the same coordinates and a new declared reference.

`LineString` and `Polygon` provide
`withArrayOfCoordinates(array $coordinates): static`. These methods return a
new aggregate with replacement coordinates while preserving family, dimension,
and SRID. The line-string method accepts coordinate tuples; the polygon method
accepts arrays of rings. The receiving aggregate determines whether tuples are
XY, XYM, XYZ, or XYZM.

`LineString::withPoint(int $pointIndex, Coordinates $coordinates): static`
returns a deep copy with one replacement point. For polygons,
`Polygon::withPoint(int $ringIndex, int $pointIndex, Coordinates $coordinates): static`
uses a ring index followed by a point index. Replacing either endpoint of a
ring updates both endpoints to preserve its closure. Both methods retain the
family, dimension, and SRID of the receiving aggregate.

`MultiPoint::withPoint(int $pointIndex, Coordinates $coordinates): static`
returns a deep copy with one replacement point and preserves the receiving
multi-point's family, dimension, and SRID.

`Polygon::withRing(int $ringIndex, array $coordinates): static` replaces
one closed ring. `MultiLineString::withLineString(int $lineStringIndex, array
$coordinates): static` replaces one line string. Both return deep copies and
derive the expected XY, XYM, XYZ, or XYZM layout from the receiving aggregate.

`MultiLineString::withPoint(int $lineStringIndex, int $pointIndex, Coordinates
$coordinates): static` replaces one point in one line string and returns a deep
copy.

`MultiPolygon::withPoint(int $polygonIndex, int $ringIndex, int $pointIndex,
Coordinates $coordinates): static` replaces one point.
`MultiPolygon::withRing(int $polygonIndex, int $ringIndex, array
$coordinates): static` replaces one ring, and `MultiPolygon::withPolygon(int
$polygonIndex, array $coordinates): static` replaces one polygon. All three
methods return deep copies and preserve family, dimension, and SRID.

`GeometryCollection::withElement(int $elementIndex, SpatialInterface
$element): static` and `GeographyCollection::withElement(...)` replace one
element. They validate the replacement against the receiver and deeply copy the
unchanged elements.

All spatial types implement `withSpatialReference(SpatialReference $reference): static`.
For aggregates, it returns a deep copy whose contained values receive the
requested reference, so the result remains internally reference-consistent.
`withSrid(int $srid)` remains as a legacy integer adapter.

The constructors use these same validation paths. Aggregated values must be
compatible with the receiving type's family, dimension, and spatial-reference
rules; invalid coordinates, missing ordinates, incompatible family/dimension/reference, or a
non-ring polygon boundary cause the corresponding spatial exception.

`getPoints()`, `getRings()`, and the other plural getters return PHP arrays, so
changing the returned array does not alter the aggregate's membership. Their
contained objects are immutable too, so the retrieved object graph is safe to
share.
