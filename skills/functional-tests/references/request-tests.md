# Frontend request tests

Request tests send an internal request through the full frontend middleware stack and return a PSR-7 response. They prove that a plugin, content element or site set really renders, including TypoScript, routing and caching.

## Ingredients

1. Page records (`pages.csv`) with a root page (`pid` 0).
