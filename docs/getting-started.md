# Getting started

This page takes you from nothing to a first reviewed change on a TYPO3
project.

## Requirements

| Tool | Needed for | Required |
| --- | --- | --- |
| Claude Code | running the plugin | yes |
| `jq` | every hook script reads its payload with `jq` | yes |
| `git` | worktrees for parallel implementers, the changelog cache, reviews against a base ref | yes |
| DDEV, host PHP or Docker | PHP lint after edits, the quality gate, the TYPO3 console | recommended |
| Docker | rendering documentation with `render-guides` | for `docs-rst` |
| `intelephense` on `PATH` | the LSP integration (`.lsp.json`) | optional |
| `gh` / `glab` | `ci-watcher`, `ticket-analysis` for GitHub and GitLab tickets | optional |
| A checkout of the TYPO3 Dev Companion | the `typo3-dev-companion` MCP server | optional |

## Install

From the marketplace that ships in this repository:

```text
/plugin marketplace add hojalatheef/typo3-agentic-toolkit
/plugin install typo3-agentic-toolkit@typo3-agentic-toolkit
```

For local development of the plugin itself, load the checkout directly:

```bash
claude --plugin-dir ~/Developer/claude-playground/typo3-agentic-toolkit
```

Every skill is then available as `/typo3-agentic-toolkit:<skill>` and every
agent as `typo3-agentic-toolkit:<agent>`. Most skills also load by
themselves when your request matches their description or when you open a
file matching their `paths`.

## Configure

Run `/plugin configure typo3-agentic-toolkit` (or
`claude plugin configure typo3-agentic-toolkit`). The options come from
`userConfig` in `.claude-plugin/plugin.json`:

| Option | Type | Default | Effect |
| --- | --- | --- | --- |
| `php_runner` | `auto` / `ddev` / `host` / `docker` | `auto` | How `php-lint.sh` runs `php -l`. `auto` tries DDEV (when `.ddev/` exists above the file), then host PHP, then a `php:8.4-cli` container |
| `strict_guard` | boolean | `false` | `guard-files.sh` denies instead of asks for edits under `vendor/`, `var/`, `public/typo3/`, `public/_assets/` |
| `dev_companion_path` | directory | unset | Path to a checkout of `github.com/TYPO3/dev-companion` (after `composer install`); `.mcp.json` starts its MCP server from there |

Hook scripts read the options as `CLAUDE_PLUGIN_OPTION_<NAME>` environment
variables.

Optional extras:

```bash
npm install -g intelephense   # PHP diagnostics and navigation through .lsp.json
bin/t3-changelog Feature-103437   # fetches the core changelog cache once
```

## The first 15 minutes

Open Claude Code in a TYPO3 project or extension directory. On start the
`SessionStart` hook prints a one-line summary into the context, for example
`TYPO3 14.3.1 · composer · ddev · PHP 8.4 · site sets: 2`, and the status
line shows `TYPO3 14.3.1 · composer · ddev`.

### 1. Onboard the repository (once)

```text
/typo3-agentic-toolkit:onboard
```

Writes `CLAUDE.md` (stack, real commands, the v13/v14 concepts the code
already uses) and `.claude/settings.json` (allow, ask and deny rules for
DDEV, Composer and the TYPO3 console). Add `--dry-run` to only print both
files. It merges into existing files and never deletes your rules. Review
the diff and commit it yourself.

### 2. Understand the work

For a ticket:

```text
/typo3-agentic-toolkit:ticket-analysis https://github.com/acme/news/issues/42
```

You get `.typo3-toolkit/tickets/42.md` with acceptance criteria, affected
code, risks, a size and the recommended next skill. No code is changed.

For a brand-new extension:

```text
/typo3-agentic-toolkit:extension-kickstart acme/event-calendar
```

It confirms vendor, key, title, TYPO3 range and features before writing a
v14 skeleton.

### 3. Build something

Small change: ask in plain words and the matching build skill loads, for
example "add a site set with an items-per-page setting" (`site-sets`) or
"make the contact form rate limited" (`extbase-modern`).

Larger feature:

```text
/typo3-agentic-toolkit:feature-team Add an "events" site set with a Fluid component for event teasers
```

### 4. Prove it

```text
/typo3-agentic-toolkit:quality-gate
/typo3-agentic-toolkit:review
```

The gate reports one PASS/FAIL/SKIP per step from real command output. The
review runs in a forked `code-reviewer` context and returns BLOCKER, MAJOR
and MINOR findings with file and line.

## The `.typo3-toolkit/` folder

Skills and agents write their working files into `.typo3-toolkit/` in the
project root. Add it to the project's `.gitignore` unless you want to keep
plans in history.

| Path | Written by | Contents |
| --- | --- | --- |
| `.typo3-toolkit/plan.md` | `typo3-architect` | The feature plan: Context, Packages (files, skill, steps, proof, dependencies), Tests, Risks and decisions |
| `.typo3-toolkit/gate/<step>.log` | `quality-gate` | Raw output of each gate step, quoted in the report |
| `.typo3-toolkit/tickets/<id>.md` | `ticket-analysis` | The analysis of one ticket |

Outside the project, the plugin keeps data in `${CLAUDE_PLUGIN_DATA}`
(falls back to `~/.local/share/typo3-agentic-toolkit`): `notes/<session>.md`
from the `PreCompact` hook and `agents.log` from the subagent hooks.

## Troubleshooting

| Symptom | Cause | Fix |
| --- | --- | --- |
| `/mcp` lists `typo3-dev-companion` as failed | `dev_companion_path` is not set, or the checkout has no `bin/typo3-dev-companion` | Expected and harmless. Set the option to a checkout with `composer install` done, or ignore it |
| `typo3-dev-companion` fails with a PHP message | `scripts/dev-companion-mcp.sh` needs PHP 8.2+ on the host or a running Docker | Install PHP 8.2+ or start Docker |
| Gate steps show `SKIP` | No DDEV, host PHP or Docker; or no `vendor/bin/typo3`; or the core is too old for `lint:yaml` (13.3) or `fluid:analyze` (14.2) | Start DDEV or install PHP; run `composer install`; a SKIP is reported as a coverage gap, never as PASS |
| PHP edits are never linted | `php-lint.sh` found no runner and stays silent | Set `php_runner` explicitly or make one of DDEV, host PHP, Docker available |
| Changelog lookups fail or the self-test skips "changelog ids" | No local core changelog cache | Run `bin/t3-changelog Feature-103437` once; it sparse-clones `TYPO3/typo3` into `~/.cache/typo3-extension-migration/typo3-core` |
| No PHP diagnostics from the LSP | `intelephense` is not on `PATH` | `npm install -g intelephense` |
| An edit under `vendor/` asks or is denied | `guard-files.sh` | Change the source instead (your extension, a Composer patch, an event or XCLASS); see [hooks.md](hooks.md) |
| A turn will not end and asks for tests | The `Stop` prompt hook saw PHP/TCA/Fluid/TypoScript changes without a test or gate run | Run the gate, or state why it cannot run |
| `feature-team` refuses to start | The working tree is not clean; worktrees need a clean tree | Commit or stash first |
