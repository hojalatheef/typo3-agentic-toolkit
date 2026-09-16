---
type: llm
weight: 3
---

PASS if `submitAction` gets the core attribute `#[RateLimit(...)]` imported from `TYPO3\CMS\Extbase\Attribute\RateLimit` with `limit: 3` and `interval: '10 minutes'` (Feature-108982, TYPO3 14.2), and a `message` translation key that exists in the extension's XLIFF file.
