# Catalog

Every component of the plugin, by category. Skills are invoked as
`/typo3-agentic-toolkit:<name>` or load by themselves when the task (or a file
matching their `paths`) fits. Agents are delegated to by name.

## Build skills — TYPO3 v13/v14 concepts

| Skill | What it builds | Key changelog entries | Activates on |
| --- | --- | --- | --- |
| `site-sets` | Site sets, typed settings, settings editor, route enhancers in sets | Feature-103437, 103439, 103522, 104794, 106640, 107837 | `Configuration/Sets/**`, `Configuration/TypoScript/**` |
| `sitepackage` | v14 site package on PAGEVIEW, Camino-style theme, page title/meta ViewHelpers | Feature-103504, 108539, 107358, 107359, 107056 | `Resources/Private/PageView/**` |
| `fluid-components` | Component collections, slots, typed arguments, XSD autocomplete | Feature-108508, 108524, 109114, 108166, 108229 | `Resources/Private/Components/**` |
| `record-rendering` | Record objects, content areas, record and text rendering ViewHelpers | Feature-103783, 103581, 108726, 108868, 108819 | content element templates |
| `schema-api` | TCA written for v14, read through `TcaSchemaFactory` | Feature-104002, 108027, 107281, 108581, 107889 | `Configuration/TCA/**` |
| `content-blocks` | Content elements with friendsoftypo3/content-blocks (extension, not core) | — | `ContentBlocks/**` |
| `extbase-modern` | Action authorization, rate limiting, history tracking, Symfony validators, uploads | Feature-107826, 108982, 109080, 107289, 106945, 103511 | `Classes/Controller/**`, `Classes/Domain/**` |
| `translations` | Translation domains, XLIFF 2.0, ICU plurals, Symfony translator | Feature-107436, 107710, 93334, 107759, 104546 | `Resources/Private/Language/**` |
| `scheduler-console` | Console commands, scheduler tasks as TCA records, message handlers | Feature-106739, 107526, 107151, 109110, 106261 | `Classes/Command/**`, `Classes/Task/**` |
| `backend-modules` | Modules with the v14 module tree, access gates, component factory | Feature-107628, 107663, 109365, 102631, 107823 | `Configuration/Backend/**` |
| `security-hardening` | CSP hashes, SRI, cipher service, HMAC, rate limits | Feature-100887, 102079, 109187, 108002, 102761 | `Configuration/ContentSecurityPolicies.php` |
| `system-resources` | System Resource API, public resource publishing, asset URIs | Feature-107537, 109163, 109409 | `Resources/Public/**` |
| `form-framework` | Form storage in the database, YAML autodiscovery, custom elements, form events | Feature-108653, 109412, 107058 | `Configuration/Form/**` |

## Quality skills

| Skill | Purpose | Claude Code feature |
| --- | --- | --- |
| `quality-gate` | composer validate, lint, CS, PHPStan, Rector dry-run, `lint:yaml`, `fluid:analyze`, tests — one verdict per step from real output | starts the `typo3-log-errors` monitor |
| `functional-tests` | typo3/testing-framework setup, CSV datasets, request tests | `paths` on `Tests/**` |
| `a11y-audit` | WCAG 2.2 AA for Fluid output and backend modules | delegates to `fluid-a11y-reviewer` |
| `review` | Adversarial review of the current change | `context: fork`, `agent: code-reviewer` |

## Orchestration and command skills

| Skill | Purpose | Claude Code feature |
| --- | --- | --- |
| `feature-team` | Plan → parallel implementers in worktrees → tests → review | subagents, worktrees, SendMessage, agent teams |
| `explain-change` | "What changed about X?" from the core changelog | `arguments`, `model: haiku`, `effort: low`, `!` injection |
| `onboard` | Generate `CLAUDE.md` and a permission allowlist for a TYPO3 project | `disable-model-invocation`, `!` injection |
