<?php

namespace App\Http\Controllers;

use App\Models\Nossonumero;
use App\Models\Parcela;
use App\Models\Vendas;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Http\Request as IlluminateRequest;


class BancoDoBrasilController extends Controller
{

    public function geraToken() {
        $client = new Client();

        $data = [
            'form_params' => [
                'grant_type' => 'client_credentials',
                'scope' => 'cobrancas.boletos-info, cobrancas.boletos-requisicao',
            ],
            'headers' => [
                'Authorization' => 'Basic ZXlKcFpDSTZJbUZrT1Rka01EZ3RZV0k1TXkwME9HVTRMVGcyWWpJdFlqVTNOemN3SWl3aVkyOWthV2R2VUhWaWJHbGpZV1J2Y2lJNk1Dd2lZMjlrYVdkdlUyOW1kSGRoY21VaU9qYzFOREkwTENKelpYRjFaVzVqYVdGc1NXNXpkR0ZzWVdOaGJ5STZNWDA6ZXlKcFpDSTZJbUpoWkRJeE1qYzRMV0V3TkRFdE5EQmxZUzA0TkROakxUUXpZMkUxTXpNek1qVXhOQ0lzSW1OdlpHbG5iMUIxWW14cFkyRmtiM0lpT2pBc0ltTnZaR2xuYjFOdlpuUjNZWEpsSWpvM05UUXlOQ3dpYzJWeGRXVnVZMmxoYkVsdWMzUmhiR0ZqWVc4aU9qRXNJbk5sY1hWbGJtTnBZV3hEY21Wa1pXNWphV0ZzSWpveExDSmhiV0pwWlc1MFpTSTZJbWh2Ylc5c2IyZGhZMkZ2SWl3aWFXRjBJam94TmprMk1ERXpOekE1TnpVMWZR',
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'verify' => false
        ];

        $response = $client->request('POST', env('API_URL_BB_AUTH'), $data);
        if ($response->getStatusCode() == 201 || $response->getStatusCode() == 200) {
            $responseData = json_decode($response->getBody(), true);
            $accessToken = $responseData['access_token'];

            return $accessToken;
        } else {
            return false;
        }
    }

    public function geraBoleto($venda, $parcela = null) {

        $venda = Vendas::find($venda);
        $tipoInscricao = (strlen($venda->cpf) > 11) ? '2' : '1';
        if($parcela) {
            $parcela = Parcela::where('id', $parcela)->first();
        } else {
            $parcela = Parcela::where('id_venda', $venda->id)->where('status', 'PENDING_PAY')->first();
        }

        $dataVencimento = date('d.m.Y', strtotime($parcela->vencimento));

        $client = new Client();

        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->geraToken(),
        ];

        $body = '{
            "numeroConvenio": ' . env('NUMEROCONVENIO') . ',
            "dataVencimento": "' . $dataVencimento . '",
            "valorOriginal": "' . $parcela->valor . '",
            "numeroCarteira": ' . env('NUMEROCARTEIRA') . ',
            "numeroVariacaoCarteira": ' . env('NUMEROVARIACAOCARTEIRA') . ',
            "codigoModalidade": "1",
            "dataEmissao": "' . date('d.m.Y') . '",
            "valorAbatimento": "0",
            "quantidadeDiasProtesto": "0",
            "quantidadeDiasNegativacao": "0",
            "orgaoNegativador": "10",
            "indicadorAceiteTituloVencido": "N",
            "numeroDiasLimiteRecebimento": "0",
            "codigoAceite": "A",
            "codigoTipoTitulo": "2",
            "descricaoTipoTitulo": "DM",
            "indicadorPermissaoRecebimentoParcial": "N",
            "numeroTituloCliente": "' . Nossonumero::gerarNumeroTituloCliente() . '",
            "pagador": {
                "tipoInscricao": "' . $tipoInscricao . '",
                "numeroInscricao": "' . $venda->cpf . '",
                "nome": "' . $venda->nome . '",
                "endereco": "' . $venda->endereco . '",
                "cep": "' . $venda->cep . '",
                "cidade": "' . $venda->cidade . '",
                "bairro": "' . $venda->bairro . '",
                "uf": "' . $venda->uf . '",
                "telefone": "' . $venda->telefone . '"
            },
            "indicadorPix": "S"
        }';

        $options = [
            'headers' => $headers,
            'body' => $body,
            'verify' => false,
        ];

        $request = new Request('POST', env('API_URL_BB_COBRANCA') . 'v2/boletos?gw-dev-app-key=eb3f8901f8222d55f78f481f2a55c8bf', $headers, $body);

