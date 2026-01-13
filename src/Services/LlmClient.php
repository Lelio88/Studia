<?php

namespace App\Services;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class LlmClient
{
    public function __construct(
        private HttpClientInterface $client,
        #[Autowire(env: 'GROQ_API_KEY')] private string $apiKey,
        #[Autowire(env: 'GROQ_API_URL')] private string $apiUrl = 'https://api.groq.com/openai/v1/chat/completions',
        #[Autowire(env: 'GROQ_MODEL')] private string $model = 'llama-3.3-70b-versatile'
    ) {}

    public function generateJson(string $systemPrompt, string $userPrompt): array
    {
        $response = $this->client->request('POST', $this->apiUrl, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'timeout' => 120,
            'json' => [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt]
                ],
                'response_format' => ['type' => 'json_object']
            ]
        ]);

        $content = $response->toArray();
        
        $rawText = $content['choices'][0]['message']['content'] ?? '{}';

        return $this->cleanAndDecode($rawText);
    }

    public function generateText(string $systemPrompt, string $userPrompt): string
    {
        $response = $this->client->request('POST', $this->apiUrl, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'timeout' => 60,
            'json' => [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt]
                ]
            ]
        ]);

        $content = $response->toArray();
        
        return $content['choices'][0]['message']['content'] ?? '';
    }

    private function cleanAndDecode(string $rawText): array
    {
        // Nettoyage au cas où le modèle ajoute du markdown malgré le json_object
        $cleanJson = str_replace(['```json', '```'], '', $rawText);
        
        $data = json_decode($cleanJson, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Erreur de décodage JSON IA : ' . json_last_error_msg());
        }

        return $data;
    }
}
