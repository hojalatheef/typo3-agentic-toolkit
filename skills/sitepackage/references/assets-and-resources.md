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

```typoscript
page.includeCSS.main = PKG:my-vendor/my-sitepackage:Resources/Public/Css/main.css
```

PHP: inject `TYPO3\CMS\Core\SystemResource\SystemResourceFactory` and
`TYPO3\CMS\Core\SystemResource\Publishing\SystemResourcePublisherInterface`,
call `createPublicResource($identifier)` and `generateUri($resource, $request,
new UriGenerationOptions(absoluteUri: true))`.

The `PKG:` syntax needs a `composer.json` in the package (also in classic
mode). For a site package that must run on v13.4, keep `EXT:`.

## Build tools (Vite, webpack)

Feature-107537 does not add a bundler integration; it only lists
`_assets/vite/foo.css` as a deprecated *legacy* path form. So:

- Core has no Vite manifest reader. If you want Vite, use a community
  extension and check that it supports v14 - this is outside core and not
  covered by this skill.
- Without an integration, build into `Resources/Public/` of the site package
  (fixed file names, no hash) and reference the result with `PKG:` or `EXT:`.
  Core cache busting handles invalidation.
- Commit either the build output or a CI build step - never rely on
  developers building locally before deploy.
