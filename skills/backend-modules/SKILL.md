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
