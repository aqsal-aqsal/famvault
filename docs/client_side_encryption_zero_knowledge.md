# 03. Client-Side Encryption Architecture (Web Crypto API)

This document details the implementation of *Zero-Knowledge Encryption* ensuring the server only ever stores encrypted data.

## 1. Cryptographic Specifications
* **Symmetric Algorithm:** AES-GCM (Key length: 256-bit)
* **Key Derivation Function:** PBKDF2
* **Hash:** SHA-256
* **PBKDF2 Iterations:** 100,000 iterations
* **IV (Initialization Vector):** 12 random bytes per file (Base64 encoded)
* **Salt:** 16 random bytes per encryption operation (Base64 encoded)

## 2. File Encryption Flow (During Upload)
```
Input: Original File (Blob) + Master Passphrase
   │
   ├──> Generate random Salt (16 bytes) & IV (12 bytes)
   ├──> Derive AES-GCM-256 Key via PBKDF2(Passphrase, Salt)
   ├──> Encrypt file binary via window.crypto.subtle.encrypt()
   │
Output:
   ├── Ciphertext Binary (stored as a .enc file)
   └── iv_salt payload: base64(salt) + ":" + base64(iv)
```

## 3. JavaScript / Alpine.js Helper (`resources/js/vault-crypto.js`)
```javascript
export class VaultCrypto {
  static async deriveKey(passphrase, salt) {
    const encoder = new TextEncoder();
    const keyMaterial = await window.crypto.subtle.importKey(
      'raw',
      encoder.encode(passphrase),
      { name: 'PBKDF2' },
      false,
      ['deriveKey']
    );

    return window.crypto.subtle.deriveKey(
      {
        name: 'PBKDF2',
        salt: salt,
        iterations: 100000,
        hash: 'SHA-256'
      },
      keyMaterial,
      { name: 'AES-GCM', length: 256 },
      false,
      ['encrypt', 'decrypt']
    );
  }

  static async encryptFile(file, passphrase) {
    const salt = window.crypto.getRandomValues(new Uint8Array(16));
    const iv = window.crypto.getRandomValues(new Uint8Array(12));
    const key = await this.deriveKey(passphrase, salt);

    const fileBuffer = await file.arrayBuffer();
    const ciphertextBuffer = await window.crypto.subtle.encrypt(
      { name: 'AES-GCM', iv: iv },
      key,
      fileBuffer
    );

    const saltB64 = btoa(String.fromCharCode(...salt));
    const ivB64 = btoa(String.fromCharCode(...iv));

    return {
      encryptedBlob: new Blob([ciphertextBuffer], { type: 'application/octet-stream' }),
      ivSalt: `${saltB64}:${ivB64}`
    };
  }

  static async decryptFile(encryptedBuffer, ivSaltString, passphrase, mimeType) {
    const [saltB64, ivB64] = ivSaltString.split(':');
    const salt = Uint8Array.from(atob(saltB64), c => c.charCodeAt(0));
    const iv = Uint8Array.from(atob(ivB64), c => c.charCodeAt(0));

    const key = await this.deriveKey(passphrase, salt);
    const decryptedBuffer = await window.crypto.subtle.decrypt(
      { name: 'AES-GCM', iv: iv },
      key,
      encryptedBuffer
    );

    return new Blob([decryptedBuffer], { type: mimeType });
  }
}
```

## 4. Master Passphrase Security
* The Master Passphrase is **never stored in the database or server disk**.
* The passphrase is only temporarily held in the user's browser `sessionStorage` during an active session, or prompted on-demand whenever accessing vault contents.
