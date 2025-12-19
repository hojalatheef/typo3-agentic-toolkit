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
