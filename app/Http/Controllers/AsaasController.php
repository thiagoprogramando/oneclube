<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

use App\Models\Vendas;
use App\Models\User;

use Carbon\Carbon;

class AsaasController extends Controller
{   
    public function geraAssasOneClube(Request $request)
    {
     
         $client = new Client();
         
        if($request->id_assas == 'false'){
            $options = [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'access_token' => env('API_TOKEN'),
                ],
                'json' => [
                    'name'      => $request->nome,
                    'cpfCnpj'   => $request->cpf,
                ],
            ];
            $response = $client->post(env('API_URL_ASSAS').'api/v3/customers', $options);
            $body = (string) $response->getBody();
            $data = json_decode($body, true);
            
            if ($response->getStatusCode() === 200) {
                $customerId = $data['id'];
            } else {
                return false;
            }
        } else {
            $customerId = $request->input('id');
        }
        
        // Calculate tomorrow's date
        $tomorrow = Carbon::now()->addDay()->format('Y-m-d');
       
        $options['json'] = [
            'customer'          => $customerId,
            'billingType'       => $request->forma_pagamento,
            'value'             => $request->valor,
            'dueDate'           => $tomorrow,
            'description'       => 'Franquias One Clube',
            'installmentCount'  => $request->parcelas,
            'installmentValue'  => ($request->valor / $request->parcelas),
        ];
        $response = $client->post(env('API_URL_ASSAS').'api/v3/payments', $options);
        $body = (string) $response->getBody();
        $data = json_decode($body, true);
        if ($response->getStatusCode() === 200) {

            $dados['json'] = [
                'paymentId'     => $data['id'],
                'customer'      => $data['customer'],
                'paymentLink'   => $data['invoiceUrl'],
            ];

            return $dados;
        } else {
            return "Erro!";
        } 
    }

    public function receberPagamento(Request $request)
    {
        
        $jsonData = $request->json()->all();

        if ($jsonData['event'] === 'PAYMENT_CONFIRMED' || $jsonData['event'] === 'PAYMENT_RECEIVED') {
            
            $idRequisicao = $jsonData['payment']['id'];

            $venda = Vendas::where('id_pay', $idRequisicao)->first();
            if ($venda) {

                $venda->status_pay = 'PAYMENT_CONFIRMED';
                $venda->save();

                return response()->json(['status' => 'success', 'response' => 'Venda Atualizada!']);
            } else {
                return response()->json(['status' => 'success', 'response' => 'Venda não existe!']);
            }
        }

        // Caso contrário, retorne uma resposta de erro
        return response()->json(['status' => 'success', 'message' => 'Webhook não utilizado']);
    }
    
    public function enviaLinkPagamento(Request $request)
    {
        $jsonData = $request->json()->all();
        if ($jsonData['event']['name'] === 'sign') {
            $email = $jsonData['event']['data']['signer']['email'];
            $key = $jsonData['document']['key'];
            
            $venda = Vendas::where('id_contrato', $key)->where(function ($query) { $query->where('status_pay', 'PENDING_PAY')->orWhereNull('status_pay'); })->first();
            if ($venda) {
                $link = $this->geraPagamentoAssas($venda->nome, $venda->cpf, $venda->id_produto);
                $venda->id_pay = $link['json']['paymentId'];
                $venda->status_pay = 'PENDING_PAY';
                $venda->save();
                return $this->notificaCliente($venda->telefone, $link['json']['paymentLink']);
            } else {
                $venda = Vendas::where('email', $email)->where(function ($query) {$query->where('status_pay', 'PENDING_PAY')->orWhereNull('status_pay');})->first();
                $link = $this->geraPagamentoAssas($venda->nome, $venda->cpf, $venda->id_produto);
                $venda->id_pay = $link['json']['paymentId'];
                $venda->status_pay = 'PENDING_PAY';
                $venda->save();
                return $this->notificaCliente($venda->telefone, $link['json']['paymentLink']);
            }
            
            return response()->json(['message' => 'Assinatura Recebida!'], 200);
        }
    
        return response()->json(['message' => 'Evento não é "sign"'], 200);
    }
    
    public function geraPagamentoAssas($nome, $cpfcnpj, $produto)
    {

        switch($produto){
            // case 1:
            //     $produto = 375;
            //     break;
            case 2:
                $produto = 1500;
                break;
            case 3:
                $produto = 375;
                break;
            case 8:
                $produto = 127;
                break;
        }
        
        $client = new Client();
        
        $options = [
            'headers' => [
                'Content-Type' => 'application/json',
                'access_token' => env('API_TOKEN'),
            ],
            'json' => [
                'name'      => $nome,
                'cpfCnpj'   => $cpfcnpj,
            ],
        ];
        
        $response = $client->post(env('API_URL_ASSAS').'api/v3/customers', $options);
        
        $body = (string) $response->getBody();
        
        $data = json_decode($body, true);
        
        if ($response->getStatusCode() === 200) {
            
            $customerId = $data['id'];
    
            // Calculate tomorrow's date
            $tomorrow = Carbon::now()->addDay()->format('Y-m-d');
    
            $options['json'] = [
                'customer' => $customerId,
                'billingType' => 'UNDEFINED',
                'value' => $produto,
                'dueDate' => $tomorrow,
                'description' => 'One Motos',
            ];
            
            $response = $client->post(env('API_URL_ASSAS').'api/v3/payments', $options);
            
            $body = (string) $response->getBody();
            
            $data = json_decode($body, true);
            
            if ($response->getStatusCode() === 200) {
    
                $dados['json'] = [
                    'paymentId'     => $data['id'],
                    'customer'      => $data['customer'],
                    'paymentLink'   => $data['invoiceUrl'],
                ];
    
                return $dados;
            } else {
                return false;
            }
            
        } else {
            return false;
        }
        
    }
    
    public function notificaCliente($telefone, $assas) {
        $client = new Client();
        
        $url = 'https://api.z-api.io/instances/3BFF0A2480DEF0812D5F8E0A24FAED45/token/97AD9B2C34BC5BBE2FD52D6B/send-link';

        $response = $client->post($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'json' => [
                'phone'     => '55'.$telefone,
                'message'   => "Prezado Cliente, segue seu link de pagamento: \r\n \r\n",
                'image'     => 'https://gruposollution.com.br/assets/img/logo.png',
                'linkUrl'   => $assas,
                'title'     => 'Pagamento Grupo Sollution',
                'linkDescription' => 'Link para Pagamento Digital'
            ],
        ]);

        $responseData = json_decode($response->getBody(), true);
        
        if( isset($responseData['id'])) {
            return true;
        } else {
            return false;
        }
    }

}
















