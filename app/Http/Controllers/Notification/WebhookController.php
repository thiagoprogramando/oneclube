<?php

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;

class WebhookController extends Controller {
    
    public function webhookZapSin(Request $request) {
        
        $jsonData = $request->getContent();
        $data = json_decode($jsonData, true);

        if (isset($data['token']) && isset($data['event_type'])) {
            
            $token = $data['token'];
            $eventType = $data['event_type'];

            $sale = Sale::where('id_contrato', $token)->first();
            if ($sale) {
                $sale->status_produto = $eventType;
                $sale->save();
            }

            return response()->json(['message' => 'Dados processados com sucesso'], 200);
        } else {
            
            return response()->json(['error' => 'Chaves ausentes no JSON recebido'], 400);
        }
    }

}
