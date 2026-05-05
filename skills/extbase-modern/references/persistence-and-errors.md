# Extbase persistence, rate limiter factory and error handling

## Unified RateLimiterFactory (Feature-109080, 14.2)

`TYPO3\CMS\Core\RateLimiter\RateLimiterFactory` is the single entry point for rate limiting (backend/frontend
login, password recovery, Extbase `#[RateLimit]`, custom code). Type-hint
`TYPO3\CMS\Core\RateLimiter\RateLimiterFactoryInterface`.

Request-scoped limiter keyed by the client IP:

```php
public function __construct(
    private readonly RateLimiterFactoryInterface $rateLimiterFactory,
) {}

public function doSomething(ServerRequestInterface $request): void
{
    $limiter = $this->rateLimiterFactory->createRequestBasedLimiter(
        $request,
        [
            'id' => 'my-extension-action',
            'policy' => 'sliding_window',
            'limit' => 10,
            'interval' => '1 hour',
        ],
    );
    if (!$limiter->consume()->isAccepted()) {
        // reject
    }
}
```

Custom key (e.g. a user ID): `$this->rateLimiterFactory->createLimiter([...config...], $userId)`.

Named service in `Services.yaml`, then call `create()` on it:

```yaml
myRateLimiter:
  class: TYPO3\CMS\Core\RateLimiter\RateLimiterFactory
  arguments:
    $config:
      id: 'my-custom-limiter'
      policy: 'sliding_window'
      limit: 5
      interval: '10 minutes'
```

Admin overrides by limiter ID in `config/system/additional.php`:

```php
$GLOBALS['TYPO3_CONF_VARS']['SYS']['rateLimiter']['login-be'] = [
    'limit' => 3,
    'interval' => '5 minutes',
];
```

Known IDs: `login-be`, `login-fe`, `backend-password-recovery`, `felogin-password-recovery`,
`extbase-<classSlug>-<actionMethod>`.

## History tracking (Feature-107289, 14.2)

Extbase writes create/update/delete of entities to `sys_history`, visible in the backend record history, with
user context (frontend user, backend user, anonymous) and full data snapshots.

- Off by default. Enable the feature toggle `extbase.enableHistoryTracking`
  (System > Settings > Feature toggles).
- When enabled it covers every Extbase table. Opt a table out in TCA:

```php
'ctrl' => [
    // ...
    'extbase' => [
        'enableHistoryTracking' => false,
    ],
],
```

- Third-party tables can be opted out with `Configuration/TCA/Overrides/`.
- Listener identifiers that can be removed or replaced: `extbase-history-tracker-persisted`,
  `extbase-history-tracker-updated`, `extbase-history-tracker-removed`
  (class `TYPO3\CMS\Extbase\EventListener\ExtbaseHistoryTracker`, events `EntityAddedToPersistenceEvent`,
  `EntityUpdatedInPersistenceEvent`, `EntityRemovedFromPersistenceEvent`).
- Snapshots contain personal data: disable for sensitive tables and prune `sys_history`.

## Query orderings with expressions (Feature-32051, 14.2)

New methods on `TYPO3\CMS\Extbase\Persistence\QueryInterface`: `orderBy()` (replace orderings),
`addOrderBy()` (append), `concat()`, `trim()`, `coalesce()`. Expressions nest.

```php
$query = $this->createQuery();
$query
    ->orderBy($query->concat($query->trim('firstName'), $query->trim('lastName')))
    ->addOrderBy('createdAt', QueryInterface::ORDER_DESCENDING);

$query->orderBy($query->coalesce('nickname', 'firstName'), QueryInterface::ORDER_ASCENDING);
```

`setOrderings([...])` keeps working; use it when the extension must also run on 13.4.

## Class configuration provider (Feature-108975, 14.2)

With `typo3/cms-lowlevel` installed, System > Configuration shows the merged
`Configuration/Extbase/Persistence/Classes.php` mapping of all active packages. Use it to verify
`tableName`, `recordType`, `subclasses` and property mapping overrides instead of dumping arrays.

## Argument mapping exceptions (Feature-104321, 13.3)

`ActionController::handleArgumentMappingExceptions()` turns `TargetNotFoundException` (unknown UID) and
`RequiredArgumentMissingException` into a page-not-found response when enabled:

```typoscript
plugin.tx_myextension.mvc.showPageNotFoundIfTargetNotFoundException = 1
plugin.tx_myextension.mvc.showPageNotFoundIfRequiredArgumentIsMissingException = 1
```

Scopes: `config.tx_extbase.mvc.*` (global), `plugin.tx_<ext>.mvc.*`, `plugin.tx_<ext>_<plugin>.mvc.*`.
