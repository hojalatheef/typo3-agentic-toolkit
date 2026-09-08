<?php

declare(strict_types=1);

namespace MyVendor\MyExtension\Service;

use TYPO3\CMS\Core\Localization\TranslatorInterface;

/**
 * TYPO3 ^14.2: TranslatorInterface (Feature-89334) + ICU plurals (Feature-104546).
 * LanguageService implements TranslatorInterface.
 */
final readonly class LabelService
{
    private const DOMAIN = 'my_extension.messages';

    public function __construct(
        private TranslatorInterface $translator,
    ) {}

    public function entryCount(int $count): string
    {
        // Associative arguments + ICU pattern => MessageFormatter
        return (string)($this->translator->translate('list.count', self::DOMAIN, ['count' => $count]) ?? '');
    }

    public function downloads(int $times): string
    {
        // Indexed arguments => sprintf ("Downloaded %d times")
        return (string)($this->translator->translate('downloaded_times', self::DOMAIN, [$times]) ?? '');
    }

    public function fromReference(string $reference, string $fallback): string
    {
        // Accepts "my_extension.messages:key", "EXT:...xlf:key" and "LLL:EXT:...xlf:key"
        return (string)($this->translator->label($reference, [], $fallback) ?? $fallback);
    }
}
