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

`f:asset.script` / `f:asset.css`: `useNonce` renamed to `csp` (Deprecation-100887, removal v15). Default `csp`
is `true` for files and `false` for inline content.

```html
<f:asset.script identifier="my-script" src="EXT:my_ext/Resources/Public/JavaScript/foo.js" />
<f:asset.script identifier="my-inline" csp="1">
    document.querySelector('.foo').classList.add('active');
</f:asset.script>
```

PHP: `$assetCollector->addJavaScript('my-script', $src, [], ['csp' => true]);` (was `'useNonce'`).
`PageRenderer::addJsInlineCode()`, `addJsFooterInlineCode()`, `addCssInlineBlock()` keep `$useNonce`.

Internal API change: `Policy::prepare()` / `compile()` take a `PolicyBag`; listeners of `PolicyPreparedEvent`
reach the hashes through `$event->policyBag->directiveHashCollection`.

## CSP violation reports (Feature-102079, 14.2)

`TYPO3\CMS\Core\Security\ContentSecurityPolicy\Event\BeforePersistingReportEvent`: `$event->originalReport`
(read), `$event->report` (write; `null` drops the report). Typical use: drop reports caused by browser
extensions (`blocked-uri` starting with `chrome-extension://`, `moz-extension://`, `safari-extension://`) or add
