@extends('dashboard/layout')
    @section('conteudo')
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Minhas Vendas</h1>
        </div>

        <!-- Minhas Vendas -->
        <div class="row">
            <div class="col-xl-12 col-md-12 mb-4">
                <div class="card border-left-dark shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1"> 
                                    <button class="btn btn-outline-success" type="button" data-toggle="modal" data-target="#exampleModal">Filtros</button>
                                    
                                    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <form method="POST" action="{{ route('vendas') }}">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLabel">Filtros:</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="hidden" value={{  csrf_token() }} name="_token">
                                                        <input type="hidden" value="{{ $produto }}" name="id">
                                                        <div class="row">
                                                            <dil class="col-6">
                                                                <div class="form-group">
                                                                    <input type="date" class="form-control" name="data_inicio" placeholder="Data inicial">
                                                                </div>
                                                            </dil>
                                                            <dil class="col-6">
                                                                <div class="form-group">
                                                                    <input type="date" class="form-control" name="data_fim" placeholder="Data Final">
                                                                </div>
                                                            </dil>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                                                        <button type="submit" class="btn btn-success">Filtrar</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <hr> 
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Cliente</th>
                                                <th>Produto</th>
                                                <th>Contrato</th>
                                                <th>Status</th>
                                                <th>Data venda</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($vendas as $key =>$venda)
                                            <tr>
                                                <td>{{ $venda->id }}</td>
                                                <td>{{ $venda->nome }}</td>
                                                <td>
                                                    @switch($venda->id_produto)
                                                        @case(1)
                                                            One Beauty
                                                            @break
                                                        @case(2)
                                                            Limpa Nome
                                                            @break
                                                        @case(3)
                                                            One Motos
                                                            @break
                                                        @case(4)
                                                            One Serviços
                                                            @break
                                                        @default
                                                            Produto Desconhecido
                                                    @endswitch
                                                </td>
                                                <td>
                                                    <a class="btn btn-outline-success" href="{{ asset('contratos/' . $venda->cpf . '.pdf') }}" download>Contrato</a>
                                                </td>
                                                <td>
                                                    @switch($venda->status_pay)
                                                        @case('PAYMENT_CONFIRMED')
                                                            Aprovado
                                                            @break
                                                        @case('PENDING_PAY')
                                                            Aguardando Pagamento
                                                            @break
                                                        @default
                                                            Status Desconhecido
                                                    @endswitch
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($venda->created_at)->format('d/m/Y') }}</td>
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
            <!-- Fim Vendas -->
        </div>

    </div>

    @endsection
