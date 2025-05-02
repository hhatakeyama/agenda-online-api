<?php

namespace App\Services;

date_default_timezone_set('America/Sao_Paulo');

use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class TwilioWhatsAppService {
    protected $sid;
    protected $token;
    protected $fromNumber;

    public function __construct() {
        $this->sid = config('services.twilio.sid');
        $this->token = config('services.twilio.token');
        $this->fromNumber = config('services.twilio.whatsapp_number');
    }

    public function sendMessage(string $to, string $message): bool {
        try {
            $client = new Client($this->sid, $this->token);

            $client->messages->create(
                'whatsapp:' . $to, // Para quem vamos enviar a mensagem
                [
                    'from' => 'whatsapp:' . $this->fromNumber, // Seu número Twilio para WhatsApp
                    'body' => $message,
                ]
            );

            return true;
        } catch (\Exception $e) {
            // Log de erro (recomendado)
            Log::error('Erro ao enviar mensagem via Twilio: ' . $e->getMessage());
            return false;
        }
    }
}
