# Frontend request tests

Request tests send an internal request through the full frontend middleware stack and return a PSR-7 response. They prove that a plugin, content element or site set really renders, including TypoScript, routing and caching.

## Ingredients

1. Page records (`pages.csv`) with a root page (`pid` 0).
2. A site configuration for that root page. The testing framework instance is not composer-based, so the configuration lives under `Environment::getConfigPath() . '/sites/<identifier>/config.yaml'`. The template [FrontendRenderingTest.php](../templates/FrontendRenderingTest.php) writes it with Symfony YAML in `setUp()`.
3. TypoScript for the root page: `$this->setUpFrontendRootPage(1, ['EXT:my_extension/Tests/Functional/Fixtures/Frontend/setup.typoscript'])`.
4. The request: `$this->executeFrontendSubRequest((new InternalRequest('https://website.local/'))->withPageId(1))`.

## Testing site sets and plugins

- To test a site set, add `dependencies: [my-vendor/my-set]` to the site configuration array and keep the root TypoScript minimal; the set provides the rest (site sets: Feature-103437).
- To render an Extbase plugin, put a `tt_content` row with the plugin's `CType` on the page in the CSV, and include the plugin TypoScript.
- For logged-in frontend users, pass an `InternalRequestContext` with `withFrontendUserId(<uid>)` and import `fe_users` and `fe_groups` rows. This is how to test `#[Authorize]` (Feature-107826): assert status 403 without login and 200 with a user in the required group.

## Assertions

| Check | Assertion |
| --- | --- |
