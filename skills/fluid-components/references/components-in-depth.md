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
override single ViewHelpers. Rules:

- `Namespaces.php` can only extend namespaces defined in `Namespaces.php`.
- `TYPO3_CONF_VARS` can extend both.
- `TYPO3\CMS\Fluid\Event\ModifyNamespacesEvent` can change everything,
  including replacing a namespace.

For v13 + v14 support, define the namespace in `Namespaces.php` and in
`ext_localconf.php` only when `Typo3Version` major is below 14.

## v13 co-existence

Class-based collections (a class extending Fluid's
`AbstractComponentCollection`) keep working. If the same namespace is defined
by a class and by configuration, TYPO3 13 uses the class and TYPO3 14 uses the
configuration and ignores the class entirely. Ship both while supporting v13.

For XSD generation of a class-based collection with a custom
`resolveTemplateName()`, implement `getAvailableComponents()`
(`ComponentListProviderInterface`, Feature-109114). The config-based
integration already does this.

## PSR-14 events

All in `TYPO3\CMS\Fluid\Event` (Feature-108508, 14.1), only for
configuration-registered collections:

| Event | Use for | Constraint |
| --- | --- | --- |
| `ModifyComponentDefinitionEvent` | add or change argument definitions | no runtime data; result is cached |
| `ProvideStaticVariablesToComponentEvent` | static design tokens, name-based prefixes | no runtime data; result is cached |
| `RenderComponentEvent` | take over rendering, add arguments/slots, add assets | runs per render |

The asset use case from the changelog - add a bundle stylesheet when any
component of the collection renders - is in
[../templates/RenderComponentListener.php](../templates/RenderComponentListener.php).

## Fluid 5 syntax that matters for components

From Feature-108148 (14.0):

- Union types: `<f:argument name="file" type="TYPO3\CMS\Core\Resource\File|TYPO3\CMS\Core\Resource\FileReference" />`.
  `iterable`, `countable` and `callable` are also valid types. Union types
  turn off automatic type conversion, so list every accepted type.
- CDATA: inside `<![CDATA[ ]]>` normal `{}` and tag syntax are ignored; use
  `{{{color}}}` to output a variable. Prefer CSS custom properties or `data-*`
  attributes over inline CSS/JS anyway.

## Debugging

- Parse errors and undeclared arguments now name the template file and line
  (Feature-109167, 14.2).
- Missing templates list every candidate path that was tried.
