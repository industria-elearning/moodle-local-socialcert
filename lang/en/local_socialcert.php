<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// See the GNU General Public License for more details: https://www.gnu.org/licenses/.

/**
 * English strings for local_socialcert.
 *
 * @package   local_socialcert
 * @category  string
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'SocialCert';
$string['description'] = 'Allows the user to share their certificate directly on LinkedIn.';

// Settings.
$string['organizationid'] = 'LinkedIn organization ID';
$string['organizationid_desc'] = 'Numeric company/organization ID used by LinkedIn Add-to-profile. Leave empty to disable until configured.';
$string['organizationname'] = 'LinkedIn organization name';
$string['organizationname_desc'] = 'Name of the organization to display in LinkedIn. Must match exactly as it appears on LinkedIn. Leave empty to disable until configured.';

$string['privacy:metadata'] = 'The SocialCert plugin does not store any personal data.';

$string['noissue'] = 'Aún no tienes un certificado emitido para este curso.';

$string['linkcertbuttontext'] = 'Share on LinkedIn';
$string['copyarticlebuttontext'] = 'Copy LinkedIn article';

$string['shareinstruction'] = 'Celebrate your achievement! Click below to showcase your certificate on LinkedIn and let your network know about your success:';

$string['copyconfirmation'] = 'Copied ✔';

$string['airesponsebtn'] = 'Activate AI';

$string['generating'] = 'Generating…';

$string['certificateimage'] = 'certificate.png';

// SocialCert – Share hero (Step 1)
$string['sharetitle']        = 'Share your achievement on LinkedIn';
$string['sharesubtitle']     = 'We’ll post a verifiable link to your certificate.';
$string['buttonlabelshare']  = 'Share on LinkedIn';
$string['whatsharelabel']    = 'What do we share?';

// Feedback/status (optional but recommended)
$string['popupblocked']      = 'Enable pop-ups to continue.';
$string['sharecompleted']    = 'LinkedIn share completed.';

$string['ai_field_heading']  = 'Post text';

$string['certificate_url']   = 'Link';

$string['ai_actioncall']   = 'Create a professional message for your LinkedIn post in one click';

$string['linktext']   = 'Certificate link';