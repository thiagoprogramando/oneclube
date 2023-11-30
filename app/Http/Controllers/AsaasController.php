<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

use App\Models\Vendas;
use App\Models\User;
use App\Models\VendaParcela;

use Carbon\Carbon;

class AsaasController extends Controller {

    public function receberPagamento(Request $request) {

        $jsonData = $request->json()->all();

        if ($jsonData['event'] === 'PAYMENT_CONFIRMED' || $jsonData['event'] === 'PAYMENT_RECEIVED') {

            $idRequisicao = $jsonData['payment']['id'];

            $venda = Vendas::where('id_pay', $idRequisicao)->first();
            if ($venda) {
                $idUsuario = $venda->id_vendedor;

                $venda->status_pay = 'PAYMENT_CONFIRMED';
                $venda->save();

                $parcelas = $this->geraParcelas($venda->id);
                if ($parcelas) {
                    $user = User::where('cpf', $venda->cpf)->orWhere('email', $venda->email)->first();

                    if (!$user) {
                        $attributes = [
                            'nome' => $venda->nome,
                            'cpf' => $venda->cpf,
                            'email' => $venda->email,
                            'password' => bcrypt($venda->cpf),
                            'tipo' => 4,
                            'status' => 1,
                        ];

                        $user = User::create($attributes);
                        $notifica = $this->notificaUsuario($attributes['email'], $venda->telefone);
                    }
                }

                $user = User::where('id', $idUsuario)->first();
                if ($user) {
                    $cpf = $user->cpf;

                    $dados = [
                        'cpf' => $cpf,
                        'value' => $venda->valor,
                        'description' => 'One Clube - Vendas',
                        'product' => $venda->id_produto,
                    ];

                    $client = new Client();
                    $response = $client->post(env('API_URL_ONECLUBE') . 'confirm-sale-product', [
                        'form_params' => $dados
                    ]);

                    if ($response->getStatusCode() === 200) {
                        return response()->json(['status' => 'success', 'response' => true]);
                    } else {
                        return response()->json(['status' => 'error', 'response' => 'Comunicação com One Clube falhou']);
                    }
                } else {
                    return response()->json(['status' => 'error', 'response' => 'Usuário não existe!']);
                }
            } else {

                $parcela = VendaParcela::where('id_assas', $idRequisicao)->first();
                if($parcela) {
                    $parcela->status = 'PAYMENT_CONFIRMED';
                    $parcela->save();
                }

                $dados = [
                    'id_assas' => $idRequisicao,
                ];
                $client = new Client();
                $response = $client->post(env('API_URL_ONECLUBE') . 'finish-payment', [
                    'form_params' => $dados
                ]);

                if ($response->getStatusCode() === 200) {
                    $responseData = json_decode($response->getBody(), true);
                    return response()->json(['status' => 'success', 'response' => $responseData]);
                } else {
                    return response()->json(['status' => 'success', 'response' => 'Erro ao quitar fatura!']);
                }
            }
        }

        return response()->json(['status' => 'success', 'message' => 'Webhook não utilizado']);
    }

    public function enviaLinkPagamento(Request $request) {

        $jsonData = $request->json()->all();
        if ($jsonData['event']['name'] === 'sign') {
            $email = $jsonData['event']['data']['signer']['email'];
            $key = $jsonData['document']['key'];

            $venda = Vendas::where('id_contrato', $key)->where(function ($query) {
                $query->where('status_pay', 'null')->orWhereNull('status_pay');
            })->first();
            if ($venda) {
                $link = $this->geraPagamentoAssas($venda->nome, $venda->cpf, $venda->id_produto, $venda->valor);
                $venda->id_pay = $link['json']['paymentId'];
                $venda->status_pay = 'PENDING_PAY';
                $venda->save();
                return $this->notificaCliente($venda->telefone, $link['json']['paymentLink']);
            } else {
                $venda = Vendas::where('email', $email)->where(function ($query) {
                    $query->where('status_pay', 'null')->orWhereNull('status_pay');
                })->first();
                $link = $this->geraPagamentoAssas($venda->nome, $venda->cpf, $venda->id_produto, $venda->valor);
                $venda->id_pay = $link['json']['paymentId'];
                $venda->status_pay = 'PENDING_PAY';
                $venda->save();
                return $this->notificaCliente($venda->telefone, $link['json']['paymentLink']);
            }

            return response()->json(['message' => 'Assinatura Recebida!'], 200);
        }

        return response()->json(['message' => 'Evento não é "sign"'], 200);
    }

    public function notificaCliente($telefone, $assas) {
        
        $client = new Client();

        $url = 'https://api.z-api.io/instances/3BF660F605143051CA98E2F1A4FCFFCB/token/3048386F0FE68A1828B852B1/send-link';

        $response = $client->post($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'json' => [
                'phone'     => '55' . $telefone,
                'message'   => "Prezado Cliente, segue seu link de pagamento da One Clube: \r\n \r\n",
                'image'     => 'https://oneclube.com.br/images/logo.png',
                'linkUrl'   => $assas,
                'title'     => 'Pagamento One Clube',
                'linkDescription' => 'Link para Pagamento Digital'
            ],
        ]);

        $responseData = json_decode($response->getBody(), true);

        if (isset($responseData['id'])) {
            return true;
        } else {
            return false;
        }
    }
}
