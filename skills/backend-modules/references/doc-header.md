# DocHeader, buttons and the backend top frame

## ComponentFactory (Feature-107823, 14.0)

Inject `TYPO3\CMS\Backend\Template\Components\ComponentFactory` and create every button or menu through it.
`ButtonBar::make*()`, `Menu::makeMenuItem()` and `MenuRegistry::makeMenu()` are deprecated (removal in v15).
