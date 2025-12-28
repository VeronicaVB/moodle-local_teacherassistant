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
 * AI Configuration manager.
 *
 * This class manages all configuration settings for the AI agent,
 * including provider selection, model configuration, and API credentials.
 *
 * @package    local_teacherassistant
 * @copyright  2025 Veronica Bermegui
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_teacherassistant\ai;

/**
 * Configuration manager for AI components.
 */
class config {

    /** @var string Default LLM provider */
    const DEFAULT_LLM_PROVIDER = 'openai';

    /** @var string Default model */
    const DEFAULT_MODEL = 'gpt-4';

    /** @var int Default max tokens */
    const DEFAULT_MAX_TOKENS = 2000;

    /** @var float Default temperature */
    const DEFAULT_TEMPERATURE = 0.7;

    /**
     * Get the configured LLM provider (OpenAI, Claude, Gemini, etc.).
     * Note: Neuron is the orchestration framework, this returns the underlying LLM provider.
     *
     * @return string LLM provider name (openai, claude, gemini, etc.)
     */
    public function get_llm_provider(): string {
        return get_config('local_teacherassistant', 'llm_provider') ?? self::DEFAULT_LLM_PROVIDER;
    }

    /**
     * Get the configured AI model.
     *
     * @return string Model identifier
     */
    public function get_model(): string {
        return get_config('local_teacherassistant', 'ai_model') ?? self::DEFAULT_MODEL;
    }

    /**
     * Get the API key for the current provider.
     *
     * @return string API key
     */
    public function get_api_key(): string {
        return get_config('local_teacherassistant', 'api_key') ?? '';
    }

    /**
     * Get the organization ID (for providers that require it like OpenAI).
     *
     * @return string Organization ID
     */
    public function get_organization_id(): string {
        return get_config('local_teacherassistant', 'organization_id') ?? '';
    }

    /**
     * Get the base URL (for local providers like Ollama).
     *
     * @return string Base URL
     */
    public function get_base_url(): string {
        return get_config('local_teacherassistant', 'base_url') ?? 'http://localhost:11434';
    }

    /**
     * Get maximum tokens for AI responses.
     *
     * @return int Maximum tokens
     */
    public function get_max_tokens(): int {
        $tokens = get_config('local_teacherassistant', 'max_tokens');
        return $tokens ? (int)$tokens : self::DEFAULT_MAX_TOKENS;
    }

    /**
     * Get temperature setting for AI responses.
     *
     * @return float Temperature (0.0 - 2.0)
     */
    public function get_temperature(): float {
        $temp = get_config('local_teacherassistant', 'temperature');
        return $temp !== false ? (float)$temp : self::DEFAULT_TEMPERATURE;
    }

    /**
     * Get system prompt template.
     *
     * @return string System prompt
     */
    public function get_system_prompt(): string {
        $default = 'You are a helpful teaching assistant for Moodle courses. ' .
                   'You help teachers and students with course-related questions.';
        return get_config('local_teacherassistant', 'system_prompt') ?? $default;
    }

    /**
     * Check if the AI system is properly configured.
     *
     * @return bool True if configured
     */
    public function is_configured(): bool {
        $provider = $this->get_llm_provider();

        // For Ollama, check base URL instead of API key.
        if ($provider === 'ollama') {
            return !empty($this->get_base_url());
        }

        // For other providers, check API key.
        $apikey = $this->get_api_key();
        return !empty($apikey);
    }

    /**
     * Get all configuration as an array.
     *
     * @return array Configuration array
     */
    public function get_all(): array {
        return [
            'llm_provider' => $this->get_llm_provider(),
            'model' => $this->get_model(),
            'api_key' => $this->get_api_key(),
            'organization_id' => $this->get_organization_id(),
            'base_url' => $this->get_base_url(),
            'max_tokens' => $this->get_max_tokens(),
            'temperature' => $this->get_temperature(),
            'system_prompt' => $this->get_system_prompt(),
        ];
    }

    /**
     * Validate the current configuration.
     *
     * @return array Array of validation errors (empty if valid)
     */
    public function validate(): array {
        $errors = [];

        $provider = $this->get_llm_provider();

        // Ollama doesn't need API key, but needs base URL.
        if ($provider === 'ollama') {
            if (empty($this->get_base_url())) {
                $errors[] = 'Base URL is required for Ollama';
            }
        } else {
            // Other providers need API key.
            if (empty($this->get_api_key())) {
                $errors[] = 'API key is not configured';
            }
        }

        $temp = $this->get_temperature();
        if ($temp < 0 || $temp > 2) {
            $errors[] = 'Temperature must be between 0 and 2';
        }

        $tokens = $this->get_max_tokens();
        if ($tokens < 1 || $tokens > 32000) {
            $errors[] = 'Max tokens must be between 1 and 32000';
        }

        return $errors;
    }
}
