# Church Administration and Finance Tracking System

### Features
- [x] User Authentication
- [x] USSD Payment Integration
- [ ] SMS Notification
- [ ] Email Notification

### Technologies
- PHP/Laravel
- Postgres
- Redis

### Authentication
Authentication is done using Laravel Sanctum

### API Documentation
API documentation is done using OpenAPI 3. You can view the documentation in this file: `/.documentation/api.v1.yaml`.

### Testing Framework
- PHPUnit—for unit and integration testing
- InfectionPHP—for mutation testing

### TODO


## Multi-Currency and FX Handling

Recording and summing payments across currencies requires careful data modeling and consistent FX policy. Key rules and our implementation choices are summarized below.

### Core rules
- Never add amounts of different currencies directly.
  - Either aggregate per currency; or
  - Convert each line to a reporting currency (e.g., GHS) using a defined FX rate and valuation date, then sum.
- Store amounts in minor units (integers) to avoid floating point errors and to respect currency-specific decimals.

### Transactions schema (finance.transactions)
We store only the fields needed for auditable, reproducible FX conversion:
- amount_raw bigint — original amount in minor units (e.g., GHS 12.34 -> 1234).
- currency char(3) — ISO 4217 code of the booked amount (e.g., GHS, USD, EUR).
- fx_rate numeric(20,10) nullable — rate used to convert currency to reporting_currency at booking time; 1 when currency == reporting_currency.
- reporting_currency char(3) — target currency for consolidated reporting (e.g., GHS).
- converted_raw bigint — amount converted to reporting_currency in minor units after applying fx_rate and rounding policy.
- original_amount_text string nullable — exact user-entered text preserved for audit.
- original_amount_currency char(3) nullable — the currency as originally entered by the user (ISO 4217), for audit.

Why minor units?
- Avoids floating-point rounding issues.
- Aligns with 0-decimal (JPY) and 3-decimal (TND/JOD) currencies.

### Summation and reporting
- Per-currency totals: sum amount_raw grouped by currency.
- Consolidated total: sum converted_raw where reporting_currency = '<YOUR_REPORTING_CCY>'.
  - Suggested tweak: “Consolidated total: sum converted_raw. If your deployment uses a single reporting currency, the WHERE reporting_currency = '<YOUR_REPORTING_CCY>' is optional but recommended for safety and future‑proofing.”
- Revaluation reports: if you need a “today” total, revalue historical balances with today’s rate separately and present FX gain/loss distinctly.

Example queries
- Per currency:
  SELECT currency, SUM(amount_raw) FROM finance.transactions GROUP BY currency;
- Reporting currency total:
  SELECT SUM(converted_raw) FROM finance.transactions WHERE reporting_currency = 'USD';

### Rounding and precision
- Use high precision decimals for fx_rate (e.g., 10–12 dp).
- Apply rounding once per line when computing converted_raw to the reporting currency’s minor units.
- Use a deterministic rounding mode (e.g., bankers or half-up) and keep it consistent.

### Rate sourcing and audit
- Persist the exact fx_rate used per transaction. Do not mutate historical rates.
- Record when and how a rate was obtained in your domain layer (e.g., fx_rate_at, source) if/when you extend the schema. For now, we store the applied rate only.

### Laravel/PHP implementation tips
- Do not use floats for money. Prefer integers (minor units) or arbitrary precision decimals.
- Consider a money library (e.g., brick/money) for conversions and rounding.
- Centralize currency metadata (minor units) and rounding mode in one place.

### Example workflow
1) User records 100.00 GHS.
2) Resolve the GHS→USD rate at booking time, e.g., 0.071234.
3) Convert with high precision; then round to cents: 100.00 × 0.071234 = 7.1234 USD → 7.12 USD => converted_raw = 712.
4) Persist amount_raw = 10000, currency = GHS, fx_rate = 0.071234, reporting_currency = USD, converted_raw = 712, original_amount_text = '100.00', original_amount_currency = 'GHS'.
5) For totals in USD: sum converted_raw.

### Testing guidance
- Use deterministic, fixed rate snapshots in tests. Don’t call live APIs.
- Test rounding edges (e.g., 0.005, 0.015) and currencies with 0 or 3 decimals.
- Property tests: convert there and back with reciprocal rate within rounding tolerances.

## FX Rates History and Currency Enum

Reason: We keep an immutable history of hourly FX snapshots to enable auditability, reproducible conversions, revaluation reports, and cross-rate triangulation even when a direct currency pair isn’t available. This table is the single source of truth for historical rates used in booking and reporting.

To support audits, revaluations, and reproducible reporting, we track hourly FX rate snapshots for currency pairs of interest.

### Currencies of interest (string-backed enum)
- Defined in app/Enums/Currency.php as a PHP 8.1+ string-backed enum with ISO 4217 codes.
- Initial set includes: USD, EUR, GHS, GBP, NGN, KES, ZAR, JPY, TND, JOD.
- Extend this enum as your business requirements grow. Use Currency::codes() for validation/UI options.

### Schema: finance.fx_rates_history
- base_currency char(3) — ISO 4217 base (e.g., USD). Prefer values from App\\Enums\\Currency.
- quote_currency char(3) — ISO 4217 quote (e.g., GHS). Prefer values from App\\Enums\\Currency.
- rate numeric(20,10) — FX rate expressed as 1 base = rate quote.
- as_of_hour timestamptz — timestamp for the hourly snapshot (UTC recommended), truncated to the hour.
- source varchar(64) nullable — provider (e.g., ECB, OXR, internal).
- meta jsonb nullable — provider payload/annotations.
- created_at/updated_at.

Indexes and constraints
- Unique on (base_currency, quote_currency, as_of_hour) to prevent duplicates per pair per hour.
- Index on as_of_hour for time-bounded queries and on (base_currency, quote_currency) for pair lookups.

Practical reasons this table exists
- Auditability: immutable historical snapshots ensure you can reproduce any past conversion and explain reported totals.
- Revaluation: compute period-end valuations by joining open balances to the appropriate as_of_hour snapshot.
- Cross-rate triangulation: derive pairs you didn’t store directly (e.g., GBP→USD) from two legs sharing a common base (e.g., GHS→USD and GHS→GBP).
- Provider resilience: if a direct pair is temporarily unavailable, you can still price via triangulation.
- Consistency: all services read from the same source of truth for historical FX.

### Usage patterns
- Write: upsert hourly snapshots from your rate provider for every currency of interest.
- Read for conversion: resolve a pair at the desired hour (or nearest prior hour) and apply your rounding policy.
- Revaluation: join open foreign-currency balances to the hourly rates for the chosen valuation timestamp.
- Keep historical snapshots immutable; never mutate past rows.
