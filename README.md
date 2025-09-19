# CertLink (local_certlinkedin)

MVP plugin that adds an **“Add to LinkedIn”** link to **Custom certificate** activities without modifying `mod_customcert`.

## Requirements
- Moodle 4.5+  
- `mod_customcert` (minimum version: 2024042212)

## Installation
1. Copy this plugin into `local/certlinkedin`.
2. Visit **Site administration → Notifications** to complete the installation.
3. Go to **Site administration → Plugins → Local plugins → CertLink** and configure:
   - **LinkedIn organization ID** (`organizationid`).

> If `organizationid` is empty, the button will not be displayed.

## How it works (MVP)
- The plugin adds an action through the callback `local_certlinkedin_extend_settings_navigation()` (see `lib.php`).
- The action appears **only** in **Custom certificate** activities when:
  1. The user has an issued certificate (record in `customcert_issues`).
  2. An `organizationid` is configured.
- The link opens `https://www.linkedin.com/profile/add?...` with:
  - `name` (certificate name),
  - `organizationId` (from configuration),
  - `issueYear` and `issueMonth` (from the issue’s `timecreated`),
  - `certUrl` (public verification link `verify.php?code=...`),
  - `certId` (the issue’s `code`).

## Important notes
- **Public verification**: make sure `mod/customcert/verify.php?code=...` is accessible without login so LinkedIn (and third parties) can validate the certificate.
- **Privacy**: this plugin does not store any personal data (see `classes/privacy/provider.php`).
- **Languages**: 
  - English is required: `lang/en/local_certlinkedin.php`.
  - Spanish optional: `lang/es/local_certlinkedin.php`.

## Customization (future)
- Add `expirationYear/Month` if your certificates include expiration dates.
- Define a custom `capability` if you want finer control over who can see the button (beyond `mod_customcert`).
- Support for **Moodle App** (mobile) with `db/mobile.php`.
- Observers to log events when publishing to LinkedIn.
- PHPUnit tests for `classes/helper.php`.

## License
GPLv3. See `LICENSE.md`.
