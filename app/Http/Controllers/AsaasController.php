<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

use App\Models\Vendas;
use App\Models\User;

use GuzzleHttp\Exception\RequestException;
use Carbon\Carbon;
class AsaasController extends Controller
{

    public function receberPagamento(Request $request)
    {
        
        $jsonData = $request->json()->all();

        if ($jsonData['event'] === 'PAYMENT_CONFIRMED' || $jsonData['event'] === 'PAYMENT_RECEIVED') {
            
            $idRequisicao = $jsonData['payment']['id'];

            $venda = Vendas::where('id_pay', $idRequisicao)->first();
            if ($venda) {

                $venda->status_pay = 'PAYMENT_CONFIRMED';
                $venda->save();

                $auth = 'whatsapp';

                $keyDocumento = $this->criaDocumento($venda);
                $keySignatario = $this->criaSignatario($venda, $auth);
                $addSignatarios = $this->adiconaSignatario($keyDocumento['key'], $keySignatario['key']);

                if($addSignatarios['type'] != null){

                    //Notifica
                    $notificar = $this->notificarSignatario($addSignatarios['url'], $auth, $venda->telefone);
                    
                    //Update Venda
                    $venda->id_contrato = $keyDocumento['key'];
                    $venda->save();

                    //Retorno
                    return response()->json(['status' => 'success', 'response' => 'Venda Atualizada!']);
                }

                return response()->json(['status' => 'error', 'response' => 'Contrato não elaborado!']);
            } else {
                return response()->json(['status' => 'success', 'response' => 'Venda não existe!']);
            }
        }

        // Caso contrário, retorne uma resposta de erro
        return response()->json(['status' => 'success', 'message' => 'Webhook não utilizado']);
    }

    public function criaDocumento($data) {
        $client = new Client();

        $url = env('API_URL_CLICKSIN').'api/v1/documents?access_token='.env('API_TOKEN_CLICKSIN');

        switch($data['produto']){
            case 2:
                $pasta = "/limpanome";
                break;
        }
        
        try {
            $response = $client->post($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'document' => [
                        'path' => $pasta.'/Contrato '.$data['cliente'].'.pdf',
                        'content_base64' => 'data:application/pdf;base64,'.$data['id_produto'].$data['nome'],
                    ],
                ],
            ]);

            $statusCode = $response->getStatusCode();
            $responseData = json_decode($response->getBody(), true);

            if (isset($responseData['document']['key'])) {
                $result = [
                    'type' => true,
                    'key' => $responseData['document']['key']
                ];
                return $result;
            } else {
                $result = [
                    'type' => false,
                    'key' => "Falha na geração de Documento!"
                ];
                return $result;
            }

        } catch (RequestException $e) {
            // Tratamento de erro: capturar e retornar a resposta de erro
            if ($e->hasResponse()) {
                $errorResponse = json_decode($e->getResponse()->getBody(), true);
                if (isset($errorResponse['errors']) && !empty($errorResponse['errors'])) {
                    $errorMessage = $errorResponse['errors'][0];
                    $result = [
                        'type' => false,
                        'key' => $errorMessage
                    ];
                    return $result;
                } else {
                    $result = [
                        'type' => false,
                        'key' => "Falha na operação!"
                    ];
                    return $result;
                }
            } else {
                $result = [
                    'type' => false,
                    'key' => "Falha na operação!"
                ];
                return $result;
            }
        }
    }

    public function criaSignatario($data, $auth) {
        $client = new Client();

        $url = env('API_URL_CLICKSIN').'api/v1/signers?access_token='.env('API_TOKEN_CLICKSIN');

        try {
            $response = $client->post($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'signer' => [
                        'email'         => $data['email'],
                        'phone_number'  => $data['telefone'],
                        'name'  => $data['cliente'],
                        'auths' => [
                            $data
                        ],
                        'documentation'  => $data['cpfcnpj'],
                        'birthday'  => $data['dataNascimento'],
                        'has_documentation'  => 'true',
                        'selfie_enabled'  => 'false',
                        'handwritten_enabled'  => 'false',
                        'official_document_enabled'  => 'false',
                        'liveness_enabled'  => 'false',
                        'facial_biometrics_enabled'  => 'false',
                    ],
                ],
            ]);

            $responseData = json_decode($response->getBody(), true);

            if (isset($responseData['signer']['key'])) {
                $result = [
                    'type' => true,
                    'key' => $responseData['signer']['key']
                ];
                return $result;
            } else {
                $result = [
                    'type' => false,
                    'key' => "Falha ao gerar Signatario!"
                ];
                return $result;
            }

        } catch (RequestException $e) {
            // Tratamento de erro: capturar e retornar a resposta de erro
            if ($e->hasResponse()) {
                $errorResponse = json_decode($e->getResponse()->getBody(), true);
                if (isset($errorResponse['errors']) && !empty($errorResponse['errors'])) {
                    $errorMessage = $errorResponse['errors'][0];
                    $result = [
                        'type' => false,
                        'key' => $errorMessage
                    ];
                    return $result;
                } else {
                    $result = [
                        'type' => false,
                        'key' => "Falha na operação!"
                    ];
                    return $result;
                }
            } else {
                $result = [
                    'type' => false,
                    'key' => "Falha na operação!"
                ];
                return $result;
            }
        }
    }

    public function adiconaSignatario($keyDocumento, $keySignatario) {
        $client = new Client();

        $url = env('API_URL_CLICKSIN').'api/v1/lists?access_token='.env('API_TOKEN_CLICKSIN');

            $response = $client->post($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'list' => [
                        'document_key'  => $keyDocumento,
                        'signer_key'    => $keySignatario,
                        'sign_as'       => 'contractor',
                        'refusable'     => false,
                        'message'       => 'Querido cliente, por favor, assine o contrato como confirmação de adesão ao nosso produto!'
                    ],
                ],
            ]);

            $responseData = json_decode($response->getBody(), true);

            if (isset($responseData['list']['key'])) {
                
                 $result = [
                    'type' => true,
                    'key'  => $responseData['list']['key'],
                    'url' => $responseData['list']['url']
                ];
                return $result;
            } else {
                $result = [
                    'type' => false,
                ];
                return $result;
            }
    }

    public function notificarSignatario($contrato, $auth, $telefone) {
        $client = new Client();
        if($auth == 'whatsapp') {
            $url = 'https://api.z-api.io/instances/3BFF0A2480DEF0812D5F8E0A24FAED45/token/97AD9B2C34BC5BBE2FD52D6B/send-link';

            $response = $client->post($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'phone'     => '55'.$telefone,
                    'message'   => "Prezado Cliente, segue sua ficha de adesão ao Grupo Sollution. Basta assinar para prosseguir com o atendimento: \r\n \r\n",
                    'image'     => 'https://gruposollution.com.br/assets/img/logo.png',
                    'linkUrl'   => $contrato,
                    'title'     => 'Assinatura de Contrato',
                    'linkDescription' => 'Link para Assinatura Digital'
                ],
            ]);

            $responseData = json_decode($response->getBody(), true);
            if( isset($responseData['id'])) {
                return true;
            } else {
                return false;
            }

        } else {
            return "Não foi whatsapp";
        }
    }

    
}
















