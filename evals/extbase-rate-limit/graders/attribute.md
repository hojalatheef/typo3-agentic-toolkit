---
type: llm
weight: 3
---

PASS if `submitAction` gets the core attribute `#[RateLimit(...)]` imported from `TYPO3\CMS\Extbase\Attribute\RateLimit` with `limit: 3` and `interval: '10 minutes'` (Feature-108982, TYPO3 14.2), and a `message` translation key that exists in the extension's XLIFF file.
FAIL if the agent builds its own limiter (session counters, a custom middleware, a hand-written Symfony RateLimiterFactory) instead of the attribute, or uses attribute properties that do not exist.
