<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Http\Controllers\BancoDoBrasilController;

use App\Models\Parcela;
use App\Models\Vendas;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class AsaasController extends Controller
{
    public function enviaLinkPagamento(Request $request) {
        $jsonData = $request->json()->all();
        if ($jsonData['event']['name'] === 'sign') {
            $key = $jsonData['document']['key'];

            $venda = Vendas::where('id_contrato', $key)->first();
            if ($venda) {
                $venda->status_contrato = 'ASSINADO';
                $venda->save();

                switch($venda->forma_pagamento){
                    case 'PIX':
                        if($this->parcela($venda->id, $venda->parcela, $venda->valor)) {
                            $pix = new BancoDoBrasilController();
                            $pix = $pix->geraBoleto($venda->id);
                            if($pix['result'] == 'success'){
                                $parcela = Parcela::where('id_venda', $venda->id)->where('status', 'PENDING_PAY')->first();
                                $parcela->codigocliente = $pix['codigoCliente'];
                                $parcela->txid = $pix['qrCodeTxId'];
                                $parcela->url = $pix['qrCodeUrl'];
                                $parcela->numerocontratocobranca = $pix['numeroContratoCobranca'];
                                $parcela->linhadigitavel = $pix['linhaDigitavel'];
                                $parcela->save();

                                return $this->enviaPix($venda->telefone, $pix['qrCodeUrl']);
                            } else {
                                return response()->json(['status' => 'success', 'message' => $pix['message']]);
                            }
                        }
                        break;
                    case 'CREDIT_CARD':

                        break;
                    case 'BOLETO':
                        if($this->parcela($venda->id, $venda->parcela, $venda->valor)) {
                            $boleto = new BancoDoBrasilController();
                            $boleto = $boleto->geraBoleto($venda->id);

                            if($boleto['result'] == 'success'){
                                $parcela = Parcela::where('id_venda', $venda->id)->where('status', 'PENDING_PAY')->first();
                                $parcela->codigocliente = $boleto['codigoCliente'];
                                $parcela->txid = $boleto['qrCodeTxId'];
                                $parcela->url = $boleto['qrCodeUrl'];
                                $parcela->numerocontratocobranca = $boleto['numeroContratoCobranca'];
                                $parcela->linhadigitavel = $boleto['linhaDigitavel'];
                                $parcela->save();

                                return $this->enviaBoleto($venda->telefone, $boleto['linhaDigitavel']);
                            } else {
                                return response()->json(['status' => 'success', 'message' => $boleto['message']]);
                            }
                        }
                        break;
                    default:
                        break;
                }
            }

            return response()->json(['message' => 'Assinatura Recebida!'], 200);
        }

        return response()->json(['message' => 'Evento não é "sign"'], 200);
    }

    public function parcela($id, $parcela, $valor) {
        $venda = Vendas::find($id);
        $valor = ($valor / $parcela);

        if($venda) {
            $primeiroVencimento = now()->addDays(3);

            Parcela::create([
                'id_venda' => $venda->id,
                'n_parcela' => 1,
                'vencimento' => $primeiroVencimento,
                'valor' => $valor,
                'status' => 'PENDING_PAY',
            ]);

            $proximoVencimento = $primeiroVencimento;

            for ($i = 2; $i <= $parcela; $i++) {
                $proximoVencimento = $proximoVencimento->addDays(30);
                Parcela::create([
                    'id_venda' => $venda->id,
                    'n_parcela' => $i,
                    'vencimento' => $proximoVencimento,
                    'valor' => $valor,
                    'status' => 'PENDING_PAY',
                ]);
            }

            return true;
        } else {
            return false;
        }
    }

    public function enviaCartao($telefone, $assas) {
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

    public function enviaBoleto($telefone, $boleto) {
        $client = new Client();

        $url = 'https://api.z-api.io/instances/3C231BB3D577C079D30146A65441921E/token/9E7F18B45CD6EFB5BBB47D0A/send-link';

        $response = $client->post($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'json' => [
                'phone'     => '55'.$telefone,
                'message'   => "Prezado Cliente, segue seu boleto da Positivo Brasil: \r\n Basta copiar a Linha Digitavel e pagar em seu Banco! \r\n",
                'image'     => 'https://grupopositivobrasil.com.br/wp-content/uploads/2022/09/Logo-Branco2.png',
                'linkUrl'   => $boleto,
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

    public function enviaPix($telefone, $pix) {
        $client = new Client();

        $url = 'https://api.z-api.io/instances/3C231BB3D577C079D30146A65441921E/token/9E7F18B45CD6EFB5BBB47D0A/send-link';

        $response = $client->post($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'json' => [
                'phone'     => '55'.$telefone,
                'message'   => "Prezado Cliente, segue a chave PIX da Positivo Brasil: \r\n Basta copiar e colar em seu Banco! \r\n",
                'image'     => 'https://grupopositivobrasil.com.br/wp-content/uploads/2022/09/Logo-Branco2.png',
                'linkUrl'   => $pix,
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

    // public function geraPagamentoAssas($nome, $cpfcnpj, $produto, $valor, $parcela, $forma_pagamento)
    // {

    //     $client = new Client();

    //     $options = [
    //         'headers' => [
    //             'Content-Type' => 'application/json',
    //             'access_token' => env('API_TOKEN'),
    //         ],
    //         'json' => [
    //             'name'      => $nome,
    //             'cpfCnpj'   => $cpfcnpj,
    //         ],
    //     ];

    //     $response = $client->post(env('API_URL_ASSAS').'api/v3/customers', $options);

    //     $body = (string) $response->getBody();

    //     $data = json_decode($body, true);

    //     if ($response->getStatusCode() === 200) {

    //         $customerId = $data['id'];
    //         $tomorrow = Carbon::now()->addDay()->format('Y-m-d');

    //         $options['json'] = [
    //             'customer' => $customerId,
    //             'billingType' => $forma_pagamento,
    //             'value' => $valor,
    //             'dueDate' => $tomorrow,
    //             'description' => 'Positivo Brasil',
    //             "installmentCount"=> $parcela,
    //             "installmentValue"=> ($valor / $parcela)
    //         ];

    //         $response = $client->post(env('API_URL_ASSAS').'api/v3/payments', $options);

    //         $body = (string) $response->getBody();

    //         $data = json_decode($body, true);

    //         if ($response->getStatusCode() === 200) {

    //             $dados['json'] = [
    //                 'paymentId'     => $data['id'],
    //                 'customer'      => $data['customer'],
    //                 'paymentLink'   => $data['invoiceUrl'],
    //             ];

    //             return $dados;
    //         } else {
    //             return false;
    //         }

    //     } else {
    //         return false;
    //     }

    // }
    // public function receberPagamento(Request $request) {
    //     $jsonData = $request->json()->all();
    //     if ($jsonData['event'] === 'PAYMENT_CONFIRMED' || $jsonData['event'] === 'PAYMENT_RECEIVED') {

    //         $idRequisicao = $jsonData['payment']['id'];

    //         $venda = Vendas::where('txid', $idRequisicao)->first();
    //         if ($venda) {
    //             $venda->status_pay = 'PAYMENT_CONFIRMED';
    //             $venda->save();

    //             return response()->json(['status' => 'success', 'message' => 'Venda Atualizada!']);
    //         }
    //         return response()->json(['status' => 'success', 'message' => 'Venda Não Existe!']);
    //     }

    //     return response()->json(['status' => 'success', 'message' => 'Webhook não utilizado']);
    // }

}
















