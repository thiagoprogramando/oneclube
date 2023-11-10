<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AsaasController;
use Illuminate\Http\Request;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\View;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Carbon\Carbon;

use App\Models\Vendas;
use App\Models\VendaLance;
use App\Models\VendaParcela;


class VendasController extends Controller
{

    public function getVendas($id)
    {
        $users = auth()->user();
        $vendas = Vendas::where('id_produto', $id)->where('id_vendedor', $users->id)->latest()->limit(30)->get();

        return view('dashboard.vendas', [
            'users' => $users,
            'vendas' => $vendas,
            'produto' => $id
        ]);
    }

    public function vendas(Request $request)
    {
        $users = auth()->user();

        $dataInicio = $request->input('data_inicio');
        $dataFim = $request->input('data_fim');

        if ($dataInicio && $dataFim) {
            $dataInicio = Carbon::parse($dataInicio);
            $dataFim = Carbon::parse($dataFim);

            $vendas = Vendas::where('id_produto', $request->input('id'))
                ->where('id_vendedor', $users->id)
                ->whereBetween('updated_at', [$dataInicio, $dataFim])
                ->get();
        } else {
            $vendas = Vendas::where('id_produto', $request->input('id'))
                ->where('id_vendedor', $users->id)
                ->latest()
                ->limit(30)
                ->get();
        }

        return view('dashboard.vendas', [
            'users' => $users,
            'vendas' => $vendas,
            'produto' => $request->input('id')
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
            'rg' => 'max:20',
        ]);

        switch ($request->produto) {
            case 3:
                $views = ['documentos.onemotos'];
                $valor = 500;
                break;
            case 2:
                $views = ['documentos.onepositive'];
                $valor = 970;
                break;
            case 8:
                $views = ['documentos.oneservicos'];
                $valor = 127;
                break;
            case 11:
                $views = ['documentos.associadonemotos'];
                $valor = 500;
                break;
            case 12:
                $views = ['documentos.onepositive'];
                $valor = 1500;
                break;
            default:
                $views = ['documentos.contratoonepage'];
                break;
        }

        $vendaData = [
            'id_vendedor' => $id,
        ];

        if ($request->entrada) {
            $valor = $request->entrada;
        }

        $vendaData['valor'] = $valor;

        if (!empty($request->cpfcnpj)) {
            $vendaData['cpf'] = preg_replace('/[^0-9]/', '', $request->cpfcnpj);
        }

        if (!empty($request->cliente)) {
            $vendaData['nome'] = $request->cliente;
        }

        if (!empty($request->dataNascimento)) {
            $vendaData['dataNascimento'] = Carbon::createFromFormat('d-m-Y', $request->dataNascimento)->format('Y-m-d');
        }

        if (!empty($request->email)) {
            $vendaData['email'] = $request->email;
        }

        if (!empty($request->telefone)) {
            $vendaData['telefone'] = preg_replace('/[^0-9]/', '', $request->telefone);
        }

        if (!empty($request->rg)) {
            $vendaData['rg'] = $request->rg;
        }

        if (!empty($request->cep) && !empty($request->estado) && !empty($request->cidade) && !empty($request->bairro) && !empty($request->numero)) {
            $vendaData['endereco'] = $request->cep . ' - ' . $request->estado . '/' . $request->cidade . ' - ' . $request->bairro . ' N° ' . $request->numero;
        }

        if (!empty($request->produto)) {
            $vendaData['id_produto'] = $request->produto;
        }

        $venda = Vendas::create($vendaData);

        if (!$venda) {
            return redirect()->route($request->franquia)->withErrors(['Falha no cadastro. Por favor, tente novamente.']);
        }

        $data = [
            'cpfcnpj' => preg_replace('/[^0-9]/', '', $request->cpfcnpj),
            'cliente' => $request->cliente,
            'dataNascimento' => Carbon::createFromFormat('d-m-Y', $request->dataNascimento)->format('Y-m-d'),
            'email' => $request->email,
            'telefone' => preg_replace('/[^0-9]/', '', $request->telefone),
            'profissao' => $request->profissao,
            'rg' => $request->rg,
            'civil' => $request->civil,
            'cep' => $request->cep,
            'numero' => $request->numero,
            'estado' => $request->estado,
            'cidade' => $request->cidade,
            'bairro' => $request->bairro,
            'endereco' => $request->endereco,
            'auth'      => 'whatsapp',
            'produto'   => $request->produto,
            'entrada'   => $request->entrada
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
                $pasta = "/onepositive";
                break;
            case 3:
                $pasta = "/onemotos";
                break;
            case 8:
                $pasta = "/oneservicos";
                break;
            case 11:
                $pasta = "/associadonemotos";
                break;
            case 12:
                $pasta = "/associadonepositive";
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
                        'path' => $pasta . '/Contrato One Motos ' . $data['cliente'] . '.pdf',
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
            $url = 'https://api.z-api.io/instances/3BF660F605143051CA98E2F1A4FCFFCB/token/3048386F0FE68A1828B852B1/send-link';

            $response = $client->post($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'phone'     => '55' . $telefone,
                    'message'   => "Prezado Cliente, segue seu contrato de adesão ao produto da One Clube: \r\n \r\n",
                    'image'     => 'https://oneclube.com.br/images/logo.png',
                    'linkUrl'   => $contrato,
                    'title'     => 'Assinatura de Contrato',
                    'linkDescription' => 'Link para Assinatura Digital'
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

    public function lance(Request $request) {
        $user = auth()->user();

        $vendaId = $request->input('id');
        $totalPago = $request->input('totalPago');
        $oferta = $request->input('oferta');

        $mesAtual = Carbon::now()->format('m');

        VendaLance::where('venda_id', $vendaId)->delete();
        VendaLance::create([
            'venda_id' => $vendaId,
            'user_id' => $user->id,
            'pago' => $totalPago,
            'oferta' => $oferta,
            'mes' => $mesAtual
        ]);

        return redirect()->route('dashboard')->with('success', 'Oferta realizada com sucesso!');
    }

    public function geraAssasParcela(Request $request) {

        $user = auth()->user();
        $id = $request->input('id');

        $dados = [
            'id_assas'  => 'false',
            'nome'      => $user->nome,
            'cpf'       => $request->input('cpf'),
            'valor'     => $request->input('valor'),
            'parcelas'  => 1,
        ];

        $request = new Request($dados);

        $assasParcela = new AsaasController();
        $resultado = $assasParcela->geraAssasOneClube($request);

        if($resultado){
            $paymentId = $resultado['json']['paymentId'];

            $parcela = VendaParcela::where('id', $id)->first();
            $parcela->id_assas = $paymentId;
            $parcela->save();

            $paymentLink = $resultado['json']['paymentLink'];
            return redirect()->route('relatorioParcelas')->with('link', $paymentLink);
        } else {
            $mensagemErro = "Houve um erro ao gerar o link de pagamento!";
            return redirect()->route('relatorioParcelas')->with('mensagemErro', $mensagemErro);
        }
    }
}
