<?php
defined(constant_name: 'MOODLE_INTERNAL') || die();

function local_socialcert_extend_settings_navigation(settings_navigation $settingsnav, context $context): void {
    global $DB, $USER;

    if (!($context instanceof context_module)) {
        return;
    }

    $cm = get_coursemodule_from_id('', $context->instanceid, 0, false, IGNORE_MISSING);
    if (!$cm || $cm->modname !== 'customcert') {
        return;
    }

    $issue = $DB->get_record('customcert_issues', [
        'customcertid' => $cm->instance,
        'userid'       => $USER->id
    ], '*', IGNORE_MISSING);

    // URL INTERNA a la página que muestra el botón.
    $content = new moodle_url('/local/socialcert/add.php', ['cmid' => $cm->id]);

    $node = navigation_node::create(
        get_string('pluginname', 'local_socialcert'),
        $content,
        navigation_node::TYPE_SETTING,
        null,
        'local_socialcert_addtolinkedin',
        new pix_icon('i/export', '')
    );

    if ($modulesettings = $settingsnav->find('modulesettings', navigation_node::TYPE_SETTING)) {
        $modulesettings->add_node($node);
    } else {
        $settingsnav->add_node($node);
    }
}
