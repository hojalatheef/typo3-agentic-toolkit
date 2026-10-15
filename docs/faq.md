# FAQ

## Which skill do I use for …?

| Task | Skill |
| --- | --- |
| Understand a ticket before coding | `ticket-analysis` |
| Start a new extension | `extension-kickstart` |
| Start a new site or theme | `sitepackage` |
| Ship TypoScript and settings without `sys_template` | `site-sets` |
| Add a table or field | `schema-api` |
| Add a content element | `record-rendering`, or `content-blocks` if the project uses that extension |
| Add an Extbase plugin, secure or rate-limit an action | `extbase-modern` |
| Build reusable UI pieces | `fluid-components` |
| Labels, plurals, XLIFF | `translations` |
| Console command, scheduler task, queue handler | `scheduler-console` |
| Backend module | `backend-modules` |
| Asset URLs, `_assets`, CDN | `system-resources` |
| Custom form element or finisher | `form-framework` |
| CSP, SRI, encryption, security review | `security-hardening` |
| A feature that touches many files | `feature-team` |
| "What changed about X?" | `explain-change` |
| Is it green? | `quality-gate` |
| Is it ready to merge? | `review` |
| Accessibility check | `a11y-audit` |
| Tests | `functional-tests` |
| Documentation | `docs-rst` |
| Record a design decision | `adr` |
| Tag a release | `release` |
| Upgrade an old extension to v13/v14 | not this plugin: use `typo3-extension-migration` |

See [skills.md](skills.md) for details.

## Does it support TYPO3 13.4?

Partly. Skills target v14 (14.3) and state for every concept the version
that introduced it, taken from the changelog folder. When a project's
`composer.json` still allows `^13.4`, the architect uses only concepts that
exist in 13.4 or plans a fallback and says so. Skills such as `sitepackage`
and `extension-kickstart` produce v14-only code (for example
`*.fluid.html` templates). Moving code between LTS versions is the job of
the separate `typo3-extension-migration` plugin.

## How do I cite a changelog ID?

1. Look it up: `bin/t3-changelog <id|keyword>` (or
   `/typo3-agentic-toolkit:explain-change <topic>`).
2. Read the rst. Its folder (`13.1`, `14.2`) is the version.
3. Cite it as `Feature-103437`, `Breaking-105377`, `Deprecation-105076` or
   `Important-105538`, ideally with the version: `(Feature-103437, 13.1)`.
4. Run `npm run test:self`; it fails on an ID with no rst.

Never cite from memory. If the lookup finds nothing, write "not found in
the changelog".

## How do I write a new skill?

1. Create `skills/<name>/SKILL.md`; `name` must equal the folder.
2. Write a `description` that says what it does and when to use it,
   including the phrases users type. It is what triggers the skill.
3. Add `argument-hint`, `allowed-tools` (as narrow as possible) and
   `paths` globs when the skill belongs to a file type.
4. Use `disable-model-invocation: true` for commands with side effects,
   `context: fork` when the work should not fill the main context.
5. Keep the body short: When to use, Steps, Checklist, Report. Move depth
   to `references/`, copy-ready files to `templates/`.
6. Cite changelog IDs you have read.
7. List it in `CATALOG.md`, [skills.md](skills.md) and, if it shows a new
   Claude Code concept, [concepts.md](concepts.md). Add an eval if the
   behaviour can regress.

## How do I write a new agent?

1. Create `agents/<name>.md` with `name`, `description`, `tools`,
   `model` and `maxTurns`.
2. Choose the model on purpose: Haiku for sweeps, Sonnet for building,
   Opus for planning and adversarial review.
3. Read-only agents get `disallowedTools: Write, Edit`.
4. Do not use `hooks`, `mcpServers` or `permissionMode` (plugin agents
   ignore them; the self-test fails).
5. End the body with a fixed reply format.
6. Document it in `CATALOG.md` and [agents.md](agents.md).

## What does it cost?

Cost follows the number and model of agents:

- Cheapest: a single build skill in the main session, or `explain-change`
  (Haiku, low effort).
- Moderate: `review`, `quality-gate`, `ticket-analysis` with a few Haiku
  scouts.
- Expensive: `feature-team` and `feature-pipeline.js` (Opus architect and
  reviewers, several Sonnet implementers). The pipeline caps at nine agents,
  the audit at eight.

To save tokens: use a build skill directly for one-file changes, give
`feature-team` a precise feature description, and resolve the plan's open
decisions before implementation starts.

## Why did my edit or command get blocked?

A guard hook decided. The reason starts with `typo3-agentic-toolkit:` and
names the fix. Rules and options are in [hooks.md](hooks.md).

## Why does the session refuse to end?

The `Stop` hook saw code changes without a test or gate run. Run
`/typo3-agentic-toolkit:quality-gate` or say why tests cannot run.

## Is anything pushed or posted automatically?

No. `release` stops before `git push`, TER and Packagist. `ticket-analysis`
never comments on the tracker without confirmation. Implementers commit only
on their own worktree branches and never push.
