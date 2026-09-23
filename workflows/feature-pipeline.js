// Feature pipeline for TYPO3 v14 extensions and site packages.
//
// architect plan -> parallel implementers (one git worktree per disjoint package)
// -> integration of branches and wiring -> tests -> parallel review and security
// audit (plus accessibility when templates changed) -> summary.
//
// args: "<feature description>" or { feature: string, base?: string, proceed?: boolean }
// At most 9 agents per run: 1 architect, up to 3 implementers, 1 integrator,
// 1 test engineer, up to 3 reviewers. Fix rounds are left to the caller, who resumes
// the implementers with SendMessage (see skills/feature-team/SKILL.md).

export const meta = {
  name: 'typo3-feature-pipeline',
  description: 'Plan, build in parallel worktrees, test and review a TYPO3 v14 feature',
  whenToUse: 'A non-trivial TYPO3 extension or site-package feature that splits into disjoint work packages and needs tests and review.',
  phases: [
    { title: 'Plan', detail: 'typo3-architect writes .typo3-toolkit/plan.md with disjoint packages' },
    { title: 'Implement', detail: 'one implementer per package, each in its own git worktree' },
    { title: 'Integrate', detail: 'merge package branches, apply the wiring package and NEEDS-OUTSIDE changes' },
    { title: 'Test', detail: 'test-engineer adds and runs unit and functional tests' },
    { title: 'Review', detail: 'code-reviewer, security-auditor and fluid-a11y-reviewer in parallel' },
    { title: 'Summary', detail: 'collect verdicts and open findings' },
  ],
}

const AGENT = (name) => 'typo3-agentic-toolkit:' + name
const MAX_PARALLEL_PACKAGES = 3

const input = typeof args === 'string' ? { feature: args } : (args || {})
if (!input.feature) {
  throw new Error('Pass the feature description as args (string or { feature }).')
}
const base = input.base || 'HEAD'

const PLAN_SCHEMA = {
  type: 'object',
  properties: {
    planFile: { type: 'string' },
    concepts: { type: 'array', items: { type: 'string' } },
    decisions: { type: 'array', items: { type: 'string' } },
    packages: {
      type: 'array',
      items: {
        type: 'object',
        properties: {
          id: { type: 'string' },
          name: { type: 'string' },
          files: { type: 'array', items: { type: 'string' } },
          skill: { type: 'string' },
          proof: { type: 'string' },
          wiring: { type: 'boolean' },
        },
        required: ['id', 'name', 'files', 'proof', 'wiring'],
      },
    },
  },
  required: ['planFile', 'packages', 'decisions'],
}

const IMPL_SCHEMA = {
  type: 'object',
  properties: {
    package: { type: 'string' },
    branch: { type: 'string' },
    changed: { type: 'array', items: { type: 'string' } },
    deferred: { type: 'array', items: { type: 'string' } },
    needsOutside: { type: 'array', items: { type: 'string' } },
    proofCommand: { type: 'string' },
    proofExitCode: { type: 'integer' },
  },
  required: ['package', 'branch', 'changed', 'needsOutside', 'proofCommand', 'proofExitCode'],
}

const STEP_SCHEMA = {
  type: 'object',
  properties: {
    ok: { type: 'boolean' },
    summary: { type: 'string' },
    details: { type: 'array', items: { type: 'string' } },
  },
  required: ['ok', 'summary'],
}

const REVIEW_SCHEMA = {
  type: 'object',
  properties: {
    verdict: { type: 'string' },
    findings: {
      type: 'array',
      items: {
        type: 'object',
        properties: {
          severity: { type: 'string', enum: ['blocker', 'major', 'minor'] },
          location: { type: 'string' },
          problem: { type: 'string' },
          fix: { type: 'string' },
        },
        required: ['severity', 'location', 'problem'],
      },
    },
  },
  required: ['verdict', 'findings'],
}

// ---------------------------------------------------------------- Plan
phase('Plan')
const plan = await agent(
  'Design this TYPO3 feature and write .typo3-toolkit/plan.md.\n\n' +
    'Feature: ' + input.feature + '\nBase ref: ' + base + '\n\n' +
    'Cut packages along disjoint file sets. Mark exactly one package as wiring=true: it owns ' +
    'ext_localconf.php, Configuration/Services.yaml, Configuration/Sets/**, composer.json and any other shared ' +
    'registration file, and is applied after the others are merged. Plan at most ' + MAX_PARALLEL_PACKAGES +
    ' non-wiring packages. Return the plan as structured output as well.',
  { agentType: AGENT('typo3-architect'), schema: PLAN_SCHEMA, label: 'architect' },
)
if (!plan) {
  throw new Error('The architect returned no plan.')
}
if (plan.decisions.length > 0 && !input.proceed) {
  log('Plan has open decisions; stopping before implementation.')
  return { status: 'needs-decision', planFile: plan.planFile, decisions: plan.decisions, packages: plan.packages.map((p) => p.id) }
}

const buildPackages = plan.packages.filter((p) => !p.wiring)
const wiring = plan.packages.find((p) => p.wiring) || null
const selected = buildPackages.slice(0, MAX_PARALLEL_PACKAGES)
if (buildPackages.length > selected.length) {
  log('Only the first ' + MAX_PARALLEL_PACKAGES + ' packages run in this pipeline; not built: ' +
    buildPackages.slice(MAX_PARALLEL_PACKAGES).map((p) => p.id).join(', '))
}

