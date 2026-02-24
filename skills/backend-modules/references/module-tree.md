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
