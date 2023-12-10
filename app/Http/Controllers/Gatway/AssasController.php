<?php

namespace App\Http\Controllers\Gatway;

use App\Http\Controllers\Controller;
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
        $customer = $this->createCustomer($sale->name, $sale->cpfcnpj);
    
        if($customer) {
            $invoiceCount = 0;
            $installmentCount = $sale->installmentCount;
            $initialPayment = 300;
    
            $dueDate = now()->addDay();
            $description = "Serviços & Consultoria G7";
    
            while ($invoiceCount < $installmentCount) {
                if ($invoiceCount > 0) {
                    $dueDate->addMonth();
                }
    
                if ($installmentCount <= 1) {
                    $chargeValue = $sale->value;
                } else {
                    $chargeValue = ($invoiceCount == 0) ? $initialPayment : (($sale->value - $initialPayment) / ($installmentCount - 1));
                }
    
                $charge = $this->createCharge($customer, $sale->billingType, $chargeValue, $description, $dueDate, $sale->wallet, $sale->comission);
    
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
    
        return false;
    }
    
    public function invoiceCreate(Request $request) {

        $user = auth()->user();
        if($user->customer) {

            $customer = $user->customer;
        } else {

            $customer = $this->createCustomer($user->name, $user->cpfcnpj);
            $user = User::where('id', $user->id)->first();
            $user->customer = $customer;
            $user->save();
        }

        if($customer) {
            $charge = $this->createCharge($customer, $request->billingType, $request->value, $request->description);
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

    private function createCustomer($name, $cpfcnpj) {
        
        $client = new Client();

        $options = [
            'headers' => [
                'Content-Type' => 'application/json',
                'access_token' => env('API_TOKEN_ASSAS'),
            ],
            'json' => [
                'name'      => $name,
                'cpfCnpj'   => $cpfcnpj,
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

    private function createCharge($customer, $billingType, $value, $description, $dueDate = null, $walletId = null, $percentualValue = null) {

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
            ],
            'verify' => false
        ];

        if ($walletId !== null) {
            $options['json']['split'] = [
                'walletId'          => $walletId,
                'percentualValue'   => $percentualValue.'.00',
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
                'email'         => $user->email,
                'cpfCnpj'       => $user->cpfcnpj,
                'birthDate'     => $user->birthDate,
                'mobilePhone'   => $user->mobilePhone,
                'address'       => $address->address,
                'addressNumber' => $address->addressNumber,
                'province'      => $address->province,
                'postalCode'    => $address->postalCode,
                "accountStatusWebhook" => [
                    "url"           => "https://g7.thiagoprogramando.com.br/api/webhookAccount",
                    "email"         => "thiago.or.code@gmail.com",
                    "interrupted"   => false,
                    "enabled"       =>  true,
                    "apiVersion"    =>  3,
                ],
                "transferWebhook"      => [
                    "url"           => "https://g7.thiagoprogramando.com.br/api/webhookAccount",
                    "email"         => "thiago.or.code@gmail.com",
                    "interrupted"   => false,
                    "enabled"       =>  true,
                    "apiVersion"    =>  3,
                ],
                "paymentWebhook"       => [
                    "url"           => "https://g7.thiagoprogramando.com.br/api/webhookAccount",
                    "email"         => "thiago.or.code@gmail.com",
                    "interrupted"   => false,
                    "enabled"       =>  true,
                    "apiVersion"    =>  3,
                ],
                "invoiceWebhook"        => [
                    "url"           => "https://g7.thiagoprogramando.com.br/api/webhookAccount",
                    "email"         => "thiago.or.code@gmail.com",
                    "interrupted"   => false,
                    "enabled"       =>  true,
                    "apiVersion"    =>  3,
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
                    $user->save();
                }
                $invoice->status = 'PAYMENT_CONFIRMED';
                $invoice->save();

                $sale = Sale::where('id', $invoice->idUser)->first();
                if($sale) {
                    $sale->status_pay = "PAYMENT_CONFIRMED";
                    $sale->save();
                }
            }

            return response()->json(['status' => 'success', 'message' => 'Requisição tratada!']);
        }

        return response()->json(['status' => 'success', 'message' => 'Webhook não utilizado!']);
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

        $response = $client->request('GET',  env('API_URL_ASSAS') . 'v3/finance/payment/statistics?status=PENDING', [
            'headers' => [
                'accept' => 'application/json',
                'access_token' => $user->apiKey,
            ],
            'verify' => false,
        ]);

        $body = (string) $response->getBody();
        if ($response->getStatusCode() === 200) {

            $data = json_decode($body, true);
            return $data['netValue'];
        } else {

            return false;
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
