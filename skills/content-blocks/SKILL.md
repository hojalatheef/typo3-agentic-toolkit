---
name: content-blocks
description: Decide between the friendsoftypo3/content-blocks extension (not TYPO3 core) and plain TCA + site sets, and scaffold a Content Block - folder layout, config.yaml basics, make:content-block command, templates and labels. Use when the user asks "should I use Content Blocks", "create a content element with Content Blocks", "make:content-block", "config.yaml for a content block" or edits files under ContentBlocks/.
argument-hint: "[vendor/element-name] [extension path]"
allowed-tools: Read, Grep, Glob, Edit, Write, Bash(vendor/bin/typo3 make:content-block*), Bash(ddev exec vendor/bin/typo3 make:content-block*), Bash(composer show friendsoftypo3/content-blocks*)
paths:
  - "**/ContentBlocks/**"
---

# Content Blocks

Content Blocks is a **community extension**, `friendsoftypo3/content-blocks`,
maintained by the TYPO3 Structured Content Initiative. It is **not part of
TYPO3 core**. It generates TCA, database fields, the new content element
wizard entry, labels and the frontend/backend template wiring from one YAML
file per element.

Arguments: `$ARGUMENTS` - optional element name and extension path.

The YAML keys, field types and folder names below are the stable basics.
Anything version-dependent is marked; **check the extension documentation for
your installed version** (`composer show friendsoftypo3/content-blocks`)
before relying on it.

## When to use

Choose Content Blocks when:

- A project needs many simple content elements, records or page types and the
  team prefers YAML over TCA arrays.
- Integrators (not PHP developers) maintain the element catalogue.
- Elements should be self-contained folders that can be copied between
  projects.

Choose plain TCA + site sets (see the `schema-api`, `record-rendering` and
`site-sets` skills) when:

- The extension is distributed and should not add a dependency.
- Fields need TCA features the extension does not expose, custom FormEngine
  elements or `itemsProcessors`.
- You need full control over database columns and migrations.
- The element is used by Extbase domain models with their own tables.

The comparison table is in
[references/decision-guide.md](references/decision-guide.md).

## Steps

1. Install: `composer require friendsoftypo3/content-blocks`. Pick the version
   whose constraint matches your TYPO3 core.
2. Scaffold: `vendor/bin/typo3 make:content-block` and answer the prompts
   (type, vendor, name, target extension). The command creates the folder
   below `ContentBlocks/` in the chosen extension.
3. Edit `config.yaml`: reuse core fields (`header`, `bodytext`) with
   `useExistingField: true`; add own fields with an `identifier` and `type`.
   See [references/config-basics.md](references/config-basics.md) and
   [templates/config.yaml](templates/config.yaml).
4. Write the frontend template; the element's data is available as `{data}`.
5. Run the database analyzer (Admin Tools > Maintenance > Analyze Database
   Structure) to add the new columns, then flush caches.
6. Add the element to a page and check frontend, backend preview and the new
   content element wizard.

## Checklist

- [ ] Extension version matches the TYPO3 major (check its `composer.json`).
- [ ] `name` is `vendor/element-name`, vendor matches the host extension.
- [ ] Core fields reused with `useExistingField: true` instead of duplicates.
- [ ] Labels live in the element's language file, not in YAML.
- [ ] Database compare run after every `config.yaml` change.
- [ ] Frontend template escapes plain text and renders rich text with `f:format.html`.
- [ ] The site package's site set includes the TypoScript/CSS the elements need.

## References

- [references/decision-guide.md](references/decision-guide.md) - Content Blocks vs. TCA + site sets, migration paths.
- [references/config-basics.md](references/config-basics.md) - folder layout, `config.yaml` keys, field types, templates.
