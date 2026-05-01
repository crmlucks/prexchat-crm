<?php

namespace Webkul\WhatsApp\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * Send message via Meta Cloud API
     */
    public function sendMetaMessage($to, $text)
    {
        $token = env('META_ACCESS_TOKEN');
        $phoneId = env('META_PHONE_NUMBER_ID');

        if (!$token || !$phoneId) {
            Log::error('Meta credentials missing.');
            return false;
        }

        try {
            $response = $this->client->post("https://graph.facebook.com/v19.0/{$phoneId}/messages", [
                'headers' => [
                    'Authorization' => "Bearer {$token}",
                    'Content-Type'  => 'application/json',
                ],
                'json' => [
                    'messaging_product' => 'whatsapp',
                    'to'                => $to,
                    'type'              => 'text',
                    'text'              => ['body' => $text],
                ],
            ]);

            return $response->getStatusCode() === 200;
        } catch (\Exception $e) {
            Log::error('Meta Send Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send message via Evolution API
     */
    public function sendEvolutionMessage($instance, $to, $text)
    {
        $url = env('EVOLUTION_API_URL');
        $key = env('EVOLUTION_API_KEY');

        if (!$url || !$key) {
            Log::error('Evolution credentials missing.');
            return false;
        }

        try {
            $response = $this->client->post("{$url}/message/sendText/{$instance}", [
                'headers' => [
                    'apikey' => $key,
                ],
                'json' => [
                    'number'      => $to,
                    'textMessage' => ['text' => $text],
                    'options'     => ['delay' => 1200, 'presence' => 'composing'],
                ],
            ]);

            return $response->getStatusCode() === 201 || $response->getStatusCode() === 200;
        } catch (\Exception $e) {
            Log::error('Evolution Send Error: ' . $e->getMessage());
            return false;
        }
    }
}
