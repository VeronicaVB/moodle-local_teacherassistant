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
 * Main AI Agent class - Entry point for all AI interactions.
 *
 * This class wraps the Neuron AI framework and provides a Moodle-friendly
 * interface for AI agent interactions. Neuron handles the LLM orchestration
 * and allows switching between providers (OpenAI, Claude, Gemini, etc.).
 *
 * @package    local_teacherassistant
 * @copyright  2025 Veronica Bermegui
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_teacherassistant\ai;

use local_teacherassistant\ai\config;
use UseTheFork\Synapse\Agent;
use UseTheFork\Synapse\Integrations\OpenAIIntegration;
use UseTheFork\Synapse\Integrations\AnthropicIntegration;
use UseTheFork\Synapse\Integrations\GeminiIntegration;
use UseTheFork\Synapse\Integrations\OllamaIntegration;
use UseTheFork\Synapse\ValueObject\Message;

/**
 * Main AI Agent class using Neuron framework.
 */
class agent {

    /** @var config Configuration instance */
    private $config;

    /** @var Agent Neuron agent instance */
    private $neuronagenT;

    /** @var array Conversation history */
    private $conversationhistory = [];

    /**
     * Constructor.
     *
     * @param config|null $config Configuration instance
     */
    public function __construct(?config $config = null) {
        $this->config = $config ?? new config();
        $this->check_neuron_installation();
        $this->initialize_neuron_agent();
    }

    /**
     * Check if Neuron framework is installed.
     *
     * @throws \moodle_exception If Neuron is not installed
     */
    private function check_neuron_installation(): void {
        if (!class_exists('UseTheFork\Synapse\Agent')) {
            throw new \moodle_exception('neuronnotinstalled', 'local_teacherassistant');
        }
    }

    /**
     * Initialize the Neuron agent with the configured LLM provider.
     *
     * @throws \moodle_exception If provider is not supported
     */
    private function initialize_neuron_agent(): void {
        $llmprovider = $this->config->get_llm_provider();
        $apikey = $this->config->get_api_key();

        // Create the appropriate integration based on LLM provider.
        $integration = $this->create_integration($llmprovider, $apikey);

        // Create Neuron agent.
        $this->neuronagent = new Agent(
            integrations: [$integration],
            instructions: $this->config->get_system_prompt()
        );
    }

    /**
     * Create the appropriate LLM integration for Neuron.
     *
     * @param string $provider Provider name
     * @param string $apikey API key
     * @return mixed Integration instance
     * @throws \moodle_exception If provider is not supported
     */
    private function create_integration(string $provider, string $apikey) {
        switch ($provider) {
            case 'openai':
                $config = ['apiKey' => $apikey];
                $orgid = $this->config->get_organization_id();
                if (!empty($orgid)) {
                    $config['organization'] = $orgid;
                }
                return new OpenAIIntegration($config);

            case 'claude':
                return new AnthropicIntegration([
                    'apiKey' => $apikey,
                ]);

            case 'gemini':
                return new GeminiIntegration([
                    'apiKey' => $apikey,
                ]);

            case 'ollama':
                // Ollama uses base URL instead of API key.
                $baseurl = $this->config->get_base_url();
                return new OllamaIntegration([
                    'baseUrl' => $baseurl,
                ]);

            case 'mistral':
                // TODO: Add support for Mistral when available in Neuron.
                throw new \moodle_exception('unsupportedllmprovider', 'local_teacherassistant', '', $provider);

            default:
                throw new \moodle_exception('unsupportedllmprovider', 'local_teacherassistant', '', $provider);
        }
    }

    /**
     * Send a message to the AI agent.
     *
     * @param int $courseid Course ID for context
     * @param string $message User message
     * @param array $options Additional options
     * @return string AI response
     * @throws \moodle_exception If the request fails
     */
    public function send_message(int $courseid, string $message, array $options = []): string {
        global $USER;

        try {
            // Build context for the AI.
            $context = $this->build_context($courseid);

            // Create a message with context if available.
            $fullmessage = $message;
            if (!empty($context)) {
                $contextstr = $this->format_context($context);
                $fullmessage = $contextstr . "\n\nUser question: " . $message;
            }

            // Create Neuron message.
            $neuronmessage = Message::make(
                role: 'user',
                content: $fullmessage
            );

            // Send to Neuron agent.
            $response = $this->neuronagent->handle($neuronmessage);

            // Extract response content.
            $responsecontent = $this->extract_response_content($response);

            // Log the interaction.
            $this->log_interaction($courseid, $USER->id, $message, $responsecontent);

            // Add to conversation history.
            $this->add_to_history('user', $message);
            $this->add_to_history('assistant', $responsecontent);

            return $responsecontent;

        } catch (\Exception $e) {
            debugging('Neuron agent error: ' . $e->getMessage(), DEBUG_DEVELOPER);
            throw new \moodle_exception('apirequestfailed', 'local_teacherassistant', '', null, $e->getMessage());
        }
    }

