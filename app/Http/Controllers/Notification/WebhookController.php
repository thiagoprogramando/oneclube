<?php

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Gatway\AssasController;

use App\Models\Invoice;
use App\Models\Sale;

use Illuminate\Http\Request;

class WebhookController extends Controller {

    public function webhookClicksing(Request $request) {

        $jsonData = $request->json()->all();
        if ($jsonData['event']['name'] === 'sign') {
            $key = $jsonData['document']['key'];

            $sale = Sale::where('id_contrato', $key)->first();
            if($sale && $sale->status_produto != 'doc_signed') {
                $sale->status_produto = 'doc_signed';
                $sale->save();

                $createInvoices = new AssasController;
                $createInvoices = $createInvoices->invoiceSale($sale->id);
                if($createInvoices) {

                    $invoice = Invoice::where('idUser', $sale->id)->where('status', 'PENDING_PAY')->where('type', 3)->first();
                    if($invoice) {

                        $sendLink = new WhatsAppController();
                        $message = "Prezado Cliente G7, *estamos enviando o link para pagamento* referente ao serviço de Limpa Nome: \r\n \r\n";
                        $sendLink = $sendLink->sendLink($sale->mobilePhone, $invoice->url, $message);
                        return response()->json(['message' => 'Ficha Finalizada Com Invoices Criadas e enviadas ao cliente!'], 200);
                    }

                    return response()->json(['message' => 'Ficha Finalizada Com Invoices Criadas.'], 200);
                }
            }

            return response()->json(['message' => 'Nenhuma operação finalizada!'], 200);
        }

        return response()->json(['error' => 'Webhook não utilizado.'], 200);
    }

}
