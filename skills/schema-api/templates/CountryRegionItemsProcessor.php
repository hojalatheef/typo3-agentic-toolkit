<?php

declare(strict_types=1);

// EXT:my_extension/Classes/Tca/CountryRegionItemsProcessor.php
// itemsProcessors replace itemsProcFunc (Feature-107889, 14.0).
// TCA: 'itemsProcessors' => [100 => ['class' => CountryRegionItemsProcessor::class, 'parameters' => ['prefix' => 'Region']]]

namespace MyVendor\MyExtension\Tca;

use TYPO3\CMS\Core\DataHandling\ItemsProcessorContext;
use TYPO3\CMS\Core\DataHandling\ItemsProcessorInterface;
use TYPO3\CMS\Core\Schema\Struct\SelectItem;
use TYPO3\CMS\Core\Schema\Struct\SelectItemCollection;

final class CountryRegionItemsProcessor implements ItemsProcessorInterface
{
    public function processItems(
        SelectItemCollection $items,
        ItemsProcessorContext $context,
    ): SelectItemCollection {
        $prefix = (string)($context->processorParameters['prefix'] ?? 'Item');
        $items->add(
            new SelectItem(
                type: 'select',
                label: $prefix . ' North',
                value: 'north',
            )
        );
        return $items;
    }
}
