---
type: llm
weight: 2
---

PASS if the component template (for example `Resources/Private/Components/…/TeaserCard/TeaserCard.fluid.html` with the default folder-per-component pattern) declares its inputs with `<f:argument name="…" type="…" />` (title, link, image or file reference, optional badge with `optional="true"` or a default) and uses `<f:slot />` or an argument for the text, and the reply shows a usage example with the collection's XML namespace (`xmlns:…="http://typo3.org/ns/Acme/Contact/Components"`).
