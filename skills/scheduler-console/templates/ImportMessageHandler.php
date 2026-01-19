<?php

declare(strict_types=1);

namespace MyVendor\MyExtension\Message;

use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Registered by attribute (Feature-101700, TYPO3 ^13.0) - no Services.yaml tag needed.
 * Consume async messages with:
 *   vendor/bin/typo3 messenger:consume <receiver> --time-limit=240 --memory-limit=128M
 */
#[AsMessageHandler]
final readonly class ImportMessageHandler
{
    public function __construct(
        private LoggerInterface $logger,
    ) {}

    public function __invoke(ImportMessage $message): void
    {
        $this->logger->info('Import started', ['source' => $message->source]);
        // ... fetch, map, persist
    }
}
