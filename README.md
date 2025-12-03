# TYPO3 Agentic Toolkit

A Claude Code plugin for building TYPO3 v13.4 / v14 extensions and site packages
with the concepts that arrived in the last release cycle — site sets, Fluid
components, the Schema API, record objects, Extbase action authorization and
rate limiting, Symfony translations, the System Resource API, CSP hashes and
more — and with every building block a Claude Code plugin can ship: skills,
subagents, hooks, function hooks, output styles, LSP, MCP, monitors, workflows
and evals.

Every TYPO3 statement in the skills cites the core changelog entry it comes
from (`Feature-108508`, `Feature-104002`, …). Look one up with
`t3-changelog <id>`.

## Install

```text
/plugin marketplace add hojalatheef/typo3-agentic-toolkit
/plugin install typo3-agentic-toolkit@typo3-agentic-toolkit
```

For local development:

```bash
claude --plugin-dir ~/Developer/claude-playground/typo3-agentic-toolkit
```

Requirements: `jq` and `git`. Optional: DDEV or Docker (PHP for hooks and
gates when the host has no working PHP), `intelephense` on `PATH` for the LSP
integration, a checkout of the TYPO3 Dev Companion for the MCP server.

## Configure

`/plugin configure typo3-agentic-toolkit` (or `claude plugin configure`) sets:

| Option | Default | Effect |
| --- | --- | --- |
| `php_runner` | `auto` | How hooks and gates run PHP: DDEV, host, then Docker |
| `strict_guard` | `false` | Deny instead of ask for edits to `vendor/`, `var/`, `public/typo3/` |
| `dev_companion_path` | unset | Starts the TYPO3 Dev Companion MCP server from this checkout |

The LSP integration needs `npm install -g intelephense`. Until
`dev_companion_path` is set, `/mcp` lists `typo3-dev-companion` as failed;
that is expected and harmless.

## What is inside

See [CATALOG.md](CATALOG.md) for the categorised list and
[docs/concepts.md](docs/concepts.md) for which Claude Code concept each
component demonstrates.

| Category | Count | Examples |
| --- | --- | --- |
| Build skills (TYPO3 concepts) | 13 | `site-sets`, `fluid-components`, `schema-api`, `extbase-modern` |
| Quality skills | 4 | `quality-gate`, `functional-tests`, `a11y-audit`, `review` |
