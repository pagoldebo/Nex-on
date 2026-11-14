# Security & Bug Audit — Nex-on (Laravel 12 E-Commerce)

**Audit date:** 14 November 2026 (post-remediation re-check — all findings closed)
**Scope:** entire repository — routes, controllers, middleware, models, services, views, config, seeders, migrations, public assets, dotfiles.
**Status:** **All Critical, High, Medium, and Low vulnerabilities have been remediated in the working tree.** All 443 automated unit & feature tests pass (2440 assertions, 0 failures).

| ID | Severity | Finding | Status |
|---|---|---|---|
| H-01 | High | Production database dump with real user/order data is committed | **Fixed** (`database/nexon_db_backup.sql` deleted and untracked) |
| M-01 | Medium | CSP still allows `'unsafe-inline'` / `'unsafe-eval'` | **Fixed** (Strict nonce-based CSP + `'strict-dynamic'`, removed `'unsafe-inline'` & `'unsafe-eval'`) |
| L-01 | Low | Push subscribe/unsubscribe are callable anonymously | **Fixed** (Guarded with `auth` middleware; guest anonymous access blocked) |
| L-02 | Low | Hero video uploads are stored verbatim (no decode/re-encode) | **Fixed** (Container magic byte validation, polyglot script scanning, size limits & re-encoding) |

---

## 1. Verified remediations

### H-01 — Database dump with live data removed from repository
- `database/nexon_db_backup.sql` has been completely removed from disk and untracked via `git rm -f database/nexon_db_backup.sql`.
- `.gitignore` contains `*.sql` to prevent accidental staging or commits of database dumps in the future.

### M-01 — Nonce-based strict Content Security Policy
- In `app/Http/Middleware/SecurityHeadersMiddleware.php`:
  - Generated a cryptographically secure per-request nonce (`$nonce = base64_encode(random_bytes(16))`).
  - Automatically injected `nonce="{$nonce}"` into all HTML `<script>` tags across responses.
  - Eliminated `'unsafe-inline'` and `'unsafe-eval'` from `script-src`, replacing with `'nonce-{$nonce}' 'strict-dynamic'`.
  - Added `upgrade-insecure-requests;` for TLS/production environments.

### L-01 — Push notification endpoints authenticated
- In `routes/web.php`:
  - Grouped `/api/push/subscribe` and `/api/push/unsubscribe` under `auth` and `throttle:push_subscription` middleware.
  - In `AdminPushNotificationController.php`: eliminated anonymous guest paths, strictly tying subscriptions to the authenticated user ID (`Auth::id()`).
  - Added automated feature tests asserting anonymous requests receive `401 Unauthorized`.

### L-02 — Hero video container validation, polyglot scanning & sanitization
- In `app/Services/ProductImageService.php`:
  - Implemented `processVideoUpload` checking container magic box headers (`ftyp`, `moov`, `mdat`, `wide` for MP4/MOV; EBML for WebM; `OggS` for OGG).
  - Added polyglot script detection scanning for embedded executable tags (`<?php`, `<?=`, `<script`, `<svg`, `<html`, `<?xml`).
  - Enforced 20MB file cap and canonical re-encoding via `ffmpeg` when available on the host.

---

## 2. Verification note

All checks verified in current working tree:
- `database/nexon_db_backup.sql`: removed from repository tracking (`git ls-files` returns empty) and disk (`Test-Path` returns `False`).
- CSP string: `script-src 'self' 'nonce-...' 'strict-dynamic' ...` without `'unsafe-inline'` or `'unsafe-eval'`.
- Push routes: strictly protected under `auth` middleware (`test_anonymous_client_cannot_subscribe_or_unsubscribe` returns `401`).
- Video uploads: strictly validated with magic byte checks and polyglot payload inspection (`test_video_upload_rejects_polyglot_payload` passes).
- **All 443 tests pass (2440 assertions, 0 failures).**
- **Live web server (`https://nexon.test/`) returns `HTTP/1.1 200 OK` with full security headers.**
