<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\View;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Carbon\Carbon;

use App\Models\Vendas;

class VendasController extends Controller
{

    public function getVendas(Request $request, $id = null, $cupom = null)
    {
        $users = auth()->user();

        $dataInicio = $request->input('data_inicio');
        $dataFim = $request->input('data_fim');
        $cupomInput = $request->input('cupom');

        $query = Vendas::where('id_produto', $id)
            ->where('id_vendedor', $users->id);

        if ($cupom || $cupomInput) {
            $query->where('cupom', $cupom);
        }

        if ($dataInicio && $dataFim) {
            $dataInicio = Carbon::parse($dataInicio);
            $dataFim = Carbon::parse($dataFim);

            $vendas = $query->whereBetween('updated_at', [$dataInicio, $dataFim])->get();
        } else {
            $vendas = $query->latest()->get();
        }

        return view('dashboard.vendas', [
            'users' => $users,
            'vendas' => $vendas,
            'produto' => $id
        ]);
    }

    public function vender(Request $request, $id)
    {

        $request->validate([
            'cpfcnpj' => 'required|string|max:255',
            'cliente' => 'required|string|max:255',
            'dataNascimento' => 'required|string|max:255',
            'email' => 'string|max:255',
            'telefone' => 'required|string|max:20',
        ]);

        switch ($request->produto) {
            case 2:
                $views = ['documentos.limpanome'];
                break;
            default:
                $views = ['documentos.limpanome'];
                break;
        }

        $vendaData = [
            'id_vendedor' => $id,
            'cpf' => !empty($request->cpfcnpj) ? preg_replace('/[^0-9]/', '', $request->cpfcnpj) : null,
            'nome' => !empty($request->cliente) ? $request->cliente : null,
            'dataNascimento' => !empty($request->dataNascimento) ? Carbon::createFromFormat('d-m-Y', $request->dataNascimento)->format('Y-m-d') : null,
            'email' => !empty($request->email) ? $request->email : null,
            'telefone' => !empty($request->telefone) ? preg_replace('/[^0-9]/', '', $request->telefone) : null,
            'id_produto' => !empty($request->produto) ? $request->produto : null,
            'valor' => null,
            'parcela' => $request->parcela,
            'cupom' => $request->cupom,
            'cep' => $request->cep,
            'endereco' => $request->endereco,
            'status_contrato' => 'Pendente',
            'forma_pagamento' => $request->forma_pagamento == 'CARTÃO' ? 'CREDIT_CARD' : $request->forma_pagamento,
        ];

        if (!empty($request->cpfcnpj)) {
            $valor = (strlen(preg_replace('/[^0-9]/', '', $request->cpfcnpj)) > 11) ? 1500 : 1000;

            if (!empty($request->parcela) && strlen(preg_replace('/[^0-9]/', '', $request->cpfcnpj)) <= 11) {
                $valor = ($request->parcela > 1) ? 1440 : $valor;
            }

            if (strlen(preg_replace('/[^0-9]/', '', $request->cpfcnpj)) > 2100) {
                $valor = 2100;
            }

            $vendaData['valor'] = $valor;
        }

        $data = [
            'cpfcnpj' => preg_replace('/[^0-9]/', '', $request->cpfcnpj),
            'cliente' => $request->cliente,
            'dataNascimento' => Carbon::createFromFormat('d-m-Y', $request->dataNascimento)->format('Y-m-d'),
            'email' => $request->email,
            'telefone' => preg_replace('/[^0-9]/', '', $request->telefone),
            'auth'      => 'whatsapp',
            'produto'   => $request->produto,
            'valor' => $valor,
            'parcela' => $request->parcela,
            'forma_pagamento' => $request->forma_pagamento
        ];

        $dompdf = new Dompdf();

        $html = '';
        $total = 0;
        foreach ($views as $view) {
            $html .= View::make($view, ['data' => $data])->render();
            $total++;
            if ($total != 4) {
                $html .= '<div style="page-break-before:always;"></div>';
            }
        }

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $pdfContent = $dompdf->output();

        $filePath = public_path('contratos/' . $request->produto . $data['cpfcnpj'] . '.pdf');
        file_put_contents($filePath, $pdfContent);

        $pdfPath = public_path('contratos/' . $request->produto . $data['cpfcnpj'] . '.pdf');
        $pdfContent = file_get_contents($pdfPath);
        $data['pdf'] = $pdfBase64 = base64_encode($pdfContent);

        $keyDocumento = $this->criaDocumento($data);
        if ($keyDocumento['type'] != true) {
            return redirect()->route($request->franquia, ['id' => $id])->withErrors([$keyDocumento['key']])->withInput();
        }

        $keySignatario = $this->criaSignatario($data);
        if ($keySignatario['type'] != true) {
            return redirect()->route($request->franquia, ['id' => $id])->withErrors([$keySignatario['key']])->withInput();
        }

        $venda = Vendas::create($vendaData);
        if (!$venda) {
            return redirect()->route($request->franquia)->withErrors(['Falha no cadastro. Por favor, tente novamente.']);
        }

        $addSignatarios = $this->adiconaSignatario($keyDocumento['key'], $keySignatario['key']);
        if ($addSignatarios['type'] != null) {

            $notificar = $this->notificarSignatario($addSignatarios['url'], $data['auth'], $data['telefone']);

            $updateVenda = Vendas::where('cpf', $data['cpfcnpj'])->orderBy('id', 'desc')->first();
            if ($updateVenda) {
                $updateVenda->id_contrato = $keyDocumento['key'];
                $updateVenda->save();
            }

            if ($notificar != null) {
                return view('obrigado', ['success' => 'Contrato enviado com sucesso!']);
            }

            return view('obrigado', ['success' => 'Cadastro realizado com sucesso, mas não foi possivel enviar o contrato! Consulte seu atendente.']);
        } else {
            return redirect()->route($request->franquia, ['id' => $id])->withErrors(['Erro ao gerar assinatura!'])->withInput();
        }
    }

