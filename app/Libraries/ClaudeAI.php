<?php
namespace App\Libraries;

/**
 * AI Analysis Library — supports Claude, Google Gemini, and Groq
 * Switch provider via AI_PROVIDER in .env ('claude', 'gemini', or 'groq')
 */
class ClaudeAI
{
    private $provider;  // 'claude', 'gemini', or 'groq'
    private $apiKey;
    private $model;
    private $maxTokens = 4096;
    private $timeout = 60;

    public function __construct()
    {
        $this->provider = isset($_SERVER['AI_PROVIDER']) ? strtolower($_SERVER['AI_PROVIDER']) : 'claude';

        if ($this->provider === 'gemini') {
            $this->apiKey = isset($_SERVER['GEMINI_API_KEY']) ? $_SERVER['GEMINI_API_KEY'] : '';
            $this->model = isset($_SERVER['GEMINI_MODEL']) ? $_SERVER['GEMINI_MODEL'] : 'gemini-2.0-flash';
        } elseif ($this->provider === 'groq') {
            $this->apiKey = isset($_SERVER['GROQ_API_KEY']) ? $_SERVER['GROQ_API_KEY'] : '';
            $this->model = isset($_SERVER['GROQ_MODEL']) ? $_SERVER['GROQ_MODEL'] : 'llama-3.3-70b-versatile';
        } else {
            $this->apiKey = isset($_SERVER['CLAUDE_API_KEY']) ? $_SERVER['CLAUDE_API_KEY'] : '';
            $this->model = 'claude-sonnet-4-6';
        }
    }

    /**
     * Check if API key is configured
     */
    public function isConfigured()
    {
        return !empty($this->apiKey);
    }

