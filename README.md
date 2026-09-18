# DelsuMart

DelsuMart is a security-first peer-to-peer marketplace for verified students of Delta State University (DELSU), Abraka. Laravel is used full-stack: PHP 8.2+, MySQL, Blade, Tailwind CSS, Alpine.js, Vite, Laravel Notifications and the Paystack HTTP API.

## Academic objectives demonstrated

1. **Multi-factor authentication** — password plus a hashed, expiring, single-use email OTP with attempt and resend controls.
2. **Manual KYC** — student ID and current fee receipt/breakdown are stored privately and reviewed by an administrator.
3. **Protected payments** — Paystack is initialized and verified server-side; successful collection moves the internal transaction state to `paid_held`.
4. **Rule-based fraud scoring** — explainable rules assign points for measurable behaviour and create reviewable flags.
5. **Testing/evaluation** — PHPUnit feature/unit tests and GitHub Actions exercise security, authorization and core marketplace workflows.

## Protected transaction architecture

States are `pending_payment → paid_held → released`, with controlled branches for `release_pending`, `disputed`, `refunded` and `cancelled`. Invalid/unauthorized transitions are rejected server-side. DelsuMart does **not** claim that ordinary Paystack collection is a generic legal escrow account. Actual automated seller settlement should only be enabled when the merchant's Paystack account and supported transfer/split capabilities are deliberately configured.

## Security controls

- CSRF protection and Laravel password hashing
- Email OTP MFA before full login
- Rate-limited registration, login, OTP verification, password reset, KYC and checkout
- Admin and verified-student middleware
- Ownership/participant authorization checks
- Private KYC and dispute evidence storage with authorized controller delivery
- MIME/size validation and randomized framework-generated upload paths
- Server-authoritative listing prices
- Paystack server-side reference/status/amount/currency verification
- HMAC webhook signature verification and idempotent payment event recording
- Database locks around critical transaction transitions
- Explainable fraud scoring and auditable administrative decisions
- HTTP security headers (CSP, frame, MIME, referrer and permissions policies; HSTS under production HTTPS)
- Environment-only secrets; production guidance requires `APP_DEBUG=false`

## Local setup

    composer install
    cp .env.example .env
    php artisan key:generate
    npm install
    npm run build

Create a MySQL database, configure the `DB_*` values, then run:

    php artisan migrate --seed
    php artisan storage:link
    php artisan serve

The public storage link is for marketplace listing images only. KYC documents and dispute evidence remain on the private/local disk.

## Mail and MFA

Configure `MAIL_*` values in `.env`. OTPs expire after the configured period and are stored hashed, not plaintext. Password-reset links use Laravel's reset broker and the `password_reset_tokens` table.

## Paystack test mode

Set test credentials only in the local environment:

    PAYSTACK_PUBLIC_KEY=pk_test_...
    PAYSTACK_SECRET_KEY=sk_test_...

Configure the Paystack dashboard webhook to the HTTPS `/paystack/webhook` endpoint. A browser callback alone never marks a transaction paid.

## Demo accounts

Development/demo only:

- Admin: `admin@delsumart.test` / `DemoAdmin2026!`
- Verified seller: `ada@delsumart.test` / `DemoStudent2026!`
- Verified buyer: `emeka@delsumart.test` / `DemoStudent2026!`
- Pending KYC: `tega@delsumart.test` / `DemoStudent2026!`
- Rejected KYC: `ese@delsumart.test` / `DemoStudent2026!`

Seed data includes active listings, a completed transaction, a protected/held transaction, a disputed transaction and an explainable fraud flag.

## Tests and CI

Run locally:

    php artisan test
    npm run build

GitHub Actions installs Composer/npm dependencies, builds production frontend assets and runs the Laravel test suite using an isolated SQLite test database. A change is not considered verified until CI reports success.

## Production checklist

Use HTTPS, `APP_ENV=production`, `APP_DEBUG=false`, secure session cookies, production MySQL, a real mail provider, supervised queue workers, backups, log monitoring, restricted administrator accounts and Paystack live credentials only after test-mode validation. Replace/remove demo credentials and data before deployment. Never commit `.env`, payment keys, database passwords or mail credentials.

## Development workflow

Production-quality changes are developed on feature branches, validated by CI, reviewed through a pull request and merged to `main` only after the checks are green.
