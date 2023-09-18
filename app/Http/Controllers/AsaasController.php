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
            'billingType'       => "BOLETO",
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
                $idUsuario = $venda->id_vendedor;

                $venda->status_pay = 'PAYMENT_CONFIRMED';
                $venda->save();

                $user = User::where('id', $idUsuario)->first();
                if ($user) {
                    $cpf = $user->cpf;

                    $dados = [
                        'cpf' => $cpf,
                        'value' => $venda->valor,
                        'description' => 'One Clube - Vendas',
                        'product' => $venda->id_produto,
                    ];

                    // Enviar a requisição POST para oneclube.com.br/recebe
                    $client = new Client();
                    $response = $client->post(env('API_URL_ONECLUBE').'confirm-sale-product', [
                        'form_params' => $dados
                    ]);

                    // Verificar se a requisição teve sucesso
                    if ($response->getStatusCode() === 200) {
                        // Retornar true em caso de sucesso
                        return response()->json(['status' => 'success', 'response' => true]);
                    } else {
                        return response()->json(['status' => 'error', 'response' => 'Comunicação com One Clube falhou']);
                    }
                } else {
                    return response()->json(['status' => 'error', 'response' => 'Usuário não existe!']);
                }
            } else {
                $dados = [
                    'id_assas' => $idRequisicao,
                ];
                $client = new Client();
                $response = $client->post(env('API_URL_ONECLUBE').'finish-payment', [
                    'form_params' => $dados
                ]);

                if ($response->getStatusCode() === 200) {
                    $responseData = json_decode($response->getBody(), true);
                    // Exibir o retorno da requisição
                    return response()->json(['status' => 'success', 'response' => $responseData]);
                } else {
                    return response()->json(['status' => 'success', 'response' => 'Erro ao quitar fatura!']);
                }
            }
        }

        // Caso contrário, retorne uma resposta de erro
        return response()->json(['status' => 'success', 'message' => 'Webhook não utilizado']);
    }

    public function enviaLinkPagamento(Request $request)
    {
        $jsonData = $request->json()->all();
        if ($jsonData['event']['name'] === 'sign') {
            $key = $jsonData['document']['key'];

            $venda = Vendas::where('id_contrato', $key)->where(function ($query) { $query->where('status_pay', 'null')->orWhereNull('status_pay'); })->first();
            if ($venda) {
                if($venda->forma_pagamento == 'CREDIT_CARD') {
                    $link = $this->geraPagamentoAssas($venda->nome, $venda->cpf, $venda->id_produto, $venda->valor, $venda->parcela, $venda->forma_pagamento);
                    $venda->id_pay = $link['json']['paymentId'];
                    $venda->status_pay = 'PENDING_PAY';
                    $venda->save();
                    $link = 'bb.pay.com.br/null';
                    return $this->notificaCliente($venda->telefone, $link);
                }
                $link = 'bb.pay.com.br/null';
                return $this->notificaCliente($venda->telefone, $link);
            }

            return response()->json(['message' => 'Assinatura Recebida!'], 200);
        }

        return response()->json(['message' => 'Evento não é "sign"'], 200);
    }

    public function geraPagamentoAssas($nome, $cpfcnpj, $produto, $valor, $parcela, $forma_pagamento)
    {

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
            $tomorrow = Carbon::now()->addDay()->format('Y-m-d');

            $options['json'] = [
                'customer' => $customerId,
                'billingType' => $forma_pagamento,
                'value' => $valor,
                'dueDate' => $tomorrow,
                'description' => 'Positivo Brasil',
                "installmentCount"=> $parcela,
                "installmentValue"=> ($valor / $parcela)
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

        $url = 'https://api.z-api.io/instances/3C231BB3D577C079D30146A65441921E/token/9E7F18B45CD6EFB5BBB47D0A/send-link';

        $response = $client->post($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'json' => [
                'phone'     => '55'.$telefone,
                'message'   => "Prezado Cliente, segue seu link de pagamento da Positivo Brasil: \r\n \r\n",
                'image'     => 'https://grupopositivobrasil.com.br/wp-content/uploads/2022/09/Logo-Branco2.png',
                'linkUrl'   => $assas,
                'title'     => 'Pagamento Positivo Brasil',
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
















