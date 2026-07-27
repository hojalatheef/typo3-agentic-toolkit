<?php

declare(strict_types=1);

namespace MyVendor\MyExtension\EventListener;

use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Security\ContentSecurityPolicy\Event\BeforePersistingReportEvent;

/**
 * Drops CSP violation reports caused by browser extensions (Feature-102079, TYPO3 ^14.2).
 */
final class CspReportFilter
{
    private const BROWSER_PREFIXES = [
        'chrome-extension://',
        'moz-extension://',
        'safari-extension://',
    ];

    #[AsEventListener('my-extension/security/csp-report-filter')]
    public function __invoke(BeforePersistingReportEvent $event): void
    {
        $blockedUri = $event->originalReport->details['blocked-uri'] ?? null;
        if (!is_string($blockedUri)) {
            return;
        }
        foreach (self::BROWSER_PREFIXES as $prefix) {
            if (str_starts_with($blockedUri, $prefix)) {
                $event->report = null;
                return;
            }
        }
    }
}
