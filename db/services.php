<?php

defined('MOODLE_INTERNAL') || die();

$functions = [
    'local_socialcert_get_ai_response' => [
        'classname'   => 'local_socialcert\\external\\ai_helper',
        'methodname'  => 'execute',
        'description' => 'Get AI response from the service.',
        'type'        => 'read',
        'ajax'        => true,
    ],
];