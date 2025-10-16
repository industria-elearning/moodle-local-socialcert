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

## Pre-requisites

* **Moodle 4.5** (recommended minimum: **2025100201**) or later.
* **`mod_customcert`** (recommended minimum: **2024042212**).
* **Provider AI** to auto-generate the LinkedIn post message (version **2025100201**) — optional.

---

## Installing via ZIP upload

1. Log in as **administrator** and go to **Site administration → Plugins → Install plugins**.
2. Upload the plugin **ZIP**.
3. Review the validation report and **complete the installation**.

## Installing manually

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

## Plugin Configuration

Go to **Site administration → Plugins → Local plugins → Share Certificate AI** and fill in:

* **LinkedIn Organization ID** (`organizationid`) — **optional**.

  * If you **don’t** provide it, the plugin will use LinkedIn’s **default ID**.
* **Organization name** (`organizationname`) — **optional but recommended** if you leave it   
  empty, the **default ID** will be used.

  * For best results, write the **exact name as it appears on LinkedIn** (same spacing, capitalization, and accents).
* **Enable AI** (`local_socialcert/enableai`) — **global toggle**.

  * Checking this box **enables** AI features for the entire account; unchecking it **disables** them globally.
  * Default: **enabled**.

---

### How to find your LinkedIn Organization ID

You must be an **administrator** of your institution’s LinkedIn Page.

1. Open your organization’s LinkedIn Page in admin view.
2. Copy the **numeric ID** from the URL.

Example:

```
https://www.linkedin.com/company/64664132/admin/...
```

In this example, the **Organization ID** is `64664132`.

---

## Organization name recommendations

* Use the **exact public name** of the LinkedIn Page (match capitalization, accents, and spaces).
* This helps ensure consistent behavior across the plugin.


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

>LinkedIn will prompt you to sign in if you’re not already logged in in your browser.

---

## AI with **Provider AI** (optional)

When **Provider AI** is installed and connected, the plugin **suggests post text** for LinkedIn as you compose your post. The suggestion uses:

* **Certificate name**
* **Course name**
* **Organization** (ID/name)

You can freely **edit or replace** the suggested text before publishing.

---

## License

**2025 Data Curso LLC** — [https://datacurso.com](https://datacurso.com)

This program is free software: you can redistribute it and/or modify it under the terms of the **GNU General Public License** as published by the Free Software Foundation, either **version 3** of the License, or (at your option) **any later version**.

This program is distributed in the hope that it will be useful, but **WITHOUT ANY WARRANTY**; without even the implied warranty of **MERCHANTABILITY** or **FITNESS FOR A PARTICULAR PURPOSE**. See the **GNU General Public License** for more details.

You should have received a copy of the **GNU General Public License** along with this program. If not, see [https://www.gnu.org/licenses/](https://www.gnu.org/licenses/).
