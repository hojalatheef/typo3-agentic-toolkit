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
