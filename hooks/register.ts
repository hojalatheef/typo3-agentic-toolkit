// Function hooks of the TYPO3 Agentic Toolkit.
//
// session.start: reads composer.lock (or the core's Typo3Version.php) and
//   shows "TYPO3 14.3.1 · composer · ddev" in the status line.
// tool.call Edit/Write of ext_emconf.php or composer.json: a toast reminding
//   to keep the version and the TYPO3 constraints of both files in sync.
//
// Runs in the engine's own environment (no Node): files are read through $.fs.
import type { Register } from 'claude-code'

const VERSION_FILES = [
  'vendor/typo3/cms-core/Classes/Information/Typo3Version.php',
  'typo3_src/typo3/sysext/core/Classes/Information/Typo3Version.php',
  'public/typo3/sysext/core/Classes/Information/Typo3Version.php',
]

type LockPackage = { name?: string; version?: string }

export function coreVersionFromLock(lock: string): string | undefined {
  try {
    const parsed = JSON.parse(lock) as { packages?: LockPackage[] }
    const core = (parsed.packages ?? []).find(p => p.name === 'typo3/cms-core')
    return core?.version?.replace(/^v/, '')
  } catch {
    return undefined
  }
}

export function coreVersionFromClass(php: string): string | undefined {
  return /const VERSION = '([^']+)'/.exec(php)?.[1]
}

export function statusLine(version: string | undefined, mode: string, ddev: boolean): string {
  return ['TYPO3 ' + (version ?? '?'), mode, ...(ddev ? ['ddev'] : [])].join(' · ')
}

export const register: Register = on => {
  on('session.start', async ($, e, next) => {
    const root = e.cwd.replace(/\/$/, '')
    const exists = (p: string) => $.fs.exists(`${root}/${p}`).catch(() => false)
    const read = (p: string) => $.fs.read(`${root}/${p}`).then(t => (typeof t === 'string' ? t : undefined)).catch(() => undefined)

    let version: string | undefined
    let mode = ''
    const lock = await read('composer.lock')
    if (lock !== undefined) {
      version = coreVersionFromLock(lock)
      if (version !== undefined) mode = 'composer'
    }
    for (const file of VERSION_FILES) {
      if (version !== undefined) break
      const php = await read(file)
      if (php === undefined) continue
      version = coreVersionFromClass(php)
      mode = file.startsWith('vendor/') ? 'composer' : 'classic'
    }
    if (version === undefined && (await exists('ext_emconf.php'))) mode = 'extension'

    if (mode !== '') {
      $.ui.status(statusLine(version, mode, await exists('.ddev/config.yaml')))
    }
    return next(e)
  })

  on(
    'tool.call',
    { tool: ['Edit', 'Write'], file_path: /(^|\/)(ext_emconf\.php|composer\.json)$/ },
    ($, e, next) => {
      const other = e.file_path.endsWith('composer.json') ? 'ext_emconf.php' : 'composer.json'
      $.ui.toast(`Keep version and typo3/cms-core constraints in ${other} in sync.`, { timeoutMs: 6000 })
      return next(e)
    },
  ).catch(($, e, next) => next(e))
}
