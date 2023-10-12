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

    public function geraToken()
    {
        $client = new Client();

        $data = [
            'form_params' => [
                'grant_type' => 'client_credentials',
                'scope' => 'cobrancas.boletos-info, cobrancas.boletos-requisicao',
            ],
            'headers' => [
                'Authorization' => 'Basic ZXlKcFpDSTZJbUV4T0dZMk1XTXROMlEwWlMwME9EQTRMVGdpTENKamIyUnBaMjlRZFdKc2FXTmhaRzl5SWpvd0xDSmpiMlJwWjI5VGIyWjBkMkZ5WlNJNk5UVXpNak1zSW5ObGNYVmxibU5wWVd4SmJuTjBZV3hoWTJGdklqb3lmUTpleUpwWkNJNklqRWlMQ0pqYjJScFoyOVFkV0pzYVdOaFpHOXlJam93TENKamIyUnBaMjlUYjJaMGQyRnlaU0k2TlRVek1qTXNJbk5sY1hWbGJtTnBZV3hKYm5OMFlXeGhZMkZ2SWpveUxDSnpaWEYxWlc1amFXRnNRM0psWkdWdVkybGhiQ0k2TVN3aVlXMWlhV1Z1ZEdVaU9pSndjbTlrZFdOaGJ5SXNJbWxoZENJNk1UWTVOakF4TXpnMU5qUTFNWDA=',
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

    public function geraBoleto($venda)
    {
        $accessToken = $this->geraToken();

        $venda = Vendas::find($venda);
        $tipoInscricao = (strlen($venda->cpf) > 11) ? '2' : '1';
        $parcela = Parcela::where('id_venda', $venda->id)->where('status', 'PENDING_PAY')->first();
        $dataVencimento = date('d.m.Y', strtotime($parcela->vencimento));

        $client = new Client();

        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $accessToken
        ];

        $body = '{
            "numeroConvenio": '.env('NUMEROCONVENIO').',
            "dataVencimento": "'.$dataVencimento.'",
            "valorOriginal": "'.$parcela->valor.'",
            "numeroCarteira": '.env('NUMEROCARTEIRA').',
            "numeroVariacaoCarteira": '.env('NUMEROVARIACAOCARTEIRA').',
            "codigoModalidade": "1",
            "dataEmissao": "'.date('d.m.Y').'",
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
            "numeroTituloCliente": "'.Nossonumero::gerarNumeroTituloCliente().'",
            "pagador": {
                "tipoInscricao": "'.$tipoInscricao.'",
                "numeroInscricao": "'.$venda->cpf.'",
                "nome": "'.$venda->nome.'",
                "endereco": "'.$venda->endereco.'",
                "cep": "'.$venda->cep.'",
                "cidade": "'.$venda->cidade.'",
                "bairro": "'.$venda->bairro.'",
                "uf": "'.$venda->uf.'",
                "telefone": "'.$venda->telefone.'"
            },
            "indicadorPix": "S"
        }';

        $options = [
            'headers' => $headers,
            'body' => $body,
            'verify' => false,
        ];

        $request = new Request('POST', env('API_URL_BB_COBRANCA').'v2/boletos?gw-dev-app-key=eb3f8901f8222d55f78f481f2a55c8bf', $headers, $body);

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

    public function webHookBancoDoBrasil(\Illuminate\Http\Request $request) {
        $data = $request->all();

        if (!empty($data)) {
            foreach ($data as $item) {
                $id = $item['id'];
                $baixa = $item['codigoEstadoBaixaOperacional'];
                $parcela = Parcela::where('numero', $id)->first();
                if($parcela) {
                    if($baixa == 1 || $baixa == 2) {
                        $parcela->status = "PAYMENT_CONFIRMED";
                        $parcela->save();

                        return ['result' => 'success', 'message' => 'Parcela atualizada!'];
                    } else {
                        return ['result' => 'success', 'message' => 'Código da Baixa não necessário, nenhuma alteração realizada!'];
                    }
                }
                return ['result' => 'success', 'message' => 'Cobrança não existe!'];
            }
        } else {
            return ['result' => 'error', 'message' => 'JSON não interpretado!'];
        }
    }

}
