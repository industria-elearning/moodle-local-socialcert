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

  public static function execute_parameters(): external_function_parameters {
    return new external_function_parameters([
      'body' => new external_single_structure([
        'certname'    => new external_value(PARAM_TEXT, 'Nombre del certificado'),
        'course'      => new external_value(PARAM_TEXT, 'Nombre del curso'),
        'org'         => new external_value(PARAM_TEXT, 'Nombre de la organización que emite el certificado'),
        'socialmedia' => new external_value(PARAM_TEXT, 'Red social donde se va a publicar el certificado'),
      ]),
    ]);
  }

  public static function execute($body) {

    $params = self::validate_parameters(self::execute_parameters(), ['body' => $body]);
    $body   = $params['body'];

    $client   = new ai_services_api();
    $response = $client->request('POST', '/certificate/answer', $body);

    if (is_array(value: $response) || is_object(value: $response)) {
      $json = json_encode(value: $response, flags: JSON_UNESCAPED_UNICODE);
    } else {
      $json = json_encode(value: ['text' => (string)$response], flags: JSON_UNESCAPED_UNICODE);
    }

    return ['json' => $json];
  }

  public static function execute_returns(): external_single_structure {
    return new external_single_structure([
      'json' => new external_value(PARAM_RAW, 'Respuesta JSON de la API externa'),
    ]);
  }
}