<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

use App\Models\Vendas;
use App\Models\User;
use App\Models\VendaParcela;

use Carbon\Carbon;

class AsaasController extends Controller
{
    public function geraAssasOneClube(Request $request)
    {

        $client = new Client();

        if ($request->id_assas == 'false') {
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
            $response = $client->post(env('API_URL_ASSAS') . 'api/v3/customers', $options);
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

        $tomorrow = Carbon::now()->addDay()->format('Y-m-d');

        $options['json'] = [
            'customer'          => $customerId,
            'billingType'       => "BOLETO",
            'value'             => $request->valor,
            'dueDate'           => $tomorrow,
            'description'       => 'One Clube',
            'installmentCount'  => $request->parcelas,
            'installmentValue'  => ($request->valor / $request->parcelas),
        ];
        $response = $client->post(env('API_URL_ASSAS') . 'api/v3/payments', $options);
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

    public function geraAssasClubeJob(Request $request) {

        $client = new Client();

        if ($request->id_assas == 'false') {
            $options = [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'access_token' => env('API_TOKEN_ASSAS_CLUBE_JOB'),
                ],
                'json' => [
                    'name'      => $request->nome,
                    'cpfCnpj'   => $request->cpf,
                ],
            ];
            $response = $client->post(env('API_URL_ASSAS_CLUBE_JOB') . 'api/v3/customers', $options);
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

        $tomorrow = Carbon::now()->addDay()->format('Y-m-d');

        $options['json'] = [
            'customer'          => $customerId,
            'billingType'       => "BOLETO",
            'value'             => $request->valor,
            'dueDate'           => $tomorrow,
            'description'       => 'Clube Job',
            'installmentCount'  => $request->parcelas,
            'installmentValue'  => ($request->valor / $request->parcelas),
        ];
        $response = $client->post(env('API_URL_ASSAS_CLUBE_JOB') . 'api/v3/payments', $options);
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

    public function receberPagamentoClubeJob(Request $request) {
        $jsonData = $request->json()->all();

        if ($jsonData['event'] === 'PAYMENT_CONFIRMED' || $jsonData['event'] === 'PAYMENT_RECEIVED') {

            $idRequisicao = $jsonData['payment']['id'];

            $dados = [
                'id_assas' => $idRequisicao,
            ];

            $client = new Client();
            $response = $client->post(env('API_URL_CLUBEJOB') . 'finish-payment', [
                'form_params' => $dados
            ]);

            if ($response->getStatusCode() === 200) {
                $responseData = json_decode($response->getBody(), true);
                return response()->json(['status' => 'success', 'response' => $responseData]);
            } else {
                return response()->json(['status' => 'success', 'response' => 'Erro ao quitar fatura!']);
            }
        }

        return response()->json(['status' => 'success', 'message' => 'Webhook não utilizado']);
    }

    public function enviaLinkPagamento(Request $request)
    {
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

    public function geraPagamentoAssas($nome, $cpfcnpj, $produto, $valor)
    {

        switch ($produto) {
            case 2:
                $nomeProduto = "One Positive";
                break;
            case 3:
                $nomeProduto = "One Motos";
                break;
            case 8:
                $nomeProduto = "One Serviços";
                break;
            case 11:
                $nomeProduto = "One Motos";
                break;
            case 12:
                $nomeProduto = "One Positive";
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

        $response = $client->post(env('API_URL_ASSAS') . 'api/v3/customers', $options);

        $body = (string) $response->getBody();

        $data = json_decode($body, true);

        if ($response->getStatusCode() === 200) {

            $customerId = $data['id'];
            $tomorrow = Carbon::now()->addDay()->format('Y-m-d');

            $options['json'] = [
                'customer' => $customerId,
                'billingType' => 'BOLETO',
                'value' => $valor,
                'dueDate' => $tomorrow,
                'description' => 'One Clube -'.$nomeProduto,
            ];

            $response = $client->post(env('API_URL_ASSAS') . 'api/v3/payments', $options);

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

    public function notificaCliente($telefone, $assas)
    {
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

    public function notificaUsuario($email, $telefone)
    {
        $client = new Client();

        $url = 'https://api.z-api.io/instances/3BF660F605143051CA98E2F1A4FCFFCB/token/3048386F0FE68A1828B852B1/send-link';
        $link = "https://myonecrm.com.br/";
        $response = $client->post($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'json' => [
                'phone'     => '55' . $telefone,
                'message'   => "Prezado Cliente, segue seu link de acesso ao sistema da One Clube! \r\n Para acessar, informe o email: ".$email." e o seu CPF como senha! \r\n",
                'image'     => 'https://oneclube.com.br/images/logo.png',
                'linkUrl'   => $link,
                'title'     => 'Acesso One Clube',
                'linkDescription' => 'Link Para Acesso Digital'
            ],
        ]);

        $responseData = json_decode($response->getBody(), true);

        if (isset($responseData['id'])) {
            return true;
        } else {
            return false;
        }
    }

    public function geraParcelas($id)
    {
        $venda = Vendas::where('id', $id)->first();

        $parcelas = 0;
        switch ($venda->id_produto) {
            case 3:
                $parcelas = 59;
                $valor = 317;
                break;
            case 11:
                $parcelas = 59;
                $valor = 317;
                break;
            default:
                return false;
        }

        $primeiraParcelaDate = Carbon::parse($venda->created_at)->addMonth();
        if ($primeiraParcelaDate->day < 28) {
            $primeiraParcelaDate->day = 28;
        }

        $interval = $primeiraParcelaDate->diffInMonths(Carbon::now());

        for ($i = 1; $i <= $parcelas; $i++) {
            $parcela = new VendaParcela();
            $parcela->venda_id = $id;
            $parcela->numero_parcela = $i;
            $parcela->cpf = $venda->cpf;
            $parcela->valor = $valor;
            $parcela->status = "PENDING_PAY";
            $parcela->vencimento = $primeiraParcelaDate->copy()->addMonths($interval + ($i - 1));
            $parcela->save();
        }

        return true;
    }

    public function consultaFatura($id)
    {
        $client = new \GuzzleHttp\Client();

        $response = $client->request('GET', env('API_URL_ASSAS') . 'api/v3/payments/' . $id, [
            'headers' => [
                'accept' => 'application/json',
                'access_token' => env('API_TOKEN'),
            ],
        ]);

        $paymentData = json_decode($response->getBody(), true);
        if ($paymentData) {
            $filteredData = [
                "dateCreated" => $paymentData["dateCreated"],
                "customer" => $paymentData["customer"],
                "value" => $paymentData["value"],
                "billingType" => $paymentData["billingType"],
                "status" => $paymentData["status"],
                "paymentDate" => $paymentData["paymentDate"],
                "invoiceUrl" => $paymentData["invoiceUrl"],
                "bankSlipUrl" => $paymentData["bankSlipUrl"],
            ];
        }

        $vendaData = Vendas::where('id_pay', $id)->first();
        if($vendaData) {
            $filteredData['cpf'] = $vendaData->cpf;
            $filteredData['telefone'] = $vendaData->telefone;
            $filteredData['vendedor'] = $vendaData->id_vendedor;
        }

        return json_encode($filteredData, JSON_PRETTY_PRINT);
    }
}
