<?php

declare(strict_types=1);

namespace MyVendor\MyExtension\Domain\Model;

use Symfony\Component\Validator\Constraints as Assert;
use TYPO3\CMS\Extbase\Attribute\FileUpload;
use TYPO3\CMS\Extbase\Attribute\Validate;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * TYPO3 ^14.0: attributes from TYPO3\CMS\Extbase\Attribute, configured with
 * named properties (Feature-97559), Symfony constraints (Feature-106945).
 *
 * The table holds personal data: if the feature toggle
 * extbase.enableHistoryTracking is on, consider
 * 'ctrl' => ['extbase' => ['enableHistoryTracking' => false]] in its TCA.
 */
class Profile extends AbstractEntity
{
    #[Validate(validator: 'NotEmpty')]
    protected string $firstName = '';

    #[Validate(validator: 'NotEmpty')]
    protected string $lastName = '';

    protected string $nickname = '';

    #[Assert\WordCount(max: 200, maxMessage: 'LLL:EXT:my_extension/Resources/Private/Language/locallang.xlf:validator.biography.tooLong')]
    protected string $biography = '';

    #[Assert\CssColor]
    protected string $favouriteColor = '';

    /** Frontend user UID of the owner, set in createAction or a DataHandler hook. */
    protected int $owner = 0;

    #[FileUpload(
        validation: [
            'maxFiles' => 1,
            'fileSize' => ['minimum' => '1K', 'maximum' => '2M'],
            'mimeType' => ['allowedMimeTypes' => ['image/jpeg', 'image/png', 'image/webp']],
            'fileExtension' => ['allowedFileExtensions' => ['jpg', 'jpeg', 'png', 'webp']],
            'imageDimensions' => ['maxWidth' => 4096, 'maxHeight' => 4096],
        ],
        uploadFolder: '1:/user_upload/profiles/',
    )]
    protected ?FileReference $avatar = null;

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getNickname(): string
    {
        return $this->nickname;
    }

    public function setNickname(string $nickname): void
    {
        $this->nickname = $nickname;
    }

    public function getBiography(): string
    {
        return $this->biography;
    }

    public function setBiography(string $biography): void
    {
        $this->biography = $biography;
    }

    public function getFavouriteColor(): string
    {
        return $this->favouriteColor;
    }

    public function setFavouriteColor(string $favouriteColor): void
    {
        $this->favouriteColor = $favouriteColor;
    }

    public function getOwner(): int
    {
        return $this->owner;
    }

    public function setOwner(int $owner): void
    {
        $this->owner = $owner;
    }

    public function getAvatar(): ?FileReference
    {
        return $this->avatar;
    }

    public function setAvatar(?FileReference $avatar): void
    {
        $this->avatar = $avatar;
    }
}
