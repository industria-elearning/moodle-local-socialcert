<?php

defined('MOODLE_INTERNAL') || die();

$callbacks = [
    [
        'hook' => \core\hook\output\before_standard_html_head_generation::class,
        'callback' => [\local_socialcert\hook_callbacks::class, 'before_standard_html_head_generation'],
    ],
    [
        'hook' => \core\hook\output\before_footer_html_generation::class,
        'callback' => [\local_socialcert\hook_callbacks::class, 'before_footer_html_generation'],
    ],
];
