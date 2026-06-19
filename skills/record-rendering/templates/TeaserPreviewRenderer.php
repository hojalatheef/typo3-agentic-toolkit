<?php

declare(strict_types=1);

// EXT:my_sitepackage/Classes/Preview/TeaserPreviewRenderer.php
// Uses RecordFieldPreviewProcessor (Feature-108819, v14.2) instead of
// extending StandardContentPreviewRenderer.
// Register in TCA: $GLOBALS['TCA']['tt_content']['types']['my_teaser']['previewRenderer'] = TeaserPreviewRenderer::class;

namespace MyVendor\MySitepackage\Preview;

use TYPO3\CMS\Backend\Preview\PreviewRendererInterface;
use TYPO3\CMS\Backend\Preview\RecordFieldPreviewProcessor;
use TYPO3\CMS\Backend\View\BackendLayout\Grid\GridColumnItem;

final class TeaserPreviewRenderer implements PreviewRendererInterface
{
    public function __construct(
        private readonly RecordFieldPreviewProcessor $fieldProcessor,
    ) {}

    public function renderPageModulePreviewHeader(GridColumnItem $item): string
    {
        return (string)$this->fieldProcessor->prepareFieldWithLabel($item->getRecord(), 'header');
    }

    public function renderPageModulePreviewContent(GridColumnItem $item): string
    {
        $record = $item->getRecord();
        $content = (string)$this->fieldProcessor->prepareText($record, 'bodytext');
        $content .= (string)$this->fieldProcessor->prepareFiles($record->get('image'));
        return $content;
    }

    public function renderPageModulePreviewFooter(GridColumnItem $item): string
    {
        return '';
    }

    public function wrapPageModulePreview(string $previewHeader, string $previewContent, GridColumnItem $item): string
    {
        return $previewHeader . $previewContent;
    }
}
