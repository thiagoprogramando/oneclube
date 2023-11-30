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


class VendasController extends Controller {

    public function getVendas($id) {

        $users = auth()->user();
        $vendas = Vendas::where('id_produto', $id)->where('id_vendedor', $users->id)->latest()->limit(30)->get();

        return view('dashboard.vendas', [
            'users' => $users,
            'vendas' => $vendas,
            'produto' => $id
        ]);
    }

    public function vendas(Request $request) {

        $users = auth()->user();

        $dataInicio = $request->input('data_inicio');
        $dataFim = $request->input('data_fim');

        if ($dataInicio && $dataFim) {
            $dataInicio = Carbon::parse($dataInicio);
            $dataFim = Carbon::parse($dataFim);

            $vendas = Vendas::where('id_produto', $request->input('id'))->where('id_vendedor', $users->id)->whereBetween('updated_at', [$dataInicio, $dataFim])->get();
        } else {
            $vendas = Vendas::where('id_produto', $request->input('id'))->where('id_vendedor', $users->id)->get();
        }

        return view('dashboard.vendas', [
            'users' => $users,
            'vendas' => $vendas,
            'produto' => $request->input('id')
        ]);
    }

    public function vender(Request $request, $id) {

        $request->validate([
            'cpfcnpj' => 'required|string|max:255',
            'cliente' => 'required|string|max:255',
            'dataNascimento' => 'required|string|max:255',
            'email' => 'string|max:255',
            'telefone' => 'required|string|max:20',
            'rg' => 'max:20',
        ]);

        $vendaData = [
            'id_vendedor' => $id,
        ];

        switch ($request->produto) {
            case 1:
                $views = ['documentos.limpanome'];
                $vendaData['valor'] = $request->valor;
                break;
            default:
                $views = ['documentos.limpanome'];
                $vendaData['valor'] = $request->valor;
                break;
        }

        if (!empty($request->cpfcnpj)) {
            $vendaData['cpfcnpj'] = preg_replace('/[^0-9]/', '', $request->cpfcnpj);
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
            $vendaData['cep'] = $request->cep;
            $vendaData['estado'] = $request->estado;
            $vendaData['cidade'] = $request->cidade;
            $vendaData['bairro'] = $request->bairro;
            $vendaData['numero'] = $request->numero;
        }

        if (!empty($request->produto)) {
            $vendaData['id_produto'] = $request->produto;
        }

        $venda = Vendas::create($vendaData);
        if (!$venda) {
            return redirect()->route($request->franquia)->withErrors(['Falha no cadastro. Por favor, tente novamente.']);
        }

        // $data = [
        //     'cpfcnpj' => preg_replace('/[^0-9]/', '', $request->cpfcnpj),
        //     'cliente' => $request->cliente,
        //     'dataNascimento' => Carbon::createFromFormat('d-m-Y', $request->dataNascimento)->format('Y-m-d'),
        //     'email' => $request->email,
        //     'telefone' => preg_replace('/[^0-9]/', '', $request->telefone),
        //     'profissao' => $request->profissao,
        //     'rg' => $request->rg,
        //     'civil' => $request->civil,
        //     'cep' => $request->cep,
        //     'numero' => $request->numero,
        //     'estado' => $request->estado,
        //     'cidade' => $request->cidade,
        //     'bairro' => $request->bairro,
        //     'endereco' => $request->endereco,
        //     'auth'      => 'whatsapp',
        //     'produto'   => $request->produto,
        //     'valor'     => 450,
        // ];

        $dompdf = new Dompdf();

        $html = '';
        $total = 0;
        foreach ($views as $view) {
            $html .= View::make($view, ['data' => $vendaData])->render();
            $total++;
            if ($total != 4) {
                $html .= '<div style="page-break-before:always;"></div>';
            }
        }

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $pdfContent = $dompdf->output();

        $filePath = public_path('contratos/' . $request->produto . $vendaData['cpfcnpj'] . '.pdf');
        file_put_contents($filePath, $pdfContent);

        $pdfPath = public_path('contratos/' . $request->produto . $vendaData['cpfcnpj'] . '.pdf');
        $pdfContent = file_get_contents($pdfPath);
        $vendaData['pdf'] = $pdfBase64 = base64_encode($pdfContent);

        $documento = $this->criaDocumento($vendaData);
        if ($documento['signer']) {
            $venda->id_contrato = $documento['token'];
            $venda->file        = $documento['sign_url'];
            $venda->save();

            $notificar = $this->notificarSignatario($documento['signer'], $vendaData['telefone']);
            if ($notificar != null) {
                return view('obrigado', ['success' => 'Obrigado! Enviaremos o contrato diretamente para o seu whatsapp.']);
            }

            return view('obrigado', ['success' => 'Cadastro realizado com sucesso, mas não foi possivel enviar o contrato! Consulte seu atendente.']);
        } else {
            return redirect()->route($request->franquia, ['id' => $id])->withErrors(['Erro ao gerar assinatura!'])->withInput();
        }
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
                    "name" => "Contrato Consultoria Financeira",
                    "base64_pdf" => 'data:application/pdf;base64,' . $data['pdf'],
                    "external_id" => $data['cpfcnpj'],

                    'signers' => [[
                        "name"      => $data['nome'],
                        "email"     => $data['email'],
                        "date_limit_to_sign" => $formattedDate,
                        "lang"      => "pt-br",
                        "brand_primary_color " => "#43F47A",
                        "brand_logo " => "https://grupo7assessoria.com.br/wp-content/uploads/2023/07/Copia-de-MULTISERVICOS-250-%C3%97-250-px-2.png",
                        "folder_path" => "LimpaNome-CRM",
                        "signed_file_only_finished" => "true",
                        "disable_signer_emails " => "true",
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

    public function notificarSignatario($contrato, $telefone) {

        $client = new Client();

        $url = env('API_URL_ZAPI') . '/send-link';

        $response = $client->post($url, [
            'headers' => [
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
                'Client-Token'  => 'Fabe25dbd69e54f34931e1c5f0dda8c5bS'
            ],
            'json' => [
                'phone'           => '55' . $telefone,
                'message'         => "Prezado Cliente, segue seu contrato de adesão ao produto da G7 Assessoria: \r\n \r\n",
                'image'           => 'https://grupo7assessoria.com.br/wp-content/uploads/2023/07/Copia-de-MULTISERVICOS-250-%C3%97-250-px-2.png',
                'linkUrl'         => $contrato,
                'title'           => 'Assinatura de Contrato',
                'linkDescription' => 'Link para Assinatura Digital'
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
