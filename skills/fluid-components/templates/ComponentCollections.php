<?php

// EXT:my_extension/Configuration/Fluid/ComponentCollections.php
// TYPO3 v14.1+ (Feature-108508). Replace MyVendor\MyExtension.

return [
    'MyVendor\\MyExtension\\Components' => [
        'templatePaths' => [
            10 => 'EXT:my_extension/Resources/Private/Components',
        ],
        // Optional: one file per component instead of one folder per component
        // 'templateNamePattern' => '{path}/{name}',
        // Optional: allow arguments without <f:argument> declaration
        // 'additionalArgumentsAllowed' => true,
    ],
];
