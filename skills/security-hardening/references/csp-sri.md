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
metadata by building a new `Report`. See [../templates/CspReportFilter.php](../templates/CspReportFilter.php).

## Subresource Integrity (Feature-109187, 14.2)

Two changelog entries share this number:

1. `integrity` and `crossorigin` for `page.includeCSS` / `includeCSSLibs` (parity with the JS includes).
   `crossorigin="anonymous"` is added automatically when `integrity` is set on a URI resource.
   `PageRenderer::addCssFile()` / `addCssLibrary()` gained `$integrity` and `$crossorigin`.
2. `integrity = auto` on `includeCSS`, `includeCSSLibs`, `includeJS`, `includeJSLibs`, `includeJSFooter`,
   `includeJSFooterlibs`: SHA-256 computed by TYPO3, cached 7 days in `cache.assets`.
   PHP constant: `TYPO3\CMS\Core\Page\ResourceHashCollection::AUTO`.

```typoscript
page.includeCSS {
    main = EXT:my_extension/Resources/Public/Css/main.css
    main.integrity = auto
}
page.includeJSFooterlibs {
    vendor = https://cdn.example.com/vendor.js
    # precomputed from a trusted source, not "auto"
    vendor.integrity = sha384-...
}
```

```php
$assetCollector->addStyleSheet(
    'my-styles',
    'EXT:my_extension/Resources/Public/Css/style.css',
    [],
    ['integrity' => ResourceHashCollection::AUTO],
);
```

`auto` for a remote URL fetches the file once and trusts that response. Inline CSS (`inline = 1`) gets no
