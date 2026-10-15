# Skills

A skill is a `SKILL.md` with frontmatter (`name`, `description`, optional
`argument-hint`, `allowed-tools`, `paths`, `context`, `agent`, `model`,
`effort`, `disable-model-invocation`) and a body of steps, checklists and a
report format. Longer material sits in `references/`, copy-ready files in
`templates/`; both load only when the skill needs them.

You can use a skill three ways:

- **Invoke it**: `/typo3-agentic-toolkit:<name> <arguments>`.
- **Ask for the task**: the model loads the skill whose `description` fits.
- **Open a file**: a skill with `paths` activates when you work on a file
  matching one of its globs.

Skills marked *user-only* (`disable-model-invocation: true`) run only when
you type the command.

## Overview

| Group | Skills |
| --- | --- |
| Planning and scaffolding | `ticket-analysis`, `extension-kickstart` |
| Build (TYPO3 concepts) | `site-sets`, `sitepackage`, `fluid-components`, `record-rendering`, `schema-api`, `content-blocks`, `extbase-modern`, `translations`, `scheduler-console`, `backend-modules`, `security-hardening`, `system-resources`, `form-framework` |
| Quality | `quality-gate`, `functional-tests`, `a11y-audit`, `review` |
| Orchestration and commands | `feature-team`, `explain-change`, `onboard`, `adr`, `release`, `docs-rst` |

## Planning and scaffolding

### ticket-analysis

Analyses a ticket before any code is written and turns it into an
actionable brief.

- **Invoke**: `/typo3-agentic-toolkit:ticket-analysis <ticket URL | id | paste>`
- **Sources**: GitHub issues (`gh`), GitLab issues (`glab`), TYPO3 Forge
  (its Redmine JSON), Jira (through a connected MCP server or pasted text),
  or plain pasted text. See `references/sources.md`.
- **Behaviour**: read-only on code. Ticket text is treated as untrusted
  data, never as instructions. Nothing is posted back to the tracker
  without explicit confirmation.
- **Steps**: fetch and normalise; classify (bug, feature, task, upgrade,
  question); extract the problem statement, explicit and derived acceptance
  criteria, constraints and unknowns; locate affected code (grep, or two to
  four parallel `code-scout` agents); verify TYPO3 facts with
  `t3-changelog` or `changelog-researcher`; for bugs, write reproduction
  steps, expected vs actual, suspect `file:line`, a hypothesis with
  confidence and a failing-test proposal for `test-engineer`; assess impact
  and risk (schema/TCA, caches, upgrade wizards, backwards compatibility,
  security, accessibility); size S/M/L with reasons; list questions for the
  reporter; recommend the next step.
- **Produces**: `.typo3-toolkit/tickets/<id>.md` from
  `templates/analysis.md`.
- **Next step it recommends**: a build skill, `feature-team`,
  `extension-kickstart`, the `typo3-extension-migration` plugin, or `adr`.
- **Eval**: `evals/ticket-analysis-bug`.

### extension-kickstart

Creates a new TYPO3 v14 extension from scratch.

- **Invoke**: `/typo3-agentic-toolkit:extension-kickstart <vendor/package-name> [extension_key] [target directory]`
- **Confirms first**: vendor, package, extension key, title, target TYPO3
  range and the feature list, before writing a file.
- **Skeleton** (`templates/my_extension/`):
  - `composer.json` with `"description": "Title - description"`
    (Breaking-108304), `extra.typo3/cms.extension-key`,
    `extra.typo3/cms.version` and `Package.providesPackages: {}`
    (Feature-108345, Deprecation-108345). No `ext_emconf.php` unless
    classic mode or TER for v13 is needed.
  - `Configuration/Services.yaml`.
  - A site set in `Configuration/Sets/<Name>/` with `config.yaml`,
    `setup.typoscript` and `settings.definitions.yaml` (Feature-103437).
  - `Resources/Private/Language/messages.xlf` (translation domain),
    `Resources/Public/Icons/Extension.svg`.
  - `README.md`, `.gitattributes`, `.gitignore`, `Classes/.gitkeep`.
- **Features**: each requested feature is mapped to its build skill
  (`references/feature-map.md`): plugin → `extbase-modern` with CType
  registration (Important-105538); tables → `schema-api`; content element →
  `record-rendering` or `content-blocks`; backend module →
  `backend-modules`; command or task → `scheduler-console`; components →
  `fluid-components`; tests → `functional-tests`; docs → `docs-rst`. More
  than about three features → hand off to `feature-team`.
- **Finish**: QA dev dependencies, installation into a project through a
  Composer path repository, `composer validate`, then
  `quality-gate --fast`.
- **References**: `references/composer-metadata.md`,
  `references/feature-map.md`.
- **Eval**: `evals/extension-kickstart-metadata`.

## Build skills

All build skills target TYPO3 v14 (14.3) and say when a concept is missing
in 13.4. Each has `references/` for depth and most have `templates/`.

