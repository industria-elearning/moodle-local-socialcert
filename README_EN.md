# Share Certificate AI (local_socialcert)

A Moodle plugin that adds an **“Add certificate to LinkedIn”** button to **Custom certificate** activities and, optionally, generates a **professional AI message** ready for your LinkedIn post. All with **one click** for the learner.

> This plugin **depends** on [`mod_customcert`](https://moodle.org/plugins/mod_customcert). It does not modify Custom certificate; it only adds a contextual action.

---

## Features

- ✅ **LinkedIn Add-to-profile** button on each issued certificate (from *Custom certificate*).
- 🤖 **Provider AI** integration (another Buen Data plugin) to **suggest post copy** for LinkedIn.
- 🔗 Public verification link (`verify.php?code=...`) included automatically.
- 🔒 Complies with Moodle’s privacy API; **no additional personal data** is stored.
- 🌐 Languages: English (default), Spanish, German, French, Portuguese, Indonesian and Russian. 

---

## Requirements

- Moodle 4.5 (recomended minimum: 2025100201) or later.
- `mod_customcert` (recommended minimum: 2024042212).
- `Provider AI` to auto‑generate the LinkedIn post message (2025100201).

---

## Installation

1. Copy this directory to `local/socialcert` in your Moodle site.
2. Navigate to **Site administration → Notifications** to complete the install.
3. Go to **Site administration → Plugins → Local plugins → Share Certificate AI** and set:
   - **LinkedIn organization ID** (`organizationid`).
   - **Organization name** (`organizationname`, optional).

> If `organizationid` is empty, the button **will not be shown** to users.

---

## LinkedIn setup (Organization ID)

You must be an **administrator of your institution’s LinkedIn Page** to see its URL. Open the Page and copy the **numeric ID** that appears in the URL. Example:

```
https://www.linkedin.com/company/64664132/admin/...
                                   ^^^^^^^^ ID
```

- **Organization ID**: required by LinkedIn for *Add-to-profile*.
- **Organization name (optional)**: exactly as it appears on LinkedIn. If left empty, LinkedIn uses the name associated with the ID.

**Screenshots**

![Activity menu with LinkedIn action](./socialcert-menu.png)
![Plugin settings screen](./socialcert-settings.png)

---

## How it works

The plugin adds an action via the callback `local_socialcert_extend_settings_navigation()` (see `lib.php`).  
The action appears on **Custom certificate** activities only when:

1. The user **has an issued certificate** (`customcert_issues`).  
2. A valid **`organizationid`** is configured.

On click, the official **LinkedIn Add-to-profile** URL opens with these parameters:

- `name`: certificate name.
- `organizationId`: the ID configured in the plugin.
- `issueYear` / `issueMonth`: taken from the issue’s `timecreated`.
- `certUrl`: public verification link `verify.php?code=...`.
- `certId`: the certificate `code`.

> Ensure that the certificate verification page is accessible **without login**, so LinkedIn can validate it.

---

## AI with *Provider AI* (optional)

When **Provider AI** is installed and connected, the plugin **suggests post text** for LinkedIn when you create your post manually.  
The AI suggestion uses:
- **Certificate name**
- **Course name**
- **Organization** (ID/name)

You can freely edit or replace the suggested text before publishing.

---

## Privacy

This plugin implements Moodle’s privacy API in `classes/privacy/provider.php` and **does not store extra personal data**. It only builds the LinkedIn URL using information already available on the certificate.

---

## Troubleshooting

- **The button doesn’t show**  
  Check that:
  - There is an entry in `customcert_issues` for the user.
  - `organizationid` is configured correctly.
  - The `verify.php?code=...` page is public.

- **LinkedIn doesn’t recognize the organization**  
  Make sure the **ID** from the LinkedIn Page URL is correct and that you are an **admin** of that Page.

---

## Roadmap (ideas)

- Support for **expiration dates** (`expirationYear/Month`).  
- Event observers/metrics when posting.
- **Moodle App** compatibility (`db/mobile.php`).

---

## License

GPLv3. See `LICENSE.md`.
