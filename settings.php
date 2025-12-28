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
 * Settings for the Teacher Assistant plugin.
 *
 * @package    local_teacherassistant
 * @copyright  2025 Veronica Bermegui
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_teacherassistant', get_string('pluginname', 'local_teacherassistant'));
    $ADMIN->add('localplugins', $settings);

    // Neuron Framework info.
    $settings->add(new admin_setting_heading(
        'local_teacherassistant/neuroninfo',
        get_string('neuroninfo', 'local_teacherassistant'),
        get_string('neuroninfo_desc', 'local_teacherassistant')
    ));

    // LLM Provider settings section.
    $settings->add(new admin_setting_heading(
        'local_teacherassistant/llmprovidersettings',
        get_string('llmprovidersettings', 'local_teacherassistant'),
        get_string('llmprovidersettings_desc', 'local_teacherassistant')
    ));

    // LLM Provider selection.
    $settings->add(new admin_setting_configselect(
        'local_teacherassistant/llm_provider',
        get_string('llm_provider', 'local_teacherassistant'),
        get_string('llm_provider_desc', 'local_teacherassistant'),
        'openai',
        [
            'openai' => 'OpenAI',
            'claude' => 'Claude (Anthropic)',
            'gemini' => 'Google Gemini',
            'mistral' => 'Mistral AI',
            'ollama' => 'Ollama (Local)',
        ]
    ));

    // API Key.
    $settings->add(new admin_setting_configpasswordunmask(
        'local_teacherassistant/api_key',
        get_string('api_key', 'local_teacherassistant'),
        get_string('api_key_desc', 'local_teacherassistant'),
        ''
    ));

    // Organization ID (optional, for OpenAI).
    $settings->add(new admin_setting_configtext(
        'local_teacherassistant/organization_id',
        get_string('organization_id', 'local_teacherassistant'),
        get_string('organization_id_desc', 'local_teacherassistant'),
        '',
        PARAM_TEXT
    ));

    // Base URL (for Ollama and other local providers).
    $settings->add(new admin_setting_configtext(
        'local_teacherassistant/base_url',
        get_string('base_url', 'local_teacherassistant'),
        get_string('base_url_desc', 'local_teacherassistant'),
        'http://localhost:11434',
        PARAM_URL
    ));

    // Model Configuration section.
    $settings->add(new admin_setting_heading(
        'local_teacherassistant/modelsettings',
        get_string('modelsettings', 'local_teacherassistant'),
        get_string('modelsettings_desc', 'local_teacherassistant')
    ));

    // AI Model.
    $settings->add(new admin_setting_configtext(
        'local_teacherassistant/ai_model',
        get_string('ai_model', 'local_teacherassistant'),
        get_string('ai_model_desc', 'local_teacherassistant'),
        'gpt-4',
        PARAM_TEXT
    ));

    // Max tokens.
    $settings->add(new admin_setting_configtext(
        'local_teacherassistant/max_tokens',
        get_string('max_tokens', 'local_teacherassistant'),
        get_string('max_tokens_desc', 'local_teacherassistant'),
        '2000',
        PARAM_INT
    ));

    // Temperature.
    $settings->add(new admin_setting_configtext(
        'local_teacherassistant/temperature',
        get_string('temperature', 'local_teacherassistant'),
        get_string('temperature_desc', 'local_teacherassistant'),
        '0.7',
        PARAM_FLOAT
    ));

    // Prompt Configuration section.
    $settings->add(new admin_setting_heading(
        'local_teacherassistant/promptsettings',
        get_string('promptsettings', 'local_teacherassistant'),
        get_string('promptsettings_desc', 'local_teacherassistant')
    ));

    // System prompt.
    $defaultprompt = 'You are a helpful teaching assistant for Moodle courses. ' .
                     'You help teachers and students with course-related questions. ' .
                     'Be concise, accurate, and supportive in your responses.';

    $settings->add(new admin_setting_configtextarea(
        'local_teacherassistant/system_prompt',
        get_string('system_prompt', 'local_teacherassistant'),
        get_string('system_prompt_desc', 'local_teacherassistant'),
        $defaultprompt,
        PARAM_TEXT
    ));
}
