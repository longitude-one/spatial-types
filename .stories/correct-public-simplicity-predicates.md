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

This finding blocks the 1.0.0 normative baseline. On 2026-10-30, Alexandre
explicitly approved false for the two cited R2 scenarios: the repeated same
non-empty Point instance in an immutable MultiPoint, and the crossing XY
immutable LineString above. This Story is limited to those corrections and
their regressions. Mutable MultiPoint already returns false for the repeated
instance and must retain that result.

The wider mapping of PHP predicates to XY/3D semantics, numeric equality and
EMPTY/partial-position behavior remains audit R3/R4. Those unresolved contracts
must not be silently decided as part of these two corrections.

## Acceptance Criteria

- [ ] Repeating a complete non-empty point at two MultiPoint member positions produces a non-simple result, including when both positions refer to the same PHP object.
- [ ] The crossing XY LineString from Context produces a non-simple result.
- [ ] The crossing correction preserves existing results for ordinary non-crossing XY line strings and does not classify the permitted first/last endpoint contact alone as an interior segment crossing.
- [ ] Predicate evaluation preserves coordinates, membership, context and shared objects.

## Constraints

- Use ISO/IEC CD 13249-3:201x(E), clauses 4.2.1.6 and 5.1.8–9, and supporting OGC 06-103r4, clauses 6.1.5–6.1.6.
- Preserve all behavior outside the two approved corrections, including unresolved equality, higher-dimensional crossing and EMPTY/partial-position contracts from R3/R4.
- Do not add constructor validation or a dependency on `spatial-validator`.
- Do not extend the mutable LineString public API as an incidental correction.

## Definition of Done

The project's common Definition of Done applies.

- [ ] `composer test-local` passes.
- [ ] `composer quality` passes.
- [ ] Explicit regressions reproduce both approved scenarios exactly, cover repeated-instance membership in XY/XYZ/XYM/XYZM and both families, and verify the already-correct mutable MultiPoint result.
- [ ] XY curve regressions distinguish interior crossings from permitted first/last endpoint contact and preserve coordinates and members; pre-existing duplicate-endpoint equality behavior is not silently changed.
- [ ] Gate decisions and missing-coverage disposition are recorded in #26's conformance matrix.
- [ ] Documentation describes the two approved corrections without claiming resolution of R3/R4.
