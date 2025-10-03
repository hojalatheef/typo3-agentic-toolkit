# Contributing

## Ground rules

- A TYPO3 statement needs a source. Read the changelog entry
  (`t3-changelog <id>`) and cite it as `Feature-<id>`, `Breaking-<id>` or
  `Deprecation-<id>`. The self-test checks that every cited ID exists.
- Samples must match the changelog or the official documentation. If you are
  not sure, leave the sample out.
- Keep skills short; move detail into `references/` and copy-ready files into
  `templates/`.

## Adding a skill

1. `skills/<name>/SKILL.md` with `name` (equal to the folder) and a
   `description` that says what it does and when to use it.
2. Add `paths:` globs when the skill belongs to a file type, so it activates
   by itself.
3. Add `disable-model-invocation: true` for commands with side effects
   (releases, generated config) so only a person can run them.
4. List it in `CATALOG.md` and, if it shows a new Claude Code concept, in
   `docs/concepts.md`.
5. Add an eval case under `evals/` when the behaviour can regress.

## Adding an agent

Every agent states `model` on purpose: Haiku for sweeps, Sonnet for
implementation, Opus for planning and adversarial review. Read-only agents
get no `Edit`/`Write`. Give each a fixed reply format so the caller can parse
it.

## Hooks

Hook scripts read the payload from stdin with `jq`, stay fast, and answer with
`hookSpecificOutput`. Add a behaviour test to `scripts/selftest.sh` for every
decision a guard makes.

## Commits

TYPO3 style: `[FEATURE]`, `[BUGFIX]`, `[TASK]`, `[DOCS]`, `[RELEASE]`, subject
in imperative mood, max 72 characters.

## Before you open a pull request

```bash
npm test && npm run lint:sh && npm run validate
```
