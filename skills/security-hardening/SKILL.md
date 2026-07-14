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
