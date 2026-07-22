# Extension security review checklist (TYPO3 v14)

Work read-only. For each finding report `path:line`, the risk, the fix and the changelog or API to use. Order by
severity: critical (exploitable without login), high, medium, low.

## Input, output and queries

- [ ] SQL only via `QueryBuilder` with `createNamedParameter()` or Extbase queries; no string concatenation into
      `where()`, `orderBy()` or `executeQuery()`. Extbase `orderBy()` expressions (Feature-32051) take property
      names, not user input.
- [ ] Fluid output escaped; every `f:format.raw` and `{value -> f:format.raw()}` justified; no
      `escapeOutput = false` in custom ViewHelpers without sanitising.
- [ ] Backend modules and AJAX routes check `$GLOBALS['BE_USER']` permissions on the record (`doesUserHaveAccess`,
      `check('tables_modify', ...)`), not only the module access.
- [ ] Redirect targets validated (no open redirect via `redirect_url` / `returnUrl`).
