# Reading TCA with the Schema API

Sources: Feature-104002 (13.2), Deprecation-104304 (13.3),
Deprecation-106393 (14.0), Feature-108027 (14.0), Feature-107047 (14.0).
The API was marked internal during v13 development; prefer the calls shown
here, which the core itself documents as migration targets.

## Design in one paragraph

`TcaSchemaFactory` builds one immutable, cached schema per table from the
final TCA. A table with a type field (`tt_content.CType`) has sub-schemata
(`tt_content.textpic`) that contain only that type's fields with
`columnsOverrides` applied. Capabilities (localization, workspaces,
soft-delete, enable fields) are objects, not array lookups. Relations are
known both ways: active (this field points to X) and passive (who points to
me, `getPassiveRelations()`). The schema is not available during bootstrap
(`ext_localconf.php`, TCA files). If TCA changes at runtime, the schema must
be rebuilt - so don't change TCA at runtime.

## Common lookups

```php
use TYPO3\CMS\Core\Schema\Capability\TcaSchemaCapability;
use TYPO3\CMS\Core\Schema\TcaSchemaFactory;

final class TableInfo
{
    public function __construct(
        private readonly TcaSchemaFactory $tcaSchemaFactory,
    ) {}

    public function describe(string $table): array
    {
        if (!$this->tcaSchemaFactory->has($table)) {
            return [];
        }
        $schema = $this->tcaSchemaFactory->get($table);

        return [
            'localizable' => $schema->hasCapability(TcaSchemaCapability::Language),
            'workspaces' => $schema->hasCapability(TcaSchemaCapability::Workspace),
            'hasTypes' => $schema->supportsSubSchema(),
            'articleTitle' => $schema->hasSubSchema('article')
                ? $schema->getSubSchema('article')->getTitle()
                : null,
            'titleLabel' => $schema->hasField('title')
                ? $schema->getField('title')->getLabel()
                : null,
        ];
    }
}
```

Type-specific values (Feature-108027): the sub-schema merges `types[<type>]`
over `ctrl`, so `$schema->getSubSchema('article')->getTitle()` returns the
type title when one is set. `getTitle()` returns the raw label reference;
translate it yourself.

Method names beyond the changelog (`hasSubSchema()`, `getSubSchema()`,
`supportsSubSchema()`, `getSubSchemata()` returning a `SchemaCollection`)
were checked against the v13.4 and v14.3 core source. The changelog sample's
`isLocalizable()` does not exist in either - use the `Language` capability.

## Replacing BackendUtility and $GLOBALS['TCA'] reads

| Before | After |
| --- | --- |
| `BackendUtility::getTcaFieldConfiguration($table, $field)` | `$factory->get($table)->getField($field)->getConfiguration()` |
| `BackendUtility::getItemLabel($table, $field)` | `$factory->get($table)->getField($field)->getLabel()` (check `hasField()` first) |
| `BackendUtility::isTableLocalizable($table)` | `->hasCapability(TcaSchemaCapability::Language)` |
| `BackendUtility::isTableWorkspaceEnabled($table)` | `->hasCapability(TcaSchemaCapability::Workspace)` |
| `BackendUtility::isRootLevelRestrictionIgnored($table)` | `->getCapability(TcaSchemaCapability::RestrictionRootLevel)->shallIgnoreRootLevelRestriction()` |
| `BackendUtility::isWebMountRestrictionIgnored($table)` | `->hasCapability(TcaSchemaCapability::RestrictionWebMount)` |
