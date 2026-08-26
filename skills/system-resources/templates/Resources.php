<?php

// EXT:my_extension/Configuration/Resources.php
// TYPO3 ^14.2 (Feature-109409). Plain PHP only: TYPO3 is not bootstrapped
// when this file is evaluated. Adds to the defaults (Resources/Private,
// Resources/Public). After changes: composer dumpautoload (classic mode:
// typo3 cache:flush).

declare(strict_types=1);

use TYPO3\CMS\Core\Package\Package;
use TYPO3\CMS\Core\Package\Resource\Definition\PublicFileDefinition;
use TYPO3\CMS\Core\Package\Resource\Definition\PublicResourceDefinition;

return static function (Package $package) {
    return [
        // Vite/webpack output, published to public/_assets/my-vendor/my-extension-build/
        // Reference as PKG:my-vendor/my-extension:Build/Public/app.js
        new PublicResourceDefinition(
            relativePath: 'Build/Public',
            publicPrefix: 'my-vendor/my-extension-build',
        ),
        // A single file, published to public/_assets/my_extension/brand/logo.svg
        new PublicFileDefinition(
            relativePath: 'Build/brand/logo.svg',
            publicPrefix: $package->getPackageKey() . '/brand/logo.svg',
        ),
    ];
};
