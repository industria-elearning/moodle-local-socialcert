<?php
defined( 'MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_socialcert',
        get_string('pluginname', 'local_socialcert'));
    $ADMIN->add('localplugins', $settings);

    $settings->add(new admin_setting_configtext(
        'local_socialcert/organizationid',
        get_string('organizationid', 'local_socialcert'),
        get_string('organizationid_desc', 'local_socialcert'),
        '',
        PARAM_RAW_TRIMMED
    ));
    
    $settings->add(new admin_setting_configtext(
        'local_socialcert/organizationname',
        get_string('organizationname', 'local_socialcert'),
        get_string('organizationname_desc', 'local_socialcert'),
        '',
        PARAM_RAW_TRIMMED
    ));

    $settings->add(new admin_setting_configcheckbox(
        'local_socialcert/enableai',
        get_string('enableai', 'local_socialcert'),
        get_string('enableai_desc', 'local_socialcert'),
        1 
    ));
}