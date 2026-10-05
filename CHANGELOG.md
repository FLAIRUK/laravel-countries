# Changelog

## 2.0.0 - Unreleased

Complete rewrite for Laravel 12 and 13 (PHP 8.2+). See the upgrade guide in the README.

- In-memory lookup API (`find`, `findOrFail`, `exists`, `search`, `options`, …) returning readonly `Country` objects.
- `CountryCode` validation rule.
- Package auto-discovery; `FLAIRUK\Countries` namespace.
- Optional publishable migration, Eloquent model, idempotent seeder, `countries:install` and `countries:seed` commands.
- Country lookups by alpha-2, alpha-3 or numeric code; `eea()`, `usingCurrency()`, `withCallingCode()`, `inRegion()`.
- Flag emoji, publishable PNG flags (`countries-flags` tag) and `flagUrl()`.
- Data corrections: EEA membership (UK removed; IS, LI, NO added), euro adoption (HR, BG), re-denominated currencies, ISO 4217 codes for GG/JE/IM, renamed countries.
- Test suite and GitHub Actions CI.
