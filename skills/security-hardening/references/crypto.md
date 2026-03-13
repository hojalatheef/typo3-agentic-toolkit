# Encryption, HMAC and password hashing

## CipherService and KeyFactory (Feature-108002, 14.0)

`TYPO3\CMS\Core\Crypto\Cipher\CipherService` encrypts with XChaCha20-Poly1305 (AEAD, libsodium / `ext-sodium`).
