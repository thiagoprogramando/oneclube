<?php

namespace App\Http\Controllers\Gatway;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Notification\WhatsAppController;
use App\Models\Address;
use Illuminate\Http\Request;

use App\Models\Invoice;
use App\Models\Sale;
use App\Models\User;

use GuzzleHttp\Client;
use Carbon\Carbon;

class AssasController extends Controller {

    public function invoiceSale($id) {

        $sale = Sale::find($id);
        $customer = $this->createCustomer($sale->name, $sale->cpfcnpj, $sale->mobilePhone, $sale->email);
    
        if($customer) {
            $invoiceCount = 0;
            $installmentCount = $sale->installmentCount;
            $initialPayment = max(390, $sale->valor / $sale->installmentCount);
    
            $dueDate = now()->addDay();
            $description = "Serviços & Consultoria G7";

            if($sale->billingType == "CREDIT_CARD") {
                $charge = $this->createCharge($customer, $sale->billingType, $sale->value, $description, $dueDate, $sale->wallet, $sale->commission, $sale->installmentCount);
    
                if ($charge) {
                    $invoice = new Invoice();
                    $invoice->idUser = $sale->id;
                    $invoice->name = "Parcela N° " . ($invoiceCount + 1);
                    $invoice->description = $description;
                    $invoice->token = $charge['id'];
                    $invoice->url = $charge['invoiceUrl'];
                    $invoice->value = $sale->value;
                    $invoice->status = "PENDING_PAY";
                    $invoice->type = 3;
                    $invoice->dueDate = $dueDate;
                    $invoice->save();
                }

                return true;
            } else {
                while ($invoiceCount < $installmentCount) {
                    if ($invoiceCount > 0) {
                        $dueDate->addMonth();
                    }
                    
                    if ($sale->installmentCount > 1) {
                        $remainingValue             = $sale->valor - $initialPayment; // Valor restante após a primeira parcela
                        $remainingInstallments      = $sale->installmentCount - 1; // Número de parcelas restantes
                        
                        if ($initialPayment <= 390) {
                            $commission = 0; // Não há comissão na primeira parcela
                            $chargeValue = ($remainingValue - 390) / $remainingInstallments; // Valor para as demais parcelas
                        } else {
                            $commission = $remainingValue / $remainingInstallments; // Comissão para as demais parcelas
                        }

                        $chargeValue  = $remainingValue / $remainingInstallments; // Valor para as demais parcelas
                    } else {
                        $chargeValue = $sale->valor;
                        $commission = 0;
                    }
                    
                    $charge = $this->createCharge($customer, $sale->billingType, $chargeValue, $description, $dueDate, $sale->wallet, $commission);
                    if ($charge) {
                        $invoice = new Invoice();
                        $invoice->idUser = $sale->id;
                        $invoice->name = "Parcela N° " . ($invoiceCount + 1);
                        $invoice->description = $description;
                        $invoice->token = $charge['id'];
                        $invoice->url = $charge['invoiceUrl'];
                        $invoice->value = $chargeValue;
                        $invoice->status = "PENDING_PAY";
                        $invoice->type = 3;
                        $invoice->dueDate = $dueDate;
                        $invoice->save();
        
                        $invoiceCount++;
                    }
                }
        
                return true;
            }
        }

        return false;
    }
    
    public function invoiceCreate(Request $request) {

        $user = auth()->user();
        if($user->customer) {
            $customer = $user->customer;
        } else {
            $customer = $this->createCustomer($user->name, $user->cpfcnpj, $user->mobilePhone, $user->email);
            $user = User::where('id', $user->id)->first();
            $user->customer = $customer;
            $user->save();
        }

        if($customer) {
            if($request->type  == 1 || $request->type  == 2) {
                $walletId = "afd76f74-6dd8-487b-b251-28205161e1e6";
                $percentualValue = "20";
                $dueDate = now()->addDay();
                $charge = $this->createCharge($customer, $request->billingType, $request->value, $request->description, $dueDate, $walletId, $percentualValue);
            } else {
                $charge = $this->createCharge($customer, $request->billingType, $request->value, $request->description);
            }
            
            if($charge) {
                $invoice = Invoice::where('id', $request->id)->first();
                $invoice->token = $charge['id'];
                $invoice->url = $charge['invoiceUrl'];
                $invoice->save();

                return redirect($charge['invoiceUrl']);
            }

            return redirect()->back()->with('error', 'Houve um problema, tente novamente mais tarde!');
        }

        return redirect()->back()->with('error', 'Tivemos um pequeno problema, tente novamente mais tarde!');
    }

    public function myAccount() {

        $user = auth()->user();

        $client = new Client();
        $options = [
            'headers' => [
                'Content-Type' => 'application/json',
                'access_token' => $user->apiKey,
            ],
            'verify' => false
        ];

        $response = $client->get(env('API_URL_ASSAS') . 'v3/myAccount/documents', $options);
        $body = (string) $response->getBody();
        
        if ($response->getStatusCode() === 200) {
            $data = json_decode($body, true);
    
            if (isset($data['data'])) {
                return $data['data'];
            } else {
                return [];
            }
        } else {
            return false;
        }
    }

