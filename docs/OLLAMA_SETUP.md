# Ollama Setup Guide

This guide explains how to configure the Teacher Assistant plugin to use Ollama for local LLM inference.

## What is Ollama?

Ollama is a tool that allows you to run large language models locally on your own machine. This means:
- **No API costs** - Run models for free
- **Privacy** - Your data never leaves your server
- **No internet required** - Works offline
- **Fast** - No network latency

## Prerequisites

1. **Install Ollama** on your server or local machine
   - Visit: https://ollama.ai
   - Download and install for your OS (Linux, macOS, Windows)

2. **Pull a model** (download it locally)
   ```bash
   # Popular models:
   ollama pull llama3.2        # Meta's Llama 3.2 (recommended)
   ollama pull mistral         # Mistral 7B
   ollama pull codellama       # Code-focused model
   ollama pull phi3            # Microsoft Phi-3 (small, fast)
   ```

3. **Verify Ollama is running**
   ```bash
   # Check if Ollama is running
   curl http://localhost:11434/api/tags

   # You should see a JSON response with your pulled models
   ```

## Moodle Configuration

1. **Navigate to Plugin Settings**
   - Site administration → Plugins → Local plugins → Teacher Assistant

2. **Configure LLM Provider**
   - **LLM Provider**: Select `Ollama (Local)`
   - **API Key**: Leave empty (not needed for Ollama)
   - **Base URL**: Enter your Ollama URL
     - Default: `http://localhost:11434`
     - If Ollama is on another server: `http://your-server-ip:11434`
     - If using Docker: `http://ollama:11434` (container name)

3. **Configure Model**
   - **AI Model**: Enter the exact name of your pulled model
     - Examples: `llama3.2`, `mistral`, `codellama`
     - Must match the name from `ollama list`
   - **Max Tokens**: 2000-4096 (depending on model)
   - **Temperature**: 0.7 (default) or adjust for creativity

4. **Test the Configuration**
   - Go to any course
   - Open the Teacher Assistant
   - Send a test message: "Hello, can you help me?"
   - You should get a response from your local model

## Advanced Configuration

### Running Ollama on a Different Server

If Ollama is running on a separate server (recommended for production):

1. **On the Ollama server**, configure it to accept external connections:
   ```bash
   # Set environment variable
   export OLLAMA_HOST=0.0.0.0:11434

   # Restart Ollama
   systemctl restart ollama
   ```

2. **In Moodle settings**, set Base URL to:
   ```
   http://your-ollama-server-ip:11434
   ```

### Using Docker

If running Ollama in Docker:

```bash
# Run Ollama container
docker run -d \
  --name ollama \
  -p 11434:11434 \
  -v ollama:/root/.ollama \
  ollama/ollama

# Pull a model
docker exec -it ollama ollama pull llama3.2

# In Moodle, use: http://localhost:11434
# Or if Moodle is also in Docker: http://ollama:11434
```

### GPU Acceleration

For better performance, use GPU acceleration:

```bash
# NVIDIA GPU
docker run -d \
  --gpus all \
  --name ollama \
  -p 11434:11434 \
  -v ollama:/root/.ollama \
  ollama/ollama
```

## Recommended Models for Education

| Model | Size | Best For | Memory Required |
|-------|------|----------|-----------------|
| **llama3.2** | 3B | General Q&A, balanced | 4GB RAM |
| **mistral** | 7B | High quality responses | 8GB RAM |
| **phi3** | 3.8B | Fast responses, efficient | 4GB RAM |
| **codellama** | 7B | Programming assistance | 8GB RAM |
| **llama3.2:70b** | 70B | Best quality (needs GPU) | 40GB+ VRAM |

## Performance Tips

1. **Use smaller models** for faster responses
   - `phi3` or `llama3.2` are good for general use

2. **Adjust context length**
   - Lower `max_tokens` = faster responses

3. **Use GPU** if available
   - Dramatically speeds up inference

4. **Consider model quantization**
   ```bash
   # 4-bit quantized version (smaller, faster)
   ollama pull llama3.2:4bit
   ```

## Troubleshooting

### Error: "Cannot connect to Ollama"

**Solution 1**: Check if Ollama is running
```bash
curl http://localhost:11434/api/tags
```

**Solution 2**: Check firewall settings
```bash
# Allow port 11434
sudo ufw allow 11434/tcp
```

**Solution 3**: Verify Base URL in Moodle settings

### Error: "Model not found"

**Solution**: Pull the model first
```bash
ollama pull llama3.2
```

### Slow responses

**Solutions**:
- Use a smaller model (`phi3` instead of `mistral`)
- Reduce `max_tokens` in settings
- Use GPU acceleration
- Pull a quantized version of the model

## Security Considerations

1. **Firewall**: If Ollama is on a public server, restrict access
   ```bash
   # Only allow from Moodle server IP
   sudo ufw allow from MOODLE_SERVER_IP to any port 11434
   ```

2. **Authentication**: Consider using a reverse proxy with auth
   ```nginx
   location /ollama/ {
       proxy_pass http://localhost:11434/;
       auth_basic "Restricted";
       auth_basic_user_file /etc/nginx/.htpasswd;
   }
   ```

3. **Resource limits**: Set limits to prevent abuse
   ```bash
   # Limit concurrent requests in Ollama
   export OLLAMA_MAX_LOADED_MODELS=1
   ```

## Cost Comparison

| Provider | Cost per 1M tokens | Ollama Cost |
|----------|-------------------|-------------|
| OpenAI GPT-4 | ~$30 | $0 (free) |
| Claude Opus | ~$15 | $0 (free) |
| Gemini Pro | ~$3.5 | $0 (free) |
| **Ollama** | **$0** | **Only hardware** |

**Note**: Ollama has one-time hardware costs but zero API fees.

## Next Steps

After setting up Ollama:
1. Test with different models to find the best fit
2. Monitor response quality and speed
3. Consider creating custom model prompts
4. Explore fine-tuning for your specific use case

## Resources

- Ollama Documentation: https://github.com/ollama/ollama
- Model Library: https://ollama.ai/library
- Neuron + Ollama: https://github.com/use-the-fork/synapse
