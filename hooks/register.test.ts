// Tests for hooks/register.ts — run with `claude plugin test .`
import { describe, expect, test } from 'claude-code/testing'
import { coreVersionFromClass, coreVersionFromLock, statusLine } from './register.ts'

const LOCK = JSON.stringify({
  packages: [
    { name: 'symfony/console', version: 'v7.3.0' },
    { name: 'typo3/cms-core', version: 'v14.3.1' },
  ],
})

const FILES: Record<string, string> = {
  '/project/composer.lock': LOCK,
  '/project/.ddev/config.yaml': 'php_version: "8.4"\n',
}

describe('pure helpers', () => {
  test('reads the core version from composer.lock', () => {
    expect(coreVersionFromLock(LOCK)).toBe('14.3.1')
    expect(coreVersionFromLock('{"packages":[]}')).toBeUndefined()
    expect(coreVersionFromLock('not json')).toBeUndefined()
  })

  test('reads the core version from Typo3Version.php', () => {
    expect(coreVersionFromClass("    protected const VERSION = '13.4.20';")).toBe('13.4.20')
  })

  test('formats the status line', () => {
    expect(statusLine('14.3.1', 'composer', true)).toBe('TYPO3 14.3.1 · composer · ddev')
    expect(statusLine(undefined, 'extension', false)).toBe('TYPO3 ? · extension')
  })
})

describe('hooks', () => {
  test('session.start shows the TYPO3 setup in the status line', async ($, on) => {
    const statuses: (string | undefined)[] = []
    on('fs.read', ($, e) => {
      const text = FILES[e.path]
      return text === undefined ? { deny: `ENOENT ${e.path}` } : { value: text }
    })
    on('fs.exists', ($, e) => ({ value: e.path in FILES }))
    on('ui.status', ($, e) => {
      statuses.push(e.text)
      return { value: undefined }
    })
    on('session.start', ($, e) => ({ cwd: e.cwd }))

    await $.session.start({ cwd: '/project', surface: 'terminal', isInteractive: true })
    expect(statuses).toEqual(['TYPO3 14.3.1 · composer · ddev'])
  })

  test('editing ext_emconf.php raises a sync reminder', async ($, on) => {
    const toasts: string[] = []
    on('ui.toast', ($, e) => {
      toasts.push(e.text)
      return { value: undefined }
    })
    on('tool.call', () => ({ result: 'ok' }))

    await $.tool.call({ tool: 'Edit', file_path: '/p/ext_emconf.php', old_string: 'a', new_string: 'b' })
    expect(toasts.length).toBe(1)
    expect(toasts[0]).toContain('composer.json')

    await $.tool.call({ tool: 'Edit', file_path: '/p/Classes/Foo.php', old_string: 'a', new_string: 'b' })
    expect(toasts.length).toBe(1)
  })
})
