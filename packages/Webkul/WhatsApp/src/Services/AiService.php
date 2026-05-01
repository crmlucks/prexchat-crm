<?php

namespace Webkul\WhatsApp\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class AiService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * Call n8n or direct OpenAI to get a response
     */
    public function getResponse($message, $context = [])
    {
        $webhookUrl = env('N8N_CHATBOT_WEBHOOK_URL');

        if (!$webhookUrl) {
            Log::warning('N8N_CHATBOT_WEBHOOK_URL not set, skipping AI response.');
            return 'Lo siento, el servicio de IA no está configurado.';
        }

        try {
            $response = $this->client->post($webhookUrl, [
                'json' => [
                    'message' => $message,
                    'context' => $context
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            return $data['output'] ?? $data['response'] ?? 'Entendido.';
        } catch (\Exception $e) {
            Log::error('AI Error: ' . $e->getMessage());
            return 'Hubo un error al procesar tu mensaje.';
        }
    }
}
