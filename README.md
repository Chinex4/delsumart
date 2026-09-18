# DelsuMart

DelsuMart is a security-first peer-to-peer marketplace for verified students of Delta State University, Abraka. It implements the supplied Chapters 1–3 with emphasis on identity, MFA, KYC, protected payments, fraud review, disputes and auditability.

## Research objectives implemented
1. Password + single-use expiring email OTP MFA.
2. Manual KYC using private student-ID and current fee-receipt uploads.
3. Paystack server-side payment initialization/verification with a protected transaction state machine.
4. Explainable rule-based fraud scoring and administrator flags.
5. PHPUnit tests and GitHub Actions CI for evaluation.

## Stack
Laravel 12 / PHP 8.2+, MySQL, Blade, Tailwind CSS 4, Alpine.js, Vite, Laravel Notifications and Paystack HTTP API.

## Escrow architecture
The application models an escrow-style protected transaction lifecycle: pending payment, paid/held, release pending, released, disputed, refunded and cancelled. Paystack collection is not represented as a generic legal escrow service. Automated seller settlement must only use Paystack capabilities supported and configured for the merchant account.

## Setup

    composer install
    cp .env.example .env
    php artisan key:generate
    npm install
    npm run build

Create a MySQL database named delsumart, configure DB values, then:

    php artisan migrate --seed
    php artisan storage:link
    php artisan serve

KYC and dispute evidence use private/local storage. Never move KYC files into public storage.

## MFA
Configure MAIL values in .env. OTPs expire after 10 minutes, are hashed, single-use, attempt-limited and resend-rate-limited.

## Paystack
Keep PAYSTACK_PUBLIC_KEY and PAYSTACK_SECRET_KEY only in .env. Configure the Paystack dashboard webhook to the HTTPS /paystack/webhook endpoint. Browser redirects never mark an order paid without server verification.

## Demo accounts
- Admin: admin@delsumart.test / DemoAdmin2026!
- Verified student: ada@delsumart.test / DemoStudent2026!
- Pending student: tega@delsumart.test / DemoStudent2026!
- Rejected student: ese@delsumart.test / DemoStudent2026!

Development only; replace these before public deployment.

## Security
CSRF, password hashing, MFA, role/KYC middleware, ownership checks, private KYC storage, upload validation, database constraints, server-authoritative prices, Paystack verification, HMAC webhooks, idempotent event records, transaction locks, fraud scoring, audit logs and environment-only secrets.

## Tests
Run: php artisan test

A successful runtime/CI execution is required before claiming the tests passed.

## Production
Use APP_ENV=production, APP_DEBUG=false, HTTPS, production mail, queue workers, secure cookies, production MySQL and Paystack live credentials only after test-mode validation.

Development is performed on develop and should enter main through a reviewed pull request.
