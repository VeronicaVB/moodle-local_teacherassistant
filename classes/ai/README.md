# AI Agent Structure

This directory contains all AI-related components for the Teacher Assistant plugin.

**Architecture**: This plugin uses **Neuron** as the AI orchestration framework. Neuron allows switching between different LLM providers (OpenAI, Claude, Gemini, etc.) with minimal code changes.

## Directory Structure

```
ai/
├── agent.php                  # Main Neuron agent wrapper - Entry point
├── config.php                 # Configuration manager (Neuron + LLM settings)
├── neuron/                    # Neuron framework integration
│   ├── agent_factory.php      # Creates and configures Neuron agents
│   ├── tools/                 # Custom tools for the agent
│   │   ├── course_tool.php
│   │   └── student_tool.php
│   └── prompts/               # Agent prompts and instructions
│       └── system_prompts.php
├── conversation/              # Conversation management (future)
│   ├── manager.php
│   └── history.php
└── README.md                  # This file
```

## Usage

### Basic Usage

```php
use local_teacherassistant\ai\agent;

// Initialize the Neuron-powered agent
$agent = new agent();

// Check if agent is ready
if ($agent->is_ready()) {
    // Send a message - Neuron handles LLM orchestration
    $response = $agent->send_message($courseid, 'What is this course about?');
    echo $response;
}
```

### Configuration

All configuration is managed through Moodle's settings page:
- Site administration > Plugins > Local plugins > Teacher Assistant

Required settings:
- **LLM Provider**: The underlying AI service (OpenAI, Claude, Gemini, etc.)
- **API Key**: Your authentication key for the LLM provider
- **AI Model**: The model identifier (e.g., gpt-4, claude-3-5-sonnet, gemini-pro)

Optional settings:
- **Max Tokens**: Maximum response length (default: 2000)
- **Temperature**: Response creativity (0.0-2.0, default: 0.7)
- **System Prompt**: Instructions for the AI assistant

**Note**: Neuron acts as the orchestration layer. You configure which LLM provider to use, and Neuron handles the communication and agent management.

## Architecture

### Component Responsibilities

1. **agent.php** - Neuron Agent Wrapper (Entry Point)
   - Wraps Neuron framework functionality
   - Provides Moodle-friendly interface
   - Manages conversation lifecycle
   - Logs interactions
   - Validates requests

2. **config.php** - Configuration Manager
   - Reads plugin settings (LLM provider, model, API keys)
   - Provides default values
   - Validates configuration
   - Supplies settings to Neuron agents

3. **neuron/** - Neuron Framework Integration
   - **agent_factory.php**: Creates and configures Neuron agents
   - **tools/**: Custom tools that agents can use (e.g., fetch course data, get student info)
   - **prompts/**: System prompts and agent instructions

### How It Works

```
User Request
    ↓
agent.php (Moodle interface)
    ↓
Neuron Framework (orchestration)
    ↓
LLM Provider (OpenAI/Claude/Gemini/etc.)
    ↓
Response back through the stack
```

### Switching LLM Providers

With Neuron, switching providers is simple - just change the configuration:

```php
// In settings or config.php
'llm_provider' => 'openai',     // or 'claude', 'gemini', etc.
'api_key' => 'your-api-key',
'model' => 'gpt-4',             // or 'claude-3-opus', etc.
```

Neuron handles all the provider-specific communication details.

## What is Neuron?

From the Neuron documentation:

> **Neuron is a PHP framework for creating and orchestrating AI Agents.** It allows you to integrate AI entities in your existing PHP applications with a powerful and flexible architecture.

> We provide tools for the entire agentic application development lifecycle, from LLM interfaces, to data loading, to multi-agent orchestration, to monitoring and debugging.

### Key Benefits for This Plugin

1. **Provider Agnostic**: Switch between OpenAI, Claude, Gemini, etc. with one line
2. **Agent Orchestration**: Build complex multi-agent systems
3. **Tool Support**: Agents can use custom tools (Moodle data access, etc.)
4. **PHP Native**: No Python dependencies or microservices needed
5. **Built for Production**: Monitoring, debugging, and error handling included

## Future Enhancements

Planned features:
- Custom Neuron tools for Moodle data (courses, students, grades)
- Multi-agent orchestration (specialist agents for different tasks)
- Conversation persistence and retrieval
- Context builder for rich course information
- Streaming responses
- RAG (Retrieval Augmented Generation) for course content

## Namespace

All classes use the namespace: `\local_teacherassistant\ai\`

Moodle's autoloader automatically maps:
- `classes/ai/agent.php` → `\local_teacherassistant\ai\agent`
- `classes/ai/neuron/agent_factory.php` → `\local_teacherassistant\ai\neuron\agent_factory`
- `classes/ai/config.php` → `\local_teacherassistant\ai\config`
