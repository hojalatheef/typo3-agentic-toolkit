<?php

declare(strict_types=1);

use Acme\Contact\Controller\ContactController;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3') or die();

ExtensionUtility::configurePlugin(
    'Contact',
    'Form',
    [ContactController::class => 'show, submit'],
    [ContactController::class => 'submit'],
);
