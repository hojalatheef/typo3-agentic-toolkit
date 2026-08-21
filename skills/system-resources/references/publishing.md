# Publishing public resources

## asset:publish (Feature-109163, 14.2)

`vendor/bin/typo3 asset:publish` copies or links each package's public resources into the document root
(`public/_assets/...` in Composer mode). It runs automatically:

- after `composer install` (skip with `TYPO3_SKIP_ASSET_PUBLISH=1 composer install`),
