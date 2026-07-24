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
- [ ] Commands that delete data or need interaction carry `#[AsNonSchedulableCommand]` (Feature-107151).

## Files and uploads

- [ ] Uploads go through `#[FileUpload]` (Feature-103511) with `fileSize`, `mimeType`, `fileExtension`;
      `addRandomSuffix` not disabled; target folder not publicly listable.
- [ ] No `move_uploaded_file()` / `$_FILES` handling.
- [ ] Assets referenced through the System Resource API (`PUBLIC:` / `EXT:` in public folders), no access to
      arbitrary extension paths (Deprecation-109409).
- [ ] File dump / download links signed with `HashService` and a purpose-specific secret.

## Secrets and crypto

- [ ] No credentials in `ext_conf_template.txt` defaults, TypoScript or committed `.env` files.
- [ ] Stored third-party secrets encrypted with `CipherService` + per-purpose derived key (Feature-108002).
- [ ] HMACs: `TYPO3\CMS\Core\Crypto\HashService` with additional secret, `HashAlgo::SHA3_256` for new code
      (Feature-102761, Breaking-106307). No `md5()`/`sha1()` tokens, no `GeneralUtility::hmac()`.
- [ ] Passwords hashed via `PasswordHashFactory` or the form finisher option `hashed: true` (Feature-106752).
- [ ] Random tokens from `Random::generateRandomHexString()` / `random_bytes()`, never `uniqid()`/`rand()`.

## Browser-side hardening

- [ ] Inline JS/CSS avoided or marked `csp="1"`; `useNonce` replaced (Deprecation-100887).
- [ ] Inline `style` attributes via `f:asset.styleAttr` where the site uses hash-based CSP (Feature-100887).
