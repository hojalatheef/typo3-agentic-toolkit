# Feature map: what to add after the skeleton

Each row names the skill that owns the feature. Follow that skill. It has the templates, the checklist and the changelog facts. The "Files" column shows what the feature adds to the skeleton, so you can tell which work packages share files.

| Feature | Skill | Files it adds | Key changelog |
| --- | --- | --- | --- |
| Extbase plugin | `extbase-modern` | `Classes/Controller/*`, `ext_localconf.php` (`configurePlugin()`), `Configuration/TCA/Overrides/tt_content.php` (`registerPlugin()`, one `CType` per plugin), `Resources/Private/Templates/<Controller>/*.fluid.html` | Important-105538, Deprecation-105076 |
| Domain model and repository | `extbase-modern` + `schema-api` | `Classes/Domain/Model/*`, `Classes/Domain/Repository/*`, `Configuration/TCA/tx_<key>_domain_model_*.php` | Feature-104002 |
| Plain records (no Extbase) | `schema-api` | `Configuration/TCA/<table>.php`. Columns and DB fields are created from TCA, so add `ext_tables.sql` only for what TCA cannot express | Feature-104002 |
| Content element (core) | `record-rendering` | `Configuration/TCA/Overrides/tt_content.php`, `Resources/Private/Templates/Content/*.fluid.html`, set `setup.typoscript` | Feature-103783 |
| Content element (Content Blocks) | `content-blocks` | `ContentBlocks/ContentElements/<name>/` | none (extension, not core) |
| Fluid components | `fluid-components` | `Configuration/Fluid/ComponentCollections.php`, `Resources/Private/Components/**` | Feature-108508 |
| Backend module | `backend-modules` | `Configuration/Backend/Modules.php`, `Classes/Controller/Backend/*`, `Resources/Private/Templates/Backend/*` | Feature-107628 |
| Console command | `scheduler-console` | `Classes/Command/*` (`#[AsCommand]`, autoconfigured) | none |
| Scheduler task | `scheduler-console` | `Classes/Task/*`, `Configuration/TCA/Overrides/tx_scheduler_task_*.php` | Feature-106739 |
| Queue handler | `scheduler-console` | `Classes/Message/*`, `Classes/MessageHandler/*` | none |
| Event listener | none: core PSR-14 `#[AsEventListener]`, autoconfigured | `Classes/EventListener/*` | none |
| More labels | `translations` | `Resources/Private/Language/*.xlf` (XLIFF 2.0 for new files) | Feature-107436, Feature-107710 |
| Site settings | `site-sets` | `Configuration/Sets/<Name>/settings.definitions.yaml`, `labels.xlf` | Feature-103437 |
| Assets | `system-resources` | `Resources/Public/**`, `PKG:` identifiers | Feature-107537 |
| Security (CSP, secrets) | `security-hardening` | `Configuration/ContentSecurityPolicies.php` | Feature-100887 |
| Forms | `form-framework` | `Configuration/Form/<Set>/config.yaml` | none |
| Tests | `functional-tests` | `Tests/Unit/**`, `Tests/Functional/**`, `Build/phpunit/*.xml`, `autoload-dev` in `composer.json` | none |
| Documentation | `docs-rst` | `Documentation/**`, `guides.xml` | none |

## Shared files

These files are touched by several features. If you hand work to `feature-team`, exactly one package owns each of them, usually a final "wiring" package:

- `composer.json` (`require`, `autoload-dev`)
- `ext_localconf.php`
- `Configuration/Services.yaml`
- `Configuration/TCA/Overrides/tt_content.php`
- `Configuration/Sets/<Name>/setup.typoscript` and `config.yaml`

## Questions to ask when the request is vague

1. Who uses it: editors (content element, plugin), integrators (site settings) or admins (backend module, command)?
2. Does it store data? Then which tables, and are they translatable or workspace-aware?
3. Cached or uncached output? Forms, rate limits and per-user output need uncached actions.
4. Which TYPO3 lines and which installation modes (Composer only, or classic too)?
