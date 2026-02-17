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
| 2.4.2 | generic page title | set it per record in detail views with `<f:page.title>{item.title}</f:page.title>` (Feature-107358, 14.0) |
| 3.1.1 | missing or wrong `lang` | let the page renderer write `<html lang>` from the site language; do not hard-code `<html>` in page templates |

## Forms

```html
<label for="email"><f:translate key="form.email" /></label>
<f:form.textfield id="email" property="email" type="email"
    additionalAttributes="{aria-describedby: 'email-error', autocomplete: 'email'}" required="true" />
<f:form.validationResults for="item.email">
    <f:if condition="{validationResults.flattenedErrors}">
        <p id="email-error" role="alert">...</p>
    </f:if>
</f:form.validationResults>
```

- 1.3.5: add `autocomplete` tokens to personal data fields.
- 3.3.1 and 3.3.3: errors in text, next to the field, with a suggestion.
- 3.3.7 (new in 2.2): do not ask for the same information twice in one process.
- 3.3.8 (new in 2.2): no puzzle captchas or paste blocking on login forms.

## Interaction

- 2.1.1: use `<button type="button">` for actions and `<a href>` for navigation; never `onclick` on non-interactive elements.
- 2.4.7 and 2.4.11 (new in 2.2): keep a visible focus outline and add `scroll-padding-top` when a sticky header exists.