    private function createCustomer($name, $cpfcnpj, $mobilePhone, $email) {
        
        $client = new Client();

        $options = [
            'headers' => [
                'Content-Type' => 'application/json',
                'access_token' => env('API_TOKEN_ASSAS'),
            ],
            'json' => [
                'name'          => $name,
                'cpfCnpj'       => $cpfcnpj,
                'mobilePhone'   => $mobilePhone,
                'email'         => $email,
            ],
            'verify' => false
        ];

        $response = $client->post(env('API_URL_ASSAS') . 'v3/customers', $options);
        $body = (string) $response->getBody();
        
        if ($response->getStatusCode() === 200) {
            $data = json_decode($body, true);
            return $data['id'];
        } else {
            return false;
        }
    }

    private function createCharge($customer, $billingType, $value, $description, $dueDate = null, $walletId = null, $percentualValue = null, $installmentCount = null) {

        $tomorrow = Carbon::now()->addDay()->format('Y-m-d');

        $client = new Client();

        $options = [
            'headers' => [
                'Content-Type' => 'application/json',
                'access_token' => env('API_TOKEN_ASSAS'),
            ],
            'json' => [
                'customer'          => $customer,
                'billingType'       => $billingType,
                'value'             => $value,
                'dueDate'           => $dueDate != null ? $dueDate : $tomorrow,
                'description'       => 'G7 - '.$description,
                'installmentCount'  => $installmentCount != null ? $installmentCount : 1,
                'installmentValue'  => $installmentCount != null ? ($value / $installmentCount) : $value,
            ],
            'verify' => false
        ];

        if ($walletId !== null && $percentualValue != 0) {
            if (!isset($options['json']['split'])) {
                $options['json']['split'] = [];
            }
        
            $options['json']['split'][] = [
                'walletId'          => $walletId,
                'totalFixedValue'   => number_format($percentualValue, 2, '.', ''),
            ];
        }        

        $response = $client->post(env('API_URL_ASSAS') . 'v3/payments', $options);
        $body = (string) $response->getBody();

        if ($response->getStatusCode() === 200) {
            $data = json_decode($body, true);
            return $dados['json'] = [
                'id'            => $data['id'],
                'invoiceUrl'    => $data['invoiceUrl'],
            ];
        } else {
            return "Erro!";
        }
    }

    private function createApiKey($id) {
        $client = new Client();

        $user = User::where('id', $id)->first();
        $address = Address::where('idUser', $id)->first();

        $options = [
            'headers' => [
                'Content-Type' => 'application/json',
                'access_token' => env('API_TOKEN_ASSAS'),
            ],
            'json' => [
                'name'          => $user->name,
                'email'         => $user->cpfcnpj.'@grupo7assessoria.com',
                'cpfCnpj'       => $user->cpfcnpj,
                'birthDate'     => $user->birthDate,
                'mobilePhone'   => $user->mobilePhone,
                'address'       => $address->address,
                'addressNumber' => $address->addressNumber,
                'province'      => $address->province,
                'postalCode'    => $address->postalCode,
                "accountStatusWebhook" => [
                    "url"           => "https://grupo7assessoria.com/api/webhookAccount",
                    "email"         => "suporte@grupo7assessoria.com",
                    "interrupted"   => false,
                    "enabled"       => true,
                    "apiVersion"    => 3,
                ],
                "transferWebhook"      => [
                    "url"           => "https://grupo7assessoria.com/api/webhookAccount",
                    "email"         => "suporte@grupo7assessoria.com",
                    "interrupted"   => false,
                    "enabled"       => true,
                    "apiVersion"    => 3,
                ],
                "paymentWebhook"       => [
                    "url"           => "https://grupo7assessoria.com/api/webhookAccount",
                    "email"         => "suporte@grupo7assessoria.com",
                    "interrupted"   => false,
                    "enabled"       => true,
                    "apiVersion"    => 3,
                ],
                "invoiceWebhook"        => [
                    "url"           => "https://grupo7assessoria.com/api/webhookAccount",
                    "email"         => "suporte@grupo7assessoria.com",
                    "interrupted"   => false,
                    "enabled"       => true,
                    "apiVersion"    => 3,
                ],
            ],
            'verify' => false
        ];

        if($user->companyType != 'PF'){
            $options['json']['companyType'] = $user->companyType;
        }

        $response = $client->post(env('API_URL_ASSAS') . 'v3/accounts', $options);
        $body = (string) $response->getBody();

        if ($response->getStatusCode() === 200) {
            $data = json_decode($body, true);
            return $dados['json'] = [
                'apiKey'      => $data['apiKey'],
                'walletId'    => $data['walletId'],
            ];
        } else {
            return "Erro!";
        }
    }

