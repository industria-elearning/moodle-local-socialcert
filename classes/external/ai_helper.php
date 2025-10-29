<?php

namespace local_socialcert\external;

defined( 'MOODLE_INTERNAL') || die();

global $CFG;
require_once("$CFG->libdir/externallib.php");

use external_api;
use external_function_parameters;
use external_single_structure;
use external_value;
use aiprovider_datacurso\httpclient\ai_services_api;

class ai_helper extends external_api {
    /**
     * Defines the parameters accepted by the external function.
     *
     * Each request must include a `body` structure containing:
     * - certname: The certificate display name.
     * - course: The course name where the certificate was issued.
     * - org: The issuing organization's name.
     * - socialmedia: The platform where the certificate will be shared.
     *
     * @return external_function_parameters The parameter structure definition.
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'body' => new external_single_structure([
                'certname'    => new external_value(PARAM_TEXT, 'Certificate name'),
                'course'      => new external_value(PARAM_TEXT, 'Course name'),
                'org'         => new external_value(PARAM_TEXT, 'Name of the issuing organization'),
                'socialmedia' => new external_value(PARAM_TEXT, 'Social network where the certificate will be published'),
            ]),
            'cmid' => new external_value(PARAM_INT, 'Course module ID'),
        ]);
    }

    /**
     * Executes the external API request to generate AI certificate content.
     *
     * Validates input parameters, sends a POST request to the external AI service,
     * and returns the response JSON as a string. If the response is not an array
     * or object, it is wrapped into a JSON object with a "text" key.
     *
     * @param array $body The body data containing certificate information.
     * @param array $cmid The body data containing certificate information.
     * @return array An associative array with a 'json' key holding the API response.
     */
    public static function execute($body, $cmid) {
        $params = self::validate_parameters(self::execute_parameters(), ['body' => $body, 'cmid' => $cmid]);

        

        try {
            if($params['cmid'] <= 0){
                throw new \moodle_exception('invalidcmid', 'local_socialcert');
            }
            $context = \context_module::instance($params['cmid']);
            self::validate_context($context);
            require_capability('mod/customcert:view', $context);

            $body   = $params['body'];

            $client   = new ai_services_api();
            $response = $client->request('POST', '/certificate/answer', $body);
            if (is_array(value: $response) || is_object(value: $response)) {
                $json = json_encode(value: $response, flags: JSON_UNESCAPED_UNICODE);
            } else {
                $json = json_encode(value: ['text' => (string)$response], flags: JSON_UNESCAPED_UNICODE);
            }

            return ['json' => $json];
        } catch (\Exception $e) {
            debugging("Unexpected error while starting resource generation (stream): " . $e->getMessage());
            return [
                'ok' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Defines the return structure for the external function.
     *
     * Returns a single JSON string representing the AI-generated response
     * for the given certificate context.
     *
     * @return external_single_structure The definition of the return structure.
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'ok' => new external_value(PARAM_BOOL, 'Response status from server', VALUE_OPTIONAL),
            'message' => new external_value(PARAM_RAW, 'Response message from server', VALUE_OPTIONAL),
            'json' => new external_value(PARAM_RAW, 'Respuesta JSON de la API externa', VALUE_OPTIONAL),
        ]);
    }
}
