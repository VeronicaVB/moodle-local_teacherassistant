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
 * Main page for local_teacherassistant.
 *
 * @package    local_teacherassistant
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

require_login();
$context = context_system::instance();

// Allow site admins or users with the capability.
if (!is_siteadmin() && !has_capability('local/teacherassistant:use', $context)) {
    throw new required_capability_exception($context, 'local/teacherassistant:use', 'nopermissions', '');
}

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/teacherassistant/index.php'));
$PAGE->set_title(get_string('pluginname', 'local_teacherassistant'));
$PAGE->set_heading(get_string('pluginname', 'local_teacherassistant'));

$PAGE->requires->js_call_amd(
            'local_teacherassistant/chat',
            'init', [2]);

echo $OUTPUT->header();

$data = new \stdClass();
$data->courseid = 2;
echo $OUTPUT->render_from_template('local_teacherassistant/chat_fullpage', $data);
echo $OUTPUT->footer();
