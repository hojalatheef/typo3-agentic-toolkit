# Content-Security-Policy hashes and Subresource Integrity

## Hashes instead of nonces (Feature-100887, 14.2)

Nonces change on every request and make responses uncacheable. With hashes, TYPO3 collects SHA-256 values of
assets during rendering (`TYPO3\CMS\Core\Security\ContentSecurityPolicy\DirectiveHashCollection`); they survive
the page cache via `MetaDataState`.
