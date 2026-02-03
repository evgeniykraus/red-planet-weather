<?php

namespace App\Clients;

use App\Clients\Contracts\BothubClientInterface;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class BothubClient implements BothubClientInterface
{
    protected string $baseUrl;

    protected string $apiKey;

    protected string $model;

    public function __construct()
    {
        $this->baseUrl = config('services.bothub.base_url');
        $this->apiKey = config('services.bothub.api_key');
        $this->model = config('services.bothub.model');
    }

    /**
     * @throws ConnectionException
     * @throws Exception
     */
    public function generateImage(string $prompt): array
    {
        $response = $this->request()->post('chat/completions', [
            'model' => $this->model,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => $prompt,
                        ],
                    ],
                ],
            ],
        ]);

        $this->throwIfError($response);

        $data = $response->json();

        return [
            // Текстовый ответ ассистента
            'base64_image' => $data['choices'][0]['message']['images'][0]['image_url']['url'] ?? null,
            // URL с изображением (сюда приходит data-uri)
            'content' => (string) ($data['choices'][0]['message']['content'] ?? ''),
        ];
    }

    protected function request(): PendingRequest
    {
        return Http::baseUrl($this->baseUrl)
            ->withToken($this->apiKey)
            ->acceptJson()
            ->timeout(30);
    }

    /**
     * @throws Exception
     */
    protected function throwIfError(Response $response): void
    {
        if ($response->failed()) {
            throw new Exception(
                "Bothub API error: {$response->status()} {$response->body()}"
            );
        }
    }
}
