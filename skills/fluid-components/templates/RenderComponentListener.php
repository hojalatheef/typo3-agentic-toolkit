<?php

declare(strict_types=1);

// EXT:my_extension/Classes/EventListener/RenderComponentListener.php
// Adds the component bundle CSS whenever a component of the collection
// renders (Feature-108508, PSR-14 events for Fluid components, 14.1).

namespace MyVendor\MyExtension\EventListener;

use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Page\AssetCollector;
use TYPO3\CMS\Fluid\Event\RenderComponentEvent;

#[AsEventListener]
final readonly class RenderComponentListener
{
    public function __construct(private AssetCollector $assetCollector) {}

    public function __invoke(RenderComponentEvent $event): void
    {
        if ($event->getComponentCollection()->getNamespace() === 'MyVendor\\MyExtension\\Components') {
            $this->assetCollector->addStyleSheet(
                'componentsBundle',
                'EXT:my_extension/Resources/Public/ComponentsBundle.css'
            );
        }
    }
}