    /**
     * Get current provider name
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * Analyze data with AI
     * @param string $systemPrompt
     * @param string $userMessage
     * @return array ['success', 'text', 'model', 'tokens_input', 'tokens_output', 'error']
     */
    public function analyze($systemPrompt, $userMessage)
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'error' => 'AI API key is not configured (' . $this->provider . ')',
                'text' => '',
                'model' => $this->model,
                'tokens_input' => 0,
                'tokens_output' => 0,
            ];
        }

        if ($this->provider === 'gemini') {
            return $this->sendGeminiRequest($systemPrompt, $userMessage);
        }

        if ($this->provider === 'groq') {
            return $this->sendGroqRequest($systemPrompt, $userMessage);
        }

        return $this->sendClaudeRequest($systemPrompt, $userMessage);
    }

    // =====================================================
    // Claude (Anthropic) API
    // =====================================================

    private function sendClaudeRequest($systemPrompt, $userMessage)
    {
        $url = 'https://api.anthropic.com/v1/messages';

        $headers = [
            'Content-Type: application/json',
            'x-api-key: ' . $this->apiKey,
            'anthropic-version: 2023-06-01',
        ];

        $payload = [
            'model' => $this->model,
            'max_tokens' => $this->maxTokens,
            'system' => $systemPrompt,
            'messages' => [
                ['role' => 'user', 'content' => $userMessage],
            ],
        ];

        $response = $this->curlPost($url, $headers, $payload);

        if (!$response['success']) {
            return $response;
        }

        $data = $response['data'];

        // Extract text from content blocks
        $text = '';
        if (isset($data['content']) && is_array($data['content'])) {
            foreach ($data['content'] as $block) {
                if (isset($block['type']) && $block['type'] === 'text') {
                    $text .= $block['text'];
                }
            }
        }

        return [
            'success' => true,
            'text' => $text,
            'model' => isset($data['model']) ? $data['model'] : $this->model,
            'tokens_input' => isset($data['usage']['input_tokens']) ? (int)$data['usage']['input_tokens'] : 0,
            'tokens_output' => isset($data['usage']['output_tokens']) ? (int)$data['usage']['output_tokens'] : 0,
            'error' => '',
        ];
    }

    // =====================================================
    // Groq API (OpenAI-compatible format)
    // =====================================================

    private function sendGroqRequest($systemPrompt, $userMessage)
    {
        $url = 'https://api.groq.com/openai/v1/chat/completions';

        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey,
        ];

        $payload = [
            'model' => $this->model,
            'max_tokens' => $this->maxTokens,
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userMessage],
            ],
        ];

        $response = $this->curlPost($url, $headers, $payload);

        if (!$response['success']) {
            return $response;
        }

        $data = $response['data'];

        // OpenAI-compatible response format
        $text = '';
        if (isset($data['choices'][0]['message']['content'])) {
            $text = $data['choices'][0]['message']['content'];
        }

        return [
            'success' => true,
            'text' => $text,
            'model' => isset($data['model']) ? $data['model'] : $this->model,
            'tokens_input' => isset($data['usage']['prompt_tokens']) ? (int)$data['usage']['prompt_tokens'] : 0,
            'tokens_output' => isset($data['usage']['completion_tokens']) ? (int)$data['usage']['completion_tokens'] : 0,
            'error' => '',
        ];
    }

    // =====================================================
    // Google Gemini API
    // =====================================================

    private function sendGeminiRequest($systemPrompt, $userMessage)
    {
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/' . $this->model . ':generateContent?key=' . $this->apiKey;

        $headers = [
            'Content-Type: application/json',
        ];

        $payload = [
            'system_instruction' => [
                'parts' => [['text' => $systemPrompt]],
            ],
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [['text' => $userMessage]],
                ],
            ],
            'generationConfig' => [
                'maxOutputTokens' => $this->maxTokens,
            ],
        ];

        $response = $this->curlPost($url, $headers, $payload);

        if (!$response['success']) {
            return $response;
        }

        $data = $response['data'];

        // Extract text from Gemini response
        $text = '';
        if (isset($data['candidates'][0]['content']['parts'])) {
            foreach ($data['candidates'][0]['content']['parts'] as $part) {
                if (isset($part['text'])) {
                    $text .= $part['text'];
                }
            }
        }

        // Check for blocked response
        if (empty($text) && isset($data['candidates'][0]['finishReason']) && $data['candidates'][0]['finishReason'] === 'SAFETY') {
            return [
                'success' => false,
                'error' => 'Gemini blocked the response due to safety filters',
                'text' => '',
                'model' => $this->model,
                'tokens_input' => 0,
                'tokens_output' => 0,
            ];
        }

        $tokensInput = isset($data['usageMetadata']['promptTokenCount']) ? (int)$data['usageMetadata']['promptTokenCount'] : 0;
        $tokensOutput = isset($data['usageMetadata']['candidatesTokenCount']) ? (int)$data['usageMetadata']['candidatesTokenCount'] : 0;

        return [
            'success' => true,
            'text' => $text,
            'model' => $this->model,
            'tokens_input' => $tokensInput,
            'tokens_output' => $tokensOutput,
            'error' => '',
        ];
    }

    // =====================================================
    // Shared cURL helper
    // =====================================================

    private function curlPost($url, $headers, $payload)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            return [
                'success' => false,
                'error' => 'cURL error: ' . $curlError,
                'text' => '',
                'model' => $this->model,
                'tokens_input' => 0,
                'tokens_output' => 0,
            ];
        }

        $data = json_decode($response, true);

        if ($httpCode !== 200 || !$data) {
            // Claude error format
            $errorMsg = isset($data['error']['message']) ? $data['error']['message'] : '';
            // Gemini/Groq error format
            if (empty($errorMsg) && isset($data['error']['status'])) {
                $errorMsg = $data['error']['status'] . ': ' . (isset($data['error']['message']) ? $data['error']['message'] : '');
            }
            if (empty($errorMsg)) {
                $errorMsg = 'HTTP ' . $httpCode;
            }

            return [
                'success' => false,
                'error' => $errorMsg,
                'text' => '',
                'model' => $this->model,
                'tokens_input' => 0,
                'tokens_output' => 0,
            ];
        }

        return [
            'success' => true,
            'data' => $data,
        ];
    }
}
