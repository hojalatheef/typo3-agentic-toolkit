# Quality gate steps in detail

## Composer and lint

- `composer validate --strict` fails on warnings too (missing license, unbound constraints). Fix them; do not drop `--strict`.
