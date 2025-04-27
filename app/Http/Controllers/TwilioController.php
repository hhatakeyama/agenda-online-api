<?php

namespace App\Http\Controllers;

use App\Services\TwilioWhatsAppService;
use Illuminate\Http\Request;

class TwilioController extends Controller {
  protected $twilioWhatsAppService;

  public function __construct(TwilioWhatsAppService $twilioWhatsAppService) {
    $this->twilioWhatsAppService = $twilioWhatsAppService;
  }

  public function sendWhatsAppMessage(Request $request) {
    $request->validate([
      'to' => 'required|string',
      'message' => 'required|string',
    ]);

    $recipientNumber = $request->input('to'); // +5517999999999
    $messageBody = $request->input('message'); // teste

    $success = $this->twilioWhatsAppService->sendMessage($recipientNumber, $messageBody);

    if ($success) {
      return response()->json(['message' => 'Mensagem enviada com sucesso!']);
    } else {
      return response()->json(['error' => 'Falha ao enviar a mensagem.'], 500);
    }
  }
}
