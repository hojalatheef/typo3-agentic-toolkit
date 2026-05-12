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

- default: `Components/Organism/Header/Navigation/Navigation.fluid.html`
- `'templateNamePattern' => '{path}/{name}'`: `Components/Organism/Header/Navigation.fluid.html`

The per-folder default is useful because CSS, JS or a language file can sit
next to the template.

File name resolving (Feature-108166, 14.0) tries, per template path,
`name.fluid.html`, `name.html`, `name`, then the same with an uppercase first
letter. `*.fluid.*` wins when both exist. Do not use `*.fluid.*` in an
extension that still supports TYPO3 below 14.

## Overriding and extending

Another extension can add a path to an existing collection; a higher key
overrides components with the same name:

```php
<?php

return [
    'SomeVendor\\VendorExtension\\Components' => [
        'templatePaths' => [
            1765990741 => 'EXT:my_extension/Resources/Private/Extensions/VendorExtension/Components',
        ],
    ],
];
```

Global ViewHelper namespaces (Feature-108524, 14.1) merge across extensions in
loading order and are searched in reverse order, so a dependent extension can
