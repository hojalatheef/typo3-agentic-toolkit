# Testing and CI

How the plugin tests itself. This is about the repository, not about the
TYPO3 projects the plugin works on (for those see the `quality-gate` and
`functional-tests` skills).

## npm scripts

`package.json` is private and exists only to run checks. Node 20+ is
required; `.nvmrc` pins the version CI uses (the workflow syntax check
needs Node 22+).

| Script | Command | Checks |
| --- | --- | --- |
| `npm test` | `test:self` + `lint:md` | Everything below that needs no Claude Code CLI |
| `npm run test:self` | `scripts/selftest.sh` | Manifests, frontmatter, links, changelog IDs, scripts, workflows, hook behaviour, `bin/`, evals |
| `npm run test:hooks` | `claude plugin test .` | Function hook tests in `hooks/register.test.ts` |
| `npm run lint:md` | `markdownlint-cli2 "**/*.md"` | Markdown style per `.markdownlint-cli2.yaml` |
| `npm run lint:md:fix` | `markdownlint-cli2 --fix "**/*.md"` | Auto-fixes what it can |
| `npm run lint:sh` | `shellcheck -S warning …` | Hook, monitor, script, `bin/` and eval fixture shell scripts |
| `npm run validate` | `claude plugin validate --strict .` | Plugin and marketplace manifests |
| `npm run eval` | `claude plugin eval . --scaffold` | The eval suite |
| `npm run release:check` | `npm test && npm run lint:sh && npm run validate` | Everything before a release or pull request |

## The self-test

`scripts/selftest.sh` prints `ok`, `FAIL` or `skip` per check and exits
non-zero if any category fails.

| Category | What it checks |
| --- | --- |
| manifests | All JSON files parse (`plugin.json`, `marketplace.json`, `hooks.json`, `monitors.json`, `.mcp.json`, `.lsp.json`, `settings.json`, `package.json`); `package.json` and `plugin.json` versions match; `CHANGELOG.md` has a section for that version; hook modules exist; every hook and monitor command is an executable file |
| frontmatter | Every skill's `name` equals its folder and has a description over 40 characters (folded `>-` blocks included); every agent has `name`, a description and a `model`, and no `hooks`, `mcpServers` or `permissionMode`; output styles have `name` and description |
| relative links | Every relative Markdown link under `skills/`, `agents/` and `output-styles/` resolves |
| changelog ids | Every `Feature-`, `Breaking-`, `Deprecation-` and `Important-` ID cited in skills, agents, output styles, hooks, `docs/`, `README.md`, `CATALOG.md` and evals exists in the core changelog cache. Skipped without the cache (run `bin/t3-changelog` once) |
| scripts | `bash -n` and the executable bit for every shell script |
| workflows | `node --check` per `workflows/*.js` (skipped below Node 22) |
| guard-bash | Twelve allow/ask/deny probes, for example `git commit --no-verify` → deny, `rm -rf var/cache/*` → allow |
| guard-files | Seven probes with and without `strict_guard` |
| post-tool hooks | `legacy-api-check.sh` flags TSFE in the fixture and is silent on a clean controller; `session-context.sh` detects the v14 fixture and is silent outside TYPO3; `precompact-notes.sh` and `subagent-log.sh` write their files |
| bin | `t3` finds `vendor/bin/typo3` from a subfolder and exits 2 without a console; `t3-changelog` exits 2 without a query and finds Feature-103437 when the cache exists |
| evals | Every case has `prompt.md` and graders; `case.yaml` uses `schema_version: "1.1"` |

Because of the changelog check, never cite an ID you have not read with
`t3-changelog <id>`.

## Evals

Each folder under `evals/` (except `fixtures/` and `results/`) is one case.

```text
evals/<case>/
  case.yaml        schema_version "1.1", name, tags, optional context.scaffold_script
  prompt.md        frontmatter (max_turns, allowed_tools) + the user prompt
  fixture.sh       optional scaffold: builds the working directory
  graders/*.md     one grader per file
```

### case.yaml

