# Quality gate steps in detail

## Composer and lint

- `composer validate --strict` fails on warnings too (missing license, unbound constraints). Fix them; do not drop `--strict`.
- An extension's `composer.json` needs `extra.typo3/cms.extension-key`, and its `typo3/cms-core` constraint must agree with `ext_emconf.php`.
