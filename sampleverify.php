<?php
// Public demo verification endpoint for testing LinkedIn flow.
// DO NOT expose sensitive data here. This is only a lightweight preview.

require(__DIR__ . '/../../config.php');

// No require_login(); this page is intentionally public for test purposes.

// Accept ?code=... like customcert's verify.
$code = optional_param('code', '', PARAM_ALPHANUMEXT);

// Basic cache headers for a public page.
@header('Cache-Control: public, max-age=300, s-maxage=300');
@header('X-Robots-Tag: noindex');

// Page setup (system context).
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/socialcert/sampleverify.php', ['code' => $code]));
$PAGE->set_title('Certificate verification (demo)');
$PAGE->set_heading('Certificate verification (demo)');

echo $OUTPUT->header();

// Very minimal, non-sensitive output for testing.
if ($code === '') {
    echo $OUTPUT->notification('Missing certificate code.', 'notifyproblem');
} else {
    echo html_writer::tag('h3', 'Demo certificate verification');
    echo html_writer::tag('p', 'Code: ' . s($code));
    echo html_writer::tag('p', 'This is a public demo endpoint for testing the LinkedIn Add-to-profile flow.');
    echo html_writer::tag('p', 'In production, use: /mod/customcert/verify.php?code=... (publicly accessible).');
}

echo $OUTPUT->footer();