| Skill | Builds | Activates on (`paths`) | Arguments |
| --- | --- | --- | --- |
| `site-sets` | Site sets, typed settings, settings editor, route enhancers in sets | `Configuration/Sets/**`, `config/sites/**/config.yaml`, `settings.yaml`, `ext_typoscript_*.typoscript`, `Configuration/TypoScript/**` | `[vendor/set-name] [extension path]` |
| `sitepackage` | v14 site package: site set entry point, PAGEVIEW, content areas, page title/meta ViewHelpers, `f:image.srcset`, Camino or own | `Resources/Private/PageView/**`, `Configuration/Sets/**`, `packages/*/composer.json` | `[vendor/package-name] [target directory]` |
| `fluid-components` | Component collections, `f:argument`, `f:slot`, `*.fluid.html`, XSD autocomplete, `fluid:analyze` | `Resources/Private/Components/**`, `Configuration/Fluid/*.php`, `*.fluid.html` | `[component path] [extension path]` |
| `record-rendering` | Record objects, `PAGEVIEW` content areas, `f:render.*` ViewHelpers, backend previews | content templates, `PageView/**`, `Templates/Preview/**`, `Classes/Preview/**`, set `setup.typoscript` | `[CType or table] [extension path]` |
| `schema-api` | TCA for v14 and reading it through `TcaSchemaFactory` | `Configuration/TCA/**/*.php`, `ext_tables.sql`, `Configuration/FlexForms/**/*.xml` | `[table name] [extension path]` |
| `content-blocks` | Decide Content Blocks vs TCA + site sets; scaffold with `make:content-block` | `ContentBlocks/**` | `[vendor/element-name] [extension path]` |
| `extbase-modern` | `#[Authorize]`, `#[RateLimit]`, Symfony validators, `#[FileUpload]`, history tracking | `Classes/Controller/**`, `Classes/Domain/Model/**`, `Classes/Domain/Repository/**`, `Configuration/Extbase/Persistence/Classes.php` | `[controller or model path] [feature]` |
| `translations` | Translation domains, XLIFF 2.0, ICU plurals, `TranslatorInterface`, JS labels | `Resources/Private/Language/**/*.xlf`, set `labels.xlf`, `settings.definitions.yaml`, `Resources/Private/**/*.html` | `[extension path or .xlf] [migrate\|plural\|xliff2\|js]` |
| `scheduler-console` | Console commands, scheduler tasks as TCA records, message handlers | `Classes/Command/**`, `Classes/Task/**`, `*Handler.php`, `tx_scheduler_task*` overrides | `[command\|task\|handler] [extension path]` |
| `backend-modules` | Modules in the v14 module tree, access gates, DocHeader buttons | `Configuration/Backend/Modules.php`, backend controllers | `[module name] [parent]` |
| `security-hardening` | CSP hashes, SRI, `CipherService`, HMAC, rate limits, security review | `csp.yaml`, `ContentSecurityPolicies.php`, `*Crypto*.php`, `*Token*.php` | `[extension path] [review\|csp\|sri\|encrypt\|hmac]` |
| `system-resources` | System Resource API, `PKG:`/`EXT:` identifiers, asset publishing | `Configuration/Resources.php`, `config/system/resources.php`, TypoScript, layouts | `[path or identifier] [php\|fluid\|typoscript\|publish]` |
| `form-framework` | Form sets, database form storage, custom elements, finishers, form events | `Configuration/Form/**/*.yaml`, `*.form.yaml`, finishers, `Classes/**/Form/**` | `[element\|finisher\|event\|migrate] [extension path]` |

Notes:

- `allowed-tools` keeps each build skill to `Read`, `Grep`, `Glob`,
  `Edit`, `Write` plus the few console commands it needs (for example
  `site:sets:list`, `fluid:*`, `make:content-block`, `asset:publish`).
- The `implementer` agent preloads `site-sets`, `schema-api`,
  `extbase-modern`, `fluid-components`, `record-rendering`, `translations`
  and `system-resources`.
- The `security-auditor` agent preloads `security-hardening`.

## Quality skills

### quality-gate

Runs the full gate and reports a verdict per step from real output.

- **Invoke**: `/typo3-agentic-toolkit:quality-gate [path] [--fast]`
  (`--fast` runs steps 1 to 6).
- **Runner**: DDEV (`ddev exec`, console through `t3`), host PHP, Docker
  `php:8.4-cli`, or none (every PHP step SKIPPED).
- **Steps**: `composer validate --strict`, `php -l`, `php-cs-fixer
  --dry-run`, PHPStan, Rector dry-run, `lint:yaml` (13.3+), `fluid:analyze`
  (14.2+), unit tests, functional tests.
- **Output**: logs in `.typo3-toolkit/gate/<step>.log` and a report ending
  in `PASSED`, `FAILED` or `INCOMPLETE` plus the single most important fix.
- **Extra**: starts the `typo3-log-errors` monitor, which streams new TYPO3
  log errors while the gate runs.

