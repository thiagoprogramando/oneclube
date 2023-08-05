<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\View;
use Illuminate\Contracts\Validation\Rule;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Carbon\Carbon;

use App\Models\Vendas;
use App\Models\Notificacao;

class VendasController extends Controller
{

    public function getVendas($id) {
        $users = auth()->user();

        $notfic = Notificacao::where(function ($query) use ($users) {
            if ($users->profile === 'admin') {
                $query->where(function ($query) {
                    $query->where('tipo', '!=', '')
                        ->orWhere('tipo', 0);
                });
            } else {
                $query->where(function ($query) use ($users) {
                    $query->where('tipo', 0)
                        ->orWhere('tipo', $users->id);
                });
            }
        })->get();

        $vendas = Vendas::where('id_produto', $id)->where('id_vendedor', $users->id)->latest()->limit(30)->get();

        // Retornar os dados para a view vendas
        return view('dashboard.vendas', [
            'notfic' => $notfic,
            'users' => $users,
            'vendas' => $vendas,
            'produto' => $id
        ]);
    }

    public function vendas(Request $request)
    {
        $users = auth()->user();

        $notfic = Notificacao::where(function ($query) use ($users) {
            if ($users->profile === 'admin') {
                $query->where(function ($query) {
                    $query->where('tipo', '!=', '')
                        ->orWhere('tipo', 0);
                });
            } else {
                $query->where(function ($query) use ($users) {
                    $query->where('tipo', 0)
                        ->orWhere('tipo', $users->id);
                });
            }
        })->get();


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

        // Retornar os dados para a view vendas
        return view('dashboard.vendas', [
            'notfic' => $notfic,
            'users' => $users,
            'vendas' => $vendas,
            'produto' => $request->input('id')
        ]);
    }

    public function vender(Request $request, $id) {
        //Registra venda
        $request->validate([
            'cpfcnpj' => 'required|string|max:255',
            'cliente' => 'required|string|max:255',
            'dataNascimento' => 'required|string|max:255',
            'email' => 'string|max:255',
            'telefone' => 'required|string|max:20',
            'rg' => 'max:20',
        ]); 

        switch ($request->produto) {
            case 2:
                $views = ['documentos.onepositive'];
                $valor = 997;
                break;
            default:
            $views = ['documentos.limpanome'];
                break;
        }

        $vendaData = [
            'id_vendedor' => $id,
        ];
        
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
            $vendaData['endereco'] = $request->cep.' - '.$request->estado.'/'.$request->cidade.' - '.$request->bairro.' N° '.$request->numero;
        }
        
        if (!empty($request->produto)) {
            $vendaData['id_produto'] = $request->produto;
        }
        
        if (!empty($valor)) {
            $vendaData['valor'] = $valor;
        }
        
        $venda = Vendas::create($vendaData);        

        if (!$venda) {
            return redirect()->route($request->franquia)->withErrors(['Falha no cadastro. Por favor, tente novamente.']);
        }

        //Gera Contrato
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
            'produto'   => $request->produto
        ];

        // Crie uma instância do Dompdf
        $dompdf = new Dompdf();
        
        $html = '';
        $total = 0;
        foreach ($views as $view) {
            $html .= View::make($view, ['data' => $data])->render();
            $total++;
            if($total != 4){
                $html .= '<div style="page-break-before:always;"></div>'; // Adicione uma quebra de página entre as views
            }
        }

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $pdfContent = $dompdf->output();
        
        $filePath = public_path('contratos/'.$request->produto.$data['cpfcnpj'].'.pdf');
        file_put_contents($filePath, $pdfContent);
        
        $pdfPath = public_path('contratos/'.$request->produto.$data['cpfcnpj'].'.pdf');
        $pdfContent = file_get_contents($pdfPath);
        $data['pdf'] = $pdfBase64 = base64_encode($pdfContent);

        $link = $this->geraPagamentoAssas($venda->nome, $venda->cpf, $venda->id_produto);
        $venda->id_pay = $link['json']['paymentId'];
        $venda->status_pay = 'PENDING_PAY';
        $venda->save();
        $notificar = $this->notificaCliente($venda->telefone, $link['json']['paymentLink']);

        if($notificar){
            return view('obrigado', ['success' => 'Pedido enviado com sucesso!']);
        } else {
            return redirect()->route($request->franquia, ['id' => $id])->withErrors(['Erro! Tivemos um pequeno imprevisto, tente novamente mais tarde!'])->withInput();
        }
    }

    public function geraPagamentoAssas($nome, $cpfcnpj, $produto)
    {

        switch($produto){
            case 2:
                $produto = 997;
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
        
        $response = $client->post(env('API_URL_ASSAS').'api/v3/customers', $options);
        
        $body = (string) $response->getBody();
        
        $data = json_decode($body, true);
        
        if ($response->getStatusCode() === 200) {
            
            $customerId = $data['id'];
    
            // Calculate tomorrow's date
            $tomorrow = Carbon::now()->addDay()->format('Y-m-d');
    
            $options['json'] = [
                'customer' => $customerId,
                'billingType' => 'BOLETO',
                'value' => $produto,
                'dueDate' => $tomorrow,
                'description' => 'One Motos',
            ];
            
            $response = $client->post(env('API_URL_ASSAS').'api/v3/payments', $options);
            
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

    public function notificaCliente($telefone, $assas) {
        $client = new Client();
        
        $url = 'https://api.z-api.io/instances/3BFF0A2480DEF0812D5F8E0A24FAED45/token/97AD9B2C34BC5BBE2FD52D6B/send-link';

        $response = $client->post($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'json' => [
                'phone'     => '55'.$telefone,
                'message'   => "Prezado Cliente, segue seu link de pagamento. Após confirmação do pagamento enviaremos o seu link para assintura do contrato! \r\n \r\n",
                'image'     => 'https://gruposollution.com.br/assets/img/logo.png',
                'linkUrl'   => $assas,
                'title'     => 'Pagamento Grupo Sollution',
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
}
