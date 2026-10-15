# Documentation

Detailed documentation for the TYPO3 Agentic Toolkit, a Claude Code plugin
for building TYPO3 v13.4 / v14 extensions and site packages. The top-level
[README](../README.md) is the short pitch; [CATALOG](../CATALOG.md) is the
one-table-per-category list. The pages below explain how everything works.

## Pages

| Page | What it covers |
| --- | --- |
| [getting-started.md](getting-started.md) | Requirements, installation, configuration, the first 15 minutes, the `.typo3-toolkit/` folder, troubleshooting |
| [architecture.md](architecture.md) | How skills, agents, hooks and workflows fit together; model routing; the plan contract; the end-to-end lifecycle |
| [skills.md](skills.md) | Every skill: purpose, invocation, auto-activation, frontmatter, output, related components |
| [agents.md](agents.md) | Every subagent: model, tools, isolation, preloaded skills, inputs, exact reply format |
| [hooks.md](hooks.md) | Every hook event and script, the exact allow/ask/deny rules, function hooks, monitors, tuning |
| [workflows.md](workflows.md) | The `feature-pipeline` and `extension-audit` workflow scripts, phase by phase |
| [testing-and-ci.md](testing-and-ci.md) | npm scripts, the self-test, eval cases, CI jobs, the plugin's own release checklist |
| [faq.md](faq.md) | Short answers: which skill for what, v13 support, citing changelog IDs, writing new components, costs |
| [concepts.md](concepts.md) | Which Claude Code concept each component demonstrates |

## Where to start

### Plugin users

You want to install the plugin and use it on a TYPO3 project.

1. [getting-started.md](getting-started.md) - install, configure, run `/onboard`.
2. [skills.md](skills.md) - find the skill for your task.
3. [hooks.md](hooks.md) - know what the guards will stop and why.
4. [faq.md](faq.md) - quick answers.

### TYPO3 developers

You build extensions and want to know how the toolkit plans, builds and
verifies a change.

1. [architecture.md](architecture.md) - the lifecycle from ticket to release.
2. [skills.md](skills.md) - Planning and scaffolding, then Build skills.
3. [agents.md](agents.md) - what each agent does and returns.
4. [workflows.md](workflows.md) - the deterministic pipelines.

### Contributors

You want to change the plugin itself.

1. [../CONTRIBUTING.md](../CONTRIBUTING.md) - ground rules and commit style.
2. [architecture.md](architecture.md) - the contracts between components.
3. [testing-and-ci.md](testing-and-ci.md) - the self-test, evals and CI.
4. [concepts.md](concepts.md) - which concept lives where, so a new
   component does not duplicate an example.
