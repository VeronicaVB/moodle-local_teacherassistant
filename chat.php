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
 * Full page chat interface for Teacher Assistant.
 *
 * @package    local_teacherassistant
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

require_login();

$courseid = required_param('courseid', PARAM_INT);
$course = get_course($courseid);
$context = context_course::instance($courseid);

// Require course enrollment and capability.
require_course_login($course);
require_capability('local/teacherassistant:use', $context);

$PAGE->set_context($context);
$PAGE->set_course($course);
$PAGE->set_url(new moodle_url('/local/teacherassistant/chat.php', ['courseid' => $courseid]));
$PAGE->set_title(get_string('pluginname', 'local_teacherassistant'));
$PAGE->set_heading($course->fullname);
$PAGE->set_pagelayout('standard');

// Add JavaScript module for full page chat.
$PAGE->requires->js_call_amd('local_teacherassistant/chat', 'init', [$courseid]);

echo $OUTPUT->header();

echo html_writer::start_div('teacherassistant-fullpage');
echo html_writer::tag('h2', get_string('pluginname', 'local_teacherassistant'));
echo html_writer::div('', 'teacherassistant-chat-container', ['id' => 'teacherassistant-chat-container']);
echo html_writer::end_div();

echo $OUTPUT->footer();
