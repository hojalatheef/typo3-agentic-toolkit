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
