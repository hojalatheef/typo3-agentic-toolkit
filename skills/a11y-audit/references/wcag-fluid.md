# WCAG 2.2 AA in Fluid templates

## Images and media

Problem: `f:image` without `alt`, or a hard-coded `alt` that ignores the editor's metadata.

```html
<f:image image="{file}" alt="{file.alternative}" width="800" />
```

For decorative images pass `alt=""`. For linked images the `alt` describes the link target, not the picture. Videos need captions (1.2.2) and, for prerecorded content, an audio description or media alternative (1.2.5).

## Structure and navigation

| Criterion | Pattern | Fix |
| --- | --- | --- |
| 1.3.1 | heading level hard-coded in a partial | pass the level in: `<f:render partial="Header" arguments="{level: 2}" />` |
| 1.3.1 | `<div>` rows for tabular data | `<table>` with `<caption>` and `<th scope="col">` |
| 2.4.1 | no skip link | first focusable element in the layout: `<a class="visually-hidden-focusable" href="#main">` |
