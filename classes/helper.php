<?php
// This file is part of Moodle - https://moodle.org/.
//
// See the GNU General Public License for more details: https://www.gnu.org/licenses/.

namespace local_certlinkedin;

defined('MOODLE_INTERNAL') || die();

/**
 * Helpers for building the LinkedIn Add-to-profile URL and rendering the button.
 *
 * @package   local_certlinkedin
 */
class helper {

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
        $orgid = (string) (get_config('local_certlinkedin', 'organizationid') ?? '');
        if ($orgid === '') {
            return null; // Not configured yet -> no button.
        }

        $params = [
            'startTask' => 'CERTIFICATION_NAME',
            'name'      => $certname,
            'organizationId' => $orgid,
            'issueYear'  => (int) date('Y', $issueunixtime),
            'issueMonth' => (int) date('n', $issueunixtime),
            'certUrl'    => $certurl,
            'certId'     => $certid,
        ];

        if (!empty($expiryunixtime)) {
            $params['expirationYear']  = (int) date('Y', $expiryunixtime);
            $params['expirationMonth'] = (int) date('n', $expiryunixtime);
        }

        // Encode using RFC3986 to get %20 instead of + for spaces.
        $query = http_build_query($params, '', '&', PHP_QUERY_RFC3986);
        return 'https://www.linkedin.com/profile/add?' . $query;
    }

    /**
     * Returns the HTML for the "Add to LinkedIn" button (or empty string if not configured).
     *
     * @param string $linkedinurl  URL built with build_linkedin_url().
     * @param string|null $label   Optional label (uses lang string by default).
     * @return string
     */
    public static function render_button(?string $linkedinurl, ?string $label = null): string {
        if (empty($linkedinurl)) {
            return ''; // No configuration or not applicable.
        }

        $label = $label ?? get_string('linkbuttontext', 'local_certlinkedin');

        // Use Moodle's html_writer to keep it simple and theme-friendly.
        return \html_writer::link($linkedinurl, $label, [
            'class' => 'btn btn-primary',
            'target' => '_blank',
            'rel' => 'noopener',
        ]);
    }
}
