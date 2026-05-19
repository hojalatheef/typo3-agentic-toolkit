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
- Keep variants as a string argument with a small fixed set (for example
  `primary`, `secondary`) and map them to CSS classes inside the component.
- Keep the strict API (`additionalArgumentsAllowed` off). Undeclared arguments
  are a typo detector.
- Pass design tokens that never change at runtime through
  `ProvideStaticVariablesToComponentEvent`, not through every call.
- Page and content templates only compose components; they do not contain
  styling markup themselves.

## Workflow per new component

1. Write the API first: the `<f:argument>` lines and a one-line comment of
   intent at the top of the file.
2. Implement markup using lower-level components.
3. Run `vendor/bin/typo3 fluid:schema:generate` so the IDE autocompletes the
   new tag (components covered since Feature-109114, 14.2).
4. Use the component in one real template.
5. Run `vendor/bin/typo3 fluid:analyze`; fix every `[ERROR]` line.
6. Review: the reviewer checks the argument list, not only the markup.
