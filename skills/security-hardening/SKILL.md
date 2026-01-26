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
