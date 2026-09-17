<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') or die();

// Static TypoScript template, selected per site in the sys_template record.
ExtensionManagementUtility::addStaticFile('acme_contact', 'Configuration/TypoScript', 'Acme Contact');
