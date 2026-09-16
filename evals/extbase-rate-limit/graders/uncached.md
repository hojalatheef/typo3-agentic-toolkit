---
type: llm
weight: 2
---

PASS if the agent checks or states that rate limiting only works for uncached actions and confirms `submitAction` is listed as non-cacheable in `configurePlugin()` (it is in the fixture), and mentions the 429 response or the `BeforeActionRateLimitResponseEvent` for customising it.
FAIL if it claims the limit works on cached actions or never considers caching.
