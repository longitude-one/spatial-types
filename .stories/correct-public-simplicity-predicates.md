# Correct public simplicity predicates

**As a** consumer of spatial predicates,
**I want** simplicity results to reflect geometric positions and segment intersections,
**so that** shared PHP object identity cannot hide a non-simple geometry.

## Context

Finding R2 of the [Story #26 audit](https://github.com/longitude-one/spatial-types/issues/26):
an immutable MultiPoint containing the same non-empty Point object twice
reports simple. The mutable equivalent reports non-simple. An immutable
LineString with coordinates `[[0,0],[2,2],[0,2],[2,0]]` reports simple despite
the interior segment crossing. The inherited implementation compares stored
point objects rather than curve segments and excludes identical instances.

ISO/IEC CD 13249-3:201x(E), clause 5.1.8, and OGC 06-103r4, clauses
6.1.5–6.1.6, distinguish spatial simplicity from PHP instance identity.
Ordinary ISO spatial operations ignore Z/M under clause 4.2.1.6; ISO 5.1.9
defines a separate 3D simplicity operation. Existing `equalsTo()` also tests
PHP class, context, strict numeric types and all ordinates.

This finding blocks the 1.0.0 normative baseline. The mapping of PHP
`isSimple()` to XY or 3D semantics, numeric equality, and behavior on
EMPTY/partial positions must be approved through the API/Normative Gate
(audit R3). This Story records a finding; it is not Ready by creation.

## Acceptance Criteria

- [ ] Repeating a complete non-empty point at two MultiPoint member positions produces a non-simple result, including when both positions refer to the same PHP object.
- [ ] The crossing XY LineString from Context produces a non-simple result.
- [ ] Curve simplicity accounts for interior segment intersections and the permitted coincidence of start/end points.
- [ ] All exposed simplicity predicates follow the approved numeric, dimensional, context and EMPTY/partial-position contract, independently of PHP object identity or mutability.
- [ ] Predicate evaluation preserves coordinates, membership, context and shared objects.

## Constraints

- Use ISO/IEC CD 13249-3:201x(E), clauses 4.2.1.6 and 5.1.8–9, and supporting OGC 06-103r4, clauses 6.1.5–6.1.6.
- Resolve audit R3's API/Normative Gate before defining the remaining dimensional and invalid-representation expectations.
- Do not add constructor validation or a dependency on `spatial-validator`.
- Do not extend the mutable LineString public API as an incidental correction.

## Definition of Done

The project's common Definition of Done applies.

- [ ] `composer test-local` passes.
- [ ] `composer quality` passes.
- [ ] Explicit regressions cover same-instance and distinct-instance duplicates, numeric equivalence, crossing segments, closed simple curves, XY/XYZ/XYM/XYZM, both families and all applicable mutability variants.
- [ ] Gate decisions and missing-coverage disposition are recorded in #26's conformance matrix.
- [ ] Public predicate documentation describes the approved dimensional and invalid-representation behavior.
