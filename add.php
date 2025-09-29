<?php
require('../../config.php');

require_login();

$cmid = required_param('cmid', PARAM_INT);

// Obtener cm y course (mínimo necesario para contexto correcto).
$cm     = get_coursemodule_from_id('', $cmid, 0, false, MUST_EXIST);
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);

// Asegurar navegación/contexto del módulo.
require_login($course, false, $cm);
$context = context_module::instance($cm->id);

$PAGE->set_url(new moodle_url('/local/socialcert/add.php', ['cmid' => $cmid]));
$PAGE->set_cm($cm, $course);
$PAGE->set_context($context);
$PAGE->set_title(get_string('pluginname', 'local_socialcert'));
$PAGE->set_heading(format_string($course->fullname));

// Cargar helper.
require_once(__DIR__ . '/classes/linkedinhelper.php');

// Verificar módulo y issue del usuario (mínimo).
if ($cm->modname !== 'customcert') {
    print_error('invalidmod', 'error');
}

$issue = $DB->get_record('customcert_issues', [
    'customcertid' => $cm->instance,
    'userid'       => $USER->id
], '*', IGNORE_MISSING);

echo $OUTPUT->header();

if (!$issue) {
    // Si no hay certificado emitido, no mostramos nada más.
    echo $OUTPUT->notification(get_string('noissue', 'local_socialcert'), 'notifymessage');
    echo $OUTPUT->footer();
    exit;
}

// Construir URL LinkedIn con tu helper.
$customcert = $DB->get_record('customcert', ['id' => $cm->instance], '*', IGNORE_MISSING);
$linkedinlinkurl = null;

if ($customcert) {
    $certname  = format_string($customcert->name, true, ['context' => $context]);
    $issued    = (int)$issue->timecreated;
    $certid    = $issue->code;
    $verifyurl = (new moodle_url('/mod/customcert/verify.php', ['code' => $issue->code]))->out(false);

    $linkedinurl = \local_socialcert\linkedin_helper::build_linkedin_url(
        $certname,
        $issued,
        $verifyurl,
        $certid,
        null
    );
}

$linkbuttonlabel = get_string('linkcertbuttontext', 'local_socialcert');

// Mostrar solo el botón (o aviso si falta config).

echo $OUTPUT->heading(get_string('shareinstruction', 'local_socialcert'));

echo \local_socialcert\linkedin_helper::render_button($linkedinurl, $linkbuttonlabel);

echo $OUTPUT->footer();
