<?php

namespace App\Http\Controllers\Sale;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SalesImport;

use App\Models\Sale;
use App\Models\User;
use Carbon\Carbon;
use Dompdf\Dompdf;

class SaleController extends Controller {

    public function getSales($id) {

        $users = auth()->user();
        $sales = Sale::where('id_produto', $id)->where('id_vendedor', $users->id)->latest('created_at')->limit(30)->get();

        return view('dashboard.users.sales', [
            'sales' => $sales,
            'produto' => $id
        ]);
    }

    public function filterSales(Request $request) {

        $user = auth()->user();

        $dataInicio = $request->input('data_inicio');
        $dataFim = $request->input('data_fim');
        $cliente = $request->input('cliente');

        $sales = Sale::query();

        if (!empty($cliente)) {
            $sales = $sales->where('name', 'LIKE', '%' . $cliente . '%');
        }

        if ($dataInicio && $dataFim) {
            $dataInicio = Carbon::parse($dataInicio);
            $dataFim = Carbon::parse($dataFim);

            $sales = $sales->where('id_produto', $request->input('id'))->where('id_vendedor', $user->id)->whereBetween('updated_at', [$dataInicio, $dataFim])->latest('created_at')->get();
        } else {
            $sales = $sales->where('id_produto', $request->input('id'))->where('id_vendedor', $user->id)->latest('created_at')->get();
        }

        return view('dashboard.users.sales', [
            'sales' => $sales,
            'produto' => $request->input('id')
        ]);
    }

    public function updateSale(Request $request) {

        $sale = Sale::where('id',  $request->id)->first();
        if($sale) {
            if(!empty($request->tag)) {
                $sale->tag = $request->tag;
            }

            if(!empty($request->status)) {
                $sale->status_pay = $request->status;
            }
            $sale->save();

            return redirect()->route('saleManager')->with('success', 'Dados atualizados com sucesso!');
        }

        return redirect()->route('saleManager')->with('error', 'Venda não encontrada!');
    }

    public function saleManager() {

        return view('dashboard.manager.sales', [
            'users' => User::all(),
            'sales' => Sale::orderBy('created_at', 'desc')->get(),
        ]);
    }

    public function filterSaleManager(Request $request) {

        $produto = $request->input('produto');
        $usuario = $request->input('usuario');
        $status = $request->input('status');
        $cliente = $request->input('cliente');

        $sales = Sale::query();

        if ($produto != 'ALL') {
            $sales = $sales->where('id_produto', $produto);
        }

        if ($usuario != 'ALL') {
            $sales = $sales->where('id_vendedor', $usuario);
        }

        if ($status != 'ALL') {
            $sales = $sales->where('status_pay', $status)->orWhereNull('status_pay');
        }

        if (!empty($cliente)) {
            $sales = $sales->where('name', 'LIKE', '%' . $cliente . '%');
        }

        $dataInicio = $request->input('data_inicio');
        $dataFim = $request->input('data_fim');

        if ($dataInicio && $dataFim) {
            $dataInicio = Carbon::parse($dataInicio);
            $dataFim = Carbon::parse($dataFim);

            $sales->whereBetween('updated_at', [$dataInicio, $dataFim]);
        }

        $sales = $sales->latest('created_at')->get();

        return view('dashboard.manager.sales', [
            'sales' => $sales,
            'users'  => User::all(),
        ]);
    }

    public function updateSaleManager(Request $request) {

        $file = $request->file('file');
        $status = $request->input('status');
        $tag = $request->input('tag');

        $import = new SalesImport($status, $tag);
        Excel::import($import, $file);

        return redirect()->route('saleManager')->with('success', 'Dados atualizados com sucesso!');
    }

    public function sell(Request $request, $id) {

        $verifySale = Sale::where('cpfcnpj', preg_replace('/[^0-9]/', '', $request->cpfcnpj))->first();
        if ($verifySale) {
            return redirect()->back()->with('error', 'Já registramos sua solicitação, te enviaremos por whatsapp!');
        }

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
            'id_vendedor'     => $id,
            'id_produto'      => $request->produto,
            'name_doc'        => "Contrato Consultoria Financeira",
            'value'           => $request->valor,
            'PRIMEIRAPARCELA' => $request->valor / $request->installmentCount < 390 ? 390 : $request->valor / $request->installmentCount,
            'status'          => 'PENDING_PAY'
        ];

        if (!empty($request->name)) {
            $saleData['name'] = $request->name;
        }

