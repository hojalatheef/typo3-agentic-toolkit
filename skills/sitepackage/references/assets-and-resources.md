# Assets and resources in a v14 site package

Source for identifiers and URL generation: Feature-107537 (14.0). Build tools
are not part of TYPO3 core; this page says what core provides and where it
stops.

## Resource identifiers

| Identifier | Example | Notes |
| --- | --- | --- |
| Package resource | `PKG:my-vendor/my-sitepackage:Resources/Public/Css/main.css` | Composer name; recommended for new projects |
| Extension resource | `EXT:my_sitepackage/Resources/Public/Css/main.css` | still supported, not deprecated |
| App resource | `PKG:typo3/app:public/_assets/style.css` | only `public/_assets`, `public/typo3temp/assets`, `public/uploads` unless `FE.addAllowedPaths` allows more |
| FAL resource | `FAL:1:/templates/css/main.css` | storage uid + identifier |
| URI | `https://cdn.example.com/x.css` or `URI:/path/x.css` | `URI:` value must be a valid URI |

Deprecated forms: `fileadmin/...`, `typo3temp/assets/...`, `_assets/...`
relative paths. `typo3conf/ext/...` paths no longer work consistently -
replace them with `EXT:` or `PKG:`.

All core places that generate URLs from these identifiers apply cache busting
automatically.

## Using them

Fluid:

```html
<f:asset.css identifier="main" href="PKG:my-vendor/my-sitepackage:Resources/Public/Css/main.css" />
<f:asset.script identifier="main" src="PKG:my-vendor/my-sitepackage:Resources/Public/JavaScript/main.js" />
<link href="{f:resource(identifier: 'PKG:my-vendor/my-sitepackage:Resources/Public/Css/print.css') -> f:uri.resource()}" rel="stylesheet" media="print" />
```

TypoScript:
