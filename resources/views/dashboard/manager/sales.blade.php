@extends('dashboard.layout')
@section('conteudo')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Vendas</h1>
        </div>

        <div class="row">
            <div class="col-xl-12 col-md-12 mb-4">
                <div class="card border-left-dark shadow h-100 py-2">
                    <div class="card-body">

                        <div class="row">
                            <div class="col-12">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    <button class="btn btn-outline-success w-25" type="button" data-toggle="modal" data-target="#exampleModal">Filtros</button>
                                    <button class="btn btn-outline-primary w-25" type="button" id="exportar">Excel</button>

                                    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <form method="POST" action="{{ route('filterSaleManager') }}">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLabel">Filtros:</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        @csrf
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="form-group">
                                                                    <select class="form-control" name="produto">
                                                                        <option value="ALL">Todos os Produtos</option>
                                                                        <option value="1">Limpa Nome</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-12">
                                                                <div class="form-group">
                                                                    <select class="form-control" name="status">
                                                                        <option value="ALL">Todos os Status</option>
                                                                        <option value="PAYMENT_CONFIRMED">Aprovados</option>
                                                                        <option value="PENDING_PAY">Pendentes de Pagamento
                                                                        </option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-12">
                                                                <div class="form-group">
                                                                    <select class="form-control" name="usuario">
                                                                        <option value="ALL">Todos os Usuários</option>
                                                                        @foreach ($users as $key => $user)
                                                                            <option value="{{ $user->id }}">
                                                                                {{ $user->nome }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <input type="date" class="form-control"
                                                                        name="data_inicio" placeholder="Data inicial">
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <input type="date" class="form-control"
                                                                        name="data_fim" placeholder="Data Final">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-danger"
                                                            data-dismiss="modal">Fechar</button>
                                                        <button type="submit" class="btn btn-success">Filtrar</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mt-3">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="tabela" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Cliente</th>
                                                <th>Produto</th>
                                                <th>Status</th>
                                                <th>Situação (Tag)</th>
                                                <th>Data</th>
                                                <th class="text-center">Opções</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($sales as $key => $sale)
                                                <tr>
                                                    <td>{{ $sale->id }}</td>
                                                    <td>{{ $sale->name }}</td>
                                                    <td>
                                                        @switch($sale->id_produto)
                                                            @case(1)
                                                                Limpa Nome
                                                            @break

                                                            @default
                                                                Produto Desconhecido
                                                        @endswitch
                                                    </td>
                                                    <td>
                                                        @switch($sale->status_pay)
                                                            @case('PAYMENT_CONFIRMED')
                                                                Aprovado
                                                            @break

                                                            @case('PENDING_PAY')
                                                                Aguardando Pagamento
                                                            @break

                                                            @default
                                                                Aguardando Pagamento
                                                        @endswitch
                                                    </td>
                                                    <td>
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

                                                            @default
                                                                Pendente de Dados
                                                            @break
                                                        @endswitch
                                                    </td>
                                                    <td>{{ \Carbon\Carbon::parse($sale->created_at)->format('d/m/Y') }}</td>
                                                    <td class="text-center">
                                                        <a class="btn btn-outline-success" href="{{ $sale->sign_url_contrato }}" target="_blank"><i class="fa fa-file"></i></a>
                                                        <a class="btn btn-outline-primary" href="{{ route('invoices', ['id' => $sale->id]) }}" target="_blank"> <i class="fa fa-credit-card"></i> </a>
                                                        <a class="btn btn-outline-dark" href="#" data-toggle="modal" data-target="#modalSale{{ $sale->id }}"><i class="far fa-edit"></i></a>
                                                    </td>
                                                </tr>

                                                <div class="modal fade" id="modalSale{{ $sale->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <form action="{{ route('updateSale') }}" method="POST">
                                                            @csrf
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="exampleModalLabel"> Atribuições da venda</h5>
                                                                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">×</span> </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="row">
                                                                        <input type="hidden" name="id" value="{{ $sale->id }}">
                                                                        <div class="form-group col-sm-12 col-lg-12">
                                                                            <select name="tag" class="form-control">
                                                                                <option value="1">Tags</option>
                                                                                <option value="1">Aguardando Conclusão de Documentos</option>
                                                                                <option value="2">Aguardando Conclusão de Honorários</option>
                                                                                <option value="3">Processo em fila </option>
                                                                                <option value="4">Processo iniciado </option>
                                                                                <option value="5">Processo em andamento</option>
                                                                                <option value="6">Processo concluído </option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button class="btn btn-danger" type="button" data-dismiss="modal">Cancelar</button>
                                                                    <button class="btn btn-success" type="submit">Atualizar</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
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
