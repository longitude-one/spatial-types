# Normative conformance review for 1.0.0

Audit for [Story #26](https://github.com/longitude-one/spatial-types/issues/26),
2026-10-16, baseline `ceaee5d142c77fc3922ab50c762fa6648fea0e30`.
**Result: blocking findings remain; this is not a release conformance claim.**
No runtime behavior or implementation-status classification is changed by this audit.

## Scope and sources

The [implementation-status API](../lib/Implementation/SpatialTypeImplementationStatus.php)
reports eleven implemented types: Point, LineString, CircularString, CompoundCurve,
Polygon, Triangle, PolyhedralSurface, GeometryCollection, MultiPoint,
MultiLineString and MultiPolygon. Both Geometry and Geography are reviewed;
XY, XYZ, XYM and XYZM apply, except PolyhedralSurface requires XYZ or XYZM.
Stories #24 and #25 are closed; parent Epic #23 remains open.

Excluded types (`false`): BrepSolid, Circle, Clothoid, CompoundSurface,
EllipticalCurve, GeodesicString, NurbsCurve, SpiralCurve, CurvePolygon, TIN,
MultiCurve and MultiSurface. Abstract model types are not instantiable.

**ISO** below means the locally inspected **ISO/IEC CD 13249-3:201x(E)**,
Committee Draft dated 2009-01-16, not a final published edition.
**OGC** means Simple Feature Access 1.2.1, OGC 06-103r4, inspected through
the supplied full transcription. ISO is primary; OGC supplements it without
silently resolving conflicts. ISO 19107 and ISO 19111 are normative dependencies
(ISO clause 2); their full texts were unavailable for direct verification.
ISO 19136/GML representation belongs to encoding/decoding, outside this audit.

## Conformance matrix

Implementation paths below are relative to `lib/`; test paths start at the
repository root (a bare test filename or dimension path is under
`tests/Unit/Types/`). Braces denote the listed path variants. Shared rows apply to every
listed type; a conformant row certifies only its stated requirement.
`Non-conformant` includes missing enforcement or missing normative regression
coverage. `Normative ambiguity` requires a Technical Architect decision.
No unapproved difference is classified as `Intentional divergence`.

| Spatial type | Requirement | Normative source | Implementation | Test coverage | Status | Notes |
| --- | --- | --- | --- | --- | --- | --- |
| All eleven | Concrete type identity and instantiability | ISO 4.2.3–19; concrete type definitions | `Types/Dimension*/{Geometry,Geography}/*`; `Implementation/SpatialTypeImplementationStatus` | `tests/Unit/Implementation/SpatialTypeImplementationStatusTest.php`; `tests/Unit/Types/ConcreteTypeContractTest.php` | Conformant | Enum identity is checked; PHP inheritance need not reproduce SQL inheritance. |
| All eleven | Distinct XY, XYZ, XYM, XYZM coordinate layouts; Z differs from M | ISO 4.2.1.6; 5.1.3, 5.1.11–12 | `Types/Dimension*`; `Value/Coordinates.php`; `Validator/DimensionValidation.php` | `tests/Unit/Types/ConcreteTypeContractTest.php`; `Dimension3m/XymContractTest.php`; `tests/Unit/Value/CoordinatesTest.php` | Conformant | PolyhedralSurface is restricted to layouts containing Z (ISO 8.5.1, rule 17). |
| All aggregates | Members share the parent's SRS; retain authority identity | ISO 4.2.14; 7.2.1, 7.3.1, 7.4.1, 8.2.1, 8.5.1, 9.1.1 | `Validator/SpatialReferenceValidation.php`; `Reference/SpatialReference.php` | `tests/Unit/Reference/SpatialReferenceTest.php`; `tests/Unit/Types/SpatialWithSridTest.php`; curve compatibility tests | Conformant | Authority equality and SRID 0 as a concrete reference are documented project rules. |
| All eleven | Reference assignment does not transform coordinates | ISO 5.1.5 versus 5.1.6 | `Types/AbstractSpatialType.php::withSpatialReference()` and aggregate overrides | `tests/Unit/Types/SpatialWithSridTest.php`; curve and surface reference-copy tests | Conformant | Coordinate transformation remains external. |
| Point | One position; all-null ordinates represent EMPTY; partial-null positions rejected | ISO 4.2.3; 6.1.1 | `Types/AbstractPoint.php`; concrete point constructors | `tests/Unit/Types/EmptyPointTest.php`; dimension-specific Point tests | Conformant | Geometry and Geography coordinate policies remain distinct. |
| LineString | Ordered defining positions and linear segments; no consecutive duplicates | ISO 4.2.5; 7.2.1; 7.2.3 | `Types/AbstractLineString.php`; `Validator/LineStringValidation.php` | `tests/Unit/Types/Dimension2/Geometry/LineStringTest.php::testConstructorRejectsConsecutiveDuplicatePoints()`; replacement tests | Conformant | Non-simple curves may be represented; that does not excuse an incorrect simplicity predicate. |
| LineString | Non-empty sequence contains at least two points | ISO 7.2.1, Description 4, 10–11 | `Types/AbstractLineString.php::addPoints()` | Missing rejection regression | Non-conformant | F1: one-point line accepted. |
| LineString | Simplicity considers segment intersections, not just repeated vertices | ISO 4.2.4; 5.1.8 | `Types/Collection/AbstractPointCollection.php::isSimple()` | Optional validator tests exist in `tests/Unit/Validator/Constraints/SimpleLineStringValidatorTest.php`; direct-method crossing regression missing | Non-conformant | F8: inherited predicate returns true for crossing segments. |
| LineString, CircularString, CompoundCurve | Endpoints, closure, Z/M and numeric equality | ISO 4.2.4; 4.2.1.6; 7.1.4–8; 7.3.7–8; 7.4.6–7 | `Types/Abstract{LineString,CircularString,CompoundCurve}.php`; `AbstractPoint::equalsTo()` | `tests/Unit/Types/CircularStringValidationTest.php`; `CompoundCurveValidationTest.php::testIntegerFloatMismatch()` and measure/elevation tests | Normative ambiguity | F9: current closure/continuity uses all ordinates and strict PHP numeric types; distinguish ISO XY closure, XYZ closure and spatial equality. |
| CircularString | EMPTY or odd count >=3; ordered arc control points; non-coincident intermediate points | ISO 4.2.6; 7.3.1, Description 4–12 | `Types/AbstractCircularString.php` | `tests/Unit/Types/CircularStringValidationTest.php`; eight dimension/family CircularString tests | Conformant | Collinear arcs and complete circles retained without linearization. No circular simplicity predicate is claimed. |
| CompoundCurve | Ordered LineString/CircularString components; contiguous endpoints; EMPTY compound | ISO 4.2.7; 7.4.1; 7.4.6–7 | `Types/AbstractCompoundCurve.php` | `tests/Unit/Types/CompoundCurveValidationTest.php`; eight dimension/family CompoundCurve tests | Conformant | Structure is represented; equality interpretation remains F9. Empty components and nested compounds are rejected under Story #25. |
| Polygon | Exterior first, then interior linear boundaries; closure; coordinate/reference compatibility | ISO 4.2.10; 8.3.1; 8.2.1 | `Types/AbstractPolygon.php`; `Boundary/PolygonBoundary.php`; `Validator/RingValidation.php` | `tests/Unit/Validator/Constraints/RingValidatorTest.php`; `tests/Unit/Types/AggregateBoundaryAndValidationTest.php` | Conformant | This row concerns storage and closure only. |
| Polygon, Triangle, PolyhedralSurface patches | Simple rings, planar patches, valid holes, no crossing rings/spikes; connected interior | ISO 4.2.8–11; 8.2.1, rules 2–3, 8–14; OGC 6.1.11.1 | `Types/AbstractPolygon.php`; `Validator/Constraints/RingValidator.php` | Polygon rejection tests missing for self-crossing rings, outside/crossing holes, disconnected interiors and non-planar XYZ/XYZM polygons | Non-conformant | F2: structural Ring validation omits polygon topology; PolyhedralSurface adds planarity/simplicity but not full patch topology. |
| Triangle | EMPTY or one four-position closed exterior ring, without holes | ISO 4.2.11; 8.4.1 | `Types/AbstractTriangle.php`; `Validator/Constraints/TriangleValidator.php` | `tests/Unit/Types/TriangleTest.php`; `tests/Unit/Validator/Constraints/TriangleValidatorTest.php` | Conformant | All four layouts and both families covered for structural rules. |
| Triangle | Three distinct, non-collinear vertices | OGC 6.1.11.1 versus ISO 4.2.11 and inherited polygon rules | `Validator/Constraints/TriangleValidator.php` | Non-collinearity rejection regression missing | Normative ambiguity | F5: collinear triangle accepted; approve application of the supporting OGC requirement before correction. |
| PolyhedralSurface | Connected planar 3D patches, common edges incident to at most two faces, opposite traversal | ISO 4.2.12; 8.5.1, rules 2, 4, 8, 17–19; OGC 6.1.12.1 | `Validator/Constraints/PolyhedralSurfaceValidator.php`; `Validator/Internal/PolyhedralSurfaceTopology.php` | `tests/Unit/Types/PolyhedralSurfaceTest.php`; `tests/Unit/Validator/Constraints/PolyhedralSurfaceValidatorTest.php` | Conformant | Exact XYZ arithmetic; M ignored. Patch-interior intersections and vertex-manifold coverage remain incomplete (F2/F4). |
| PolyhedralSurface | Whether free edges/open surfaces are permitted; simple sphere topology | ISO 8.5.1, rules 6–7, versus OGC 6.1.12.1 | `Validator/Internal/PolyhedralSurfaceTopology.php` | `tests/Unit/Types/PolyhedralSurfaceTest.php::testOpenSurfaceAndIndependentCopies()` | Normative ambiguity | F4: ISO wording unconditionally says closed sphere; OGC makes closure conditional. No arbitration invented here. |
| GeometryCollection | Zero or more heterogeneous spatial types, nested collections, common SRS | ISO 4.2.14; 9.1.1 | `Types/AbstractCollection.php` | `tests/Issue/Issue1/CollectionTest.php`; `tests/Unit/Types/ConcreteTypeContractTest.php` | Conformant | Heterogeneous geometry types, rather than coordinate layouts, are covered by this row. |
| GeometryCollection | Restrictions on mixed coordinate layouts/families | ISO 4.2.14; 9.1.1, rule 8 | `Types/AbstractCollection.php::addElement()` | Collection dimension/family rejection tests; no approved divergence evidence located | Normative ambiguity | F6: model restrictions exceed the general collection definition; decide mapped subtype versus intentional restriction. |
| MultiPoint | Point-only membership, no geometric ordering requirement; EMPTY members preserved | ISO 4.2.15; 9.2.1 | `Types/AbstractMultiPoint.php`; `Collection/AbstractPointCollection.php` | `tests/Issue/EmptyMultiPointMembersTest.php` | Conformant | Array order is a storage choice; it does not assert geometric order. Duplicate points are allowed, but affect simplicity. |
| MultiPoint | Simplicity detects coincident positions, ignores Z/M, independent of PHP identity | ISO 9.2.1, Description 5; 5.1.8 | `Types/Collection/AbstractPointCollection.php::isSimple()` | Same-instance, numeric-equivalence and differing-Z/M normative regressions missing | Non-conformant | F8: repeated same point object is incorrectly simple. |
| MultiLineString | LineString-only members; crossings allowed; non-empty member invariants | ISO 4.2.16–17; 9.3.1; 9.4.1 | `Types/AbstractMultiLineString.php`; `Collection/AbstractLineStringCollection.php` | `tests/Issue/EmptyMultiLineStringValidationTest.php`; dimension-specific MultiLineString tests | Non-conformant | Storage/type checks covered; one-point member defect inherited from F1. No multi-curve simplicity predicate is claimed. |
| MultiPolygon | Polygon-only members; EMPTY members preserved | ISO 4.2.19; 9.6.1 | `Types/AbstractMultiPolygon.php` | `tests/Issue/EmptyMultiPolygonDimension{2,3m,3z,4zm}Test.php` | Conformant | Membership storage only; validity is below. |
| MultiPolygon | Disjoint polygon interiors; coplanar boundaries intersect only at finitely many points; no spikes | ISO 9.6.1, Description 3–8; OGC 6.1.14 | `Types/AbstractMultiPolygon.php::addPolygon()` | Overlap, shared-edge and inherited invalid-polygon rejection regressions missing | Non-conformant | F3: even duplicate polygons are accepted. |
| MultiPoint, MultiLineString, MultiPolygon, GeometryCollection | Geometric EMPTY versus zero stored members | ISO 5.1.7; 9.1.1, Description 6–7 | Aggregate `isEmpty()` methods | Empty-member regressions assert non-empty containers, including `EmptyMultiPointMembersTest.php::testDimension2GeometryPreservesEmptyMembers()` | Normative ambiguity | F7: approve cardinality-versus-point-set interpretation; preserving empty members does not settle this predicate. |
| All eleven | WKT/WKB/GML, vendor formats, decoding and coordinate transformation algorithms | ISO 5.1.6, 5.1.46–58; ISO 19136 | External encoders/decoders/transformers | Not required in this library | Not applicable | Spatial domain invariants remain in scope; SQL routine completeness is not claimed (ISO clause 20). |

## Findings and release disposition

Every finding below blocks the 1.0.0 normative baseline until resolved or
explicitly arbitrated. Existing passing tests do not waive any finding.
Follow-up corrections must cover constructors and immutable replacements in
both families and each applicable layout, using explicit normative regressions.

| ID | Observed behavior / missing evidence | Expected result or decision required |
| --- | --- | --- |
| F1 | One-point LineString accepted: `[[0, 0]]`. | Reject malformed non-empty sequences; test direct construction, replacement and MultiLineString members. |
| F2 | Bow-tie Polygon accepted: `[[[0,0],[2,2],[0,2],[2,0],[0,0]]]`; no full polygon topology validation. | Enforce simple planar boundaries, containment, permitted contacts and connected interiors, including inherited types; add missing topology regressions. |
| F3 | MultiPolygon accepts two identical Polygon objects. | Reject intersecting interiors and shared boundary intervals; test point contacts and EMPTY members separately. |
| F4 | Open PolyhedralSurface accepted; complete patch-intersection/manifold evidence absent. | Normative Gate: reconcile ISO rule 6 with rule 7 and OGC; approve open-surface policy and residual topology obligations before changes. |
| F5 | Collinear Triangle accepted: `[[[0,0],[1,0],[2,0],[0,0]]]`. | Normative Gate: adopt supporting OGC non-collinearity requirement or document a justified approved divergence; add corresponding tests. |
| F6 | GeometryCollection rejects mixed layouts/families beyond common SRS. | Architecture/Normative Gate: approve the mapping/restriction with rationale, or plan compatible correction before stabilization. |
| F7 | A container with only EMPTY members reports false from `isEmpty()`. | Normative Gate: arbitrate point-set semantics versus storage cardinality, accounting for approved empty-member Stories #8–10. |
| F8 | Crossing LineString and `MultiPoint([$p, $p])` both report simple. | Correct public predicates against geometric positions, intersections and applicable Z/M rules; optional validators do not repair the methods. |
| F9 | Curve closure/continuity compares M and strict integer/float identity. | Normative/API Gate: define whether these public methods map to ISO XY, XYZ or another approved domain predicate; retain Story #25 behavior until approved. |
| F10 | Direct ISO 19107/19111 verification and complete per-invariant, per-layout rejection evidence unavailable. | Obtain supporting normative evidence where ISO delegates requirements; close remaining coverage gaps before claiming an exhaustive baseline. |

No behavioral correction is included. The audit is suitable for refinement
of follow-up findings, **not yet for public-contract stabilization**.
