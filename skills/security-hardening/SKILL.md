---
name: security-hardening
description: >-
  Harden a TYPO3 v14 extension or site with the security APIs of v13/v14 - hash-based Content-Security-Policy (csp.yaml useHash/useNonce, f:asset csp argument, f:asset.styleAttr), BeforePersistingReportEvent for CSP reports, Subresource Integrity (integrity = auto), CipherService/KeyFactory encryption, HashService HMACs with SHA3-256, rate limiting, Extbase #[Authorize], and password hashing in the form SaveToDatabase finisher. Use for an extension security review, when storing secrets or tokens, when adding CDN or inline assets, or when the user asks to "make CSP cacheable", "encrypt an API key", "sign a URL" or "review this extension for security".
argument-hint: "[extension path] [review|csp|sri|encrypt|hmac]"
allowed-tools: Read, Grep, Glob, Edit, Write
paths:
  - "**/config/sites/*/csp.yaml"
  - "**/Configuration/ContentSecurityPolicies.php"
  - "**/Classes/**/*Crypto*.php"
  - "**/Classes/**/*Token*.php"
---

# Security hardening (TYPO3 v14)

Arguments: `$ARGUMENTS`. With `review` (default when a path is given), run the
[extension security review](references/review-checklist.md) and report findings; change nothing unless asked.

TYPO3 14 ships primitives that replace most hand-rolled security code: authenticated encryption
(Feature-108002, 14.0), HMACs with a mandatory extra secret (Feature-102761, 13.0), hash-based CSP that keeps pages
cacheable (Feature-100887, 14.2), automatic SRI hashes (Feature-109187, 14.2) and a central rate limiter
(Feature-109080, 14.2).

## When to use

- Security review of an extension before release or before installing it in a project.
- Storing credentials, API tokens or personal data that must be readable later: `CipherService`.
- Signing URLs, form fields or one-time links: `HashService` with an additional secret.
- Adding inline scripts/styles or CDN assets to a site that sends a CSP, or wanting a static/proxy-cached site
  with CSP: hashes instead of nonces.
- CSP report noise from browser extensions: `BeforePersistingReportEvent` (Feature-102079, 14.2).
- Form registration storing passwords via `SaveToDatabase`: `hashed: true` (Feature-106752, 14.0).
- Brute-force exposed endpoints: `#[RateLimit]` / `RateLimiterFactoryInterface` - see the `extbase-modern`
  skill.

## Steps

1. Review: walk through [references/review-checklist.md](references/review-checklist.md). Report each finding as
   `file:line - risk - fix`, ordered by severity.
2. Secrets: use [templates/TokenVault.php](templates/TokenVault.php). Derive one key per purpose with
   `KeyFactory::deriveSharedKeyFromEncryptionKey('<seed>')`; store `(string)$cipherValue`. Never change
   `encryptionKey` afterwards, or data cannot be decrypted.
3. Signed values: `HashService::hmac($data, '<purpose-secret>', HashAlgo::SHA3_256)` and
   `validateHmac()`; never compare HMACs with `===`.
4. CSP: copy [templates/csp.yaml](templates/csp.yaml) to `config/sites/<site>/csp.yaml`. Switch Fluid
   `useNonce` to `csp`, inline `style=""` to `f:asset.styleAttr`. Details in
   [references/csp-sri.md](references/csp-sri.md).
5. SRI: `integrity = auto` for own assets; precomputed hashes for third-party CDN files.
6. Controllers: `#[Authorize]` + `#[RateLimit]` on state-changing Extbase actions (14.2).
7. Forms: `hashed: true` for password fields mapped to a database column.

## Checklist

- [ ] No custom crypto (`openssl_encrypt`, `base64` "encryption", `md5()`/`sha1()` signatures).
- [ ] No `GeneralUtility::hmac()` / Extbase `HashService`; `TYPO3\CMS\Core\Crypto\HashService` with a
      purpose-specific additional secret.
- [ ] HMACs for new features use `HashAlgo::SHA3_256` (default is still SHA1, Breaking-106307).
- [ ] Encrypted values are stored as `(string)CipherValue`; decryption failures (`CipherDecryptionFailedException`)
      are handled.
- [ ] `csp.yaml` exists per site; inline assets have `csp="1"` or are moved to files.
- [ ] No `useNonce` in `f:asset.*` or `AssetCollector` options (deprecated in 14.2).
- [ ] External assets have `integrity` (precomputed for third-party hosts).
- [ ] Public write endpoints are rate limited and, if user-bound, authorized.
- [ ] `SaveToDatabase` password fields have `hashed: true`.
- [ ] Uploads use `#[FileUpload]` with MIME/extension/size validation.

## Pitfalls

- `useHash: true` without `useNonce: false` still emits nonces - responses stay uncacheable for proxies.
- `integrity = auto` on a remote URL trusts whatever the remote server delivered on the first fetch (cached 7
  days in `cache.assets`).
- Inline `<f:asset.script>` defaults to `csp="0"` (no hash). Opt in explicitly.
- `CipherService` output differs on every call (random nonce). Do not use it for lookups; store a separate HMAC
  if you must search by value.
- `HashService` default algorithm is SHA1 for BC; pass the algorithm explicitly.

## References

- [references/review-checklist.md](references/review-checklist.md) - full extension security review.
- [references/csp-sri.md](references/csp-sri.md) - CSP hashes, report event, SRI.
- [references/crypto.md](references/crypto.md) - `CipherService`, `KeyFactory`, `HashService`, form password
  hashing.
