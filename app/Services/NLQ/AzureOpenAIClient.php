<?php

namespace App\Services\NLQ;

use Illuminate\Support\Facades\Http;

class AzureOpenAIClient
{
    public function chat(array $messages, array $options = []): array
    {
        $endpoint = rtrim(config('services.azure_openai.endpoint'), '/');
        $deployment = config('services.azure_openai.deployment');
        $apiVersion = config('services.azure_openai.api_version');

        $url = "{$endpoint}/openai/deployments/{$deployment}/chat/completions?api-version={$apiVersion}";

        $response = Http::withHeaders([
            'api-key' => config('services.azure_openai.key'),
            'Content-Type' => 'application/json',
        ])->timeout(30)->post($url, array_merge([
            'messages' => $messages,
            'temperature' => 0,
            'max_tokens' => 800,
        ], $options));

        $response->throw();

        return $response->json();
    }
}
