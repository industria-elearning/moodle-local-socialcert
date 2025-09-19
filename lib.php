<?php
defined('MOODLE_INTERNAL') || die();

/**
 * Adds a mock "Add to LinkedIn" action to the activity navigation of Custom certificate.
 * MVP: Always shows the button with a dummy URL if the user has a certificate issued.
 *
 * @param settings_navigation $settingsnav
 * @param context $context
 * @return void
 */
function local_certlinkedin_extend_settings_navigation(settings_navigation $settingsnav, context $context): void {
    global $DB, $USER;

    if (!($context instanceof context_module)) {
        return;
    }

    // Only apply to customcert modules.
    $cm = get_coursemodule_from_id('', $context->instanceid, 0, false, IGNORE_MISSING);
    if (!$cm || $cm->modname !== 'customcert') {
        return;
    }

    // Does the user have an issued certificate?
    $issue = $DB->get_record('customcert_issues', [
        'customcertid' => $cm->instance,
        'userid' => $USER->id
    ], '*', IGNORE_MISSING);

    if (!$issue) {
        return;
    }

    // MVP: use a mock URL, doesn’t matter where it points.
    $mockurl = new moodle_url('https://example.com/mock-linkedin');

    $node = navigation_node::create(
        get_string('linkbuttontext', 'local_certlinkedin'),
        $mockurl,
        navigation_node::TYPE_SETTING,
        null,
        'local_certlinkedin_mvp',
        new pix_icon('i/export', '')
    );
    $node->showinflatnavigation = true;
    $node->attributes['target'] = '_blank';
    $node->attributes['rel'] = 'noopener';

    if ($modulesettings = $settingsnav->find('modulesettings', navigation_node::TYPE_SETTING)) {
        $modulesettings->add_node($node);
    } else {
        $settingsnav->add_node($node);
    }
}