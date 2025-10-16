<?php

require('../../config.php');

require_once(__DIR__ . '/classes/linkedin_helper.php');

$cmid = required_param('cmid', PARAM_INT);
$cm     = get_coursemodule_from_id('', $cmid, 0, false, MUST_EXIST);
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);

require_login($course, false, $cm);

$context = context_module::instance($cm->id);

if ($cm->modname !== 'customcert') {
    print_error('invalidmod', 'error');
}

$issue = $DB->get_record('customcert_issues', [
    'customcertid' => $cm->instance,
    'userid'       => $USER->id
], '*', IGNORE_MISSING);

$active = true;

if (!$issue) {
    $issued = false;
} else {
    $issued = true;
}

$customcert = $DB->get_record('customcert', ['id' => $cm->instance], '*', IGNORE_MISSING);
$linkedinurl = null;
$certname = '';
$issued = 0;
$verifyurl = '';
$certid = '';

if ($customcert) {
    $certname  = format_string($customcert->name, true, ['context' => $context]);
    $issued    = (int)$issue->timecreated;
    $certid    = $issue->code;
    $verifyurl = (new moodle_url('/mod/customcert/verify.php', ['code' => $issue->code]))->out(false);

    $linkedinurl = \local_socialcert\linkedin_helper::build_linkedin_url(
        certname: $certname,
        issueunixtime: $issued,
        certurl: $verifyurl || '',
        certid: $certid || ''
    );
}

$response = ['status' => 'ok', 'msg' => 'Certificado enviado'];

$json = json_encode(value: $response, flags: JSON_UNESCAPED_UNICODE);

$orgname = get_config('local_socialcert', 'organizationname');
$enableai = (bool) (int) get_config('local_socialcert', 'enableai');

$course = format_string($course->fullname, true, ['context' => context_course::instance($course->id)]);
$displayname = format_string(fullname($USER), true, ['context' => $context]);

$img = new moodle_url('/local/socialcert/assets/logo_title.png');
$imgurl = $img->out(false);

$datanetwork = $issued ? 'linkedin' : '';

$contextdata = [
    'aibuttonid'        => 'btn-ai',
    'buttonid'          => 'btn-normal',
    'imageid'           => 'ai-image',
    'responseid'        => 'ai-response',
    'socialmedia'       => 'LinkedIn',
    'author_name'       => $displayname,
    'certid'            => $certid,
    'certname'          => $certname,
    'cmid'              => $cm->id,
    'course'            => $course,
    'datanetwork'       => $datanetwork,
    'enableai'          => $enableai,
    'imageurl'          => $imgurl,
    'issued'            => !$issued,
    'org'               => $orgname,
    'shareurl'          => $linkedinurl,
    'verifyurl'         => $verifyurl,
    'ai_actioncall'     => get_string('ai_actioncall', 'local_socialcert'),
    'buttonlabel'       => get_string('linkcertbuttontext', 'local_socialcert'),
    'buttonlabelshare'  => get_string('buttonlabelshare', 'local_socialcert'),
    'intro'             => get_string('shareinstruction', 'local_socialcert'),
    'linktext'          => get_string('linktext', 'local_socialcert'),
    'popupblocked'      => get_string('popupblocked', 'local_socialcert'),
    'sharecompleted'    => get_string('sharecompleted', 'local_socialcert'),
    'sharesubtitle'     => get_string('sharesubtitle', 'local_socialcert'),
    'sharetitle'        => get_string('sharetitle', 'local_socialcert'),
    'whatsharelabel'    => get_string('whatsharelabel', 'local_socialcert'),
    'certerror'         => get_string('certerror', 'local_socialcert')
];

$PAGE->set_url(new moodle_url('/local/socialcert/add.php', ['cmid' => $cmid]));
$PAGE->set_cm($cm, $course);
// $PAGE->set_context($context);
$PAGE->set_pagelayout('standard');
// $PAGE->set_title(get_string('pluginname', 'local_socialcert'));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->requires->css('/local/socialcert/styles.css');
$PAGE->requires->js_call_amd('local_socialcert/actions', 'init');

echo $OUTPUT->header();

echo $OUTPUT->render_from_template('local_socialcert/main_panel', $contextdata);

echo $OUTPUT->footer();
