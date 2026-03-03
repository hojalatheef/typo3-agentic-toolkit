<?php

declare(strict_types=1);

namespace MyVendor\MyExtension\EventListener;

use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Form\Event\AfterFormIsBuiltEvent;
use TYPO3\CMS\Form\Event\BeforeFormIsDeletedEvent;
use TYPO3\CMS\Form\Event\BeforeFormIsSavedEvent;
use TYPO3\CMS\Form\Event\BeforeRenderableIsRenderedEvent;
use TYPO3\CMS\Form\Event\BeforeRenderableIsValidatedEvent;

/**
 * PSR-14 form events (TYPO3 ^14.0) replacing the removed ext/form hooks.
 * Keep only the methods you need.
 */
final class FormEventListener
{
    /** Replaces hook beforeFormSave (Feature-107388). */
    #[AsEventListener('my-extension/form/before-save')]
    public function normaliseLabel(BeforeFormIsSavedEvent $event): void
    {
        $event->form['label'] = trim((string)($event->form['label'] ?? ''));
    }

    /** Replaces hook beforeFormDelete (Feature-107382). Stoppable. */
    #[AsEventListener('my-extension/form/before-delete')]
    public function protectShippedForms(BeforeFormIsDeletedEvent $event): void
    {
        if (str_starts_with($event->formPersistenceIdentifier, 'EXT:my_extension/')) {
            $event->preventDeletion = true;
        }
    }

    /** Replaces hook afterBuildingFinished (Feature-98239). */
    #[AsEventListener('my-extension/form/after-built')]
    public function afterBuilt(AfterFormIsBuiltEvent $event): void
    {
        // $event->form is the built form definition, e.g. add a finisher or change a label.
    }

    /** Replaces hook afterSubmit (Feature-107568): normalise before core validation. */
    #[AsEventListener('my-extension/form/before-validated')]
    public function normaliseIban(BeforeRenderableIsValidatedEvent $event): void
    {
        if ($event->renderable->getType() !== 'Iban' || !is_string($event->value)) {
            return;
        }
        $event->value = strtoupper(str_replace(' ', '', $event->value));
    }

    /** Replaces hook beforeRendering (Feature-107569). */
    #[AsEventListener('my-extension/form/before-rendered')]
    public function formatDate(BeforeRenderableIsRenderedEvent $event): void
    {
        $renderable = $event->renderable;
        if ($renderable->getType() !== 'Date') {
            return;
        }
        $date = $event->formRuntime[$renderable->getIdentifier()];
        if ($date instanceof \DateTime) {
            $event->formRuntime[$renderable->getIdentifier()] = $date->format('Y-m-d');
        }
    }
}
