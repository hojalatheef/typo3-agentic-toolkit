---
name: release
description: Prepare a release of a TYPO3 extension - bump the version in ext_emconf.php, composer.json (if it carries one) and Documentation/guides.xml, write the CHANGELOG entry, run the quality gate, create an annotated tag, and walk the TER and Packagist checklist. User-only command (/release <version>); never pushes or uploads without explicit confirmation.
argument-hint: "<version, e.g. 3.2.0>"
disable-model-invocation: true
user-invocable: true
allowed-tools: Read, Edit, Grep, Glob, Bash
---

# Release a TYPO3 extension

Version to release: $ARGUMENTS

You prepare everything locally. `git push`, `git push --tags`, TER uploads and Packagist updates happen only after the user types an explicit yes to the exact command you show.

## Current state

!`git describe --tags --abbrev=0 2>/dev/null || echo "no tags"`

!`git status --short 2>/dev/null | head -20 || true`

!`grep -E "'version'" ext_emconf.php 2>/dev/null || true; grep -o 'release="[^"]*"' Documentation/guides.xml 2>/dev/null || true; jq -r '.version // "composer.json has no version (good)"' composer.json 2>/dev/null || true`

## Preconditions

1. The version argument is valid SemVer and greater than the last tag. Stop otherwise.
2. The working tree is clean and on the release branch (usually `main`). Stop otherwise.
3. Decide major, minor or patch from the commits since the last tag (`git log --oneline <last-tag>..HEAD`): a `[!!!]` commit means major. If the requested version contradicts that, warn and ask.

## Steps

1. **Bump versions** - see [references/version-files.md](references/version-files.md):
    - `ext_emconf.php`: `'version' => '<version>'`.
    - `Documentation/guides.xml`: `<project ... version="<major.minor>" release="<version>"/>`.
    - `composer.json`: only if it already has a `version` key (Packagist reads tags; a `version` key is discouraged).
    - Check that the `typo3/cms-core` constraints in `composer.json` and `ext_emconf.php` agree.
2. **CHANGELOG** - add a section at the top of `CHANGELOG.md` grouped by commit prefix (`[!!!]` breaking first, then `[FEATURE]`, `[BUGFIX]`, `[TASK]`, `[DOCS]`). Write for integrators; drop pure CI noise.
3. **Gate** - run the `quality-gate` skill. Any FAIL stops the release.
4. **Docs** - if `Documentation/` exists, render it (`docs-rst` skill). Warnings stop the release.
5. **Commit** - `git commit -am "[RELEASE] Release of version <version>"`.
6. **Tag** - `git tag -a <version> -m "Release <version>"`. TYPO3 extensions conventionally tag without a `v` prefix; follow the existing tags if they differ.
7. **Stop and confirm** - print the push commands and the checklist below, and wait.

## Publish checklist

Show this, filled in, and run nothing from it without confirmation:

- [ ] `git push origin <branch>` and `git push origin <version>`
