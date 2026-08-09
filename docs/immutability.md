# Immutability

Spatial values are often reused in several places. Treating their coordinates
as immutable makes that reuse safe and makes a value's location explicit.

## Current contract

`Point` is immutable through the public API. Its coordinates and SRID are set
by its constructor and it has no public setter. A point with a different
coordinate or SRID is a distinct value and must be constructed as such.

```php
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\Point;

$paris = new Point(2.3522, 48.8566, 4326);
$movedParis = new Point(2.3600, $paris->getY(), $paris->getSrid());
```

The original `$paris` remains unchanged. This is particularly useful when a
point is part of an aggregate.

## Why coordinates are immutable

Consider a point used by a line string, where that line string is then used by
a multi-line string:

```php
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineString;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiLineString;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\Point;

$point = new Point(2.3522, 48.8566, 4326);
$line = new LineString([
    $point,
    new Point(2.3600, 48.8600, 4326),
], 4326);
$multiLine = new MultiLineString([$line], 4326);
```

`$point` is referenced by `$line`, and `$line` is referenced by `$multiLine`.
If a public operation such as `setX()` could change `$point`, the line and the
multi-line string would both change as an indirect side effect. A caller that
only holds `$multiLine` could therefore observe a geometry change without any
operation being made on it.

Because points are immutable, that operation does not exist. Code that needs
another location creates another point and explicitly builds the spatial value
that should contain it. This preserves validation of geography coordinate
ranges, coordinate dimensions, family, and SRID compatibility.

Changing an SRID follows the same value-object rule: it produces another
spatial value. Merely assigning a new SRID does not transform its coordinates;
a coordinate transformation must first calculate new ordinates, then construct
a value with those ordinates and the target SRID.

## Scope and aggregate types

This guarantee currently applies to `Point`. Aggregate types such as
`LineString`, `Polygon`, and `MultiLineString` still expose public membership
mutators (`addPoint()`, `addRing()`, and similar methods), and are therefore
mutable. Their child points nevertheless remain safe to share because their
coordinates cannot be altered after construction.

See [Instantiable spatial types](instantiable-spatial-types.md#mutability-contract)
for the complete current mutability contract.
