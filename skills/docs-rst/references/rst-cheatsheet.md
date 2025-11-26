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
```
