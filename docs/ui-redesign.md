# DelsuMart UI redesign

## Functionality audit

The redesign preserves the current account and payout routes, production deployment configuration, authentication and transaction logic.

- Public: `home`, `listings.index`, `listings.show`; real active listings, seller verification, uploaded images, search/category/price filters and sorting.
- Authentication: registration, password plus email OTP login, password reset; existing throttles and field names.
- Student: authenticated overview/account/KYC/payouts; owner-scoped listings, purchases, sales, transactions and participant disputes. Verified-only creation/removal and checkout, buyer-only receipt confirmation.
- Admin: existing middleware protects overview, student controls, KYC decisions, listings, transactions, payouts, disputes, fraud reviews and audits.
- KYC: local/private storage, administrator or submitting owner only, fixed `id-card` and `fee-receipt` types. Previews use the same authorized endpoint and never emit disk paths.
- Payments: server-verified Paystack collection with internal `paid_held` state. Webhook signatures, amount/currency checks, CSRF exception, transaction locks and dispute restrictions remain intact.
- Payouts: existing email OTP, server-side bank verification, balance reservation, limits, admin decisions and audit logging remain intact.
- States: pending payment, protected, release pending, released, disputed, refunded and cancelled. Human-readable labels do not change stored values.
- No policy classes exist; route middleware and controller authorization remain authoritative.

## Design system

Midnight navy, cobalt accents, white surfaces, restrained corners, consistent outlined icons, visible keyboard focus and reduced-motion support. Public pages emphasize campus discovery. Student navigation emphasizes trading; the separate admin shell emphasizes operational review.

Shared Blade components cover buttons, fields, badges, alerts, cards, stats, empty states, headers, tables, avatars, listing cards, private documents, dispute summaries and dialogs. Native dialogs contain focus and support Escape. Alpine's CSP build supports responsive sidebars without adding `unsafe-eval` to the security policy.

The mobile drawer traps keyboard focus, makes the background inert, restores focus on close, and handles viewport changes. Desktop sidebar collapse persists locally. Tables scroll within labeled, keyboard-focusable regions. Product images use uploaded assets, with neutral placeholders when unavailable. Marketing imagery and category icons are centrally configured; the marketplace filter also includes actual stored categories.

## Presentation queries

Small read-only query additions provide recent dashboard activity, eagerly loaded parties, transaction status/date/party filters, verification/account filters, risk ranges, and student dispute/audit summaries. Mutation controllers have formatting changes only. PHP syntax-tree comparison verified that source formatting did not alter behavior.

The payout form's client code lives in the application bundle and remains compatible with CSP. It handles network errors and ignores stale bank-resolution responses; the server still revalidates bank details on submission.

## Verification

Use the locked dependencies and run:

```sh
composer install
npm install
npm run build
./vendor/bin/pint --test
php artisan test
```

Feature coverage includes authentication/MFA/reset, student and admin screens, private document access, KYC resubmission and decisions, verified-only trading, own-listing protection, payment amount verification and signed webhooks, disputed transaction release prevention, payout authorization/balance/OTP checks, owner-scoped filters, risk ranges, and payout page privacy.

Browser review uses a separate SQLite database with clearly marked test documents in private storage. No live payment or payout is initiated for UI verification.

Latest local validation: 29 tests, 209 assertions; production Vite build and Laravel Pint passed. A syntax-tree comparison against `main` found behavioral changes only in read-only methods on ListingController, DashboardController, AdminController, AdminMarketplaceController and AdminStudentController. All mutation methods, security middleware, services, routes and migrations retain their original semantics.
