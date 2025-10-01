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

     public static function build_linkedin_article_html(
        string $certname,
        int $issueunixtime,
        string $certurl,
        string $certid,
        ?int $expiryunixtime = null
    ): string {

        // Use NOWDOC to avoid interpolation. The closing label must be at column 0.
        $bodyhtml = <<<'HTML'
            <h1>Compelling Title of the Article</h1>
            <p>Short intro paragraph that states the problem and the value readers will get.</p>

            <h2>Key Takeaways</h2>
            <ul>
            <li>Takeaway one.</li>
            <li>Takeaway two.</li>
            <li>Takeaway three.</li>
            </ul>

            <h2>Background</h2>
            <p>Explain the context with clear sentences and short paragraphs.</p>

            <blockquote>Memorable pull quote or key insight to highlight.</blockquote>

            <h2>How-To / Steps</h2>
            <ol>
            <li><strong>Step 1:</strong> What to do and why it matters.</li>
            <li><strong>Step 2:</strong> Keep steps concise.</li>
            <li><strong>Step 3:</strong> Link to sources <a href="https://example.com">like this</a>.</li>
            </ol>

            <figure>
            <img src="https://example.com/your-image.jpg" alt="Descriptive alt text">
            <figcaption>Short caption for accessibility and context.</figcaption>
            </figure>

            <h2>Conclusion</h2>
            <p>Wrap up with a call to action or question for readers.</p>
        HTML;

        $article = sprintf(
            "<h1>%s</h1>\n<p>Issued on %s</p>\n%s",
            htmlspecialchars($certname, ENT_QUOTES),
            userdate($issueunixtime),
            $bodyhtml
        );

        return $article;
    }

    /**
     * @param string|null $linkedinurl URL for real LinkedIn action (or null/# when using JS).
     * @param string $label Button text.
     * @param array $attrs Extra HTML attributes (data-action, data-target, class, etc.).
     * @return string HTML for a button or link.
     */
    public static function render_button(?string $linkedinurl, string $label = '', array $attrs = []): string {
        // Base classes; allow caller to override/extend.
        $attrs = array_merge(['class' => 'btn btn-primary'], $attrs);

        // If it has a JS action, render a real <button>.
        if (!empty($attrs['data-action'])) {
            return \html_writer::tag('button', $label, $attrs);
        }

        // Otherwise render a normal link (LinkedIn share, etc.).
        if (empty($linkedinurl)) {
            return ''; // Nothing to render.
        }

        $linkattrs = array_merge($attrs, ['target' => '_blank', 'rel' => 'noopener']);
        return \html_writer::link($linkedinurl, $label, $linkattrs);
    }
}
