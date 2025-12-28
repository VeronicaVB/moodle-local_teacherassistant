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
 * English language strings for local_teacherassistant.
 *
 * @package    local_teacherassistant
 * @copyright  2025 Veronica Bermegui
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Teacher Assistant';
$string['privacy:metadata'] = 'The Teacher Assistant plugin does not store any personal data.';
$string['teacherassistant:use'] = 'Use Teacher Assistant';
$string['expand'] = 'Expand';
$string['minimize'] = 'Minimize';
$string['send'] = 'Send';
$string['askquestion'] = 'Ask me anything about this course...';

// Neuron Framework.
$string['neuroninfo'] = 'Neuron AI Framework';
$string['neuroninfo_desc'] = 'This plugin uses <strong>Neuron</strong>, a PHP framework for creating and orchestrating AI agents. Neuron allows you to switch between different LLM providers (OpenAI, Claude, Gemini, etc.) seamlessly. Configure your preferred LLM provider below.';

// LLM Provider settings.
$string['llmprovidersettings'] = 'LLM Provider Settings';
$string['llmprovidersettings_desc'] = 'Configure which LLM (Large Language Model) provider to use. Neuron will handle the communication with your chosen provider.';
$string['llm_provider'] = 'LLM Provider';
$string['llm_provider_desc'] = 'Select the LLM service provider (OpenAI, Claude, Gemini, Mistral, or Ollama for local models).';
$string['api_key'] = 'API Key';
$string['api_key_desc'] = 'Your API key for authenticating with the selected LLM provider. Get this from your provider\'s dashboard. Not required for Ollama (local models).';
$string['organization_id'] = 'Organization ID';
$string['organization_id_desc'] = 'Optional. Your organization ID (required for some OpenAI accounts).';
$string['base_url'] = 'Base URL';
$string['base_url_desc'] = 'Base URL for local LLM providers like Ollama. Default: http://localhost:11434';

// Model configuration.
$string['modelsettings'] = 'Model Configuration';
$string['modelsettings_desc'] = 'Configure AI model parameters and behavior.';
$string['ai_model'] = 'AI Model';
$string['ai_model_desc'] = 'The AI model to use. Examples:<br>
- OpenAI: gpt-4, gpt-4-turbo, gpt-3.5-turbo<br>
- Claude: claude-3-5-sonnet-20241022, claude-3-opus-20240229<br>
- Gemini: gemini-pro, gemini-1.5-pro<br>
- Ollama: llama3.2, mistral, codellama (must be pulled first)';
$string['max_tokens'] = 'Max Tokens';
$string['max_tokens_desc'] = 'Maximum number of tokens in AI responses (1-32000).';
$string['temperature'] = 'Temperature';
$string['temperature_desc'] = 'Controls randomness in responses. Lower values (0.0-0.5) are more focused, higher values (0.5-2.0) are more creative.';

// Prompt configuration.
$string['promptsettings'] = 'Prompt Configuration';
$string['promptsettings_desc'] = 'Configure the AI system prompts and behavior instructions.';
$string['system_prompt'] = 'System Prompt';
$string['system_prompt_desc'] = 'The system prompt that defines the AI assistant\'s role and behavior.';

// Error messages.
$string['agentnotconfigured'] = 'The AI agent is not properly configured. Please check the plugin settings.';
$string['llmprovidernotconfigured'] = 'The LLM provider is not properly configured.';
$string['invalidjsonresponse'] = 'Invalid JSON response from AI service.';
$string['unexpectedresponseformat'] = 'Unexpected response format from AI service.';
$string['apirequestfailed'] = 'AI API request failed.';
$string['unsupportedllmprovider'] = 'Unsupported LLM provider: {$a}';
$string['neuronnotinstalled'] = 'Neuron framework is not installed. Please run: composer require use-the-fork/neuron';
