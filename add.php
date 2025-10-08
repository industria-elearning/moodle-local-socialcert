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

if (!$issue) {
    // Si no hay certificado emitido, no mostramos nada más.
    echo $OUTPUT->notification(get_string('noissue', 'local_socialcert'), 'notifymessage');
    echo $OUTPUT->footer();
    exit;
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
        $certname,
        $issued,
        $verifyurl,
        $certid
    );
}

$imageurl = \local_socialcert\linkedin_helper::local_socialcert_get_first_customcert_image_url($context);

if (empty($imageurl)) {
    $imageurl = 'https://marketplace.canva.com/EAGH2_8N5Q8/1/0/1600w/canva-certificado-de-participaci%C3%B3n-elegante-dorado-5gpsNPggz7w.jpg';
}

$response = ['status' => 'ok', 'msg' => 'Certificado enviado'];

$json = json_encode($response, JSON_UNESCAPED_UNICODE);

$orgname = get_config('local_socialcert', 'organizationname');

$course = format_string($course->fullname, true, ['context' => context_course::instance($course->id)]);
$displayname = format_string(fullname($USER), true, ['context' => $context]);

$contextdata = [
    'intro'             => get_string('shareinstruction', 'local_socialcert'),
    'shareurl'          => $linkedinurl,    
    'buttonid'          => 'btn-normal',
    'buttonlabel'       => get_string('linkcertbuttontext', 'local_socialcert'),
    'aibuttonid'        => 'btn-ai',
    'aibuttonlabel'     => 'Activate AI',
    'responseid'        => 'ai-response',
    'copytextlabel'     => 'Copiar respuesta',
    'imageid'           => 'ai-image',
    'imageurl'          => $imageurl,
    'imagealt'          => 'Resultado IA',
    'copyimagelabel'    => 'Copiar imagen',
    'certname'          => $certname,
    'verifyurl'         => $verifyurl,
    'certid'            => $certid,
    'cmid'              => $cm->id,
    'course'            => $course,
    'org'               => $orgname,
    'socialmedia'       => 'LinkedIn',
    'sharetitle'        => get_string('sharetitle', 'local_socialcert'),
    'sharesubtitle'     => get_string('sharesubtitle', 'local_socialcert'),
    'buttonlabelshare'  => get_string('buttonlabelshare', 'local_socialcert'),
    'whatsharelabel'    => get_string('whatsharelabel', 'local_socialcert'),
    'popupblocked'      => get_string('popupblocked', 'local_socialcert'),
    'sharecompleted'    => get_string('sharecompleted', 'local_socialcert'),
    'ai_actioncall'     => get_string('ai_actioncall', 'local_socialcert'),
    'author_name'        => $displayname
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