    /**
     * Send a message with conversation history.
     *
     * @param int $courseid Course ID for context
     * @param array $messages Array of messages with 'role' and 'content'
     * @param array $options Additional options
     * @return string AI response
     * @throws \moodle_exception If the request fails
     */
    public function send_conversation(int $courseid, array $messages, array $options = []): string {
        global $USER;

        try {
            // Convert messages to Neuron format.
            $neuronmessages = [];
            foreach ($messages as $msg) {
                $neuronmessages[] = Message::make(
                    role: $msg['role'],
                    content: $msg['content']
                );
            }

            // Get the last message to send.
            $lastmessage = end($neuronmessages);

            // Send to Neuron agent (Neuron manages history internally).
            $response = $this->neuronagent->handle($lastmessage);

            // Extract response content.
            $responsecontent = $this->extract_response_content($response);

            // Log the interaction.
            $lastusermessage = '';
            for ($i = count($messages) - 1; $i >= 0; $i--) {
                if ($messages[$i]['role'] === 'user') {
                    $lastusermessage = $messages[$i]['content'];
                    break;
                }
            }
            $this->log_interaction($courseid, $USER->id, $lastusermessage, $responsecontent);

            return $responsecontent;

        } catch (\Exception $e) {
            debugging('Neuron agent error: ' . $e->getMessage(), DEBUG_DEVELOPER);
            throw new \moodle_exception('apirequestfailed', 'local_teacherassistant', '', null, $e->getMessage());
        }
    }

    /**
     * Extract response content from Neuron response.
     *
     * @param mixed $response Neuron response
     * @return string Response content
     */
    private function extract_response_content($response): string {
        // Neuron returns a Message object or similar.
        // Adjust this based on actual Neuron response structure.
        if (is_object($response) && method_exists($response, 'content')) {
            return $response->content;
        } else if (is_string($response)) {
            return $response;
        } else if (is_array($response) && isset($response['content'])) {
            return $response['content'];
        }

        return (string)$response;
    }

    /**
     * Build context information for the AI.
     *
     * @param int $courseid Course ID
     * @return array Context array
     */
    private function build_context(int $courseid): array {
        global $DB, $USER;

        $context = [];

        // Get course information.
        try {
            $course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
            $context['course_name'] = $course->fullname;
            $context['course_shortname'] = $course->shortname;
        } catch (\dml_exception $e) {
            debugging('Failed to get course information: ' . $e->getMessage(), DEBUG_DEVELOPER);
        }

        // Get user role in course.
        $coursecontext = \context_course::instance($courseid);
        $roles = get_user_roles($coursecontext, $USER->id);
        if (!empty($roles)) {
            $role = reset($roles);
            $context['user_role'] = $role->shortname;
        }

        return $context;
    }

    /**
     * Format context array into a readable string.
     *
     * @param array $context Context data
     * @return string Formatted context
     */
    private function format_context(array $context): string {
        $parts = [];

        if (isset($context['course_name'])) {
            $parts[] = "Course: {$context['course_name']}";
        }

        if (isset($context['user_role'])) {
            $parts[] = "User role: {$context['user_role']}";
        }

        if (empty($parts)) {
            return '';
        }

        return "Context:\n" . implode("\n", $parts);
    }

    /**
     * Add a message to conversation history.
     *
     * @param string $role Message role (user, assistant, system)
     * @param string $content Message content
     */
    private function add_to_history(string $role, string $content): void {
        $this->conversationhistory[] = [
            'role' => $role,
            'content' => $content,
            'timestamp' => time(),
        ];
    }

    /**
     * Get conversation history.
     *
     * @return array Conversation history
     */
    public function get_history(): array {
        return $this->conversationhistory;
    }

    /**
     * Clear conversation history.
     */
    public function clear_history(): void {
        $this->conversationhistory = [];
    }

    /**
     * Log AI interaction to database.
     *
     * @param int $courseid Course ID
     * @param int $userid User ID
     * @param string $message User message
     * @param string $response AI response
     */
    private function log_interaction(int $courseid, int $userid, string $message, string $response): void {
        global $DB;

        try {
            $record = new \stdClass();
            $record->courseid = $courseid;
            $record->userid = $userid;
            $record->message = $message;
            $record->response = $response;
            $record->llmprovider = $this->config->get_llm_provider();
            $record->model = $this->config->get_model();
            $record->timecreated = time();

            // TODO: Create the database table 'local_teacherassistant_log'.
            // For now, we'll just debug log.
            debugging('AI Interaction logged: ' . json_encode($record), DEBUG_DEVELOPER);

            // Uncomment when table is created:
            // $DB->insert_record('local_teacherassistant_log', $record);
        } catch (\Exception $e) {
            debugging('Failed to log AI interaction: ' . $e->getMessage(), DEBUG_DEVELOPER);
        }
    }

    /**
     * Check if the AI agent is ready to use.
     *
     * @return bool True if ready
     */
    public function is_ready(): bool {
        return $this->config->is_configured() && isset($this->neuronagent);
    }

    /**
     * Get the Neuron agent instance.
     *
     * @return Agent|null Neuron agent instance
     */
    public function get_neuron_agent(): ?Agent {
        return $this->neuronagent ?? null;
    }

    /**
     * Get the configuration instance.
     *
     * @return config Configuration instance
     */
    public function get_config(): config {
        return $this->config;
    }

    /**
     * Validate the agent configuration.
     *
     * @return array Array of validation errors (empty if valid)
     */
    public function validate(): array {
        $errors = [];

        // Validate general configuration.
        $configerrors = $this->config->validate();
        $errors = array_merge($errors, $configerrors);

        // Check Neuron installation.
        if (!class_exists('UseTheFork\Synapse\Agent')) {
            $errors[] = 'Neuron framework is not installed';
        }

        return $errors;
    }
}
