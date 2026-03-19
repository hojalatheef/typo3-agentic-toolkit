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
