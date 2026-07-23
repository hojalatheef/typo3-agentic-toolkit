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
- [ ] No `GeneralUtility::_GP()` (removed in 13.0), `$_GET` or `$_POST`; values come from the PSR-7 request.

## Access control and abuse

- [ ] Extbase edit/update/delete actions carry `#[Authorize]` with an ownership callback (Feature-107826, 14.2)
      or an equivalent explicit check.
- [ ] Group checks use `fe_groups` UIDs, not titles.
- [ ] Login-like, contact, registration and search endpoints are rate limited: `#[RateLimit]` (Feature-108982)
      or `RateLimiterFactoryInterface::createRequestBasedLimiter()` (Feature-109080). The actions are uncached.
- [ ] Module access uses `user`/`admin`/`systemMaintainer` or a dedicated gate (Feature-109365), never a check
      inside the controller only.
