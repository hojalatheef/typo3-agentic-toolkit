# Content-Security-Policy hashes and Subresource Integrity

## Hashes instead of nonces (Feature-100887, 14.2)

Nonces change on every request and make responses uncacheable. With hashes, TYPO3 collects SHA-256 values of
