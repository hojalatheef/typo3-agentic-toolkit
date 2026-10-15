# Hooks

Hooks run on every matching event, whatever skill or agent is active. They
are declared in `hooks/hooks.json`; command hooks are Bash scripts in
`hooks/scripts/` that read the event payload as JSON on stdin with `jq` and
answer with JSON on stdout. All decisions carry the prefix
`typo3-agentic-toolkit:` in their reason so you can tell where they came
from.

## Overview

| Event | Matcher | Handler | Timeout | Effect |
| --- | --- | --- | --- | --- |
| `SessionStart` | `startup\|resume\|clear` | `session-context.sh` | 15 s | Adds TYPO3 version, mode, DDEV, PHP and site-set count to the context |
| `FileChanged` | `composer.lock` | `session-context.sh` | 15 s | Re-detects the setup and posts it as a system message |
| `PreToolUse` | `Bash` | `guard-bash.sh` | 10 s | Denies or asks before risky commands |
| `PreToolUse` | `Edit\|Write\|MultiEdit` | `guard-files.sh` | 10 s | Asks (or denies) before editing generated files |
| `PostToolUse` | `Edit\|Write\|MultiEdit` | `php-lint.sh` | 90 s, async | Lints the written PHP file |
| `PostToolUse` | `Edit\|Write\|MultiEdit` | `legacy-api-check.sh` | 10 s | Flags APIs deprecated or removed in v13/v14 |
| `PreCompact` | all | `precompact-notes.sh` | 15 s | Saves session facts before compaction |
| `SubagentStart` / `SubagentStop` | all | `subagent-log.sh` | 5 s | Logs agent runs and durations |
| `Stop` | all | prompt hook | 30 s | Blocks the end of a turn when code changed without tests |
| function hooks | `session.start`, `tool.call` | `hooks/register.ts` | - | Status line and a toast |

## SessionStart and FileChanged: `session-context.sh`

Detects the setup in the working directory:

- **Version**: `typo3/cms-core` from `composer.lock`, else `Typo3Version.php`
  in `vendor/`, `typo3_src/`, `public/typo3/` or `typo3/`. When nothing is
  installed it falls back to the `require` constraint of `composer.json`
  marked `(declared)`.
- **Mode**: `extension` (an `ext_emconf.php` or `composer.json` type
  `typo3-cms-extension`), `composer`, or `classic`.
- **DDEV and PHP**: `php_version` from `.ddev/config.yaml`, else
  `config.platform.php` or `require.php`.
- **Counts**: own site sets (`Configuration/Sets/*/config.yaml`, skipping
  `vendor/`, `node_modules/`, system extensions) and sites under
  `config/sites/`.

On `SessionStart` the summary goes into `additionalContext` with a hint to
use `bin/t3` and `bin/t3-changelog`; it writes `TYPO3_VERSION` and
`TYPO3_MODE_KIND` to `$CLAUDE_ENV_FILE` when set, and returns `watchPaths`
with `composer.lock` so the `FileChanged` hook fires after Composer runs.
On `FileChanged` it returns a `systemMessage` starting with
`composer.lock changed:`. Outside a TYPO3 directory it prints nothing.

## PreToolUse Bash: `guard-bash.sh`

The command is matched case-insensitively. Rules are evaluated in this
order; the first match decides.

| Decision | Pattern | Why |
| --- | --- | --- |
| deny | `git … --no-verify` | Skipping git hooks hides failures |
| deny | `composer … --ignore-platform-req` / `--ignore-platform-reqs` | Resolves for a PHP version nobody runs |
| deny | `rm -r` / `rm -rf` / `rm --recursive` of `var/` (whole folder or `var/*`), `public/fileadmin/…`, `config/…` | Deletes caches and logs wholesale, user files, or site and system configuration |
| ask | `database:updateschema` with `*`, `remove`, `drop` or `destructive` | Destructive schema change |
| ask | `ddev delete`, `ddev import-db` | Replaces or removes the database |
| ask | `DROP DATABASE/SCHEMA/TABLE`, `TRUNCATE` | Destroys data |

Everything else passes. Allowed on purpose (tested in the self-test):
`rm -rf var/cache/*`, `database:updateschema safe`, `composer update -W`, a
commit message that merely contains `-n`.

## PreToolUse Edit/Write: `guard-files.sh`

The file path is made relative to the session `cwd`.

| Path | Default | With `strict_guard: true` |
| --- | --- | --- |
| below `vendor/`, `var/`, `public/typo3/`, `public/_assets/` | ask | deny |
| `config/system/settings.php` | ask | ask |
| anything else | allow | allow |

The reason suggests the durable fix: change your extension, add a Composer
patch (`cweagans/composer-patches`), use an event or XCLASS, or for
settings prefer `config/system/additional.php`, site settings or a site set.

The option is read from `CLAUDE_PLUGIN_OPTION_STRICT_GUARD` (or
`CLAUDE_PLUGIN_OPTION_strict_guard`).

## PostToolUse: `php-lint.sh`

Runs asynchronously (`async: true`, 90 s) on `*.php` files only.

- Runner from `php_runner`: `ddev` (`ddev exec php -l` from the nearest
  folder with `.ddev/`), `host` (`php -l`), `docker`
  (`php:8.4-cli php -l`), or `auto` which tries the three in that order
  until one produces a lint result.
