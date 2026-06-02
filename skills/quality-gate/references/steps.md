# Quality gate steps in detail

## Composer and lint

- `composer validate --strict` fails on warnings too (missing license, unbound constraints). Fix them; do not drop `--strict`.
- An extension's `composer.json` needs `extra.typo3/cms.extension-key`, and its `typo3/cms-core` constraint must agree with `ext_emconf.php`.
- `php -l` must run with the lowest PHP version the extension supports, because newer syntax lints fine on a newer PHP only.

## Coding standards

The TYPO3 coding standards come from `typo3/coding-standards`:

```bash
composer require --dev typo3/coding-standards
vendor/bin/typo3-coding-standards setup extension   # or: project
```

That writes `.php-cs-fixer.dist.php` and `.editorconfig`. The config builds on `TYPO3\CodingStandards\CsFixerConfig::create()`; keep custom rules to a minimum. Run `vendor/bin/php-cs-fixer fix --dry-run --diff` in the gate and `vendor/bin/php-cs-fixer fix` to apply.

## Static analysis and Rector

- PHPStan for TYPO3 projects usually adds `saschaegerer/phpstan-typo3`. Do not raise or lower the level in the gate run, and treat any new `ignoreErrors` or baseline entry as a failure to explain.
- Rector uses `ssch/typo3-rector`. A dry-run with changes means the code still uses something the configured rule sets would modernise. Report the rule names from the output.
- Fractor (`a9f/typo3-fractor`) covers TypoScript, FlexForm, Fluid and YAML. If `fractor.php` exists, add `vendor/bin/fractor process --dry-run` as step 5b.

## TYPO3 CLI checks

| Command | Since | Changelog | Notes |
| --- | --- | --- | --- |
| `typo3 lint:yaml <paths>` | 13.3 | Feature-104973 | Directories are linted recursively; `--exclude=<file>` repeats |
| `typo3 fluid:analyze` | 14.2 | Feature-108763 | Only `*.fluid.*` templates; `--include-system-extensions`; `--stdin --json` for tool integration |
| `typo3 fluid:cache:warmup` | 14.0 | Feature-108229 | Compiles `*.fluid.*` templates and prints compile-time deprecations |

Run them through `t3` so DDEV projects use the container. Typical YAML targets: `Configuration/`, `config/sites/`, `Configuration/Sets/`.
