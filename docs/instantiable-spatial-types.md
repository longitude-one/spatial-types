# Instantiable spatial types

This reference documents the public, concrete spatial value types provided by
`longitude-one/spatial-types`. It covers their namespaces, direct constructors,
observable API, and mutation semantics.

## Standards and model

The library follows the spatial-object model established by the
[OGC Simple Features Access standard](https://www.ogc.org/standards/sfa/) and
the `ST_Geometry` hierarchy of SQL/MM Spatial (ISO/IEC 13249-3). In practice,
that model supplies the instantiable `Point`, `LineString`, `Polygon`,
`Triangle`, `PolyhedralSurface`, `MultiPoint`, `MultiLineString`, `MultiPolygon`, and collection types.

### ISO/IEC 13249-3 geometry-type coverage

The following matrix covers the SQL/MM geometry hierarchy used by this
library. It distinguishes the standard's instantiable types from the concrete
types exposed by the library. SQL/MM's curve-capable types are listed even
though the library deliberately implements only the linear subset. The SQL/MM
type declarations are reproduced in the informative SQL/MM comparison in the
[OGC Simple Features Access specification](https://docs.ogc.org/is/06-104r4/06-104r4/pdf).

| ISO/IEC 13249-3 type | Instantiable in SQL/MM | Library representation                       | Coverage note                                                                 |
| -------------------- | ---------------------- | -------------------------------------------- | ----------------------------------------------------------------------------- |
| `ST_Geometry`        | No                     | —                                            | Abstract root type; `SpatialInterface` is the common PHP contract.            |
| `ST_Point`           | Yes                    | `Point`                                      | Implemented for every family and coordinate layout.                           |
| `ST_Curve`           | No                     | —                                            | Abstract one-dimensional base type.                                           |
| `ST_LineString`      | Yes                    | `LineString`                                 | Implemented for every family and coordinate layout.                           |
| `ST_CircularString`  | Yes                    | —                                            | Circular-arc curves are not implemented.                                      |
| `ST_CompoundCurve`   | Yes                    | —                                            | Compositions of linear and circular curves are not implemented.               |
| `ST_Surface`         | No                     | —                                            | Abstract two-dimensional base type.                                           |
| `ST_CurvePolygon`    | Yes                    | —                                            | Curve-bounded polygons are not implemented.                                   |
| `ST_Polygon`         | Yes                    | `Polygon`                                    | Implemented with `LineString` rings only.                                     |
| `ST_Triangle`        | Yes                    | `Triangle`                                   | Four exterior positions, including closure, and no interior rings (section 8.4). |
| `ST_PolyhdrlSurface` | Yes                    | `PolyhedralSurface`                          | Connected polygon patches in XYZ or XYZM; an open boundary is allowed. |
| `ST_TIN`             | Yes                    | —                                            | Triangulated surfaces are not implemented. |
| `ST_GeomCollection`  | Yes                    | `GeometryCollection` / `GeographyCollection` | Implemented as a heterogeneous collection that can contain other collections. |
| `ST_MultiPoint`      | Yes                    | `MultiPoint`                                 | Implemented.                                                                  |
| `ST_MultiCurve`      | Yes                    | —                                            | Not implemented; it could contain any `ST_Curve` subtype.                     |
| `ST_MultiLineString` | Yes                    | `MultiLineString`                            | Implemented.                                                                  |
| `ST_MultiSurface`    | Yes                    | —                                            | Not implemented; it could contain any `ST_Surface` subtype.                   |
| `ST_MultiPolygon`    | Yes                    | `MultiPolygon`                               | Implemented. |

`ST_SpatialRefSys` is an SQL/MM spatial-reference-system metadata type rather
than a subtype of `ST_Geometry`; it is outside this value-type hierarchy. This
library identifies a reference with `Reference\SpatialReference`, which can
carry an authority and its integer identifier, without modelling a full
reference-system definition. The supplied ISO/IEC CD 13249-3:201x(E), section
8.5, names the polyhedral surface type `ST_PolyhdrlSurface`.

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
See [Spatial validators](validators.md) for the constraints available through
Symfony Validator, their constructor integration, and opt-in validation.

## Class catalogue

All classes in the following matrix are concrete and instantiable. The class
name is composed from the dimension, family, and type:

```php
LongitudeOne\SpatialTypes\Types\<dimension>\<family>\<type>
```

| Dimension         | Namespace segment | Coordinates  |
| ----------------- | ----------------- | ------------ |
| 2D                | `Dimension2`      | `X, Y`       |
| 3D with elevation | `Dimension3z`     | `X, Y, Z`    |
| 3D with measure   | `Dimension3m`     | `X, Y, M`    |
| 4D                | `Dimension4zm`    | `X, Y, Z, M` |

For each dimension, both `Geometry` and `Geography` provide:

| Type                     | Geometry class                  | Geography class                   |
| ------------------------ | ------------------------------- | --------------------------------- |
| Point                    | `…\Geometry\Point`              | `…\Geography\Point`               |
| Line string              | `…\Geometry\LineString`         | `…\Geography\LineString`          |
| Polygon                  | `…\Geometry\Polygon`            | `…\Geography\Polygon`             |
| Triangle                 | `…\Geometry\Triangle`           | `…\Geography\Triangle`            |
| Multi-point              | `…\Geometry\MultiPoint`         | `…\Geography\MultiPoint`          |
| Multi-line string        | `…\Geometry\MultiLineString`    | `…\Geography\MultiLineString`     |
| Multi-polygon            | `…\Geometry\MultiPolygon`       | `…\Geography\MultiPolygon`        |
| Heterogeneous collection | `…\Geometry\GeometryCollection` | `…\Geography\GeographyCollection` |

`PolyhedralSurface` is additionally available in `Dimension3z` and
`Dimension4zm`, in both families. Its faces require Z; XY and XYM variants are
not provided. It is a surface, not a heterogeneous collection or a solid.

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

Each coordinate parameter is nullable. Omitting every ordinate (or passing
`null` for every ordinate) creates an empty point; incomplete tuples are
invalid. This preserves the selected coordinate layout and optional SRID:

```php
$emptyPoint = new Point(srid: 4326);
assert($emptyPoint->isEmpty());
assert([] === $emptyPoint->toArray());
```

`FromIndexedArrayFactory::createPoint([])` creates the same empty point in its
requested family, dimension, and spatial-reference context. Empty points cannot
be added to point-defined aggregates such as `LineString` or `MultiPoint`.

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
new Triangle(array $rings, int|SpatialReference $srid = 0);
new MultiLineString(array $lineStrings, int|SpatialReference $srid = 0);
new MultiPolygon(array $polygons, int|SpatialReference $srid = 0);
new PolyhedralSurface(array $patches = [], int|SpatialReference $srid = 0);
```

- A polygon ring is a `LineStringInterface` or an array of point tuples. Every
  supplied ring must be closed.
- A triangle uses the same ring representation as a polygon, with exactly four
  exterior positions (including closure) and no interior rings. `[]` represents
  an empty triangle; `[[]]` is invalid.
- A multi-line-string element is a `LineStringInterface` or an array of point
  tuples.
- A multi-polygon element is a `PolygonInterface` or an array of rings.
- A polyhedral-surface patch is also a `PolygonInterface` (including a triangle)
  or an array of rings. All patches must match the surface family, XYZ/XYZM
  layout and complete spatial reference. `new PolyhedralSurface()` or `[]`
  creates an empty surface; empty member patches are rejected. A single patch
  is accepted, as are open and closed assemblies: enclosing a volume is not
  required. Faces must be planar with finite XYZ coordinates and simple closed
  rings. Shared edges join at most two faces, in opposite directions, and the
  whole patch adjacency graph must be connected. Shared edges may have
  different vertex subdivisions. M does not participate in adjacency checks.
  These checks use exact floating-point comparisons in Cartesian XYZ, including
  for Geography; they do not compute geodesic edges. General face-interior
  intersections, hole containment and vertex-manifold topology are not checked.
  Direct construction is supported; no new public factory entry point is added.

```php
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\Polygon;

$polygon = new Polygon([
    [[0, 0], [4, 0], [4, 3], [0, 0]],
], 3857);
```

`TriangleInterface` extends `PolygonInterface`. All eight dimension/family
combinations provide a concrete `Triangle`:

```php
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\Triangle;

$triangle = new Triangle([[[0, 0], [4, 0], [0, 4], [0, 0]]], 3857);
$empty = new Triangle([]);
```

These structural constraints follow sections 4.2.11 and 8.4 of
ISO/IEC CD 13249-3:201x(E). The `Triangle` constraint enforces them and
composes `Ring` to reject unclosed rings and consecutive duplicate points.
It can also [validate ordinary polygons](validators.md#triangle-structure). Family, coordinate layout, geographic ranges and
spatial reference are validated as for polygons; non-collinearity and full
surface topology are not checked.

Triangles provide the polygon accessors and immutable replacement methods.
Copies preserve the concrete triangle class and enforce its structural
constraints. `getType()` returns `GeometryTypeEnum::TRIANGLE`; JSON uses
`Triangle` with the same nested coordinate representation as polygons.
Triangle factory entry points, SQL/MM visibility attributes and text/binary/GML
conversion routines are not currently exposed.

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

| Method                                            | Result                                                          |
| ------------------------------------------------- | --------------------------------------------------------------- |
| `getFamily(): SpatialModelEnum`                   | `SpatialModelEnum::GEOMETRY` or `SpatialModelEnum::GEOGRAPHY`.  |
| `getType(): GeometryTypeEnum`                     | The OGC/SQL/MM type, such as `GeometryTypeEnum::POLYGON`.       |
| `getSrid(): int`                                  | The object's SRID.                                              |
| `getSpatialReference(): SpatialReference`         | The full reference identity, including its optional authority.  |
| `hasZ(): bool` / `hasM(): bool`                   | Whether the selected coordinate layout has Z or M.              |
| `hasSameDimension(SpatialInterface $other): bool` | Whether both values use the same Z/M layout.                    |
| `isEmpty(): bool`                                 | Whether the object corresponds to the empty set.                |
| `toArray(): array`                                | Nested coordinate arrays only; it omits type, family, and SRID. |
| `jsonSerialize(): array`                          | `['type' => string, 'coordinates' => array, 'srid' => int]`.    |

There is intentionally no public `getDimension()` method. Use `hasZ()` and
`hasM()` to inspect the coordinate layout.

### Point getters

Every point provides `getX()` and `getY()`. `getLongitude()` is an alias for
`getX()`, and `getLatitude()` is an alias for `getY()`; those aliases are useful
for Geography values. `getZ()` is available only for `XYZ`/`XYZM` points and
`getM()` only for `XYM`/`XYZM` points. Calling an unavailable getter throws
`BadMethodCallException`. For an empty point, its available coordinate getters
and `getCoordinates()` return `null`; `toArray()` returns `[]`.

`equalsTo(PointInterface $other): bool` compares the concrete point class,
family, SRID, coordinate layout, and all applicable ordinates. `toArray()`
returns one tuple in the layout's order.

### Aggregate getters and predicates

| Type                                         | Element access                                               | Predicates                            |
| -------------------------------------------- | ------------------------------------------------------------ | ------------------------------------- |
| `LineString`                                 | `getPoints()`, `getPoint($index)`, `getElements()`           | `isEmpty()`, `isLine()`, `isClosed()` |
| `MultiPoint`                                 | `getPoints()`, `getPoint($index)`, `getElements()`           | `isEmpty()`, `isSimple()`             |
| `Polygon` / `Triangle`                       | `getRings()`, `getRing($index)`, `getElements()`             | `isEmpty()`                           |
| `MultiLineString`                            | `getLineStrings()`, `getLineString($index)`, `getElements()` | `isEmpty()`                           |
| `MultiPolygon`                               | `getPolygons()`, `getPolygon($index)`, `getElements()`       | `isEmpty()`                           |
| `PolyhedralSurface` | `getPatches()`, `getPatch($index)`, `getElements()` | `isEmpty()` |
| `GeometryCollection` / `GeographyCollection` | `getElements()`                                              | `isEmpty()`, `hasElement($spatial)`   |

For point, ring, line-string, polygon, and patch single-element accessors, negative
indexes count from the end (`-1` is the last element). An index is wrapped by
the element count; accessing an empty aggregate raises `OutOfBoundsException`.

In the current implementation, `isLine()` is true for a line string with at
least two points. `isClosed()` requires a line and equal first/last points.
Use the Symfony constraint `Validator\\Constraints\\Ring` to validate a
linear ring: it requires at least four points and equal first and last points.

### Checking line-string simplicity

A `LineString` is allowed to be non-simple: construction and immutable update
methods do not reject self-intersections. When an application needs the
SQL/MM `ST_IsSimple` predicate, validate the value explicitly with Symfony's
validator and the `SimpleLineString` constraint:

```php
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineString;
use LongitudeOne\SpatialTypes\Validator\Constraints\SimpleLineString;
use Symfony\Component\Validator\Validation;

$lineString = new LineString([[0, 0], [2, 2], [0, 2], [2, 0]]);
$violations = Validation::createValidator()->validate($lineString, new SimpleLineString());

$isSimple = 0 === count($violations); // false: the two non-neighbouring segments cross
```

The constraint considers the `X, Y` projection only. Z and M ordinates are
ignored, so two segments that cross in `XY` are non-simple even if they have
different elevations or measures. It rejects proper crossings, tangencies and
overlaps; consecutive segments may share their common endpoint, as may the
first and last segments of a closed line. `Ring` validates only ring structure;
apply `SimpleLineString` separately when a simple ring is required. Polygon
topology, such as an inner-ring containment check, remains outside this
constraint.

### Checking three-dimensional line-string simplicity

For `XYZ` and `XYZM` line strings, use the separate
`SimpleThreeDimensionalLineString` constraint. It implements the spatial
three-dimensional check: X, Y and Z determine whether segments meet, while M
is ignored. Consequently, segments that cross in their XY projection at
different elevations are simple in 3D; segments meeting at the same XYZ
position are not, even if their M values differ.

```php
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\LineString;
use LongitudeOne\SpatialTypes\Validator\Constraints\SimpleThreeDimensionalLineString;
use Symfony\Component\Validator\Validation;

$lineString = new LineString([[0, 0, 0], [2, 2, 0], [0, 2, 1], [2, 0, 1]]);
$violations = Validation::createValidator()->validate($lineString, new SimpleThreeDimensionalLineString());

$isSimpleInThreeDimensions = 0 === count($violations); // true
```

Apply this constraint only to line strings that have a Z ordinate (`XYZ` or
`XYZM`). Use `SimpleLineString` when the required rule is the usual 2D
projection check.

## Immutability contract

Every spatial type is immutable through the public API: construction sets its
ordinates, SRID, and aggregate membership, and no public mutator exists.
`withCoordinates(Coordinates $coordinates): static` returns a point with
replacement coordinates of the same dimension; `withSpatialReference(SpatialReference $reference): static`
returns one with the same coordinates and a new declared reference.

`LineString`, `Polygon`, `Triangle`, and `PolyhedralSurface` provide
`withArrayOfCoordinates(array $coordinates): static`. These methods return a
new aggregate with replacement coordinates while preserving family, dimension,
and SRID. The line-string method accepts coordinate tuples; the polygon method
accepts arrays of rings. The receiving aggregate determines whether tuples are
XY, XYM, XYZ, or XYZM. For `PolyhedralSurface`, replacement coordinates are
arrays of patches and use XYZ or XYZM; `[]` returns an empty surface.

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
`PolyhedralSurface` provides `withPoint($patchIndex, $ringIndex, $pointIndex,
$coordinates)`, `withRing($patchIndex, $ringIndex, $coordinates)` and
`withPatch($patchIndex, $coordinates)` with the same deep-copy semantics.
Every replacement revalidates the complete surface. A local edit that breaks
adjacency is rejected; use `withArrayOfCoordinates()` to update several faces
atomically. Editing a shared vertex does not silently move neighbouring faces.
Empty surfaces reject indexed access and indexed replacements.

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

## Diagnostic messages

Untrusted values included in exceptions raised by this library are formatted with
`LongitudeOne\Core\Diagnostic\DiagnosticValueFormatter` (spatial-core 1.1+).
Control characters, invisible Unicode formatting characters and line separators
are escaped visibly; invalid UTF-8 bytes are escaped and each formatted value is
limited to 2,048 characters. This affects diagnostic output only, not coordinate parsing.
Caller-supplied dimension and family validation messages are formatted as a whole.

Previous exceptions from dependencies retain their original messages and formatting.
Exceptions constructed directly by application code retain PHP's standard constructor behavior.
The formatter does not escape messages for HTML, JSON, XML or SQL.
