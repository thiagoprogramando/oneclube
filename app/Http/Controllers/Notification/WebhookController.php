<?php

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Gatway\AssasController;

use App\Models\Invoice;
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
            if ($sale && $sale->status_produto == null && $eventType == 'doc_signed') {
                $sale->status_produto = $eventType;
                $sale->save();

                $createInvoices = new AssasController;
                $createInvoices = $createInvoices->invoiceSale($sale->id);
                if($createInvoices) {

                    $invoice = Invoice::where('idUser', $sale->id)->where('status', 'PENDING_PAY')->where('type', 3)->first();
                    if($invoice) {

                        $sendLink = new WhatsAppController();
                        $message = "Prezado Cliente G7, *estamos enviando o link para pagamento* referente ao serviço de Limpa Nome: \r\n \r\n";
                        $sendLink = $sendLink->sendLink($sale->mobilePhone, $invoice->url, $message);
                        return response()->json(['message' => 'Processo concluído!'], 200);
                    }

                    return response()->json(['message' => 'Faturas criadas!'], 200);
                }
            }

            return response()->json(['message' => 'Nenhuma operação finalizada!'], 200);
        } else {
            
            return response()->json(['error' => 'Token e Event Não Localizados'], 200);
        }

        return response()->json(['error' => 'Webhook não utilizado.'], 200);
    }

}