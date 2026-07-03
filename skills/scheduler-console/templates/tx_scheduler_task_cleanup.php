<?php

// EXT:my_extension/Configuration/TCA/Overrides/tx_scheduler_task_cleanup.php
// TYPO3 ^14.0 (Feature-107526, Feature-107488), priority item ^14.2 (Feature-109110).

defined('TYPO3') or die();

use MyVendor\MyExtension\Task\CleanupTask;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

if (!isset($GLOBALS['TCA']['tx_scheduler_task'])) {
    return;
}

// Own column - the database analyzer creates it from TCA.
ExtensionManagementUtility::addTCAcolumns(
    'tx_scheduler_task',
    [
        'tx_myextension_table' => [
            'label' => 'my_extension.tasks:cleanup.table',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'my_extension.tasks:cleanup.table.log', 'value' => 'tx_myextension_log'],
                    ['label' => 'my_extension.tasks:cleanup.table.queue', 'value' => 'tx_myextension_queue'],
                ],
                'required' => true,
            ],
        ],
    ]
);

ExtensionManagementUtility::addRecordType(
    [
        'label' => 'my_extension.tasks:cleanup.title',
        'description' => 'my_extension.tasks:cleanup.description',
        'value' => CleanupTask::class,
        'icon' => 'mimetypes-x-tx_scheduler_task_group',
        'iconOverlay' => 'content-clock',
        'group' => 'my_extension',
    ],
    '
        --div--;core.form.tabs:general,
            tasktype,
            task_group,
            description,
            tx_myextension_table,
            number_of_days;my_extension.tasks:cleanup.days,
        --div--;core.form.tabs:timing,
            --palette--;;execution,
        --div--;core.form.tabs:access,
            disable,
        --div--;core.form.tabs:extended,',
    [
        'columnsOverrides' => [
            'number_of_days' => [
                'config' => [
                    'eval' => 'required',
                ],
            ],
        ],
    ],
    '',
    'tx_scheduler_task'
);

// Frequency preset (replaces SC_OPTIONS.scheduler.frequencyOptions, Breaking-107488).
$GLOBALS['TCA']['tx_scheduler_task']['columns']['execution_details']['config']
    ['overrideFieldTca']['frequency']['config']['valuePicker']['items'][] = [
        'value' => '30 3 * * *',
        'label' => 'my_extension.tasks:frequency.nightly',
    ];

// Extra priority level above "High" (150). Requires TYPO3 ^14.2.
ExtensionManagementUtility::addTcaSelectItem(
    'tx_scheduler_task',
    'priority',
    [
        'label' => 'LLL:my_extension.tasks:priority.critical',
        'value' => 200,
    ],
    150,
    'after',
);
