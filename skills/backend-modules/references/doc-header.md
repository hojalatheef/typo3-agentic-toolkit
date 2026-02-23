# DocHeader, buttons and the backend top frame

## ComponentFactory (Feature-107823, 14.0)

Inject `TYPO3\CMS\Backend\Template\Components\ComponentFactory` and create every button or menu through it.
`ButtonBar::make*()`, `Menu::makeMenuItem()` and `MenuRegistry::makeMenu()` are deprecated (removal in v15).

| Method | Result |
| --- | --- |
| `createBackButton(string\|UriInterface $returnUrl)` | "Go back" link button. |
| `createCloseButton(string\|UriInterface $closeUrl)` | Close button. |
| `createSaveButton(string $formName = '')` | Save button for a form. |
| `createReloadButton(string\|UriInterface $requestUri)` | Reload (normally automatic, see below). |
| `createViewButton(array $previewDataAttributes = [])` | Frontend preview button. |
| `createLinkButton()`, `createInputButton()`, `createGenericButton()`, `createSplitButton()`, `createDropDownButton()`, `createFullyRenderedButton()`, `createShortcutButton()`, `createDropDownDivider()`, `createDropDownItem()` | Empty instances for fluent configuration. |
| `createMenu()`, `createMenuItem()` | Menu components. |

Pre-configured buttons are mutable (`setDataAttributes()`, `setClasses()`, `setIcon()`); `AbstractButton`
subclasses accept `setAttributes()`. `LinkButton`, `GenericButton` and `DropDownButton` support
`setSize(ButtonSize::...)`. `ButtonBar::addButton()`, `Menu::addMenuItem()`, `MenuRegistry::addMenu()` are
fluent now.

```php
$this->moduleTemplate->addButtonToButtonBar(
    $this->componentFactory->createBackButton($returnUrl),
    ButtonBar::BUTTON_POSITION_LEFT,
    1
);
$buttonBar
    ->addButton($backButton, ButtonBar::BUTTON_POSITION_LEFT, 1)
    ->addButton($saveButton, ButtonBar::BUTTON_POSITION_LEFT, 2);
```

Record list and file list action buttons use `ActionGroup` / `ComponentGroup` enums; listeners of
`ModifyRecordListRecordActionsEvent` and `ProcessFileListActionsEvent` changed with it (Breaking-107884, not
covered here).

## Reload and shortcut buttons (Feature-108008, 14.0)

Both are added automatically on the right side, as the last two buttons, before `ModifyButtonBarEvent`. Only
provide the shortcut data:

```php
$view->getDocHeaderComponent()->setShortcutContext(
    routeIdentifier: 'my_module',
    displayName: 'My Module',
    arguments: ['id' => $pageId]
);
```

Opt out with `disableAutomaticReloadButton()` / `disableAutomaticShortcutButton()` on the
`DocHeaderComponent`. Manually created `ShortcutButton` instances trigger a deprecation (Deprecation-108008).

## DocHeader layout and language selector (Feature-107875, 14.0)

Layout: top row has breadcrumb (left) and language selector (right); button bar has the module actions dropdown
in left group 0, action buttons in left groups 1+, functional buttons (reload, bookmark, clear cache) on the
right.

Module actions dropdown from the module configuration:

```php
$view = $this->moduleTemplateFactory->create($request);
$view->makeDocHeaderModuleMenu();
```

It is hidden when only one action exists. MenuRegistry menus are converted automatically.

Language selector:

```php
$languageDropdown = $this->componentFactory->createDropDownButton()
    ->setLabel($languageService->sL('core.core:labels.language'))
    ->setShowLabelText(true)
    ->setShowActiveLabelText(true);

foreach ($siteLanguages as $siteLanguage) {
    $languageDropdown->addItem(
        $this->componentFactory->createDropDownRadio()
            ->setHref((string)$this->uriBuilder->buildUriFromRoute('my_module', [
                'id' => $pageId,
                'language' => $siteLanguage->getLanguageId(),
            ]))
            ->setLabel($siteLanguage->getTitle())
            ->setIcon($this->iconFactory->getIcon($siteLanguage->getFlagIdentifier()))
            ->setActive($currentLanguageId === $siteLanguage->getLanguageId())
    );
}
$view->getDocHeaderComponent()->setLanguageSelector($languageDropdown);
```
