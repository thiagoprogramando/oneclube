@extends('dashboard.layout')
@section('conteudo')
    <div class="container-fluid">

        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Painel Principal</h1>
        </div>

        <div class="row">
            <div class="col-xl-4 col-md-4 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-green text-uppercase mb-1">
                                    Saldo (Disponível para saque)</div>
                                <div class="h5 mb-0 font-weight-bold text-green">R$ {{ number_format($balance, 2, ',', '.') }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-4 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Saldo (Recebíveis)</div>
                                <div class="h5 mb-0 font-weight-bold text-warning">R$ {{ number_format($statistics, 2, ',', '.') }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-4 mb-4">
                <div class="card border-left-dark shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                    Saldo (Acumulado)</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">R$ {{ number_format($accumulated, 2, ',', '.') }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12 col-md-12 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1"> Últimas Vendas </div>
                            </div>
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="tabela" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Cliente</th>
                                                <th>Produto</th>
                                                <th class="text-center">Situação Contrato</th>
                                                <th class="text-center">1° Parcela Paga</th>
                                                <th class="text-center">Data</th>
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
                                                                @break
                                                        @endswitch
                                                    </td>
                                                    <td class="text-center">
                                                        @switch($sale->status_produto)
                                                            @case('doc_signed')
                                                                Assinado
                                                                @break
                                                            @case('null')
                                                                Aguardando Assinatura
                                                                @break
                                                            @default
                                                                Aguardando Assinatura
                                                                @break
                                                        @endswitch
                                                    </td>
                                                    <td class="text-center">
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
                                                    <td class="text-center"> {{ \Carbon\Carbon::parse($sale->created_at)->format('d/m/Y') }} </td>
                                                    <td class="text-center">
                                                        <a class="btn btn-outline-primary" href="{{ route('invoices', ["id"=> $sale->id]) }}" target="_blank"> <i class="fa fa-credit-card"></i> </a>
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
