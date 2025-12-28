# Configuration Examples

This document provides configuration examples for different LLM providers.

## OpenAI Configuration

### GPT-4 (Recommended for quality)
```
LLM Provider: OpenAI
API Key: sk-proj-xxxxxxxxxxxxxxxxxxxxx
Organization ID: org-xxxxxxxxxxxxxx (optional)
AI Model: gpt-4-turbo
Max Tokens: 2000
Temperature: 0.7
```

### GPT-3.5 Turbo (Cost-effective)
```
LLM Provider: OpenAI
API Key: sk-proj-xxxxxxxxxxxxxxxxxxxxx
AI Model: gpt-3.5-turbo
Max Tokens: 2000
Temperature: 0.7
```

**Get API Key**: https://platform.openai.com/api-keys

**Pricing**:
- GPT-4 Turbo: $10/1M input tokens, $30/1M output tokens
- GPT-3.5 Turbo: $0.50/1M input tokens, $1.50/1M output tokens

---

## Claude (Anthropic) Configuration

### Claude 3.5 Sonnet (Best balance)
```
LLM Provider: Claude (Anthropic)
API Key: sk-ant-xxxxxxxxxxxxxxxxxxxxx
Base URL: (leave empty)
AI Model: claude-3-5-sonnet-20241022
Max Tokens: 4096
Temperature: 0.7
```

### Claude 3 Opus (Highest quality)
```
LLM Provider: Claude (Anthropic)
API Key: sk-ant-xxxxxxxxxxxxxxxxxxxxx
AI Model: claude-3-opus-20240229
Max Tokens: 4096
Temperature: 0.7
```

**Get API Key**: https://console.anthropic.com/settings/keys

**Pricing**:
- Claude 3.5 Sonnet: $3/1M input tokens, $15/1M output tokens
- Claude 3 Opus: $15/1M input tokens, $75/1M output tokens

---

## Google Gemini Configuration

### Gemini 1.5 Pro
```
LLM Provider: Google Gemini
API Key: AIzaSyxxxxxxxxxxxxxxxxxxxxxxxxx
Base URL: (leave empty)
AI Model: gemini-1.5-pro
Max Tokens: 2048
Temperature: 0.7
```

**Get API Key**: https://aistudio.google.com/app/apikey

**Pricing**:
- Gemini 1.5 Pro: $3.50/1M input tokens, $10.50/1M output tokens
- Gemini 1.5 Flash: $0.35/1M input tokens, $1.05/1M output tokens

---

## Ollama (Local) Configuration

### Llama 3.2 (Recommended for local)
```
LLM Provider: Ollama (Local)
API Key: (leave empty)
Base URL: http://localhost:11434
AI Model: llama3.2
Max Tokens: 2048
Temperature: 0.7
```

### Setup Steps:
```bash
# 1. Install Ollama
curl -fsSL https://ollama.ai/install.sh | sh

# 2. Pull the model
ollama pull llama3.2

# 3. Verify it's running
curl http://localhost:11434/api/tags
```

### Mistral (Fast and efficient)
```
LLM Provider: Ollama (Local)
API Key: (leave empty)
Base URL: http://localhost:11434
AI Model: mistral
Max Tokens: 2048
Temperature: 0.7
```

```bash
ollama pull mistral
```

**Pricing**: FREE (only hardware costs)

---

## Production Recommendations

### For Small Institutions (Budget-conscious)
```
Option 1: Ollama with Llama 3.2 (FREE)
- No API costs
- Requires local server with 8GB+ RAM
- Good quality for most use cases

Option 2: GPT-3.5 Turbo (Low cost)
- ~$2-5/month for typical usage
- No infrastructure needed
- Cloud-based
```

### For Medium Institutions (Balanced)
```
Option 1: Claude 3.5 Sonnet
- Best quality/cost ratio
- $10-30/month for typical usage
- Excellent for educational content

Option 2: Gemini 1.5 Pro
- Good quality, competitive pricing
- $5-20/month for typical usage
```

