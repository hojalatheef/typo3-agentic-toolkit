<?php

declare(strict_types=1);

namespace MyVendor\MyExtension\Service;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\SystemResource\Publishing\SystemResourcePublisherInterface;
use TYPO3\CMS\Core\SystemResource\Publishing\UriGenerationOptions;
use TYPO3\CMS\Core\SystemResource\SystemResourceFactory;

/**
 * Public URLs for system resources (Feature-107537, TYPO3 ^14.0).
 * Replaces PathUtility::getPublicResourceWebPath(),
 * GeneralUtility::createVersionNumberedFilename() and FilePathSanitizer.
 *
 * Accepted identifiers:
 *   PKG:my-vendor/my-extension:Resources/Public/Images/logo.svg
 *   EXT:my_extension/Resources/Public/Images/logo.svg
 *   PKG:typo3/app:public/typo3temp/assets/generated.css
 *   FAL:1:/brand/logo.svg
 *   URI:/static/logo.svg  or  https://cdn.example.com/logo.svg
 */
final readonly class AssetUriService
{
    public function __construct(
        private SystemResourceFactory $systemResourceFactory,
        private SystemResourcePublisherInterface $resourcePublisher,
    ) {}

    /**
     * Site-relative URL with cache busting, e.g. for HTML output.
     */
    public function url(string $identifier, ?ServerRequestInterface $request): string
    {
        return (string)$this->resourcePublisher->generateUri(
            $this->systemResourceFactory->createPublicResource($identifier),
            $request,
        );
    }

    /**
     * Absolute URL, e.g. for e-mails, feeds or Open Graph tags.
     * Requires a request; on CLI construct one for the target site.
     */
    public function absoluteUrl(string $identifier, ServerRequestInterface $request): string
    {
        return (string)$this->resourcePublisher->generateUri(
            $this->systemResourceFactory->createPublicResource($identifier),
            $request,
            new UriGenerationOptions(absoluteUri: true),
        );
    }
}