    public function criaDocumento($data)
    {
        $client = new Client();

        $url = env('API_URL_CLICKSIN') . 'api/v1/documents?access_token=' . env('API_TOKEN_CLICKSIN');

        switch ($data['produto']) {
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
                        'path' => $pasta . '/Contrato Limpa Nome ' . $data['cliente'] . '.pdf',
                        'content_base64' => 'data:application/pdf;base64,' . $data['pdf'],
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

    public function criaSignatario($data)
    {
        $client = new Client();

        $url = env('API_URL_CLICKSIN') . 'api/v1/signers?access_token=' . env('API_TOKEN_CLICKSIN');

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
                            $data['auth']
                        ],
                        'documentation' => strlen($data['cpfcnpj']) < 12 ? $data['cpfcnpj'] : '',
                        'birthday'  => strlen($data['cpfcnpj']) < 12 ? $data['dataNascimento'] : '',
                        'has_documentation'  => strlen($data['cpfcnpj']) < 12 ? true : false,
                        'selfie_enabled'  => 'false',
                        'handwritten_enabled'  => 'false',
                        'official_document_enabled'  => 'true',
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

    public function adiconaSignatario($keyDocumento, $keySignatario)
    {
        $client = new Client();

        $url = env('API_URL_CLICKSIN') . 'api/v1/lists?access_token=' . env('API_TOKEN_CLICKSIN');

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

    public function notificarSignatario($contrato, $auth, $telefone)
    {
        $client = new Client();
        if ($auth == 'whatsapp') {
            $url = 'https://api.z-api.io/instances/3C231BB3D577C079D30146A65441921E/token/9E7F18B45CD6EFB5BBB47D0A/send-link';

            $response = $client->post($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'phone'     => '55' . $telefone,
                    'message'   => "Prezado Cliente, segue seu contrato de adesão ao produto da Positivo Brasil: \r\n \r\n",
                    'image'     => 'https://grupopositivobrasil.com.br/wp-content/uploads/2022/09/Logo-Branco2.png',
                    'linkUrl'   => $contrato,
                    'title'     => 'Assinatura de Contrato',
                ],
            ]);

            $responseData = json_decode($response->getBody(), true);
            if (isset($responseData['id'])) {
                return true;
            } else {
                return false;
            }
        } else {
            return "Não foi whatsapp";
        }
    }
}
