<?php

namespace App\Services\Chatbot;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class OpenAiResponsesClient
{
    public function isConfigured(): bool
    {
        return filled(config('services.openai.api_key'));
    }

    public function structured(array $messages, string $instructions, array $schema, string $schemaName = 'chat_plan'): array
    {
        $response = $this->send([
            'model' => config('services.openai.model'),
            'instructions' => $instructions,
            'input' => $this->normalizeMessages($messages),
            'text' => [
                'format' => [
                    'type' => 'json_schema',
                    'name' => $schemaName,
                    'strict' => true,
                    'schema' => $schema,
                ],
            ],
        ]);

        $text = $this->extractText($response);
        $parsed = json_decode($text, true);

        if (! is_array($parsed)) {
            throw new RuntimeException('OpenAI returned an invalid structured response.');
        }

        return [
            'raw' => $response,
            'text' => $text,
            'parsed' => $parsed,
        ];
    }

    public function text(array $messages, string $instructions): array
    {
        $response = $this->send([
            'model' => config('services.openai.model'),
            'instructions' => $instructions,
            'input' => $this->normalizeMessages($messages),
        ]);

        return [
            'raw' => $response,
            'text' => $this->extractText($response),
        ];
    }

    protected function send(array $payload): array
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('OpenAI is not configured.');
        }

        $baseUrl = rtrim((string) config('services.openai.base_url', 'https://api.openai.com/v1'), '/');

        try {
            return Http::asJson()
                ->acceptJson()
                ->withToken((string) config('services.openai.api_key'))
                ->timeout((int) config('services.openai.timeout', 30))
                ->post($baseUrl.'/responses', $payload)
                ->throw()
                ->json();
        } catch (RequestException $exception) {
            $message = data_get($exception->response?->json(), 'error.message')
                ?: $exception->getMessage();

            throw new RuntimeException($message, previous: $exception);
        }
    }

    protected function normalizeMessages(array $messages): array
    {
        return collect($messages)
            ->map(fn (array $message) => [
                'role' => $message['role'],
                'content' => $message['content'],
            ])
            ->values()
            ->all();
    }

    protected function extractText(array $response): string
    {
        $outputText = data_get($response, 'output_text');

        if (is_string($outputText) && trim($outputText) !== '') {
            return $outputText;
        }

        foreach (data_get($response, 'output', []) as $item) {
            foreach (($item['content'] ?? []) as $contentItem) {
                $text = $contentItem['text'] ?? null;

                if (is_string($text) && trim($text) !== '') {
                    return $text;
                }
            }
        }

        throw new RuntimeException('OpenAI returned an empty response.');
    }
}
