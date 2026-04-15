<?php

// EXT:my_extension/Configuration/Backend/Modules.php
// TYPO3 ^14.0 (module tree Feature-107628, card overview Feature-107712,
// dependsOnSubmodules Feature-107663). Custom 'access' gate requires ^14.2 (Feature-109365).

use MyVendor\MyExtension\Controller\Backend\ReportController;

return [
    // Container: shows cards for its submodules, hidden when none is accessible.
    'my_extension_tools' => [
        'parent' => 'content',
        'position' => ['after' => 'records'],
        'access' => 'user',
        'path' => '/module/content/my-tools',
        'iconIdentifier' => 'my-extension-module',
        // Resources/Private/Language/Modules/tools.xlf (title, short_description, description)
        'labels' => 'my_extension.modules.tools',
        'navigationComponent' => '@typo3/backend/tree/page-tree-element',
        'showSubmoduleOverview' => true,
        'appearance' => [
            'dependsOnSubmodules' => true,
        ],
    ],

    'my_extension_tools_report' => [
        'parent' => 'my_extension_tools',
        'access' => 'user',
        'path' => '/module/content/my-tools/report',
        'iconIdentifier' => 'my-extension-module-report',
        'labels' => 'my_extension.modules.report',
        'routes' => [
            '_default' => [
                'target' => ReportController::class . '::indexAction',
            ],
            'detail' => [
                'path' => '/detail',
                'target' => ReportController::class . '::detailAction',
            ],
        ],
    ],

    // Restricted by a custom gate (templates/EditorGate.php, identifier 'myEditor').
    'my_extension_tools_review' => [
        'parent' => 'my_extension_tools',
        'access' => 'myEditor',
        'path' => '/module/content/my-tools/review',
        'iconIdentifier' => 'my-extension-module-review',
        'labels' => 'my_extension.modules.review',
        'routes' => [
            '_default' => [
                'target' => ReportController::class . '::reviewAction',
            ],
        ],
    ],

    // Administrators' module (formerly parent 'system').
    'my_extension_admin' => [
        'parent' => 'admin',
        'access' => 'admin',
        'path' => '/module/admin/my-extension',
        'iconIdentifier' => 'my-extension-module-admin',
        'labels' => 'my_extension.modules.admin',
        'routes' => [
            '_default' => [
                'target' => ReportController::class . '::adminAction',
            ],
        ],
    ],
];
