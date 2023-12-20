<?php

namespace App\Http\Controllers\Sale;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

use App\Models\Sale;
use App\Models\User;
use Carbon\Carbon;
use Dompdf\Dompdf;

class SaleController extends Controller {

    public function getSales($id) {

        $users = auth()->user();
        $sales = Sale::where('id_produto', $id)->where('id_vendedor', $users->id)->latest()->limit(30)->get();

        return view('dashboard.users.sales', [
            'sales' => $sales,
            'produto' => $id
        ]);
    }

    public function filterSales(Request $request) {

        $user = auth()->user();

        $dataInicio = $request->input('data_inicio');
        $dataFim = $request->input('data_fim');

        if ($dataInicio && $dataFim) {
            $dataInicio = Carbon::parse($dataInicio);
            $dataFim = Carbon::parse($dataFim);

            $sales = Sale::where('id_produto', $request->input('id'))->where('id_vendedor', $user->id)->whereBetween('updated_at', [$dataInicio, $dataFim])->get();
        } else {
            $sales = Sale::where('id_produto', $request->input('id'))->where('id_vendedor', $user->id)->get();
        }

        return view('dashboard.users.sales', [
            'sales' => $sales,
            'produto' => $request->input('id')
        ]);
    }

    public function updateSale(Request $request) {

        $sale = Sale::where('id',  $request->id)->first();
        if($sale) {
            $sale->tag = $request->tag;
            $sale->save();

            return redirect()->back()->with('success', 'Venda atualizada com sucesso!');
        }

        return redirect()->back()->with('error', 'Venda não encontrada!');
    }

    public function saleManager() {

        return view('dashboard.manager.sales', [
            'users' => User::all(),
            'sales' => Sale::all(),
        ]);
    }

    public function filterSaleManager(Request $request) {

        $produto = $request->input('produto');
        $usuario = $request->input('usuario');
        $status = $request->input('status');

        $sales = Sale::query();

        if ($produto != 'ALL') {
            $sales = $sales->where('id_produto', $produto);
        }

        if ($usuario != 'ALL') {
            $sales = $sales->where('id_vendedor', $usuario);
        }

        if ($status != 'ALL') {
            $sales = $sales->where('status_pay', $status);
        }

        $dataInicio = $request->input('data_inicio');
        $dataFim = $request->input('data_fim');

        if ($dataInicio && $dataFim) {
            $dataInicio = Carbon::parse($dataInicio);
            $dataFim = Carbon::parse($dataFim);

            $sales->whereBetween('updated_at', [$dataInicio, $dataFim]);
        }

        $sales = $sales->get();

        return view('dashboard.manager.sales', [
            'sales' => $sales,
            'users'  => User::all(),
        ]);
    }

