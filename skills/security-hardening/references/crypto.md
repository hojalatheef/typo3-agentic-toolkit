# Encryption, HMAC and password hashing

## CipherService and KeyFactory (Feature-108002, 14.0)

`TYPO3\CMS\Core\Crypto\Cipher\CipherService` encrypts with XChaCha20-Poly1305 (AEAD, libsodium / `ext-sodium`).
Both services are autoconfigured; inject them.

| API | Purpose |
| --- | --- |
| `KeyFactory::deriveSharedKeyFromEncryptionKey(string $seed, int $subKeyId = 1)` | Key derived from `$GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']`; one seed per purpose. |
| `KeyFactory::createSharedKeyFromString(string $key)` | Key from outside, e.g. `getenv('MY_APP_KEY')`. |
