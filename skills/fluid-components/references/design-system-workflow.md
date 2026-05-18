# Design-system workflow with Fluid components

A practical way to run a component library inside a TYPO3 v14 site package.
The TYPO3 mechanics are from Feature-108508, Feature-108524, Feature-104114,
Feature-109114, Feature-108763 and Feature-108229; the folder conventions are
a recommendation, not a core rule.

## Folder layout

```text
packages/my_sitepackage/
├── Configuration/Fluid/
│   ├── ComponentCollections.php
│   └── Namespaces.php
└── Resources/Private/Components/
    ├── Atom/
    │   ├── Button/Button.fluid.html
    │   └── Icon/Icon.fluid.html
    ├── Molecule/
    │   └── TeaserCard/
    │       ├── TeaserCard.fluid.html
    │       └── TeaserCard.css
    └── Organism/
        └── Header/Navigation/Navigation.fluid.html
```

Call sites use the dot path: `<my:atom.button>`, `<my:molecule.teaserCard>`,
`<my:organism.header.navigation>`.

## Naming and API rules

- One component, one responsibility. If a component needs more than about six
  arguments, split it or pass content through a slot.
- Arguments describe data, not markup: `title`, `link`, `icon`, `variant`.
- Use `<f:slot />` for free HTML content; use arguments for everything the
  component must format itself.
