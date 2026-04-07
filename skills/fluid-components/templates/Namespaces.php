<?php

// EXT:my_extension/Configuration/Fluid/Namespaces.php
// TYPO3 v14.1+ (Feature-108524). Makes "my:" available in all templates
// without an xmlns declaration. Check with: vendor/bin/typo3 fluid:namespaces

return [
    'my' => ['MyVendor\\MyExtension\\Components'],
];
