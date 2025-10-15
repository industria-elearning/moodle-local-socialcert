# Share Certificate AI (local_socialcert)

A Moodle plugin that adds an **“Add certificate to LinkedIn”** button to **Custom certificate** activities and, optionally, generates a **professional AI message** ready for your LinkedIn post. All with **one click** for the learner.

> **Dependency:** Requires [`mod_customcert`](https://moodle.org/plugins/mod_customcert). It doesn’t modify *Custom certificate*; it only adds a contextual action.

---

## Features

* **LinkedIn Add-to-profile** button on each issued certificate (from *Custom certificate*).
* **Provider AI** integration (another Buen Data plugin) to **suggest LinkedIn post copy**.
* Public verification link (`verify.php?code=...`) included automatically.
* Complies with Moodle’s privacy API; **no additional personal data** is stored.
* Languages: English (default), Spanish, German, French, Portuguese, Indonesian, and Russian.

---

## Requirements

* **Moodle 4.5** (recommended minimum: **2025100201**) or later.
* **`mod_customcert`** (recommended minimum: **2024042212**).
* **Provider AI** to auto-generate the LinkedIn post message (version **2025100201**) — optional.

---

## Installation

### Via ZIP upload

1. Log in as **administrator** and go to **Site administration → Plugins → Install plugins**.
2. Upload the plugin **ZIP**.
3. Review the validation report and **complete the installation**.

### Manual copy (dirroot)

1. Copy this directory to:

   ```
   {your/moodle/dirroot}/local/socialcert
   ```

2. Log in as administrator and visit **Site administration → Notifications** to complete the upgrade,
   or run from CLI:

   ```bash
   php admin/cli/upgrade.php
   ```

---

## Configuration (LinkedIn Organization ID)

Go to **Site administration → Plugins → Local plugins → Share Certificate AI** and set:

* **LinkedIn organization ID** (`organizationid`) — *required*.
* **Organization name** (`organizationname`) — *optional*.

> If `organizationid` is empty, the button **will not be shown** to users.

### How to find your Organization ID

You must be an **administrator** of your institution’s LinkedIn Page. Open the Page and copy the **numeric ID** from the URL. Example:

```
https://www.linkedin.com/company/64664132/admin/...
                                   ^^^^^^^^ ID
```

* **Organization ID**: required for *Add-to-profile*.
* **Organization name (optional)**: exactly as shown on LinkedIn. If left empty, LinkedIn uses the name associated with the ID.

**Screenshots**

![Activity menu with LinkedIn action](./socialcert-menu.png)
![Plugin settings screen](./socialcert-settings.png)

---

## Using it in a course

1. Create a **Custom certificate** activity in your course.
2. Ensure the user **has an issued certificate** (`customcert_issues`).
3. The **“Add to LinkedIn profile”** action appears on the activity menu **only if**:

   * There is a certificate issue for the current user, **and**
   * A valid **`organizationid`** is configured.

---

## How it works

The plugin adds an action via the callback `local_socialcert_extend_settings_navigation()` (see `lib.php`).
On click, it opens the official **LinkedIn Add-to-profile** URL with these parameters:

* `name`: certificate name
* `organizationId`: the ID configured in the plugin
* `issueYear` / `issueMonth`: derived from the issue’s `timecreated`
* `certUrl`: public verification link `verify.php?code=...`
* `certId`: the certificate `code`

> Ensure the verification page is **public (no login required)** so LinkedIn can validate it.

---

## AI with **Provider AI** (optional)

When **Provider AI** is installed and connected, the plugin **suggests post text** for LinkedIn as you compose your post. The suggestion uses:

* **Certificate name**
* **Course name**
* **Organization** (ID/name)

You can freely **edit or replace** the suggested text before publishing.

---

## Privacy

This plugin implements Moodle’s Privacy API in `classes/privacy/provider.php` and **does not store additional personal data**. It only builds the LinkedIn URL using information already available on the certificate.

---

## Troubleshooting

* **The button doesn’t show**

  * Confirm there’s an entry in `customcert_issues` for the user.
  * Verify `organizationid` is configured correctly.
  * Make sure `verify.php?code=...` is **publicly accessible**.

* **LinkedIn doesn’t recognize the organization**

  * Confirm the **numeric ID** copied from the LinkedIn Page URL is correct.
  * Ensure you are an **admin** of that Page.

---

## Roadmap (ideas)

* Support for **expiration dates** (`expirationYear` / `expirationMonth`).
* Event observers/metrics for posting.
* **Moodle App** compatibility (`db/mobile.php`).

---

## License

**GPLv3**. See `LICENSE.md`.
