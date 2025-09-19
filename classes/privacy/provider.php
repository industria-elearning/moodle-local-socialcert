<?php
// This file is part of Moodle - https://moodle.org/.
//
// See the GNU General Public License for more details: https://www.gnu.org/licenses/.

namespace local_certlinkedin\privacy;

defined('MOODLE_INTERNAL') || die();

use core_privacy\local\metadata\null_provider;

/**
 * Privacy API: this plugin stores no personal data.
 *
 * @package   local_certlinkedin
 */
class provider implements null_provider {

    /**
     * Explain that we do not store any personal data.
     *
     * @return string
     */
    public static function get_reason(): string {
        return 'privacy:metadata';
    }
}
