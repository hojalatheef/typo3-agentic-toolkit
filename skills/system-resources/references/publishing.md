# Publishing public resources

## asset:publish (Feature-109163, 14.2)

`vendor/bin/typo3 asset:publish` copies or links each package's public resources into the document root
(`public/_assets/...` in Composer mode). It runs automatically:

- after `composer install` (skip with `TYPO3_SKIP_ASSET_PUBLISH=1 composer install`),
- on `extension:setup`,
- when an extension is activated in the Extension Manager (also classic mode).

The changelog expects "not publishing at composer install" to become the default later. Put `asset:publish` (or
`extension:setup`) into the deployment explicitly now.

## Publishing strategies

```php
// config/system/additional.php
$GLOBALS['TYPO3_CONF_VARS']['SYS']['SystemResources']['filesystemPublishingType'] = 'mirror';
```

| Value | Behaviour |
| --- | --- |
| `link` (default) | Symlinks (*nix) or junctions (Windows). |
