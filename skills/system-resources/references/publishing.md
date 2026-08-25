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
| `mirror` | Copy files and folders - container images, read-only file systems, restrictive hosting. |
| `auto` | Mirror in `Production` context, link in `Development`. |

## Resource definitions (Feature-109409, 14.2)

Default per package: `ResourceDefinition('Resources/Private')` and, if present,
`PublicResourceDefinition('Resources/Public')`. `Configuration/Resources.php` adds to that:

```php
<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Package\Package;
use TYPO3\CMS\Core\Package\Resource\Definition\PublicFileDefinition;
use TYPO3\CMS\Core\Package\Resource\Definition\PublicResourceDefinition;

return static function (Package $package) {
    return [
        // whole folder, stable URL below public/_assets/my-vendor/my-extension-build
        new PublicResourceDefinition(
            relativePath: 'Build/Public',
            publicPrefix: 'my-vendor/my-extension-build',
        ),
        // single file, hashed folder
        new PublicFileDefinition(relativePath: 'Build/styles.css'),
    ];
};
```

Rules:

- Plain PHP only - TYPO3 is not bootstrapped; only autoloading and the passed package object are available.
- Relative paths: no leading/trailing slash, no `../`.
- `publicPrefix` must be unique across all packages (exception otherwise).
- After changes: `composer dumpautoload` (Composer mode) or `typo3 cache:flush` (classic mode).

The `typo3/app` package is configured in `config/system/resources.php`
