# Content-Security-Policy hashes and Subresource Integrity

## Hashes instead of nonces (Feature-100887, 14.2)

Nonces change on every request and make responses uncacheable. With hashes, TYPO3 collects SHA-256 values of
assets during rendering (`TYPO3\CMS\Core\Security\ContentSecurityPolicy\DirectiveHashCollection`); they survive
the page cache via `MetaDataState`.

Opt in per site in `config/sites/<site>/csp.yaml`:

```yaml
behavior:
  useNonce: false
  useHash: true

enforce:
  inheritDefault: true
  includeResolutions: true
```

| `Behavior` property | Values |
| --- | --- |
| `useHash` | `true` collect and apply hashes; `null` (default) off; `false` explicitly off |
| `useNonce` | `false` removes nonce sources - required for proxy/static-file caching |

What gets hashed:

- Inline `<script>` / `<style>` content.
- Static files: the existing `integrity` value is reused, otherwise the file is hashed.
- Inline style attributes through the new ViewHelper (directive `style-src-attr`):

```html
<div style="{f:asset.styleAttr(value: 'color: green', csp: true)}"></div>
```
