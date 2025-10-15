<?php
// This file is part of Moodle - https://moodle.org/.
//
// See the GNU General Public License for more details: https://www.gnu.org/licenses/.

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

//   /**
//    * Devuelve la URL pluginfile de la primera imagen asociada al certificado
//    * en fileareas comunes de mod_customcert, o null si no hay.
//    *
//    * @param context_module $context
//    * @return string|null
//    */
//   public static function local_socialcert_get_first_customcert_image_url(\context_module $context): ?string {
//     $fs = get_file_storage();

//     // Fileareas donde suelen vivir las imágenes de customcert
//     // (elementos tipo "image" y fondos de página/plantilla).
//     $fileareas = ['image', 'background', 'pagebackground', 'element'];

//     foreach ($fileareas as $filearea) {
//       // itemid = 0 devuelve todos los itemid de ese filearea en el contexto.
//       $files = $fs->get_area_files(
//         $context->id, 'mod_customcert', $filearea,
//         0, 'itemid, filepath, filename', false // sin directorios
//       );

//       foreach ($files as $f) {
//         if (strpos($f->get_mimetype(), 'image/') === 0) {
//           return \moodle_url::make_pluginfile_url(
//             $f->get_contextid(),
//             $f->get_component(),
//             $f->get_filearea(),
//             $f->get_itemid(),
//             $f->get_filepath(),
//             $f->get_filename()
//           )->out(false);
//         }
//       }
//     }
//     return null;
//   }
// }
