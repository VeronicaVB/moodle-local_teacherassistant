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
 * External API for sending messages to Teacher Assistant.
 *
 * @package    local_teacherassistant
 * @copyright  2025 Veronica Bermegui
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_teacherassistant\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_value;
use core_external\external_single_structure;
use local_teacherassistant\ai\agent;

/**
 * External API for sending messages to Teacher Assistant.
 */
class send_message extends external_api {

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'Course ID'),
            'message' => new external_value(PARAM_TEXT, 'User message'),
        ]);
    }

    /**
     * Send a message to the AI assistant.
     *
     * @param int $courseid Course ID
     * @param string $message User message
     * @return array Response from AI
     */
    public static function execute($courseid, $message) {
        global $USER;

        // Validate parameters.
        $params = self::validate_parameters(self::execute_parameters(), [
            'courseid' => $courseid,
            'message' => $message,
        ]);

        // Validate context.
        $context = \context_course::instance($params['courseid']);
        self::validate_context($context);

        // Check capability.
        require_capability('local/teacherassistant:use', $context);

        try {
            // Initialize AI agent.
            $agent = new agent();

            // Check if agent is ready.
            if (!$agent->is_ready()) {
                throw new \moodle_exception('agentnotconfigured', 'local_teacherassistant');
            }

            // Send message to AI agent.
            $response = $agent->send_message($params['courseid'], $params['message']);

            return [
                'success' => true,
                'message' => $response,
            ];
        } catch (\moodle_exception $e) {
            return [
                'success' => false,
                'message' => get_string('error') . ': ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Returns description of method result value.
     *
     * @return external_single_structure
     */
    public static function execute_returns() {
        return new external_single_structure([
            'success' => new external_value(PARAM_BOOL, 'Whether the request was successful'),
            'message' => new external_value(PARAM_RAW, 'AI response message'),
        ]);
    }
}
