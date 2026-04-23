# Extbase attributes in TYPO3 v14

All attributes live in `TYPO3\CMS\Extbase\Attribute` since 14.0 (Deprecation-107229). The old
`TYPO3\CMS\Extbase\Annotation` namespace still works until v15 but is deprecated; docblock annotations were
removed (Breaking-107229).

## Authorize (Feature-107826, 14.2)

`#[Authorize]` on an action method. Options, combinable in one attribute or stacked as several attributes
(all must pass):

| Option | Type | Meaning |
| --- | --- | --- |
| `requireLogin` | `bool` | A frontend user must be logged in. |
| `requireGroups` | `array` | User must be in at least one of the groups (OR). UIDs recommended; titles work but editors can rename them. |
| `callback` | `string` or `array` | `'methodName'` (public method on the controller) or `[SomeClass::class, 'method']`. Returns `bool`. Receives the action arguments. |

```php
#[Authorize(requireLogin: true)]
#[Authorize(requireGroups: [1, 2])]
#[Authorize(callback: [MyObjectAuthorization::class, 'checkOwnership'])]
public function editAction(MyObject $myObject): ResponseInterface
```

A callback class needs DI visibility: `#[Autoconfigure(public: true)]` (Symfony attribute).

On failure a `TYPO3\CMS\Core\Http\PropagateResponseException` with HTTP 403 is thrown; the site error handler
renders it. To replace the response, listen to `TYPO3\CMS\Extbase\Event\Mvc\BeforeActionAuthorizationDeniedEvent`
and call `$event->setResponse()`. `$event->getFailureReason()` returns an
`TYPO3\CMS\Extbase\Authorization\AuthorizationFailureReason` case: `NOT_LOGGED_IN`, `MISSING_GROUP`,
`CALLBACK_DENIED`. In that listener do not change state, do not use Extbase persistence, and only customise
responses of uncached actions.

## RateLimit (Feature-108982, 14.2)

`#[RateLimit]` on an action method. Uses Symfony RateLimiter with caching framework storage, keyed by client IP.

| Property | Default | Notes |
| --- | --- | --- |
| `limit` | `5` | Max requests in the window. |
| `interval` | `'15 minutes'` | Any interval string, e.g. `'1 minute'`, `'1 hour'`. |
| `policy` | `'sliding_window'` | Or `'fixed_window'`. |
| `message` | none | Translation key without domain (`messages.rate_limit_message`) or `LLL:EXT:...` reference. |

```php
#[RateLimit(limit: 3, interval: '1 minute', message: 'message.ratelimitexceeded')]
public function createAction(): ResponseInterface
```

Exceeding the limit returns HTTP 429. `TYPO3\CMS\Extbase\Event\Mvc\BeforeActionRateLimitResponseEvent` lets a
listener change the response (`getRequest()`, `getRateLimit()`), for example throw a `PropagateResponseException`
built with `ErrorController::accessDeniedAction()`.

Only uncached actions are protected - a cached page is delivered before Extbase runs.

The limiter ID is `extbase-<slugified class>-<action method>`, e.g.
`extbase-vendor-myextension-controller-mycontroller-dosomethingaction`; admins can override it in