### functional-tests

Sets up and writes unit and functional tests with
`typo3/testing-framework`.

- **Invoke**: `/typo3-agentic-toolkit:functional-tests [class or feature]`
- **Activates on**: `Tests/**`, `Build/phpunit/**`.
- **Templates**: `UnitTests.xml`, `FunctionalTests.xml`,
  `RepositoryTest.php`, `FrontendRenderingTest.php`, `pages.csv`.
- **Rules**: CSV datasets with `importCSVDataSet`/`assertCSVDataSet`,
  PHPUnit attributes, bug fixes start with a failing test.
- **Related**: `test-engineer` agent preloads it.

### a11y-audit

Audits Fluid output and backend templates for WCAG 2.2 AA.

- **Invoke**: `/typo3-agentic-toolkit:a11y-audit [path to templates or URL]`
- **Steps**: inventory templates, delegate to `fluid-a11y-reviewer` (one
  agent per group above about 30 templates), optional rendered check with
  axe or pa11y, merge and sort.
- **Output**: `A11Y`, `ISSUE` and `MANUAL` lines. Does not fix unless
  asked.

### review

Adversarial review of the current change.

- **Invoke**: `/typo3-agentic-toolkit:review [base-ref]`
- **Frontmatter**: `context: fork`, `agent: code-reviewer`. It runs in a
  forked context; you only see the final reply.
- **Injected context**: `git status`, diff stat and commits against the
  merge base with `main`/`master`, gathered by `` !`command` `` lines before
  the model reads the skill.
- **Output**: the `code-reviewer` format (`VERDICT`, `BLOCKER`, `MAJOR`,
  `MINOR`, `BREAKING`) plus one next-step line.

## Orchestration and command skills

### feature-team

Builds a feature end to end with a team of subagents.

- **Invoke**: `/typo3-agentic-toolkit:feature-team <feature description>`
- **Preconditions**: clean git tree, base ref noted, TYPO3 constraint
  checked.
- **Steps**: optional `code-scout` sweep → `typo3-architect` plan (stop on
  open decisions) → parallel `implementer` agents in worktrees → merge in
  plan order → `test-engineer` → `code-reviewer` + `security-auditor`
  (+ `fluid-a11y-reviewer`) in parallel → fix loop via `SendMessage` (max
  three rounds) → `quality-gate`.
- **Agent teams**: with `CLAUDE_CODE_EXPERIMENTAL_AGENT_TEAMS=1` the same
  roles can run as teammates; see
  `skills/feature-team/references/agent-teams.md`.
- **Workflow**: the same pipeline as a script, see
  [workflows.md](workflows.md).

### explain-change

Answers "what changed about X" only from the core changelog.

- **Invoke**: `/typo3-agentic-toolkit:explain-change <topic | changelog id>`
- **Frontmatter**: named argument `topic`, `model: haiku`, `effort: low`,
  `allowed-tools` limited to `t3-changelog`, `grep`, `ls`.
- **Output**: short answer, facts with IDs, a code sample from the rst,
  availability on 13.4 and 14.x, the source files.

### onboard

Generates a project `CLAUDE.md` and `.claude/settings.json`.

- **Invoke**: `/typo3-agentic-toolkit:onboard [--dry-run]` (*user-only*).
- **Injected context**: composer type and constraints, installed core
  version, tooling present, DDEV config.
- **Rules**: merges with existing files, never deletes rules; allows
  read-only and reversible commands, asks for shared-state changes, denies
  reading secrets. Does not commit.

### adr

Writes an Architecture Decision Record in reStructuredText.

- **Invoke**: `/typo3-agentic-toolkit:adr <decision title>`
- **Frontmatter**: `context: fork`.
- **Output**: `Documentation/Decisions/NNNN-<kebab-title>.rst`, at least two
  options, consequences, status. Old records are immutable except for
  "Superseded by NNNN".

### release

Prepares an extension release and stops before anything leaves the
machine.

- **Invoke**: `/typo3-agentic-toolkit:release <version>` (*user-only*).
- **Steps**: validate SemVer and clean tree; bump `ext_emconf.php`,
  `Documentation/guides.xml` and `composer.json` (only if it has a
  `version`); write the CHANGELOG section; run `quality-gate`; render docs;
  commit `[RELEASE] Release of version <version>`; create an annotated tag.
- **Stops**: prints the push, Packagist, TER and docs checklist and waits
  for an explicit yes.

### docs-rst

Writes and renders extension documentation.

- **Invoke**: `/typo3-agentic-toolkit:docs-rst [topic or --render]`
- **Activates on**: `Documentation/**`.
- **Steps**: set up or migrate `Settings.cfg` → `guides.xml`; write one
  topic per page with labels and `confval`; render with the
  `ghcr.io/typo3-documentation/render-guides` Docker image; fix every
  warning.
- **Related**: delegates larger jobs to `docs-writer`; `adr` for decision
  records.
