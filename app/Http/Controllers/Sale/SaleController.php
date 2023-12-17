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

        $venda = Sale::create($saleData);
        if (!$venda) {
            return redirect()->route($request->franquia)->withErrors(['Falha no cadastro. Por favor, tente novamente.']);
        }

        $dompdf = new Dompdf();

        $html = '';
        $total = 0;
        switch ($request->produto) {
            case 1:
                $views = ['documentos.limpanome'];
                break;
            default:
                $views = ['documentos.limpanome'];
                break;
        }
        foreach ($views as $view) {
            $html .= View::make($view, ['data' => $saleData])->render();
            $total++;
            if ($total != 4) {
                $html .= '<div style="page-break-before:always;"></div>';
            }
        }

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $pdfContent = $dompdf->output();

        $filePath = public_path('contratos/' . $request->produto . $saleData['cpfcnpj'] . '.pdf');
        file_put_contents($filePath, $pdfContent);

        $pdfPath = public_path('contratos/' . $request->produto . $saleData['cpfcnpj'] . '.pdf');
        $pdfContent = file_get_contents($pdfPath);
        $saleData['pdf'] = $pdfBase64 = base64_encode($pdfContent);

        $documento = $this->criaDocumento($saleData);
        if ($documento['signer']) {
            $venda->id_contrato         = $documento['token'];
            $venda->sign_url_contrato   = $documento['sign_url'];
            $venda->save();
            
            $message = "Prezado Cliente, segue seu *contrato de adesão* ao produto da G7 Assessoria: \r\n \r\n";
            $notificar = $this->notificarSignatario($documento['sign_url'], $saleData['mobilePhone'], $message);
            if ($notificar != null) {
                return redirect()->route('obrigado')->with('success', 'Obrigado! Enviaremos o contrato diretamente para o seu WhatsApp.');
            }

            return redirect()->route('obrigado')->with('success', 'Cadastro realizado com sucesso, mas não foi possivel enviar o contrato! Consulte seu atendente.');
        } else {
            return redirect()->route($request->franquia, ['id' => $id])->withErrors(['Erro ao gerar assinatura!'])->withInput();
        }
    }

    public function fichaAssociativa($id) {

        $sale = Sale::find($id);

        $dompdf = new Dompdf();

        $html = '';
        $total = 0;
        switch ($sale->id_produto) {
            case 1:
                $views = ['documentos.fichAssociativa'];
                break;
            default:
                $views = ['documentos.fichAssociativa'];
                break;
        }
        $saleData = [
            'name'      => $sale->name,
            'cpfcnpj'   => $sale->cpfcnpj,
            'email'   => $sale->email,
            'name_doc'   => "Ficha Associativa",
        ];
        foreach ($views as $view) {
            $html .= View::make($view, ['data' => $saleData])->render();
            $total++;
            if ($total != 4) {
                $html .= '<div style="page-break-before:always;"></div>';
            }
        }

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $pdfContent = $dompdf->output();

        $filePath = public_path('contratos/fichas/' . $sale->id_produto . $saleData['cpfcnpj'] . '.pdf');
        file_put_contents($filePath, $pdfContent);

        $pdfContent = file_get_contents($filePath);
        $saleData['pdf'] = base64_encode($pdfContent);
        
        $documento = $this->criaDocumento($saleData);
        if ($documento['signer']) {
            $sale->id_ficha         = $documento['token'];
            $sale->sign_url_ficha   = $documento['sign_url'];
            $sale->save();

            $message = "Olá, Cliente G7. Agora que você concordou com os termos, precisamos que preencha sua *Ficha Associativa*: \r\n \r\n";
            $notificar = $this->notificarSignatario($documento['sign_url'], $sale->mobilePhone, $message);
            if ($notificar != null) {
                return true;
            }

            return true;
        }

        return false;
    }

    private function criaDocumento($data) {

        $client = new Client();

        $url = env('API_URL_ZAPSIGN') . 'api/v1/docs/';

        $currentDate = Carbon::now();
        $dateLimitToSign = $currentDate->addDays(3);
        $formattedDate = $dateLimitToSign->format('Y-m-d');

        try {
            $response = $client->post($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization'  =>  'Bearer ' . env('API_TOKEN_ZAPSIGN')
                ],
                'json' => [
                    "name" => $data['name_doc'],
                    "base64_pdf" => 'data:application/pdf;base64,' . $data['pdf'],
                    "external_id" => $data['cpfcnpj'],

                    'signers' => [[
                        "name"                      => $data['name'],
                        "email"                     => $data['email'],
                        "auth_mode"                 => "default",
                        "date_limit_to_sign"        => $formattedDate,
                        "lang"                      => "pt-br",
                        "brand_primary_color "      => "#43F47A",
                        "brand_logo "               => "https://grupo7assessoria.com.br/wp-content/uploads/2023/07/Copia-de-MULTISERVICOS-250-%C3%97-250-px-2.png",
                        "folder_path"               => "LimpaNomeCRM",
                        "signed_file_only_finished" => "true",
                        "disable_signer_emails "    => "true",
                    ]],
                ],
            ]);

            $responseData = json_decode($response->getBody(), true);

            return $data = [
                "token"         => $responseData['token'],
                "sign_url"      => $responseData['signers'][0]['sign_url'],
                "signer"        => $responseData['signers'][0],
            ];
        } catch (RequestException $e) {
            return false;
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
