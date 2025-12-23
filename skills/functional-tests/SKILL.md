---
name: functional-tests
description: Set up and write unit and functional tests for a TYPO3 v13/v14 extension with typo3/testing-framework - PHPUnit configs, FunctionalTestCase, CSV datasets with importCSVDataSet and assertCSVDataSet, Extbase repository tests, frontend request tests, and running them in DDEV or with SQLite. Use when the user asks to "add tests", "write a functional test", "test this repository", "set up testing-framework", or when a feature has no tests.
argument-hint: "[class or feature to test]"
allowed-tools: Read, Edit, Write, Grep, Glob, Bash
paths:
  - "Tests/**"
  - "Build/phpunit/**"
---

# Functional tests with typo3/testing-framework

Tests prove behaviour against a real TYPO3 instance that the testing framework builds per test case: core and extension code loaded, a fresh database, your CSV fixtures imported.

Target: $ARGUMENTS

## When to use

| Test type | Base class | Use for |
| --- | --- | --- |
| Unit | `TYPO3\TestingFramework\Core\Unit\UnitTestCase` | pure logic, value objects, services with mocked collaborators |
| Functional | `TYPO3\TestingFramework\Core\Functional\FunctionalTestCase` | repositories, DataHandler hooks, TCA, services that use the database or DI container |
| Functional request | `FunctionalTestCase` with `executeFrontendSubRequest()` | rendered frontend output of plugins, content elements, site sets |

## Setup

1. Require the framework and PHPUnit in dev:

    ```bash
    composer require --dev typo3/testing-framework phpunit/phpunit
    ```

    Pick the testing-framework major that supports the project's TYPO3 line; read its README for the matrix.

2. Copy the PHPUnit configs from [templates/](templates/) to `Build/phpunit/`:
   [UnitTests.xml](templates/UnitTests.xml), [FunctionalTests.xml](templates/FunctionalTests.xml). They use the bootstraps shipped by the framework.
3. Add the autoload-dev mapping to `composer.json`: `"MyVendor\\MyExtension\\Tests\\": "Tests/"`, then `composer dump-autoload`.
4. Layout:

    ```text
    Tests/
      Unit/...Test.php
      Functional/
        ...Test.php
        Fixtures/*.csv
    ```

## Steps

1. Pick the test type from the table above.
2. Start from a template: [templates/RepositoryTest.php](templates/RepositoryTest.php) for Extbase repositories, [templates/FrontendRenderingTest.php](templates/FrontendRenderingTest.php) for request tests, [templates/pages.csv](templates/pages.csv) for fixtures.
3. Set `$testExtensionsToLoad` to your extension (by composer name or extension key) and `$coreExtensionsToLoad` to core extensions you need beyond the defaults.
4. Write fixtures as CSV: one block per table, first line the table name, second line the columns starting with an empty cell. See [references/csv-datasets.md](references/csv-datasets.md).
5. Assert database state after writes with `$this->assertCSVDataSet(__DIR__ . '/Fixtures/Expected.csv')`, not with hand-written queries.
6. Use PHPUnit attributes: `#[Test]`, `#[DataProvider('provider')]`, `#[CoversClass(...)]`.
7. Run and report real numbers.

## Running

| Environment | Command |
| --- | --- |
| Core-style runner | `Build/Scripts/runTests.sh -s functional -d sqlite` |
| DDEV | `ddev exec typo3DatabaseDriver=pdo_sqlite vendor/bin/phpunit -c Build/phpunit/FunctionalTests.xml` |
| Host or Docker | `typo3DatabaseDriver=pdo_sqlite vendor/bin/phpunit -c Build/phpunit/FunctionalTests.xml` |

SQLite is the quickest; run MariaDB, MySQL or PostgreSQL in CI when the code uses database-specific SQL. Use the `quality-gate` skill to run tests together with the other checks.

## Checklist

- [ ] Each test asserts one behaviour and has a descriptive name.
- [ ] Fixtures contain only the columns the test needs.
- [ ] No test depends on another test's state or order.
- [ ] Bug fixes come with a test that failed before the fix.
- [ ] No `markTestSkipped` without a ticket reference.
- [ ] Frontend tests set up a site configuration and a root page with TypoScript.

## References

- [references/csv-datasets.md](references/csv-datasets.md) - CSV fixture format and assertions.
- [references/request-tests.md](references/request-tests.md) - frontend request tests, site configuration, Extbase plugin output.
- Agent `test-engineer` - delegate test writing for a whole feature.