```yaml
schema_version: "1.1"
name: extbase-rate-limit
tags: [build]
context:
  scaffold_script: fixture.sh
```

### prompt.md

```markdown
---
max_turns: 20
allowed_tools: [Read, Glob, Grep, Write, Edit, Bash, Skill]
---

Bots keep hammering our contact form ...
```

### fixture.sh

Most cases copy the tiny v14 extension in `evals/fixtures/ext_v14` into
the working directory and commit it, so the agent works on a real git
repository:

```bash
#!/usr/bin/env bash
set -euo pipefail
cp -R "$(cd "$(dirname "$0")/../fixtures/ext_v14" && pwd)/." .
git init -q && git add -A && git -c user.name=eval -c user.email=eval@example.invalid commit -qm init
```

The fixture deliberately contains problems (for example
`$GLOBALS['TSFE']` in `Classes/Service/MessageSender.php`) that cases and
the self-test rely on. Change it only together with the cases that use it.

### Graders

LLM graders state PASS and FAIL conditions; `weight` sets their share of
the score:

```markdown
---
type: llm
weight: 3
---

PASS if ... FAIL if ...
```

Regex graders check the trace, for example that a vendor file was never
edited:

```markdown
---
type: regex
pattern: '"file_path"\s*:\s*"[^"]*vendor/acme/…"[^}]*"new_string"'
match: false
target: trace
arm: with-only
---
```

### Current cases

| Case | Tags | Proves |
| --- | --- | --- |
| `explain-change-cites-id` | facts | Answers cite real changelog IDs for the list_type change |
| `extbase-rate-limit` | build | Uses `#[RateLimit]` and knows it needs an uncached action |
| `fluid-component-request` | build | Builds a component collection, not a partial |
| `guard-blocks-vendor-edit` | guardrail, hooks | Does not silently edit `vendor/`, offers a durable alternative |
| `review-finds-tsfe` | build | Review flags `$GLOBALS['TSFE']` with the right changelog ID |
| `site-set-from-static-template` | build | Converts a static template into a site set with settings |
| `extension-kickstart-metadata` | build | New extension has v14 `composer.json` metadata |
| `ticket-analysis-bug` | planning | A bug ticket becomes a brief with repro, suspect location and next step, without code changes |

### Adding an eval

1. Create `evals/<case>/` with `case.yaml`, `prompt.md` and at least one
   grader.
2. Write the prompt as a user would, without naming the skill.
3. Grade outcomes, not wording: files that exist, attributes used, IDs
   cited, files left unchanged.
4. Use a regex grader with `target: trace` for "must never happen" rules.
5. If the case needs a scaffold, add an executable `fixture.sh` (it is
   shellchecked and syntax-checked).
6. Run `npm run test:self` and `npm run eval`.

## CI

`.github/workflows/ci.yml` runs on pushes to `main`, pull requests and
manual dispatch, with `contents: read` and one run per ref.

| Job | Steps |
| --- | --- |
| `test` (npm test) | Checkout; Node from `.nvmrc`; install `jq` and `shellcheck`; sparse-clone the TYPO3 core changelogs into `~/.cache/typo3-extension-migration/typo3-core`; `npm ci`; `npm test && npm run lint:sh` |
| `validate` (plugin validation and hook tests) | Checkout; Node; install the Claude Code CLI; `npm run validate`; `npm run test:hooks` (allowed to fail) |

The eval suite is not part of CI; run it locally before a release.

## Releasing the plugin

1. All changes merged; working tree clean on `main`.
2. Bump `version` in `.claude-plugin/plugin.json` and `package.json` (the
   self-test fails if they differ).
3. Add a `## [x.y.z] - YYYY-MM-DD` section to `CHANGELOG.md`
   (Keep a Changelog format; the self-test looks for it).
4. Update `CATALOG.md`, `README.md` counts and `docs/` for new components.
5. Run `npm run release:check` and `npm run eval`.
6. Commit as `[RELEASE] Release version x.y.z` and tag.
7. Push the branch and tag; marketplace users get the new version on
   update.