// ---------------------------------------------------------------- Implement
phase('Implement')
const built = (await parallel(selected.map((p) => () => agent(
  'Implement package ' + p.id + ' (' + p.name + ') from ' + plan.planFile + '.\n' +
    'File set (hard boundary): ' + p.files.join(', ') + '\n' +
    'Build skill: ' + (p.skill || 'see plan') + '\n' +
    'Proof command: ' + p.proof + '\n' +
    'Finish with one commit on your worktree branch, message "[WIP] ' + p.id + ' ' + p.name + '", and return the branch name.',
  { agentType: AGENT('implementer'), isolation: 'worktree', schema: IMPL_SCHEMA, label: 'implement ' + p.id, phase: 'Implement' },
)))).filter(Boolean)

const failedProofs = built.filter((b) => b.proofExitCode !== 0)
if (failedProofs.length > 0) {
  log('Proof failed for: ' + failedProofs.map((b) => b.package).join(', '))
}

// ---------------------------------------------------------------- Integrate
phase('Integrate')
const needsOutside = built.flatMap((b) => b.needsOutside.map((n) => b.package + ': ' + n))
const integration = await agent(
  'Integrate the feature branches into the current branch, in this order, with git merge --no-ff:\n' +
    built.map((b) => '- ' + b.branch + ' (' + b.package + ')').join('\n') + '\n\n' +
    'Stop at the first conflict and report it; do not resolve conflicts by guessing, disjoint packages should not conflict.\n' +
    (wiring
      ? 'Then implement the wiring package ' + wiring.id + ' (' + wiring.name + ') from ' + plan.planFile +
        ' in the main working tree, limited to: ' + wiring.files.join(', ') + '. Proof: ' + wiring.proof + '\n'
      : '') +
    (needsOutside.length ? 'Also apply these NEEDS-OUTSIDE changes:\n' + needsOutside.map((n) => '- ' + n).join('\n') + '\n' : '') +
    'Use the TYPO3 v14 build skills named in the plan. Do not push. Report ok=false if any merge conflicted or the proof failed.',
  // general-purpose on purpose: the implementer definition forces a worktree, the integrator must work in the main tree.
  { agentType: 'general-purpose', schema: STEP_SCHEMA, label: 'integrate' },
)

// ---------------------------------------------------------------- Test
phase('Test')
const tests = await agent(
  'Add and run tests for the feature described in ' + plan.planFile + ' (section "Tests"). ' +
    'Changes are on the current branch compared with ' + base + '. Use CSV fixtures for functional tests. ' +
    'Report the real counts from the PHPUnit output in summary; ok=true only if all suites exit 0.',
  { agentType: AGENT('test-engineer'), schema: STEP_SCHEMA, label: 'tests' },
)

// ---------------------------------------------------------------- Review
phase('Review')
const touchesTemplates = plan.packages.some((p) => p.files.some((f) => f.indexOf('Resources/Private') !== -1))
const reviewers = [
  { name: 'code-reviewer', prompt: 'Review the diff ' + base + '...HEAD plus uncommitted changes against ' + plan.planFile + '.' },
  { name: 'security-auditor', prompt: 'Audit the diff ' + base + '...HEAD plus uncommitted changes for security issues.' },
]
if (touchesTemplates) {
  reviewers.push({ name: 'fluid-a11y-reviewer', prompt: 'Review the Fluid templates changed in ' + base + '...HEAD for WCAG 2.2 AA.' })
}
const reviews = await parallel(reviewers.map((r) => () => agent(
  r.prompt + ' Map your reply format onto the structured output: verdict, and findings with severity blocker, major or minor.',
  { agentType: AGENT(r.name), schema: REVIEW_SCHEMA, label: r.name, phase: 'Review' },
)))

// ---------------------------------------------------------------- Summary
phase('Summary')
const reviewResults = reviewers.map((r, i) => ({ reviewer: r.name, result: reviews[i] }))
const openFindings = reviewResults.flatMap((r) => (r.result ? r.result.findings : [])
  .filter((f) => f.severity !== 'minor')
  .map((f) => ({ reviewer: r.reviewer, severity: f.severity, location: f.location, problem: f.problem, fix: f.fix || '' })))
const ready = Boolean(integration && integration.ok) && Boolean(tests && tests.ok) &&
  failedProofs.length === 0 && openFindings.length === 0 && reviews.every(Boolean)

log(ready ? 'Feature ready for the quality gate.' : 'Feature not ready: ' + openFindings.length + ' blocker/major findings.')

return {
  status: ready ? 'ready' : 'needs-fixes',
  planFile: plan.planFile,
  concepts: plan.concepts || [],
  packages: built.map((b) => ({ id: b.package, branch: b.branch, proofExitCode: b.proofExitCode, deferred: b.deferred || [] })),
  notBuilt: buildPackages.slice(MAX_PARALLEL_PACKAGES).map((p) => p.id),
  integration,
  tests,
  reviews: reviewResults.map((r) => ({ reviewer: r.reviewer, verdict: r.result ? r.result.verdict : 'no result' })),
  openFindings,
  next: ready
    ? 'Run the quality-gate skill, then commit or squash the [WIP] commits.'
    : 'Resume the owning implementer with SendMessage for each open finding, then re-run tests and the raising reviewer.',
}
