<?php

declare(strict_types=1);

namespace MyVendor\MyExtension\Authorization;

use MyVendor\MyExtension\Domain\Model\Profile;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use TYPO3\CMS\Core\Context\Context;

/**
 * Callback target for #[Authorize(callback: [ProfileAuthorization::class, 'isOwner'])].
 * Must be public in the DI container (Feature-107826).
 */
#[Autoconfigure(public: true)]
final class ProfileAuthorization
{
    public function __construct(
        private readonly Context $context,
    ) {}

    public function isOwner(Profile $profile): bool
    {
        $userId = (int)$this->context->getAspect('frontend.user')->get('id');
        return $userId > 0 && $profile->getOwner() === $userId;
    }
}
