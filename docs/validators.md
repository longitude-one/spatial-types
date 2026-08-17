# Spatial validators

This library exposes Symfony Validator constraints for checking spatial values.
They are useful in two complementary situations:

- **Internal invariants** protect a value while it is built or updated. The
  library invokes the relevant validator and throws `InvalidValueException` or
  a compatibility exception when the invariant is not met.
- **Application rules** are opt-in. Create a Symfony validator and apply the
  constraint yourself; validation returns a `ConstraintViolationList` and does
  not alter the spatial value.

The second category is deliberately not imposed by constructors. For example,
a `LineString` can be non-simple when an application needs to retain imported
or invalid geometry for later review.

## Summary

| Constraint | Value | Rule | Applied internally | Typical external use |
| --- | --- | --- | --- | --- |
| `NoConsecutiveDuplicatePoints` | `LineString` | Adjacent points must differ. | Yes: `LineString` construction and coordinate replacement. | Audit an implementation of `LineStringInterface`. |
| `MinimumPointCount` | `LineString` | Requires at least the configured number of points (four by default). | Through `Ring` when a polygon receives a ring. | Validate a candidate ring before building a polygon. |
| `FirstPointEqualsLastPoint` | `LineString` | First and last points are equal. | Through `Ring` when a polygon receives a ring. | Validate a candidate ring. |
| `Ring` | `LineString` | Composes minimum point count, closure, and no consecutive duplicate points. | Yes: polygon boundaries. | Validate a ring independently. |
| `SameFamily` | Spatial value | Uses the requested `Geometry` or `Geography` family. | Yes: aggregate membership. | Validate an incoming member against an expected family. |
| `SameDimension` | Spatial value | Uses the requested `XY`, `XYZ`, `XYM`, or `XYZM` layout. | Yes: aggregate membership. | Validate an incoming member against an expected layout. |
| `SameSpatialReference` | Spatial value | Uses the requested complete spatial reference. | Yes: aggregate membership. | Validate an incoming member against an expected reference. |
| `SimpleLineString` | `LineString` | Is simple in the XY projection; Z and M are ignored. | No. | Apply the usual two-dimensional simplicity rule. |
| `SimpleThreeDimensionalLineString` | `XYZ`/`XYZM` `LineString` | Is simple in XYZ space; M is ignored. | No. | Apply a three-dimensional simplicity rule. |

“Applied internally” describes the library's public constructors and immutable
replacement methods. It does not mean every constraint is automatically run
when Symfony validates an object.

## Using a constraint externally

Create Symfony's validator, validate the value, then inspect the violation
list. No exception is thrown merely because the rule fails.

```php
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineString;
use LongitudeOne\SpatialTypes\Validator\Constraints\SimpleLineString;
use Symfony\Component\Validator\Validation;

$line = new LineString([[0, 0], [2, 2], [0, 2], [2, 0]]);
$violations = Validation::createValidator()->validate($line, new SimpleLineString());

if (0 !== count($violations)) {
    // The line self-intersects in XY.
}
```

Several constraints may be evaluated together:

```php
use LongitudeOne\SpatialTypes\Validator\Constraints\FirstPointEqualsLastPoint;
use LongitudeOne\SpatialTypes\Validator\Constraints\MinimumPointCount;
use LongitudeOne\SpatialTypes\Validator\Constraints\NoConsecutiveDuplicatePoints;
use LongitudeOne\SpatialTypes\Validator\Constraints\SimpleLineString;

$violations = Validation::createValidator()->validate($line, [
    new MinimumPointCount(),
    new FirstPointEqualsLastPoint(),
    new NoConsecutiveDuplicatePoints(),
    new SimpleLineString(),
]);
```

Use `Ring` instead of its three structural components when simplicity is not a
requirement. Add `SimpleLineString` separately when it is.

## Two-dimensional and three-dimensional simplicity

`SimpleLineString` checks only X/Y. An XY crossing is a violation even when
the segments use different elevations. This corresponds to the usual 2D
topological predicate.

`SimpleThreeDimensionalLineString` accepts only line strings with a Z ordinate
(`XYZ` or `XYZM`) and checks X/Y/Z. Two segments that cross in projection but
are at different elevations do not meet in 3D. M is not a spatial coordinate
for this rule and is ignored.

```php
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\LineString;
use LongitudeOne\SpatialTypes\Validator\Constraints\SimpleThreeDimensionalLineString;

$line = new LineString([[0, 0, 0], [2, 2, 0], [0, 2, 1], [2, 0, 1]]);
$violations = Validation::createValidator()->validate($line, new SimpleThreeDimensionalLineString());

// No violation: the projected crossing occurs at two different elevations.
```

## Compatibility constraints

The three compatibility constraints use the expected value as their
constructor argument. This mirrors the checks performed when a spatial value
is added to an aggregate.

```php
use LongitudeOne\SpatialTypes\Enum\DimensionEnum;
use LongitudeOne\SpatialTypes\Enum\FamilyEnum;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Validator\Constraints\SameDimension;
use LongitudeOne\SpatialTypes\Validator\Constraints\SameFamily;
use LongitudeOne\SpatialTypes\Validator\Constraints\SameSpatialReference;

$violations = Validation::createValidator()->validate($line, [
    new SameFamily(FamilyEnum::GEOMETRY),
    new SameDimension(DimensionEnum::X_Y_Z),
    new SameSpatialReference(SpatialReference::fromSrid(2154), 'line string'),
]);
```

An SRID of `0` is a concrete unnamed spatial reference, not a wildcard. See
[Spatial reference systems](spatial-reference-systems.md) for the membership
rules and their rationale.
