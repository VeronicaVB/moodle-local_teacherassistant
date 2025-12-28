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

/**
 * Hook callbacks for local_teacherassistant.
 *
 * @package    local_teacherassistant
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_teacherassistant;

use core\hook\output\before_standard_footer_html_generation;

/**
 * Hook callbacks for Teacher Assistant plugin.
 */
class hook_callbacks {

    /**
     * Callback for before_standard_footer_html_generation hook.
     * Injects the Teacher Assistant popup HTML and JavaScript.
     *
     * @param before_standard_footer_html_generation $hook
     */
    public static function before_standard_footer_html_generation(
        before_standard_footer_html_generation $hook
    ): void {
        global $PAGE;

        // Only inject on course pages.
        if (!isset($PAGE->context) || $PAGE->context->contextlevel !== CONTEXT_COURSE) {
            return;
        }

        // Don't inject on grading pages.
        $script = $PAGE->url->get_path();
        if (strpos($script, '/grade/') !== false ||
            strpos($script, 'grading') !== false ||
            strpos($script, '/local/teacherassistant/') !== false) {
            return;
        }

        // Check if user has capability.
        if (!has_capability('local/teacherassistant:use', $PAGE->context)) {
            return;
        }

        // Include the JavaScript module.
        $PAGE->requires->js_call_amd('local_teacherassistant/popup', 'init');

        // Add the HTML container for the popup.
        $hook->add_html('<div id="teacherassistant-container"></div>');
    }
}
