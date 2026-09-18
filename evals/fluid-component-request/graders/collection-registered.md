---
type: llm
weight: 3
---

PASS if the agent registered a component collection in `Configuration/Fluid/ComponentCollections.php` (the configuration-only registration of Feature-108508, TYPO3 14.1) mapping a namespace such as `Acme\\Contact\\Components` to a templatePaths entry like `EXT:acme_contact/Resources/Private/Components`, OR wrote a custom `ComponentCollection` class and explains why configuration was not enough.
FAIL if it builds the card as a partial (`<f:render partial=…>`), a custom ViewHelper written in PHP, or a section, without any component collection.
