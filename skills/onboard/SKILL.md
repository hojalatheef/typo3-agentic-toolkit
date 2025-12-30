---
name: onboard
description: Onboard a TYPO3 project or extension for Claude Code - generate a project CLAUDE.md (stack, commands, conventions, v14 concepts in use) and a .claude/settings.json permission allowlist for ddev, composer and the TYPO3 console, based on the detected TYPO3 version and tooling. User-only command; run /onboard once per repository.
argument-hint: "[--dry-run]"
disable-model-invocation: true
allowed-tools: Read, Grep, Glob, Write, Edit, Bash(ls:*), Bash(cat:*), Bash(jq:*), Bash(git:*), Bash(ddev describe:*), Bash(composer show:*)
---

# Onboard a TYPO3 repository

Create two files that make every later session faster and safer: `CLAUDE.md` (what this project is and how to work in it) and `.claude/settings.json` (which commands may run without asking). With `--dry-run` in `$ARGUMENTS`, print both files instead of writing them.

## Detected facts

Project type and TYPO3 version:

!`[ -f composer.json ] && jq -r '"type: \(.type // "project")\nname: \(.name // "-")\ncore constraint: \(.require["typo3/cms-core"] // "-")\nphp constraint: \(.require.php // "-")"' composer.json 2>/dev/null || echo "no composer.json"`

Installed core version (from composer.lock):

!`[ -f composer.lock ] && jq -r '.packages[] | select(.name=="typo3/cms-core") | "typo3/cms-core \(.version)"' composer.lock 2>/dev/null || echo "no composer.lock"`

Tooling present:

!`for f in .ddev/config.yaml Build/Scripts/runTests.sh phpstan.neon phpstan.neon.dist rector.php fractor.php .php-cs-fixer.dist.php Documentation/guides.xml ext_emconf.php config/sites CLAUDE.md .claude/settings.json; do [ -e "$f" ] && echo "present: $f"; done 2>/dev/null || true`

DDEV project:

!`[ -f .ddev/config.yaml ] && grep -E '^(name|type|php_version|database|docroot):' .ddev/config.yaml 2>/dev/null || echo "no ddev"`

## Steps

1. If `CLAUDE.md` or `.claude/settings.json` already exist, read them and merge: keep the user's content, add only missing sections and rules. Never delete existing allow or deny rules.
2. Read `composer.json` scripts, `Build/Scripts/runTests.sh -h` (if present) and the folder layout (`packages/`, `Classes/`, `Configuration/Sets/`) to fill in real commands.
3. Find which v13/v14 concepts the code already uses so the CLAUDE.md can say "follow this": `Configuration/Sets/*/config.yaml` (site sets), `Configuration/Fluid/ComponentCollections.php` (Fluid components), `#[Authorize]`, `#[RateLimit]`, `*.fluid.html`.
4. Write `CLAUDE.md` from [references/claude-md-template.md](references/claude-md-template.md). Keep it under 120 lines; every command in it must exist in this repository.
5. Write `.claude/settings.json` from [references/settings-template.md](references/settings-template.md), dropping rules for tools the project does not have.
6. Show the user both diffs and a three-line summary. Do not commit.

## Rules

- Allow read-only and local, reversible commands. Ask for anything that changes shared state: `git push`, `composer update`, `ddev delete`, database imports, `typo3 database:updateschema` with destructive types.
- Deny reading secrets: `.env`, `config/system/settings.php` credentials, `auth.json`.
