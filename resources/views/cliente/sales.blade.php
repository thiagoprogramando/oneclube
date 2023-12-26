@extends('cliente.layout')
@section('conteudo')
    <div class="container-fluid">

        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Contratos</h1>
        </div>

        <div class="row">
            <div class="col-xl-12 col-md-12 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1"> Contratos </div>
                            </div>
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="tabela" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Documento</th>
                                                <th>Produto</th>
                                                <th>Situação Contrato</th>
                                                <th class="text-center">Situação Produto</th>
                                                <th class="text-center">Opções</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($sales as $key => $sale)
                                                <tr>
                                                    <td>{{ $sale->id }}</td>
                                                    <td>{{ $sale->cpfcnpj }}</td>
                                                    <td>
                                                        @switch($sale->id_produto)
                                                            @case(1)
                                                                Limpa Nome
                                                                @break
                                                            @default
                                                                Produto Desconhecido
                                                                @break
                                                        @endswitch
                                                    </td>
                                                    <td>
                                                        @switch($sale->status_produto)
                                                            @case('doc_signed')
                                                                Assinado
                                                                @break
                                                            @case('null')
                                                                <a target="_blank" href="{{ $sale->sign_url_contrato }}">Aguardando Assinatura</a>
                                                                @break
                                                            @default
                                                                <a target="_blank" href="{{ $sale->sign_url_contrato }}">Aguardando Assinatura</a>
                                                                @break
                                                        @endswitch
                                                    </td>
                                                    <td class="text-center">
                                                        @switch($sale->tag)
                                                            @case(1)
                                                                Aguardando Conclusão de Documentos
                                                            @break

                                                            @case(2)
                                                                Aguardando Conclusão de Honorários
                                                            @break

                                                            @case(3)
                                                                Processo em fila
                                                            @break

                                                            @case(4)
                                                                Processo iniciado
                                                            @break

                                                            @case(5)
                                                                Processo em andamento
                                                            @break

                                                            @case(6)
                                                                Processo concluído
                                                            @break

                                                            @case(7)
                                                                Duplicado
                                                            @break

                                                            @case(8)
                                                                Lixeira
                                                            @break

                                                            @default
                                                                Pendente de Dados
                                                            @break
                                                        @endswitch
                                                    </td>
                                                    <td class="text-center">
                                                        <a class="btn btn-outline-primary" href="{{ route('faturasCliente', ["id"=> $sale->id]) }}"> <i class="fa fa-credit-card"></i> </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
