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

namespace local_socialcert;

defined('MOODLE_INTERNAL') || die();

/**
 * Helpers for building the LinkedIn Add-to-profile URL and rendering the button.
 *
 * @package   local_socialcert
 */
class linkedin_helper {

  /**
   * Builds the LinkedIn "Add to profile" URL for a certificate.
   *
   * Reference base: https://www.linkedin.com/profile/add
   *
   * Required params for our MVP:
   * - name: Certificate name to display in LinkedIn.
   * - organizationId: Admin-configured LinkedIn org/company ID (global setting).
   * - issueYear & issueMonth: From the certificate issue time.
   * - certUrl: Public verification URL (must be accessible without login).
   * - certId: Unique certificate id/code.
   *
   * @param string   $certname        Display name of the certificate.
   * @param int      $issueunixtime   Issued timestamp (UNIX).
   * @param string   $certurl         Public verification URL (no auth).
   * @param string   $certid          Unique certificate ID (e.g., issue code).
   * @param int|null $expiryunixtime  Optional expiration timestamp.
   * @return string|null              Fully built URL, or null if not enough config.
   */
  public static function build_linkedin_url(
    string $certname,
    int $issueunixtime,
    string $certurl,
    string $certid,
    ?int $expiryunixtime = null
  ): ?string {
    // OrganizationId is mandatory for the MVP.
    $defaultorgid = '1337'; // Replace with your default or test org ID.

    $raworgid = get_config('local_socialcert', 'organizationid'); // false si no existe
    $orgid = (string)(empty($raworgid) ? $defaultorgid : $raworgid);

    $params = [
      'startTask' => 'CERTIFICATION_NAME',
      'name'      => $certname,
      'organizationId' => $orgid,
      'issueYear'  => (int) date(format: 'Y', timestamp: $issueunixtime),
      'issueMonth' => (int) date(format: 'n', timestamp: $issueunixtime),
      'certUrl'    => $certurl,
      'certId'     => $certid
    ];

    if (!empty($expiryunixtime)) {
      $params['expirationYear']  = (int) date(format: 'Y', timestamp: $expiryunixtime);
      $params['expirationMonth'] = (int) date(format: 'n', timestamp: $expiryunixtime);
    }

    // Encode using RFC3986 to get %20 instead of + for spaces.
    $query = http_build_query(data: $params, numeric_prefix: '', arg_separator: '&', encoding_type: PHP_QUERY_RFC3986);
    return 'https://www.linkedin.com/profile/add?' . $query;
  }
}
