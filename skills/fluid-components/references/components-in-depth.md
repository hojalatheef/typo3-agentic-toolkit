# Fluid components in depth

Facts on this page come from Feature-108508 (both entries), Feature-108524,
Feature-108166, Feature-108148, Feature-109114 and Feature-109167. For the
Fluid-side syntax reference see the Fluid Standalone documentation
("Defining Components").

## Collection options

`Configuration/Fluid/ComponentCollections.php` returns an array keyed by the
collection namespace (Feature-108508, 14.1):

| Option | Default | Effect |
| --- | --- | --- |
| `templatePaths` | - | sorted by key, processed in reverse order (highest key wins) |
| `templateNamePattern` | one folder per component | variables `{path}` and `{name}`; `'{path}/{name}'` drops the per-component folder |
| `additionalArgumentsAllowed` | `false` | `true` lets callers pass arguments without `<f:argument>` |

Resolution examples for `<my:organism.header.navigation />`:
