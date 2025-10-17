<?php
namespace local_socialcert\output;

use renderable;
use templatable;
use renderer_base;
use context_course;
use context_module;
use moodle_url;

class main_panel implements renderable, templatable {
    /** @var int */
    protected $cmid;

    public function __construct(int $cmid) {
        $this->cmid = $cmid;
    }

    public function export_for_template(renderer_base $output): array {
        global $DB, $USER;

        $cm     = get_coursemodule_from_id('', $this->cmid, 0, false, MUST_EXIST);
        $course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
        $context = context_module::instance($cm->id);

        // ¿Existe emisión?
        $issue = $DB->get_record('customcert_issues', [
            'customcertid' => $cm->instance,
            'userid'       => $USER->id
        ], '*', IGNORE_MISSING);

        $customcert = $DB->get_record('customcert', ['id' => $cm->instance], '*', IGNORE_MISSING);

        $certname = '';
        $issuedts = 0;
        $certid = '';
        $verifyurl = '';
        $shareurl = null;
        $datanetwork = '';

        if ($customcert && $issue) {
            $certname  = format_string($customcert->name, true, ['context' => $context]);
            $issuedts  = (int) $issue->timecreated;
            $certid    = $issue->code;
            $verifyurl = (new moodle_url('/mod/customcert/verify.php', ['code' => $issue->code]))->out(false);

            // Helper tuyo (corrijo los “||” por operador ternario null-coalesce/Elvis):
            $shareurl = \local_socialcert\linkedin_helper::build_linkedin_url(
                certname: $certname,
                issueunixtime: $issuedts,
                certurl: $verifyurl ?: '',
                certid: $certid ?: ''
            );

            $datanetwork = 'linkedin';
        }

        $issued = $issue ? false : true;

        $orgname  = get_config('local_socialcert', 'organizationname');
        $enableai = (bool)((int) get_config('local_socialcert', 'enableai'));

        $coursefullname = format_string($course->fullname, true, ['context' => context_course::instance($course->id)]);
        $displayname    = format_string(fullname($USER), true, ['context' => $context]);

        $imgurl = (new moodle_url('/local/socialcert/assets/logo_title.png'))->out(false);

        return [
            'aibuttonid'        => 'btn-ai',
            'buttonid'          => 'btn-normal',
            'imageid'           => 'ai-image',
            'responseid'        => 'ai-response',
            'socialmedia'       => 'LinkedIn',
            'author_name'       => $displayname,
            'certid'            => $certid,
            'certname'          => $certname,
            'cmid'              => $cm->id,
            'course'            => $coursefullname,
            'datanetwork'       => $datanetwork,
            'enableai'          => $enableai,
            'imageurl'          => $imgurl,
            'issued'            => $issued,
            'org'               => $orgname,
            'shareurl'          => $shareurl,
            'verifyurl'         => $verifyurl,
            'ai_actioncall'     => get_string('ai_actioncall', 'local_socialcert'),
            'buttonlabel'       => get_string('linkcertbuttontext', 'local_socialcert'),
            'buttonlabelshare'  => get_string('buttonlabelshare', 'local_socialcert'),
            'intro'             => get_string('shareinstruction', 'local_socialcert'),
            'linktext'          => get_string('linktext', 'local_socialcert'),
            'popupblocked'      => get_string('popupblocked', 'local_socialcert'),
            'sharecompleted'    => get_string('sharecompleted', 'local_socialcert'),
            'sharesubtitle'     => get_string('sharesubtitle', 'local_socialcert'),
            'sharetitle'        => get_string('sharetitle', 'local_socialcert'),
            'whatsharelabel'    => get_string('whatsharelabel', 'local_socialcert'),
            'certerror'         => get_string('certerror', 'local_socialcert'),
        ];
    }
}
