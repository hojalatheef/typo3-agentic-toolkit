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
```
