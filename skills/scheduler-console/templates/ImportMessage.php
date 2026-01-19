<?php

declare(strict_types=1);

namespace MyVendor\MyExtension\Message;

/**
 * Message DTO dispatched by ImportCommand. Keep it small and serializable.
 */
final readonly class ImportMessage
{
    public function __construct(
        public string $source,
    ) {}
}
