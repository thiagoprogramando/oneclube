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
            $initialPayment = 300;
            $valuePerInstallment = ($sale->value - $initialPayment) / ($sale->installmentCount - 1);
            $installmentCount = $sale->installmentCount;
            $dueDate = now()->addDay();
            while ($invoiceCount < $installmentCount) {
                
                if ($invoiceCount > 0) {
                    $dueDate->addMonth();
                }

                $description = "Serviços & Consultoria G7";
                if ($invoiceCount == 0) {
                    $charge = $this->createCharge($customer, $sale->billingType, 300, $description, $dueDate);
                } else {
                    $charge = $this->createCharge($customer, $sale->billingType, $valuePerInstallment, $description, $dueDate);
                }
                
                if ($charge) {

                    $invoice                = new Invoice();
                    $invoice->idUser        = $sale->id;
                    $invoice->name          = "Parcela N° " . ($invoiceCount + 1);
                    $invoice->description   = $description;
                    $invoice->token         = $charge['id'];
                    $invoice->url           = $charge['invoiceUrl'];
                    $invoice->value         = ($invoiceCount == 0) ? $initialPayment : $valuePerInstallment;
                    $invoice->status        = "PENDING_PAY";
                    $invoice->type          = 3;
                    $invoice->dueDate       = $dueDate;
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

    private function createCharge($customer, $billingType, $value, $description, $dueDate = null) {

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
                // 'split'             => [
                //     'walletId'      => 'afd76f74-6dd8-487b-b251-28205161e1e6',
                //     'fixedValue'    => 5
                // ]
            ],
            'verify' => false
        ];

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
            ],
            'verify' => false
        ];

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
            }

            return response()->json(['status' => 'success', 'message' => 'Requisição tratada!']);
        }

        return response()->json(['status' => 'success', 'message' => 'Webhook não utilizado!']);
    }
}
