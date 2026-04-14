# Module tree, container modules and access gates

## Renamed modules (Feature-107628, 14.0)

All old identifiers stay as aliases. Use the new ones in `parent`, `position` and `buildUriFromRoute()`.

| Label in v14 (was) | Identifier (was) | Notes |
| --- | --- | --- |
| Content (Web) | `content` (`web`) | Top level. |
| Media (File) | `media` (`file`) | Top level. |
| Sites (Site Management) | `site` | Top level, identifier unchanged. |
| Administration (System) | `admin` (`system`) | Top level for backend administrators: users, scheduler, integrations. |
| System (Admin Tools) | `system` (`tools`) | Top level for system maintainers / DevOps. |
| Content > Layout (Page) | unchanged | Rename only. |
| Content > Records (List) | `records` (`web_list`) | |
| Content > Preview (View) | `page_preview` | Moved after Records. |
| Content > Publish (Workspaces) | `workspaces_publish` (`workspaces_admin`) | Only visible inside a workspace. |
| Content > Status (Info) | `content_status` (`web_info`) | Moved to EXT:backend; third-level modules no longer need `typo3/cms-info`. |
| Media > Media (Filelist) | `media_management` | Rename only. |
| Sites > Setup (Sites + Settings) | `site_configuration` | `site_settings` removed; actions `editSettings`, `saveSettings`, `dumpSettings`. |
| Sites > Link Management > Redirects | `redirects` (`site_redirects`) | New second-level parent with QR Codes. |
| Administration > Users (Backend Users) | `backend_user_management` | Unchanged identifier. |
| System > Database (DB Check) | `system_database` (`system_dbint`) | |
| Administration > Integrations | `integrations` | Webhooks `integrations_webhooks` (`webhooks_management`), Reactions `integrations_reactions` (`system_reactions`) (Feature-107795). |

Placement rule from the changelog:

```php
return [
    'my_administration_module' => [
        'parent' => 'admin',  // previously 'system'
        'access' => 'admin',
    ],
    'my_maintainer_module' => [
        'parent' => 'system', // previously 'tools'
        'access' => 'systemMaintainer',
    ],
];
```

The `integrations` identifier was taken from the core `Modules.php` files (`EXT:core`, `EXT:webhooks`,
`EXT:reactions`); the changelog itself only names the module and its children.

## Container modules (Feature-107712, Feature-107663, 14.0)

```php
'my_extension_tools' => [
    'parent' => 'content',
    'access' => 'user',
    'path' => '/module/content/my-tools',
    'iconIdentifier' => 'my-extension-module',
    'labels' => 'my_extension.modules.tools',
    'showSubmoduleOverview' => true,
    'appearance' => [
        'dependsOnSubmodules' => true,
    ],
],
'my_extension_tools_report' => [
    'parent' => 'my_extension_tools',
    'access' => 'user',
    'path' => '/module/content/my-tools/report',
    // ...
],
```

- `showSubmoduleOverview`: the default route targets `SubmoduleOverviewController`, which shows one card per
  accessible submodule (icon, title, description, "Open module"). `BackendModuleValidator` skips the redirect to
  the first submodule. `ModuleTemplate` adds a "Module Overview" entry to the submodule dropdown. No accessible
  submodule: an info message instead of an empty page.
- `appearance.dependsOnSubmodules`: hide the container in the menu if no submodule is available (missing
  permission, extension not installed).
- Custom `ModuleInterface` implementations need `hasSubmoduleOverview(): bool` (Breaking-107712); `Module` and
  `BaseModule` already have it.

## Module access gates (Feature-109365, 14.2)

`access` values are resolved by gates implementing `TYPO3\CMS\Backend\Module\ModuleAccessGateInterface`.
Built-in: `UserGate` (admins + `userMods` / `groupMods`), `AdminGate`, `SystemMaintainerGate` in
`TYPO3\CMS\Backend\Module\AccessGate`.

```php
use TYPO3\CMS\Backend\Module\ModuleAccessGateInterface;
use TYPO3\CMS\Backend\Module\ModuleAccessResult;
use TYPO3\CMS\Backend\Module\ModuleInterface;
use TYPO3\CMS\Core\Attribute\AsModuleAccessGate;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;

#[AsModuleAccessGate(identifier: 'myEditor', after: ['user'])]
final readonly class EditorGate implements ModuleAccessGateInterface
{
    public function decide(ModuleInterface $module, BackendUserAuthentication $user): ModuleAccessResult
    {
        if ($module->getAccess() !== 'myEditor') {
            return ModuleAccessResult::Abstain;
        }
        return in_array(3, $user->userGroupsUID, true)
            ? ModuleAccessResult::Granted
            : ModuleAccessResult::Denied;
    }
}
```

Results: `Granted`, `Denied`, `Abstain` (not responsible). Order gates with `before` / `after`. Reference the gate
with `'access' => 'myEditor'` in `Modules.php`.

## Labels and controllers

- Labels per module (Feature-93334): file `Resources/Private/Language/Modules/<name>.xlf` with ids `title`,
  `short_description`, `description`; `'labels' => 'my_extension.modules.<name>'`. A single-module extension
  uses `module.xlf` and `'labels' => 'my_extension.module'`.
