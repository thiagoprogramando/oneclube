<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;

use App\Http\Controllers\BancoDoBrasilController;

use App\Models\Parcela;
use App\Models\Vendas;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Carbon\Carbon;

class AsaasController extends Controller {

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
                        if($this->parcela($venda->id, 1, $venda->valor)) {
                            $pix = new BancoDoBrasilController();
                            $pix = $pix->geraBoleto($venda->id);
                            if($pix['result'] == 'success'){
                                $parcela = Parcela::where('id_venda', $venda->id)->where('status', 'PENDING_PAY')->first();
                                $parcela->codigocliente = $pix['codigoCliente'];
                                $parcela->txid = $pix['qrCodeTxId'];
                                $parcela->url = $pix['qrCodeEmv'];
                                $parcela->numerocontratocobranca = $pix['numeroContratoCobranca'];
                                $parcela->linhadigitavel = $pix['linhaDigitavel'];
                                $parcela->numero = $pix['numero'];
                                $parcela->save();

                                return $this->enviaPix($venda->telefone, $pix['qrCodeEmv']);
                            } else {
                                $nomeArquivo = date('Y-m-d') . 'erro.txt';
                                $caminhoArquivo = public_path('erros/' . $nomeArquivo);
                                File::put($caminhoArquivo, $pix['message']);
                            }
                        }
                        break;
                    case 'CREDIT_CARD':
                        if($this->parcela($venda->id, 1, $venda->valor)) {
                            $link = $this->geraPagamentoAssas($venda->nome, $venda->cpf, $venda->valor, $venda->parcela, $venda->forma_pagamento);
                            $parcela = Parcela::where('id_venda', $venda->id)->where('status', 'PENDING_PAY')->first();
                            $parcela->codigocliente = $link['json']['customerId'];
                            $parcela->txid = $link['json']['paymentId'];
                            $parcela->url = $link['json']['paymentLink'];
                            $parcela->numerocontratocobranca = $link['json']['paymentId'];
                            $parcela->linhadigitavel = $link['json']['paymentLink'];
                            $parcela->save();

                            return $this->enviaCartao($venda->telefone, $link['json']['paymentLink']);
                        }
                        break;
                    case 'BOLETO':
                        if($this->parcela($venda->id, $venda->parcela, $venda->valor)) {
                            $pix = new BancoDoBrasilController();
                            $pix = $pix->geraBoleto($venda->id);
                            if($pix['result'] == 'success'){
                                $parcela = Parcela::where('id_venda', $venda->id)->where('status', 'PENDING_PAY')->first();
                                $parcela->codigocliente = $pix['codigoCliente'];
                                $parcela->txid = $pix['qrCodeTxId'];
                                $parcela->url = $pix['qrCodeEmv'];
                                $parcela->numerocontratocobranca = $pix['numeroContratoCobranca'];
                                $parcela->linhadigitavel = $pix['linhaDigitavel'];
                                $parcela->numero = $pix['numero'];
                                $parcela->save();

                                return $this->enviaPix($venda->telefone, $pix['qrCodeEmv']);
                            } else {
                                $nomeArquivo = date('Y-m-d') . 'erro.txt';
                                $caminhoArquivo = public_path('erros/' . $nomeArquivo);
                                File::put($caminhoArquivo, $pix['message']);
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

            if($parcela != 1) {
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
            }
            
            return true;
        } else {
            return false;
        }
    }

    public function geraPagamentoAssas($nome, $cpfcnpj, $valor, $parcela, $forma_pagamento) {

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
                    'customerId'    => $customerId
                ];

                return $dados;
            } else {
                return false;
            }

        } else {
            return false;
        }

    }

    public function enviaCartao($telefone, $assas) {
        $client = new Client();

        $url = 'https://api.z-api.io/instances/3C44E6488AC460277EC9B2E461726623/token/4845B3E5DE0FB497C11BFF7D/send-link';

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

        $url = 'https://api.z-api.io/instances/3C44E6488AC460277EC9B2E461726623/token/4845B3E5DE0FB497C11BFF7D/send-link';

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
            $url = 'https://api.z-api.io/instances/3C44E6488AC460277EC9B2E461726623/token/4845B3E5DE0FB497C11BFF7D/send-text';

            $response = $client->post($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'phone'     => '55'.$telefone,
                    'message'   => $boleto,
                ],
            ]);

            return true;
        } else {
            return false;
        }
    }

    public function enviaPix($telefone, $pix) {
        $client = new Client();

        $url = 'https://api.z-api.io/instances/3C44E6488AC460277EC9B2E461726623/token/4845B3E5DE0FB497C11BFF7D/send-link';

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
            $url = 'https://api.z-api.io/instances/3C44E6488AC460277EC9B2E461726623/token/4845B3E5DE0FB497C11BFF7D/send-text';

            $response = $client->post($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'phone'     => '55'.$telefone,
                    'message'   => $pix,
                ],
            ]);

            return true;
        } else {
            return false;
        }
    }

    public function recebeParcelaBancoDoBrasil($id) {

        $parcelaCliente = Parcela::where('id', $id)->first();
        $venda = Vendas::where('id', $parcelaCliente->id_venda)->first();
        if($this->enviaBoleto($venda->telefone, $parcelaCliente->linhadigitavel)) {
            return redirect()->back()->with('success', 'Dados enviados para o seu WhatsApp!');
        }

        return redirect()->back()->with('error', 'Parcela não encontrada!');
    }

    public function receberPagamentoAssas(Request $request) {
        $jsonData = $request->json()->all();
        if ($jsonData['event'] === 'PAYMENT_CONFIRMED' || $jsonData['event'] === 'PAYMENT_RECEIVED') {

            $idRequisicao = $jsonData['payment']['id'];

            $parcela = Parcela::where('txid', $idRequisicao)->first();
            if ($parcela) {
                $parcela->status = 'PAYMENT_CONFIRMED';
                $parcela->save();

                return response()->json(['status' => 'success', 'message' => 'Venda Atualizada!']);
            }
            
            $client = new Client();
            $response = $client->post(env('API_URL_CLUBEPOSITIVO') . 'finish-payment', [
                'id_assas' => $idRequisicao
            ]);

            if ($response->getStatusCode() === 200) {
                return response()->json(['status' => 'success', 'response' => true]);
            } else {
                return response()->json(['status' => 'error', 'response' => 'Comunicação com Positivo Afiliados falhou']);
            }
            return response()->json(['status' => 'success', 'message' => 'Nenhum retorno obtido!']);
        }

        return response()->json(['status' => 'success', 'message' => 'Webhook não utilizado']);
    }

}
















