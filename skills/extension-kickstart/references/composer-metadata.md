# composer.json as the extension's metadata

Since TYPO3 14.2, `composer.json` can hold everything `ext_emconf.php` used to provide (Feature-108345). An extension that still ships `ext_emconf.php` without these fields triggers a deprecation during cache warm-up. TYPO3 v15 no longer reads `ext_emconf.php` at all (Deprecation-108345). Classic mode has required a valid `composer.json` since 14.0 (Breaking-108310).

## Fields

| Field | Example | Notes |
| --- | --- | --- |
| `name` | `acme/event-calendar` | Composer package name. The site set uses the same name. |
| `type` | `typo3-cms-extension` | Must start with `typo3-cms-` (Breaking-108310) |
| `description` | `"Event Calendar - Lists and filters events"` | The part before the first ` - ` becomes the extension title (Breaking-108304) |
| `license` | `GPL-2.0-or-later` | `composer validate --strict` warns without it |
| `require.php` | `^8.2` | Replaces `constraints.depends.php` from `ext_emconf.php` |
| `require.typo3/cms-*` | `^14.3` | Every core extension the code uses, not just `cms-core` |
| `require.<other extension>` | `"acme/base": "^2.0"` | TYPO3 treats any package not shipped by core or listed in `providesPackages` as an extension dependency and loads it first |
| `autoload.psr-4` | `"Acme\\EventCalendar\\": "Classes/"` | Add `autoload-dev` for `Tests/` once tests exist |
| `extra.typo3/cms.extension-key` | `event_calendar` | Required (Breaking-108310) |
| `extra.typo3/cms.version` | `1.0.0` | Or top-level `version`. Must match the git tag. Stability goes in the suffix: `1.0.0-beta2`, `1.0.0-RC1` |
| `extra.typo3/cms.Package.providesPackages` | `{}` | Required, even when empty. Lists Composer packages the extension bundles itself in classic mode, optionally with a path to a bundled `autoload.php` |
| `extra.typo3/cms.exclude-from-updates` | `true` | Replaces `state = excludeFromUpdates` |

The `release` skill bumps `extra.typo3/cms.version` together with the tag.

## Bundled Composer packages (classic mode only)

```json
"extra": {
    "typo3/cms": {
        "extension-key": "event_calendar",
        "version": "1.0.0",
        "Package": {
            "providesPackages": {
                "league/ical": "Resources/Private/Php/ComposerVendor"
            }
        }
    }
}
```

In classic mode, TYPO3 includes `Resources/Private/Php/ComposerVendor/autoload.php` early when it is a Composer-generated autoloader. In Composer mode, the package comes from `require` as usual.

## Supporting v13 and v14 together

- Constraint: `"typo3/cms-core": "^13.4 || ^14.3"`.
- Keep the composer fields above. v13 ignores the ones it does not know.
- v13 **classic-mode** installs still read `ext_emconf.php`. Ship one only if such installs or TER uploads are a requirement, and keep its `version` and constraints identical to `composer.json`.
- Use only concepts that exist in 13.4. Fluid components, `*.fluid.html`, translation domains and `#[Authorize]` are v14-only. Plan a fallback or drop v13.
- Run the quality gate once per supported core (`typo3-extension-migration:verify` automates this).

## Verify

```bash
composer validate --strict
vendor/bin/typo3 extension:list | grep <extension_key>   # inside a project: title comes from the description
```

Look entries up with `t3-changelog 108345` and `t3-changelog 108304`.
