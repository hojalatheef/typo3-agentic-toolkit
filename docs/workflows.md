# Workflows

Workflow scripts in `workflows/` orchestrate several agents
deterministically: fixed phases, structured output per agent (JSON
schemas), caps on the number of agents, and resume. Claude Code runs them
through its Workflow tool, and only when you opt in, for example by asking
to "run the feature pipeline as a workflow". Watch progress with
`/workflows`.

Both scripts address agents by their namespaced type,
`typo3-agentic-toolkit:<agent>`.

## feature-pipeline.js

`meta.name`: `typo3-feature-pipeline`. Plan, build in parallel worktrees,
test and review a TYPO3 v14 feature.

### Arguments

```text
"<feature description>"
{ "feature": "<text>", "base": "<git ref, default HEAD>", "proceed": false }
```

Without a feature the script throws. `proceed: true` continues even when
the plan lists open decisions.

### Phases

| Phase | Agent(s) | What happens |
| --- | --- | --- |
| Plan | `typo3-architect` | Writes `.typo3-toolkit/plan.md` and returns it as structured output: `planFile`, `concepts`, `decisions`, `packages` (id, name, files, skill, proof, `wiring`). Exactly one package is the wiring package; at most three non-wiring packages. Stops with `status: needs-decision` when `decisions` is not empty and `proceed` is not set |
| Implement | up to 3 × `implementer`, `isolation: worktree` | One agent per package in parallel. Each gets its file set as a hard boundary, the build skill and the proof command, ends with a `[WIP] <id> <name>` commit and returns `branch`, `changed`, `deferred`, `needsOutside`, `proofExitCode`. Packages beyond three are listed as not built |
| Integrate | `general-purpose` | Merges the branches with `git merge --no-ff` in order, stops at the first conflict, then implements the wiring package in the main tree and applies all `NEEDS-OUTSIDE` changes. A general-purpose agent is used because `implementer` always runs in a worktree |
| Test | `test-engineer` | Adds and runs tests for the plan's "Tests" section; `ok` only if all suites exit 0 |
| Review | `code-reviewer`, `security-auditor`, plus `fluid-a11y-reviewer` when any package touches `Resources/Private` | In parallel; each returns a verdict and findings with severity `blocker`, `major` or `minor` |
| Summary | - | Collects verdicts and open blocker/major findings |

At most nine agents run: one architect, three implementers, one
integrator, one test engineer, three reviewers.

### Result

```json
{
  "status": "ready | needs-fixes | needs-decision",
  "planFile": ".typo3-toolkit/plan.md",
  "concepts": ["Site sets (Feature-103437)"],
  "packages": [{ "id": "P1", "branch": "…", "proofExitCode": 0, "deferred": [] }],
  "notBuilt": [],
  "integration": { "ok": true, "summary": "…" },
  "tests": { "ok": true, "summary": "…" },
  "reviews": [{ "reviewer": "code-reviewer", "verdict": "approve" }],
  "openFindings": [],
  "next": "Run the quality-gate skill, then commit or squash the [WIP] commits."
}
```

`ready` requires a clean integration, passing tests, every proof at exit 0,
every reviewer answering and no blocker or major finding. The fix loop is
not part of the script: resume the owning implementer with `SendMessage`
for each open finding, then re-run tests and the reviewer that raised it.

## extension-audit.js

`meta.name`: `typo3-extension-audit`. Audits an extension for legacy APIs
and missing v13/v14 concepts, with every changelog ID verified.

### Arguments

```text
{ "path": "<extension root, default .>", "areas": ["Classes/", "…"] }
```

Default areas: `Classes/`; `Configuration/` (TCA, Sets, Services.yaml,
Backend, Fluid); `Resources/Private/` (Templates, Partials, Layouts,
Components, Language); `ext_localconf.php`, `ext_tables.php`,
`ext_emconf.php`, `composer.json`; `Tests/` and `Build/`.

### Phases

| Phase | Agent(s) | What happens |
| --- | --- | --- |
| Scout | up to 5 × `code-scout` | One per area in parallel. Each hit has location, pattern, concept and changelog ID (`unknown` when none). Returns `status: clean` early when nothing is found |
| Verify | up to 2 × `changelog-researcher` | IDs are deduplicated across scouts (a deliberate barrier), batched 15 per researcher, and confirmed against the rst files with their version folder. IDs over the cap are logged as not checked |
| Synthesize | `code-reviewer` | Opens each location, drops false hits, and writes a report grouped into must (removed or deprecated in the declared range), should (v14 concept clearly better) and could (optional), each with change, changelog ID and build skill. Unverified hits are listed separately |

At most eight agents run.

### Result

```json
{
  "status": "audited | clean",
  "root": ".",
  "counts": { "hits": 0, "confirmed": 0, "unverified": 0, "idsNotChecked": 0 },
  "facts": [],
  "report": "…"
}
```

## Resume

Every run gets an id (`wf_…`). After a failure or an edit of the script,
re-run with `resumeFromRunId`: agent calls whose prompt and options did not
change return their cached result instantly, only new or changed calls run
again. Resume works within the same Claude Code session.

## Workflow or feature-team skill?

| Situation | Use |
| --- | --- |
| You want to stay in the loop, approve the plan, and have review findings fixed automatically | `feature-team` skill |
| You want a deterministic, resumable run with structured JSON output | `feature-pipeline.js` |
| More than three build packages | `feature-team` (the script builds only the first three) |
| Fix rounds after review | `feature-team` (the script stops and hands them back) |
| An audit before modernising or before planning a v14 feature | `extension-audit.js` |
| A quick look at one area | one `code-scout` agent, no workflow |

Workflows can start many agents and use many tokens, so Claude Code runs
them only when you ask for a workflow explicitly.
