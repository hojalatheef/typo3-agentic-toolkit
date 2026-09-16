<?php

declare(strict_types=1);

namespace Acme\Contact\Domain\Model;

use TYPO3\CMS\Extbase\Attribute\Validate;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Message extends AbstractEntity
{
    #[Validate(['validator' => 'NotEmpty'])]
    protected string $name = '';

    #[Validate(['validator' => 'EmailAddress'])]
    protected string $email = '';

    #[Validate(['validator' => 'NotEmpty'])]
    protected string $text = '';

    public function getName(): string { return $this->name; }
    public function setName(string $name): void { $this->name = $name; }
    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): void { $this->email = $email; }
    public function getText(): string { return $this->text; }
    public function setText(string $text): void { $this->text = $text; }
}
