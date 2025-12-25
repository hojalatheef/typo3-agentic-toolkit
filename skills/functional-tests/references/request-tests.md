# Frontend request tests

Request tests send an internal request through the full frontend middleware stack and return a PSR-7 response. They prove that a plugin, content element or site set really renders, including TypoScript, routing and caching.

## Ingredients

1. Page records (`pages.csv`) with a root page (`pid` 0).
2. A site configuration for that root page. The testing framework instance is not composer-based, so the configuration lives under `Environment::getConfigPath() . '/sites/<identifier>/config.yaml'`. The template [FrontendRenderingTest.php](../templates/FrontendRenderingTest.php) writes it with Symfony YAML in `setUp()`.
