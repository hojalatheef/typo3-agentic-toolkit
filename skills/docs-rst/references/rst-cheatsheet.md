# TYPO3 reST cheat sheet

## guides.xml

```xml
<?xml version="1.0" encoding="UTF-8"?>
<guides xmlns="https://www.phpdoc.org/guides"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="https://www.phpdoc.org/guides vendor/phpdocumentor/guides-cli/resources/schema/guides.xsd"
        links-are-relative="true">
    <extension class="\T3Docs\Typo3DocsTheme\DependencyInjection\Typo3DocsThemeExtension"
               project-home="https://extensions.typo3.org/extension/my_ext"
               project-contact="mailto:dev@example.com"
               project-repository="https://github.com/my-vendor/my-ext"
               project-issues="https://github.com/my-vendor/my-ext/issues"
               edit-on-github="my-vendor/my-ext"
               edit-on-github-branch="main"
               typo3-core-preferred="stable"/>
    <project title="My extension"
             version="1.0"
             release="1.0.0"
             copyright="since 2026 by My Company"/>
    <inventory id="t3coreapi" url="https://docs.typo3.org/m/typo3/reference-coreapi/main/en-us/"/>
</guides>
```

## Page skeleton

```rst
..  include:: /Includes.rst.txt

..  _configuration:

=============
Configuration
=============

..  toctree::
    :titlesonly:

    SiteSet
    ExtensionConfiguration
```

`Includes.rst.txt` holds shared substitutions and default roles, for example `..  highlight:: php`.

## Directives

| Need | Directive |
| --- | --- |
| Code sample | `..  code-block:: php` with `:caption: EXT:my_ext/Classes/Foo.php` |
| Setting | `..  confval:: myExt.itemsPerPage` with `:type: int` and `:default: 10` |
| Note or warning | `..  note::`, `..  warning::`, `..  tip::` |
| Image | `..  figure:: /Images/Module.png` with `:alt:` and `:class: with-shadow` |
