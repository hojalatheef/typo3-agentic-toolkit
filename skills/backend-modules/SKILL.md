---
name: backend-modules
description: >-
  Register and build TYPO3 v14 backend modules - the renamed module tree (content, media, admin, system, records, content_status, integrations), showSubmoduleOverview cards, appearance.dependsOnSubmodules, custom module access gates (#[AsModuleAccessGate]), #[AsController], ComponentFactory buttons, automatic reload/shortcut buttons via setShortcutContext(), the DocHeader language selector and BeforeBackendPageRenderEvent. Use when writing Configuration/Backend/Modules.php or a backend controller, or when the user asks to "add a backend module", "group modules", "restrict a module" or fix deprecated ButtonBar::make* / manual shortcut buttons.
argument-hint: "[module name] [parent, e.g. content|admin|system|integrations]"
allowed-tools: Read, Grep, Glob, Edit, Write
paths:
  - "**/Configuration/Backend/Modules.php"
  - "**/Classes/Controller/Backend/**/*.php"
  - "**/Classes/Backend/**/*.php"
  - "**/Classes/Module/**/*.php"
---

# Backend modules (TYPO3 v14)

Arguments: `$ARGUMENTS`.

TYPO3 14.0 renamed the module tree (Feature-107628), added card overviews for container modules
(Feature-107712) and moved button creation into `ComponentFactory` (Feature-107823). 14.2 adds pluggable access
gates (Feature-109365). Old identifiers keep working as aliases, but new code uses the new ones.

## When to use

- Registering a module: pick the parent from the new tree; see [references/module-tree.md](references/module-tree.md).
- Several related modules: create a container module with `showSubmoduleOverview` and
  `appearance.dependsOnSubmodules`.
- Access that is neither `user`, `admin` nor `systemMaintainer`: write a gate (14.2).
- Controller code with `ButtonBar::make*()`, `Menu::makeMenuItem()`, `MenuRegistry::makeMenu()`, manual
  reload/shortcut buttons or `#[Controller]`: migrate.
- Global backend JavaScript in the top frame: `BeforeBackendPageRenderEvent` (Feature-109087, 14.2).
- A module talks to external systems (webhooks, APIs, AI services): place it below `integrations`
  (Feature-107795).

## Steps

1. Copy [templates/Modules.php](templates/Modules.php) to `Configuration/Backend/Modules.php`. Adjust parent,
   `access`, `path`, `iconIdentifier`, `labels` (translation domain, e.g. `my_extension.modules.overview`).
2. Copy [templates/ReportController.php](templates/ReportController.php). It uses `#[AsController]`
   (Feature-102631, 13.0), `ModuleTemplateFactory`, `ComponentFactory`, `setShortcutContext()` and
   `makeDocHeaderModuleMenu()`.
3. Container module: `showSubmoduleOverview => true` makes the default route render cards (icon, title,
   description) of all accessible submodules; give each submodule a `description` / `short_description` label.
   Add `appearance => ['dependsOnSubmodules' => true]` so the container disappears when no submodule is
   accessible.
4. Custom access: copy [templates/EditorGate.php](templates/EditorGate.php), register with
   `#[AsModuleAccessGate(identifier: '...')]`, and use the identifier as `access` value.
5. Buttons: inject `ComponentFactory`; use `createSaveButton()`, `createBackButton()`, `createCloseButton()`,
   `createViewButton()` or `create*Button()` + fluent setters. Do not add reload or shortcut buttons - set the
   shortcut context instead.
6. Language-aware modules: build a `createDropDownButton()` with `createDropDownRadio()` items and pass it to
   `DocHeaderComponent::setLanguageSelector()`.
7. Details: [references/module-tree.md](references/module-tree.md),
   [references/doc-header.md](references/doc-header.md).

## Checklist

- [ ] `parent` uses new identifiers: `content` (was `web`), `media` (was `file`), `admin` (admin tasks, was
      `system`), `system` (maintainer tasks, was `tools`), `content_status` (was `web_info`).
- [ ] Route references use `records` instead of `web_list`, `redirects` instead of `site_redirects`,
      `system_database` instead of `system_dbint`.
- [ ] Labels: one XLIFF per module with `title`, `short_description`, `description` and a domain reference.
- [ ] Controller has `#[AsController]` (`TYPO3\CMS\Backend\Attribute\AsController`), not
      `TYPO3\CMS\Core\Attribute\Controller` and not a `backend.controller` tag.
- [ ] No `ButtonBar::make*()`, `Menu::makeMenuItem()`, `MenuRegistry::makeMenu()` (deprecated, removed in v15).
