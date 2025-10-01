<?php

// require('../../config.php');

// require_once(__DIR__ . '/classes/linkedinhelper.php');

// use aiprovider_datacurso\httpclient\ai_services_api;




// $cmid = required_param('cmid', PARAM_INT);

// // Obtener cm y course (mínimo necesario para contexto correcto).
// $cm     = get_coursemodule_from_id('', $cmid, 0, false, MUST_EXIST);
// $course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);

// Asegurar navegación/contexto del módulo.
// require_login($course, false, $cm);
// $context = context_module::instance($cm->id);

// $PAGE->set_url(new moodle_url('/local/socialcert/add.php', ['cmid' => $cmid]));
// $PAGE->set_cm($cm, $course);
// $PAGE->set_context($context);
// $PAGE->set_title(get_string('pluginname', 'local_socialcert'));
// $PAGE->set_heading(format_string($course->fullname));

// Cargar helper.

// Verificar módulo y issue del usuario (mínimo).
// if ($cm->modname !== 'customcert') {
//     print_error('invalidmod', 'error');
// }

// $issue = $DB->get_record('customcert_issues', [
//     'customcertid' => $cm->instance,
//     'userid'       => $USER->id
// ], '*', IGNORE_MISSING);


// $body  = [
//     'certname'    => 'Fundamentos de Python',
//     'course'      => 'Curso de Python',
//     'org'         => 'Google',
//     'socialmedia' => 'LinkedIn'
// ];

// $client = new ai_services_api();
// $response = $client->request('POST', '/certificate/answer', $body);

// $response = ['status' => 'ok', 'msg' => 'Certificado enviado'];

// // Convertir la variable PHP en JSON (para que JS pueda leerla)
// $json = json_encode($response, JSON_UNESCAPED_UNICODE);



// $PAGE->requires->js_call_amd('local_socialcert/copyhtml', 'init');

// // O si prefieres enlazarlo a un botón:

// echo $OUTPUT->header();


// if (!$issue) {
//     // Si no hay certificado emitido, no mostramos nada más.
//     echo $OUTPUT->notification(get_string('noissue', 'local_socialcert'), 'notifymessage');
//     echo $OUTPUT->footer();
//     exit;
// }

// // Construir URL LinkedIn con tu helper.
// $customcert = $DB->get_record('customcert', ['id' => $cm->instance], '*', IGNORE_MISSING);
// $linkedinlinkurl = null;

// $certname = '';
// $issued = 0;
// $certid = '';
// $verifyurl = '';

// if ($customcert) {
//     $certname  = format_string($customcert->name, true, ['context' => $context]);
//     $issued    = (int)$issue->timecreated;
//     $certid    = $issue->code;
//     $verifyurl = (new moodle_url('/mod/customcert/verify.php', ['code' => $issue->code]))->out(false);

//     $linkedinurl = \local_socialcert\linkedin_helper::build_linkedin_url(
//         $certname,
//         $issued,
//         $verifyurl,
//         $certid,
//         null
//     );
// }

// $articlehtml = \local_socialcert\linkedin_helper::build_linkedin_article_html(
//     $certname,
//     $issued,
//     $verifyurl,
//     $certid,
//     null
// );

// // $articleid = \html_writer::random_id('article');

// $linkbuttonlabel = get_string('linkcertbuttontext', 'local_socialcert');

// $copyarticlebuttontext = get_string('copyarticlebuttontext', 'local_socialcert');

// echo $OUTPUT->heading(get_string('shareinstruction', 'local_socialcert'));

// echo \local_socialcert\linkedin_helper::render_button($linkedinurl, $linkbuttonlabel);

// echo \html_writer::div($response['reply'], '', ['id' => $articleid]);

// echo \local_socialcert\linkedin_helper::render_button(null, $copyarticlebuttontext, [
//     'data-action' => 'copy-as-html',
//     'data-target' => $articleid,
//     'class'       => 'btn btn-secondary'
// ]);

// // En tu HTML imprime el botón si usas init():
// echo html_writer::tag('button', 'IA Response', ['id' => 'btn-cert']);

// echo $OUTPUT->footer();


require('../../config.php');

require_once(__DIR__ . '/classes/linkedinhelper.php');

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

$body  = [
    'certname'    => 'Fundamentos de Python',
    'course'      => 'Curso de Python',
    'org'         => 'Google',
    'socialmedia' => 'LinkedIn'
];

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
    $imageurl = $OUTPUT->image_url('cert', 'local_socialcert')->out(false);
}

$response = ['status' => 'ok', 'msg' => 'Certificado enviado'];

$json = json_encode($response, JSON_UNESCAPED_UNICODE);

$customcert = $DB->get_record('customcert', ['id' => $cm->instance], '*', IGNORE_MISSING);

$contextdata = [
    'intro'          => get_string('shareinstruction', 'local_socialcert'),
    'shareurl'       => $linkedinurl,    
    'buttonid'       => 'btn-normal',
    'buttonlabel'    => get_string('linkcertbuttontext', 'local_socialcert'),
    'aibuttonid'     => 'btn-ai',
    'aibuttonlabel'  => 'Activar IA',
    'responseid'     => 'ai-response',
    'copytextlabel'  => 'Copiar respuesta',
    'imageid'        => 'ai-image',
    'imageurl'       => $imageurl,
    'imagealt'       => 'Resultado IA',
    'copyimagelabel' => 'Copiar imagen',
    'certname'       => $certname,
    'verifyurl'      => $verifyurl,
    'certid'         => $certid
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

echo $OUTPUT->render_from_template('local_socialcert/mainpanel', $contextdata);

echo $OUTPUT->footer();