        if (!empty($request->cpfcnpj)) {
            if($this->isCpfCnpjValid($request->cpfcnpj) == false) {
                return redirect()->back()->withErrors($validator)->with('error', 'CPF ou CNPJ inválido!');
            }
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

        $document = $this->criaDocumento($saleData);
        if ($document['signers'][0]['sign_url']) {
            $venda = Sale::create($saleData);
            $venda->id_contrato = $document['token'];
            $venda->sign_url_contrato = $document['signers'][0]['sign_url'];
            $venda->save();

            $message = "Prezado Cliente, segue seu *contrato de adesão* ao produto da G7 Assessoria: \r\n ASSINAR O CONTRATO CLICANDO NO LINK 👇🏼✍🏼";
            $notificar = $this->notificarSignatario($document['signers'][0]['sign_url'], $saleData['mobilePhone'], $message);
            if ($notificar != null) {
                return redirect()->route('obrigado')->with('success', 'Obrigado! Enviaremos o contrato diretamente para o seu WhatsApp.');
            }
            
            return redirect()->route('obrigado')->with('success', 'Cadastro realizado com sucesso, mas não foi possivel enviar o contrato! Consulte seu atendente.');
        } else {
            return redirect()->route('obrigado')->with('error', 'Erro ao gerar assinatura!');
        }
    }

    private function isCpfCnpjValid($value) {

        $cleanedValue = preg_replace('/[^0-9]/', '', $value);
    
        if (strlen($cleanedValue) === 11) {
            return true;
        } elseif (strlen($cleanedValue) === 14) {
            return true;
        }

        return false;
    }

    private function criaDocumento($data) {

        $client = new Client();

        $url = env('API_URL_ZAPSIGN') . 'api/v1/models/create-doc/';

        $currentDate = Carbon::now();
        $day = $currentDate->format('d');
        $month = $currentDate->format('m');
        switch ($month) {
            case '01':
                $monthName = 'Janeiro';
                break;
            case '02':
                $monthName = 'Fevereiro';
                break;
            case '03':
                $monthName = 'Março';
                break;
            case '04':
                $monthName = 'Abril';
                break;
            case '05':
                $monthName = 'Maio';
                break;
            case '06':
                $monthName = 'Junho';
                break;
            case '07':
                $monthName = 'Julho';
                break;
            case '08':
                $monthName = 'Agosto';
                break;
            case '09':
                $monthName = 'Setembro';
                break;
            case '10':
                $monthName = 'Outubro';
                break;
            case '11':
                $monthName = 'Novembro';
                break;
            case '12':
                $monthName = 'Dezembro';
                break;
            default:
                $monthName = 'Mês Desconhecido';
                break;
        }
        $year = $currentDate->format('Y');

        try {
            $response = $client->post($url, [
                'headers' => [
                    'Content-Type'  => 'application/json',
                    'Authorization' => 'Bearer '.env('API_TOKEN_ZAPSIGN'),
                ],
                'json' => [
                    "template_id"       => '31842406-05a1-4cdb-8480-e58dd84032e0',
                    "signer_name"       => $data['name'],
                    "signer_email"      => $data['email'],
                    "folder_path"       => 'Limpa Nome '.$day.'-'.$monthName,
                    "data"  => [
                        [
                            "de"    => "NOME",
                            "para"  => $data['name']
                        ],
                        [
                            "de"    => "RG",
                            "para"  => $data['rg']
                        ],
                        [
                            "de"    => "CPFCNPJ",
                            "para"  => $data['cpfcnpj']
                        ],
                        [
                            "de"    => "DATANASCIMENTO",
                            "para"  => Carbon::createFromFormat('Y-m-d', $data['birthDate'])->format('d/m/Y')
                        ],
                        [
                            "de"    => "ENDERECO",
                            "para"  => $data['address']
                        ],
                        [
                            "de"    => "VALOR",
                            "para"  => $data['value']
                        ],
                        [
                            "de"    => "PRIMEIRAPARCELA",
                            "para"  => $data['PRIMEIRAPARCELA']
                        ],
                        [
                            "de"    => "DIA",
                            "para"  => $day
                        ],
                        [
                            "de"    => "MES",
                            "para"  => $monthName
                        ],
                        [
                            "de"    => "ANO",
                            "para"  => $year
                        ],
                    ],
                ],
                        
            ]);

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            return [
                'error' => 'Erro ao criar o documento',
                'message' => $e->getMessage(),
                'response' => $e->getResponse() ? $e->getResponse()->getBody()->getContents() : null,
            ];
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
