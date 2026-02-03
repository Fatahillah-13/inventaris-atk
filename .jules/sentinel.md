## 2026-02-03 - [Employee Data Scraping Protection]
**Vulnerability:** Public AJAX route `/ajax/employee-by-nik/{nik}` was exposing employee names and departments without any rate limiting.
**Learning:** Publicly accessible endpoints that bridge to internal APIs (like the employee API) are high-risk targets for automated scraping and data enumeration. Even if intended for public forms, they must be protected by rate limiting.
**Prevention:** Always apply `throttle` middleware to public endpoints that return sensitive or personally identifiable information (PII).
