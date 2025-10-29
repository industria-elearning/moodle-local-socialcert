<?php
namespace local_socialcert;

use core\hook\output\before_standard_html_head_generation;
use core\hook\output\before_footer_html_generation;

class hook_callbacks {

    // 1) Cargar CSS/JS antes de imprimir <head>
    public static function before_standard_html_head_generation(
        before_standard_html_head_generation $hook
    ): void {
        global $PAGE;

        if ($PAGE->pagetype !== 'mod-customcert-view' ||
            empty($PAGE->cm->id) || !isloggedin() || isguestuser()) {
            return;
        }

        $PAGE->requires->css('/local/socialcert/styles.css');
    }

    // 2) Inyectar tu HTML antes del </body>
    public static function before_footer_html_generation(
        before_footer_html_generation $hook
    ): void {
        global $PAGE, $OUTPUT;

        $cmid  = $PAGE->cm->id;

        if (
            $PAGE->pagetype !== 'mod-customcert-view' ||
            empty($cmid) ||
            !isloggedin() ||
            isguestuser()
        ) {
            return;
        }

        $panel = new \local_socialcert\output\main_panel(cmid: $cmid);
        $context = $panel->export_for_template(output: $OUTPUT);
        $html  = $OUTPUT->render_from_template(
            'local_socialcert/main',
            $context
        );

        $hook->add_html($html);

        $PAGE->requires->js_call_amd('local_socialcert/actions', 'init', [
            'cmid' => $cmid,
        ]);
    }
}