        try {
            $res = $client->sendAsync($request, $options)->wait();
            $responseData = json_decode($res->getBody(), true);

            if ($responseData) {
                return [
                    'result' => 'success',
                    'qrCodeUrl' => $responseData['qrCode']['url'],
                    'qrCodeTxId' => $responseData['qrCode']['txId'],
                    'qrCodeEmv' => $responseData['qrCode']['emv'],
                    'linhaDigitavel' => $responseData['linhaDigitavel'],
                    'codigoBarraNumerico' => $responseData['codigoBarraNumerico'],
                    'numeroContratoCobranca' => $responseData['numeroContratoCobranca'],
                    'codigoCliente' => $responseData['codigoCliente'],
                    'numero' => $responseData['numero'],
                ];
            } else {
                return ['result' => 'error', 'message' => 'Erro desconhecido'];
            }
        } catch (\Exception $e) {
            return ['result' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function geraParcela($venda, $parcela) {
        $dadosBoleto = $this->geraBoleto($venda, $parcela);

        $venda = Vendas::find($venda);
        $parcela = Parcela::find($parcela);
        if ($parcela) {
            if ($dadosBoleto['result'] == 'success') {

                $parcela->codigocliente = $dadosBoleto['codigoCliente'];
                $parcela->txid = $dadosBoleto['qrCodeTxId'];
                $parcela->url = $dadosBoleto['qrCodeEmv'];
                $parcela->numerocontratocobranca = $dadosBoleto['numeroContratoCobranca'];
                $parcela->linhadigitavel = $dadosBoleto['linhaDigitavel'];
                $parcela->numero = $dadosBoleto['numero'];
                $parcela->save();

                return true;
            }
        } else {
            return false;
        }
    }

    public function notificaCliente($telefone, $qrcode, $linhadigitavel, $n_parcela) {
        $client = new Client();

        $url = 'https://api.z-api.io/instances/3C44E6488AC460277EC9B2E461726623/token/4845B3E5DE0FB497C11BFF7D/send-link';

        $response = $client->post($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'json' => [
                'phone'     => '55'.$telefone,
                'message'   => "Prezado Cliente, segue os dados para pagamento da parcela N°: ".$n_parcela." \r\n \r\n QR Code: ".$qrcode." ou caso prefira, Boleto: \r\n\r\n",
                'image'     => 'https://grupopositivobrasil.com.br/wp-content/uploads/2022/09/Logo-Branco2.png',
                'linkUrl'   => $linhadigitavel,
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
                    'message'   => $linhadigitavel,
                ],
            ]);

            return true;
        } else {
            return false;
        }
    }

    public function enviaPortalCliente($venda) {
        $venda = Vendas::where('id', $venda)->first();

        $client = new Client();

        $url = 'https://api.z-api.io/instances/3C44E6488AC460277EC9B2E461726623/token/4845B3E5DE0FB497C11BFF7D/send-link';

        $response = $client->post($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'json' => [
                'phone'     => '55'.$venda->telefone,
                'message'   => "Prezado Cliente, segue os dados pra acesso a todos os boletos da Positivo Brasil: \r\n Basta informa seu CPF ou CNPJ para ter acesso! \r\n",
                'image'     => 'https://grupopositivobrasil.com.br/wp-content/uploads/2022/09/Logo-Branco2.png',
                'linkUrl'   => "https://grupopositivoafiliado.com.br/cliente",
                'title'     => 'Acesso Positivo Brasil',
                'linkDescription' => 'Link para Acesso Digital'
            ],
        ]);

        $responseData = json_decode($response->getBody(), true);

        if( isset($responseData['id'])) {
            return true;
        } else {
            return false;
        }
    }

    public function webHookBancoDoBrasil(\Illuminate\Http\Request $request) {
        $data = $request->all();
    
        if (empty($data)) {
            return ['result' => 'error', 'message' => 'JSON não interpretado!'];
        }
    
        foreach ($data as $item) {
            $id = $item['id'];
            $baixa = $item['codigoEstadoBaixaOperacional'];
            $parcela = Parcela::where('numero', $id)->first();
    
            if (!$parcela) {
                return ['result' => 'error', 'message' => 'Cobrança não existe!'];
            }
    
            if ($baixa == 1 || $baixa == 2) {
                $parcela->status = "PAYMENT_CONFIRMED";
                $parcela->save();
    
                $parcelasPendentes = Parcela::where('id_venda', $parcela->id_venda)
                    ->where('status', 'PENDING_PAY')
                    ->where('linhadigitavel', null)
                    ->get();
    
                foreach ($parcelasPendentes as $parcelaPendente) {
                    return $parcelaPendente->email;
                    // $this->geraParcela($parcelaPendente->venda, $parcelaPendente->id);
                }
    
                $this->enviaPortalCliente($parcela->id_venda);
                return ['result' => 'success', 'message' => 'Parcela atualizada!'];
            } else {
                return ['result' => 'success', 'message' => 'Código da Baixa não necessário, nenhuma alteração realizada!'];
            }
        }
    }    
}
