<?php
defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    // 1) Crea la página y añádela al árbol de admin.
    $settings = new admin_settingpage('local_certlinkedin',
        get_string('pluginname', 'local_certlinkedin'));
    $ADMIN->add('localplugins', $settings);

    // 2) Añade el ajuste (dentro/independiente de fulltree; ambas formas valen).
    $settings->add(new admin_setting_configtext(
        'local_certlinkedin/organizationid',
        get_string('organizationid', 'local_certlinkedin'),
        get_string('organizationid_desc', 'local_certlinkedin'),
        '',
        PARAM_RAW_TRIMMED
    ));
}