- On `Parse error` or `Fatal error` it returns `decision: block` with the
  first error lines and asks Claude to fix the syntax before continuing.
- A clean file, or no available PHP, stays silent.

## PostToolUse: `legacy-api-check.sh`

Greps the file just written (`*.php`, `*.yaml`, `*.yml`, `*.typoscript`,
`*.tsconfig`, `*.html`) and returns one hint per hit as `additionalContext`:

| Trigger | Hint cites |
| --- | --- |
| file named `ext_tables.php` | Deprecation-109438 (14.3): move to `Modules.php`, `Routes.php`, TCA overrides |
| `$GLOBALS['TSFE']`, `TypoScriptFrontendController` | Deprecation-105230, Breaking-107831: read request attributes |
| `ExtensionUtility::registerModule`, `ExtensionManagementUtility::addModule` | Breaking-96733, Feature-96733: `Configuration/Backend/Modules.php` |
| `list_type` | Deprecation-105076, Breaking-105377, Important-105538: register plugins as their own CType |
| Extbase `ObjectManager` | Deprecation-94619, Breaking-96107: constructor injection |
| `addPItoST43` | Deprecation-102821, Breaking-105377 |
| `GeneralUtility::_GP/_GET/_POST/_GPmerged(` | Deprecation-100053, Breaking-100963: read the PSR-7 request |
| `GeneralUtility::makeInstance(…Repository/Service/ConnectionPool/EventDispatcher/Context::class` | prefer constructor injection |

Silent when nothing matches. It hints; it does not block.

## PreCompact: `precompact-notes.sh`

Before the context is compacted it appends a section to
`${CLAUDE_PLUGIN_DATA}/notes/<session>.md` with time, trigger, working
directory, TYPO3 version, git branch and up to 30 changed files from
`git status --porcelain`. Read the file back when the compacted summary is
thin. Without `CLAUDE_PLUGIN_DATA` it uses
`${XDG_DATA_HOME:-~/.local/share}/typo3-agentic-toolkit`.

## SubagentStart / SubagentStop: `subagent-log.sh`

`SubagentStart` stores the start time per agent id under
`agents-running/`. `SubagentStop` appends a tab-separated line to
`${CLAUDE_PLUGIN_DATA}/agents.log`:

```text
2026-10-15T09:12:44Z  <session>  code-reviewer  <agent id>  95
```

Fields: finish time (UTC), session, agent type, agent id, duration in
seconds (`-` when the start was not seen).

## Stop: prompt hook

A `type: prompt` hook: a small model reads the hook input and answers JSON.

- If `stop_hook_active` is true → `{"ok": true}` (no loops).
- If the last assistant message says PHP, TCA, `Services.yaml`, Fluid or
  TypoScript files changed in the turn **and** does not mention tests or a
  gate (`phpunit`, `composer test`, `phpstan`, `php-cs-fixer`,
  `rector --dry-run`, the `quality-gate` or `verify` skill) or a reason they
  could not run → `{"ok": false, "reason": "PHP changed without a test run: …"}`.
- Otherwise `{"ok": true}`.

## Function hooks: `hooks/register.ts`

Loaded through `"modules": ["./register.ts"]` in `hooks.json`. They run in
the engine's own environment (no Node) and read files through `$.fs`.

- `session.start`: reads `composer.lock` or a core `Typo3Version.php` and
  sets the status line, for example `TYPO3 14.3.1 · composer · ddev`.
  Mode is `composer`, `classic` or `extension` (an `ext_emconf.php` with no
  installed core).
- `tool.call` for `Edit`/`Write` of `ext_emconf.php` or `composer.json`: a
  six-second toast reminding you to keep version and `typo3/cms-core`
  constraints in the other file in sync.

`hooks/register.test.ts` tests the pure helpers (`coreVersionFromLock`,
`coreVersionFromClass`, `statusLine`); run it with
`claude plugin test .`.

## Monitor: `typo3-log-errors`

Declared in `monitors/monitors.json` with
`"when": "on-skill-invoke:quality-gate"`. `monitors/watch-typo3-log.sh`
follows `var/log/typo3_*.log` (Composer mode) or
`typo3temp/var/log/typo3_*.log` (classic) with `tail -F` and prints each
new `ERROR`, `CRITICAL`, `ALERT` or `EMERGENCY` line, or any line naming an
exception, as `TYPO3 log: …` (cut at 400 characters). Without a log it
prints one idle line and exits.

## Tuning and disabling

| Goal | How |
| --- | --- |
| Deny instead of ask for generated files | Set `strict_guard` to `true` |
| Force a PHP runner | Set `php_runner` to `ddev`, `host` or `docker` |
| Allow a guarded command once | Answer the ask prompt; deny rules have no override by design |
| Silence one hook for a while | Disable the plugin for the session (`/plugin`) or run Claude Code without `--plugin-dir`; editing `hooks/hooks.json` in a local checkout also works but is not covered by the self-test |
| See which agents ran and how long | Read `${CLAUDE_PLUGIN_DATA}/agents.log` |
| Recover facts after compaction | Read `${CLAUDE_PLUGIN_DATA}/notes/<session>.md` |

When you change a guard, add a behaviour line to the `guard-bash` or
`guard-files` block in `scripts/selftest.sh` for every decision it makes
(see [testing-and-ci.md](testing-and-ci.md)).