    public function sell(Request $request, $id) {

        $validator = Validator::make($request->all(), [
            'name'          => 'required|string|max:255',
            'cpfcnpj'       => 'required|string|max:255',
            'birthDate'     => 'required|string|max:255',
            'email'         => 'string|max:255',
            'mobilePhone'   => 'required|string|max:20',
            'rg'            => 'required|max:20',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->with('error', 'Verique todos os dados e tente novamente!');
        }

        $saleData = [
            'id_vendedor' => $id,
            'id_produto'  => $request->produto,
            'name_doc'    => "Contrato Consultoria Financeira"
        ];

        switch ($request->valor) {
            case 1997:
                $saleData['value'] = $request->valor;
                $saleData['comission'] = 50;
                break;
            case 1497:
                $saleData['value'] = $request->valor;
                $saleData['comission'] = 40;
                break;
            case 1197:
                $saleData['value'] = $request->valor;
                $saleData['comission'] = 30;
                break;
            case 997:
                $saleData['value'] = $request->valor;
                $saleData['comission'] = 20;
                break;
            default:
                $saleData['value'] = $request->valor;
                $saleData['comission'] = 0;
                break;
        }

        if (!empty($request->name)) {
            $saleData['name'] = $request->name;
        }

        if (!empty($request->cpfcnpj)) {
            $saleData['cpfcnpj'] = preg_replace('/[^0-9]/', '', $request->cpfcnpj);
        }

        if (!empty($request->birthDate)) {
            $saleData['birthDate'] = Carbon::createFromFormat('d-m-Y', $request->birthDate)->format('Y-m-d');
        }

        if (!empty($request->email)) {
            $saleData['email'] = $request->email;
        }

        if (!empty($request->mobilePhone)) {
            $saleData['mobilePhone'] = preg_replace('/[^0-9]/', '', $request->mobilePhone);
        }

        if (!empty($request->rg)) {
            $saleData['rg'] = $request->rg;
        }

        if (!empty($request->cep) && !empty($request->estado) && !empty($request->cidade) && !empty($request->bairro) && !empty($request->numero)) {
            $saleData['address'] = $request->cep . ' - ' . $request->estado . '/' . $request->cidade . ' - ' . $request->bairro . ' N° ' . $request->numero;
        }

        if (!empty($request->billingType)) {
            $saleData['billingType'] = $request->billingType;
        }

        if (!empty($request->installmentCount)) {
            $saleData['installmentCount'] = $request->installmentCount;
        }

        $saleDate['ato'] = $request->installmentCount > 1 ? 300 : $request->valor;

        $venda = Sale::create($saleData);
        if (!$venda) {
            return redirect()->route($request->franquia)->withErrors(['Falha no cadastro. Por favor, tente novamente.']);
        }

        return $keyDocumento = $this->criaDocumento($saleData);
        if ($keyDocumento) {
            $venda->id_contrato = $keyDocumento;
            $venda->save();

            $keySignatario = $this->criaSignatario($saleData);
            if($keySignatario) {
                
                $addSignatarios = $this->adiconaSignatario($keyDocumento, $keySignatario);
                if ($addSignatarios['type'] != null) {
                    $venda->sign_url_contrato = $addSignatarios['url'];
                    $venda->save();

                    $message = "Prezado Cliente, segue seu *contrato de adesão* ao produto da G7 Assessoria: \r\n \r\n";
                    $notificar = $this->notificarSignatario($addSignatarios['url'], $saleData['mobilePhone'], $message);
                    if ($notificar != null) {
                        return redirect()->route('obrigado')->with('success', 'Obrigado! Enviaremos o contrato diretamente para o seu WhatsApp.');
                    }
                }

                return redirect()->route($request->franquia, ['id' => $id])->withErrors(['Tivemos um pequeno problema, tente novamente mais tarde!'])->withInput();
            }
            
            return redirect()->route('obrigado')->with('success', 'Cadastro realizado com sucesso, mas não foi possivel enviar o contrato! Consulte seu atendente.');
        } else {
            return redirect()->route($request->franquia, ['id' => $id])->withErrors(['Erro ao gerar assinatura!'])->withInput();
        }
    }

    private function criaDocumento($data) {

        $client = new Client();

        $url = env('API_URL_CLICKSING') . 'api/v1/templates/F6C08860-AE25-44B0-B3EC-971C8E6DBF84/documents?access_token=' . env('TOKEN_CLICKSING');

        $currentDate = Carbon::now();
        $formattedDate = $currentDate->format('Y-m-d');
        $day = $currentDate->format('d');
        $month = $currentDate->format('m');
        $year = $currentDate->format('Y');

        try {
            $response = $client->post($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => [
                    "path"              => '/G7 Limpa Nome '.$formattedDate.'/'.$data['name'].'.docx',
                    "template"          => [
                        "NOME"              => $data['name'],
                        "RG"                => $data['rg'],
                        "CPFCNPJ"           => $data['cpfcnpj'],
                        "DATANASCIMENTO"    => $data['birthDate'],
                        "ENDERECO"          => $data['address'],
                        "ATO"               => $data['ato'],
                        "DIA"               => $day,
                        "MES"               => $month,
                        "ANO"               => $year,
                    ]
                ],
            ]);

            $responseData = json_decode($response->getBody(), true);

            return $responseData['key'];
        } catch (RequestException $e) {
            return [
                'error' => $e->getMessage(),
                'code'  => $e->getCode(),
            ];
        }
    }

    private function criaSignatario($data) {
        $client = new Client();

        $url = env('API_URL_CLICKSING') . 'api/v1/signers?access_token=' . env('TOKEN_CLICKSING');

        try {
            $response = $client->post($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'signer' => [
                        'email'                      => $data['email'],
                        'phone_number'               => $data['phone'],
                        'name'                       => $data['name'],
                        'auths'                      => [ 'whatsapp' ],
                        'documentation'              => $data['cpfcnpj'],
                        'birthday'                   => $data['birthDate'],
                        'has_documentation'          => 'true',
                        'selfie_enabled'             => 'false',
                        'handwritten_enabled'        => 'false',
                        'official_document_enabled'  => 'true',
                        'liveness_enabled'           => 'false',
                        'facial_biometrics_enabled'  => 'false',
                    ],
                ],
            ]);

            $responseData = json_decode($response->getBody(), true);

            if (isset($responseData['signer']['key'])) {
                return  $responseData['signer']['key'];
            } else {
                return false;
            }
        } catch (RequestException $e) {
            return false;
        }
    }

    private function adiconaSignatario($keyDocumento, $keySignatario) {
        
        $client = new Client();

        $url = env('API_URL_CLICKSING') . 'api/v1/lists?access_token=' . env('TOKEN_CLICKSING');

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

    private function notificarSignatario($contrato, $telefone, $message) {

        $client = new Client();

        $url = 'https://api.z-api.io/instances/3C71DE8B199F70020C478ECF03C1E469/token/DC7D43456F83CCBA2701B78B/send-link';
        try {

            $response = $client->post($url, [
                'headers' => [
                    'Content-Type'  => 'application/json',
                    'Accept'        => 'application/json',
                    'Client-Token'  => 'Fabe25dbd69e54f34931e1c5f0dda8c5bS',
                ],
                'json' => [
                    'phone'           => '55' . $telefone,
                    'message'         => $message,
                    'image'           => 'https://grupo7assessoria.com.br/wp-content/uploads/2023/07/Copia-de-MULTISERVICOS-250-%C3%97-250-px-2.png',
                    'linkUrl'         => $contrato,
                    'title'           => 'Assinatura de Documento',
                    'linkDescription' => 'Link para Assinatura Digital',
                ],
            ]);

            $responseData = json_decode($response->getBody(), true);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