    public function webhookInvoice(Request $request) {
        $jsonData = $request->json()->all();
        
        if ($jsonData['event'] === 'PAYMENT_CONFIRMED' || $jsonData['event'] === 'PAYMENT_RECEIVED') {
            
            $token = $jsonData['payment']['id'];
            $invoices = Invoice::where('token', $token)->get();
            foreach ($invoices as $invoice) {

                if($invoice->type == 1) {
                    $createApiKey = $this->createApiKey($invoice->idUser);
                    $user = User::where('id', $invoice->idUser)->first();
                    $user->walletId = $createApiKey['walletId'];
                    $user->apiKey   = $createApiKey['apiKey'];
                    $user->status   = 2;
                    $user->save();
                }
                $invoice->status = 'PAYMENT_CONFIRMED';
                $invoice->save();

                $sale = Sale::where('id', $invoice->idUser)->first();
                if($sale) {
                    $sale->status_pay = "PAYMENT_CONFIRMED";
                    $sale->save();

                    $link    = 'https://grupo7assessoria.com/cliente';
                    $message = "✅🥳 Olá, cliente G7. Recebemos o seu pagamento, *segue link para acessar Faturas, consultar processos* e demais informações sobre seus contratos. \r\n\r\n PRONTO AGORA SÓ ACOMPANHAR 👇🏼📲";
                    $whatsapp = new WhatsAppController();
                    $sendLink = $whatsapp->sendLink($sale->mobilePhone, $link, $message);
                }
            }

            return response()->json(['status' => 'success', 'message' => 'Requisição tratada!']);
        }

        return response()->json(['status' => 'success', 'message' => 'Webhook não utilizado!']);
    }

    public function webhookAccount(Request $request) {
        
        $jsonData = $request->json()->all();
        $user = User::where('walletId', $jsonData['accountStatus']['id'])->first();

        switch ($jsonData['event']) {
            case 'ACCOUNT_STATUS_GENERAL_APPROVAL_APPROVED':
                $user->status = 1;
                $user->save();
                break;
            case 'ACCOUNT_STATUS_GENERAL_APPROVAL_PENDING':
                $user->status = 2;
                $user->save();
                break;
        }        
        return response()->json(['status' => 'success', 'message' => 'Tratamento realizado para status da Conta!']);
    }

    public function balance() {

        $client = new Client();
        $user = auth()->user();

        $response = $client->request('GET',  env('API_URL_ASSAS') . 'v3/finance/balance', [
            'headers' => [
                'accept' => 'application/json',
                'access_token' => $user->apiKey,
            ],
            'verify' => false,
        ]);

        $body = (string) $response->getBody();
        if ($response->getStatusCode() === 200) {

            $data = json_decode($body, true);
            return $data['balance'];
        } else {

            return false;
        }
    }

    public function statistics() {

        $client = new Client();
        $user = auth()->user();

        $response = $client->request('GET',  env('API_URL_ASSAS') . 'v3/finance/split/statistics', [
            'headers' => [
                'accept' => 'application/json',
                'access_token' => $user->apiKey,
            ],
            'verify' => false,
        ]);

        $body = (string) $response->getBody();
        if ($response->getStatusCode() === 200) {

            $data = json_decode($body, true);
            return $data['income'];
        } else {

            return false;
        }
    }

    public function extract() {

        $client = new Client();
        $user = auth()->user();
        $startDate = $user->created_at->toDateString();
        $finishDate = now()->toDateString();

        $response = $client->request('GET',  env('API_URL_ASSAS') . "v3/financialTransactions?startDate={$startDate}&finishDate={$finishDate}&order=desc", [
            'headers' => [
                'accept' => 'application/json',
                'access_token' => $user->apiKey,
            ],
            'verify' => false,
        ]);

        $body = (string) $response->getBody();
        if ($response->getStatusCode() === 200) {

            $data = json_decode($body, true);
            return $data['data'];
        } else {
            return [];
        }
    }

    public function withdraw($chave, $valor, $type) {
        $client = new Client();
        $user = auth()->user();

        try {
            $response = $client->request('POST', env('API_URL_ASSAS').'v3/transfers', [
                'headers' => [
                    'accept'       => 'application/json',
                    'Content-Type' => 'application/json',
                    'access_token' => $user->apiKey,
                ],
                'json' => [
                    'value' => $valor,
                    'operationType' => 'PIX',
                    'pixAddressKey' => $chave,
                    'pixAddressKeyType' => $type,
                    'description' => 'Saque G7',
                ],
                'verify'  => false,
            ]);
    
            $body = $response->getBody()->getContents();
            $decodedBody = json_decode($body, true);
    
            if ($decodedBody['status'] === 'PENDING') {
                return ['success' => true, 'message' => 'Saque agendado com sucesso'];
            } else {
                return ['success' => false, 'message' => 'Situação do Saque: ' . $decodedBody['status']];
            }
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $response = $e->getResponse();
            $body = $response->getBody()->getContents();
            $decodedBody = json_decode($body, true);
    
            return ['success' => false, 'error' => $decodedBody['errors'][0]['description']];
        }
    }
}
