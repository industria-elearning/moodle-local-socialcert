<?php
defined('MOODLE_INTERNAL') || die();

use local_certlinkedin\helper;

function local_certlinkedin_extend_settings_navigation(settings_navigation $settingsnav, context $context): void {
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
    if (!$issue) {
        return;
    }

    // 1) Intentamos LinkedIn real.
    require_once(__DIR__ . '/classes/helper.php');

    $customcert = $DB->get_record('customcert', ['id' => $cm->instance], '*', IGNORE_MISSING);
    $linkedinurl = null;
    if ($customcert) {
        $certname  = format_string($customcert->name, true, ['context' => $context]);
        $issued    = (int)$issue->timecreated;
        $certid    = $issue->code;
        $verifyurl = (new moodle_url('/mod/customcert/verify.php', ['code' => $issue->code]))->out(false);

        $linkedinurl = helper::build_linkedin_url($certname, $issued, $verifyurl, $certid, null);
        // build_linkedin_url() devuelve null si no hay organizationid configurado.
    }

    // 2) Fallback al mock si aún no está configurado.
    if (empty($linkedinurl)) {
        $linkedinurl = 'https://www.linkedin.com/profile/add?startTask=CERTIFICATION_NAME&name=Test%20Certificate&organizationId=1337&issueYear=2018& issueMonth=2&expirationYear=2020&expirationMonth=5&certUrl=https%3A%2F%2Fdocs.microsoft.com %2Fen-us%2Flearn%2Fcertifications%2Fd365-functional-consultant-sales&certId=1234';
    }

    $node = navigation_node::create(
        get_string('linkbuttontext', 'local_certlinkedin'),
        new moodle_url($linkedinurl),
        navigation_node::TYPE_SETTING,
        null,
        'local_certlinkedin_addtolinkedin',
        new pix_icon('i/export', '')
    );
    $node->showinflatnavigation = true;
    $node->attributes['target'] = '_blank';
    $node->attributes['rel'] = 'noopener';

    // $node->set_attribute('onclick', "window.open(this.href, '_blank'); return false;");
    // $node->set_attribute('rel', 'noopener');

    if ($modulesettings = $settingsnav->find('modulesettings', navigation_node::TYPE_SETTING)) {
        $modulesettings->add_node($node);
    } else {
        $settingsnav->add_node($node);
    }
}