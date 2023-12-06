<?php

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Gatway\AssasController;
use App\Http\Controllers\Sale\SaleController;
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
            if ($sale && $eventType == 'doc_signed') {
                $sale->status_produto = $eventType;
                $sale->save();

                $fichaAssociativa =  new SaleController();
                $ficha = $fichaAssociativa->fichaAssociativa($sale->id);
                if($ficha) {
                    return response()->json(['message' => 'Contrato Finalizado Com Ficha Enviada!'], 200);
                }

                return response()->json(['message' => 'Contrato Finalizado Sem Ficha Enviada!'], 200);
            }

            $sale = Sale::where('id_ficha', $token)->first();
            if ($sale && $eventType == 'doc_signed') {
                $sale->status_ficha = $eventType;
                $sale->save();

                $createInvoices = new AssasController;
                $createInvoices = $createInvoices->invoiceSale($sale->id);
                if($createInvoices) {
                    return response()->json(['message' => 'Ficha Finalizada Com Invoices Criadas.'], 200);
                }

                return response()->json(['message' => 'Ficha Finalizada Sem Invoices Criadas.'], 200);
            }

            return response()->json(['message' => 'Nenhuma operação finalizada!'], 200);
        } else {
            
            return response()->json(['error' => 'Webhook não utilizado.'], 200);
        }
    }

}
