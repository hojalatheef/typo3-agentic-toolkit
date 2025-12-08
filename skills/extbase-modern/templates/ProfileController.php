<?php

declare(strict_types=1);

namespace MyVendor\MyExtension\Controller;

use MyVendor\MyExtension\Authorization\ProfileAuthorization;
use MyVendor\MyExtension\Domain\Model\Profile;
use MyVendor\MyExtension\Domain\Repository\ProfileRepository;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Attribute\Authorize;
use TYPO3\CMS\Extbase\Attribute\IgnoreValidation;
use TYPO3\CMS\Extbase\Attribute\RateLimit;
use TYPO3\CMS\Extbase\Attribute\Validate;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * Requires TYPO3 ^14.2 (#[Authorize], #[RateLimit], orderBy()).
 *
 * Register list/show as cacheable and new/create/edit/update/delete as
 * non-cacheable actions: #[RateLimit] and custom #[Authorize] responses
 * only work for uncached actions.
 *
 * Recommended TypoScript (Feature-104321):
 *   plugin.tx_myextension.mvc.showPageNotFoundIfTargetNotFoundException = 1
 */
final class ProfileController extends ActionController
{
    public function __construct(
        private readonly ProfileRepository $profileRepository,
    ) {}

    public function listAction(): ResponseInterface
    {
        $query = $this->profileRepository->createQuery();
        $query
            ->orderBy($query->coalesce('nickname', 'firstName'), QueryInterface::ORDER_ASCENDING)
            ->addOrderBy('lastName', QueryInterface::ORDER_ASCENDING);

        $this->view->assign('profiles', $query->execute());
        return $this->htmlResponse();
    }

    public function showAction(Profile $profile): ResponseInterface
    {
        $this->view->assign('profile', $profile);
        return $this->htmlResponse();
    }

    #[Authorize(requireLogin: true)]
    public function newAction(
        #[IgnoreValidation]
        ?Profile $profile = null,
    ): ResponseInterface {
        $this->view->assign('profile', $profile ?? new Profile());
        return $this->htmlResponse();
    }

    #[Authorize(requireLogin: true)]
    #[RateLimit(limit: 3, interval: '10 minutes', message: 'messages.rate_limit_exceeded')]
    public function createAction(Profile $profile): ResponseInterface
    {
        $this->profileRepository->add($profile);
        return $this->redirect('list');
    }

    #[Authorize(requireLogin: true)]
    #[Authorize(callback: [ProfileAuthorization::class, 'isOwner'])]
    public function editAction(
        #[IgnoreValidation]
        Profile $profile,
    ): ResponseInterface {
        $this->view->assign('profile', $profile);
        return $this->htmlResponse();
    }

    #[Authorize(requireLogin: true)]
    #[Authorize(callback: [ProfileAuthorization::class, 'isOwner'])]
    #[RateLimit(limit: 10, interval: '1 hour')]
    public function updateAction(Profile $profile): ResponseInterface
    {
        $this->profileRepository->update($profile);
        return $this->redirect('show', null, null, ['profile' => $profile]);
    }

    #[Authorize(requireLogin: true, requireGroups: [1])]
    #[Authorize(callback: [ProfileAuthorization::class, 'isOwner'])]
    public function deleteAction(Profile $profile): ResponseInterface
    {
        $this->profileRepository->remove($profile);
        return $this->redirect('list');
    }

    public function searchAction(
        #[Validate(validator: 'StringLength', options: ['maximum' => 100])]
        string $term = '',
    ): ResponseInterface {
        $this->view->assign('term', $term);
        return $this->htmlResponse();
    }

    /**
     * Per-request tuning of the #[FileUpload] configuration of Profile::$avatar.
     */
    public function initializeCreateAction(): void
    {
        $this->arguments->getArgument('profile')
            ->getFileHandlingServiceConfiguration()
            ->getFileUploadConfigurationForProperty('avatar')
            ?->setUploadFolder($this->settings['uploadFolder'] ?? '1:/user_upload/profiles/');
    }
}
