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