### For Large Institutions (Premium)
```
GPT-4 Turbo or Claude 3 Opus
- Highest quality responses
- $30-100/month for typical usage
- Best for high-stakes scenarios
```

---

## Environment-Specific Configurations

### Development Environment
```
LLM Provider: Ollama (Local)
API Key: (empty)
Base URL: http://localhost:11434
AI Model: phi3
Max Tokens: 1024
Temperature: 0.5

Why: Free, fast, good for testing
```

### Staging Environment
```
LLM Provider: OpenAI
API Key: sk-proj-test-xxxxx (separate test key)
AI Model: gpt-3.5-turbo
Max Tokens: 2000
Temperature: 0.7

Why: Low cost, production-like
```

### Production Environment
```
LLM Provider: Claude (Anthropic)
API Key: sk-ant-prod-xxxxx (production key)
AI Model: claude-3-5-sonnet-20241022
Max Tokens: 4096
Temperature: 0.7

Why: Best quality for users
```

---

## Multi-Model Strategy

Some institutions use different providers for different purposes:

### Strategy 1: Hybrid Approach
- **Quick Q&A**: Ollama (llama3.2) - Free, fast
- **Complex questions**: Claude 3.5 Sonnet - High quality
- **Code help**: GPT-4 - Best for programming

### Strategy 2: Failover
- **Primary**: Claude 3.5 Sonnet
- **Backup**: OpenAI GPT-3.5 Turbo (if Claude is down)
- **Development**: Ollama (local testing)

---

## Temperature Settings Guide

| Temperature | Use Case | Example |
|-------------|----------|---------|
| 0.0 - 0.3 | Factual answers, deterministic | Math problems, definitions |
| 0.4 - 0.7 | Balanced (default) | General Q&A, explanations |
| 0.8 - 1.0 | Creative responses | Brainstorming, creative writing |
| 1.1 - 2.0 | Very creative (risky) | Experimental only |

**Recommendation**: Start with 0.7 and adjust based on response quality.

---

## Max Tokens Guide

| Max Tokens | Use Case | Avg Response Length |
|------------|----------|---------------------|
| 512 | Short answers | 1-2 paragraphs |
| 1024 | Normal responses | 2-4 paragraphs |
| 2048 | Detailed explanations | 4-8 paragraphs |
| 4096 | Long-form content | 8+ paragraphs |

**Cost Tip**: Lower max tokens = lower costs per request

---

## Testing Your Configuration

After configuring, test with these prompts:

1. **Basic test**:
   ```
   "Hello, can you introduce yourself?"
   ```

2. **Educational test**:
   ```
   "Explain what photosynthesis is in simple terms."
   ```

3. **Course-specific test**:
   ```
   "What topics are covered in this course?"
   ```

If all tests work, your configuration is successful!

---

## Monitoring and Optimization

### Track These Metrics:
- Average response time
- Token usage per day/month
- User satisfaction
- Error rate

### Optimize By:
- Adjusting temperature for better responses
- Lowering max_tokens to reduce costs
- Switching providers if quality/cost isn't optimal
- Using Ollama for high-volume, low-stakes queries

---

## Security Best Practices

1. **Never commit API keys** to version control
2. **Use environment variables** for sensitive data
3. **Rotate API keys** regularly
4. **Set usage limits** in provider dashboards
5. **Monitor for unusual activity**
6. **Use separate keys** for dev/staging/production

---

## Cost Estimation

### Example: 100 students, 10 questions/day each

**With GPT-3.5 Turbo**:
- ~1000 requests/day
- ~500K tokens/day (average)
- Cost: ~$0.75/day = ~$22.50/month

**With Claude 3.5 Sonnet**:
- Same usage
- Cost: ~$2.50/day = ~$75/month

**With Ollama**:
- Same usage
- Cost: $0 (free, but needs ~$500 server one-time)

---

## Next Steps

1. Choose your provider based on budget and needs
2. Configure the settings in Moodle
3. Test thoroughly in development
4. Monitor usage and costs
5. Adjust configuration as needed
