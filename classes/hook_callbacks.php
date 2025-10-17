<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

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

        if ($PAGE->pagetype !== 'mod-customcert-view' ||
            empty($PAGE->cm->id) || !isloggedin() || isguestuser()) {
            return;
        }

        $cmid  = $PAGE->cm->id;
        $panel = new \local_socialcert\output\main_panel(cmid: $cmid);
        $context = $panel->export_for_template(output: $OUTPUT);
        $html  = $OUTPUT->render_from_template(
            'local_socialcert/main',
            $context
        );

        $hook->add_html($html);

        $PAGE->requires->js_call_amd('local_socialcert/actions', 'init');
    }
}
