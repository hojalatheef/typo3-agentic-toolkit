# PSR-14 events of EXT:form (TYPO3 v14)

All events live in `TYPO3\CMS\Form\Event`. The hooks they replace were removed in 14.0 (Breaking entries with the
same numbers). Register listeners with `#[AsEventListener('vendor/identifier')]`.

## Form Manager events (backend persistence)

| Event | Replaces hook | Public properties | Changelog |
| --- | --- | --- | --- |
| `BeforeFormIsCreatedEvent` | `beforeFormCreate` | `$form` (array), `$formPersistenceIdentifier` | Feature-107343 |
| `BeforeFormIsDuplicatedEvent` | `beforeFormDuplicate` | `$form` (array), `$formPersistenceIdentifier` | Feature-107380 |
| `BeforeFormIsDeletedEvent` | `beforeFormDelete` | `$formPersistenceIdentifier` (read-only), `$preventDeletion` (stoppable) | Feature-107382 |
| `BeforeFormIsSavedEvent` | `beforeFormSave` | `$form` (array), `$formPersistenceIdentifier` | Feature-107388 |

```php
#[AsEventListener('my-extension/before-form-is-saved')]
public function __invoke(BeforeFormIsSavedEvent $event): void
{
    $event->form['label'] = trim($event->form['label'] ?? '');
}
```

## Runtime events (building, validating, rendering)

| Event | Replaces hook | Public properties | Changelog |
| --- | --- | --- | --- |
| `BeforeRenderableIsAddedToFormEvent` | `initializeFormElement` | `$renderable` | Feature-107518 |
| `BeforeRenderableIsRemovedFromFormEvent` | `beforeRemoveFromParentRenderable` | `$renderable` (read-only), `$preventRemoval` (stoppable) | Feature-107528 |
| `AfterFormIsBuiltEvent` | `afterBuildingFinished` | `$form` | Feature-98239 |
| `BeforeRenderableIsValidatedEvent` | `afterSubmit` | `$value` (writable), `$formRuntime`, `$renderable`, `$request` (read-only) | Feature-107568 |
| `BeforeRenderableIsRenderedEvent` | `beforeRendering` | `$renderable`, `$formRuntime` | Feature-107569 |

`BeforeRenderableIsValidatedEvent` runs before core validation: normalise `$event->value` or add errors to the
processing rule (`Error` is `TYPO3\CMS\Core\Error\Error` in the changelog example):

```php
$processingRule = $event->renderable->getRootForm()->getProcessingRule($event->renderable->getIdentifier());
$processingRule->getProcessingMessages()->addError(new Error('Passwords do not match', 1700000001));
```

`BeforeRenderableIsRenderedEvent` can rewrite runtime values for display, e.g. format a `DateTime`:
`$event->formRuntime[$identifier] = $date->format('Y-m-d');`.

## Migration from hooks

1. Find registrations: `grep -rn "SC_OPTIONS\]\['ext/form'\]" ext_localconf.php Classes`.
2. Map each hook to the event in the tables above.
3. Move the logic into an invokable listener class; take data from the event properties instead of method
   arguments.
4. `afterSubmit` returned the new value - now assign `$event->value`.
5. To stop a deletion or removal, set `$event->preventDeletion` / `$event->preventRemoval` to `true`.
6. Delete the `ext_localconf.php` registration. Extensions that still support 13.4 need both the hook (13.4) and
   the listener (14); test listener registration on both cores, because the event classes do not exist in 13.4.
