// Extension audit against TYPO3 v14 practice.
//
// parallel code-scouts (one per area) -> changelog-researcher verifies every cited
// changelog ID -> code-reviewer synthesises a prioritised report.
//
// args: optional { path?: string, areas?: string[] }
// At most 8 agents per run: up to 5 scouts, up to 2 researchers, 1 reviewer.

export const meta = {
  name: 'typo3-extension-audit',
  description: 'Audit a TYPO3 extension for legacy APIs and missing v13/v14 concepts, verified against the core changelog',
  whenToUse: 'Before modernising an extension, before planning a v14 feature, or to get a verified list of what to update.',
  phases: [
    { title: 'Scout', detail: 'one code-scout per area of the extension' },
    { title: 'Verify', detail: 'changelog-researcher confirms each cited changelog ID' },
    { title: 'Synthesize', detail: 'code-reviewer turns verified hits into a prioritised report' },
  ],
}

const AGENT = (name) => 'typo3-agentic-toolkit:' + name
const MAX_SCOUTS = 5
const IDS_PER_RESEARCHER = 15
const MAX_RESEARCHERS = 2

const input = args || {}
const root = input.path || '.'
const defaultAreas = [
  'Classes/',
  'Configuration/ (TCA, Sets, Services.yaml, Backend, Fluid)',
  'Resources/Private/ (Templates, Partials, Layouts, Components, Language)',
  'ext_localconf.php, ext_tables.php, ext_emconf.php, composer.json',
  'Tests/ and Build/',
]
const areas = (input.areas && input.areas.length ? input.areas : defaultAreas)
if (areas.length > MAX_SCOUTS) {
  log('Only the first ' + MAX_SCOUTS + ' areas are scouted; skipped: ' + areas.slice(MAX_SCOUTS).join(', '))
}

const SCOUT_SCHEMA = {
  type: 'object',
  properties: {
    area: { type: 'string' },
    hits: {
      type: 'array',
      items: {
        type: 'object',
        properties: {
          location: { type: 'string' },
          pattern: { type: 'string' },
          concept: { type: 'string' },
          changelogId: { type: 'string' },
        },
        required: ['location', 'pattern', 'concept', 'changelogId'],
      },
    },
    facts: { type: 'array', items: { type: 'string' } },
  },
  required: ['area', 'hits'],
}

const VERIFY_SCHEMA = {
  type: 'object',
  properties: {
    results: {
      type: 'array',
      items: {
        type: 'object',
        properties: {
          changelogId: { type: 'string' },
          confirmed: { type: 'boolean' },
          version: { type: 'string' },
          file: { type: 'string' },
          note: { type: 'string' },
        },
        required: ['changelogId', 'confirmed'],
      },
    },
  },
  required: ['results'],
}

// ---------------------------------------------------------------- Scout
phase('Scout')
const scouted = (await parallel(areas.slice(0, MAX_SCOUTS).map((area, i) => () => agent(
  'Scout the TYPO3 extension at ' + root + ', area: ' + area + '.\n' +
    'Find legacy patterns that a TYPO3 v13/v14 concept replaces, using your table. For every hit give ' +
    'file:line, the pattern, the current concept and the changelog ID (for example Feature-103437). ' +
    'Use changelogId "unknown" when you cannot name one. Add neutral facts the planner needs.',
  { agentType: AGENT('code-scout'), schema: SCOUT_SCHEMA, label: 'scout ' + (i + 1), phase: 'Scout' },
)))).filter(Boolean)

const hits = scouted.flatMap((s) => s.hits.map((h) => Object.assign({ area: s.area }, h)))
log(hits.length + ' hits from ' + scouted.length + ' scouts')
if (hits.length === 0) {
  return { status: 'clean', root, areas: scouted.map((s) => s.area), facts: scouted.flatMap((s) => s.facts || []) }
}

// ---------------------------------------------------------------- Verify
// Barrier on purpose: IDs are deduplicated across all scouts before verification.
phase('Verify')
const ids = Array.from(new Set(hits.map((h) => h.changelogId).filter((id) => id && id !== 'unknown'))).sort()
const batches = []
for (let i = 0; i < ids.length && batches.length < MAX_RESEARCHERS; i += IDS_PER_RESEARCHER) {
  batches.push(ids.slice(i, i + IDS_PER_RESEARCHER))
}
const unverifiedOverflow = ids.slice(MAX_RESEARCHERS * IDS_PER_RESEARCHER)
if (unverifiedOverflow.length) {
  log('Not verified (over the researcher cap): ' + unverifiedOverflow.join(', '))
}
const verified = (await parallel(batches.map((batch, i) => () => agent(
  'For each changelog ID below, confirm with t3-changelog that the rst exists and say which version folder it is in. ' +
    'confirmed=false when no rst matches. IDs: ' + batch.join(', '),
  { agentType: AGENT('changelog-researcher'), schema: VERIFY_SCHEMA, label: 'verify ' + (i + 1), phase: 'Verify' },
)))).filter(Boolean).flatMap((v) => v.results)

const byId = {}
verified.forEach((v) => { byId[v.changelogId] = v })
const confirmedHits = hits.filter((h) => byId[h.changelogId] && byId[h.changelogId].confirmed)
const doubtfulHits = hits.filter((h) => !(byId[h.changelogId] && byId[h.changelogId].confirmed))

// ---------------------------------------------------------------- Synthesize
phase('Synthesize')
const report = await agent(
  'Write a prioritised audit report for the TYPO3 extension at ' + root + '.\n' +
    'Open the code at each location before including it; drop hits that are not real.\n' +
    'Verified hits (changelog confirmed):\n' +
    confirmedHits.map((h) => '- ' + h.location + ' | ' + h.pattern + ' | ' + h.concept + ' (' + h.changelogId + ', ' + (byId[h.changelogId].version || '?') + ')').join('\n') + '\n' +
    'Unverified hits (no confirmed changelog entry; list separately, do not recommend without evidence):\n' +
    doubtfulHits.map((h) => '- ' + h.location + ' | ' + h.pattern + ' | ' + h.concept + ' (' + h.changelogId + ')').join('\n') + '\n\n' +
    'Group by priority: must (removed or deprecated in the declared TYPO3 range), should (v14 concept clearly better), ' +
    'could (optional modernisation). For each group give location, change, changelog ID and the build skill to use ' +
    '(site-sets, schema-api, extbase-modern, fluid-components, record-rendering, translations, system-resources, ' +
    'security-hardening, backend-modules, scheduler-console, form-framework, content-blocks, sitepackage).',
  { agentType: AGENT('code-reviewer'), label: 'synthesis' },
)

return {
  status: 'audited',
  root,
  counts: { hits: hits.length, confirmed: confirmedHits.length, unverified: doubtfulHits.length, idsNotChecked: unverifiedOverflow.length },
  facts: scouted.flatMap((s) => s.facts || []),
  report,
}
