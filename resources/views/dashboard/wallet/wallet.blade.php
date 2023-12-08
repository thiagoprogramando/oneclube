@extends('dashboard.layout')
@section('conteudo')
    <div class="container-fluid">

        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Carteira Digital</h1>
            <a href="#" data-toggle="modal" data-target="#modalSaque" class="d-sm-inline-block btn btn-primary shadow-sm">
                <i class="fas fa-comment-dollar text-white-50"></i> Saque
            </a>
        </div>

        <div class="row">
            <div class="col-xl-6 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-green text-uppercase mb-1">
                                    Saldo (Disponível para saque)</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">R$ {{ $balance }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Saldo (Recebíveis)</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">R$ {{ $statistics }}</div>
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
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1"> Últimas Transações </div>
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
                                                <th class="text-center">Situação Ficha</th>
                                                <th class="text-center">1° Parcela Paga</th>
                                                <th>Data</th>
                                                <th class="text-center">Opções</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- @foreach ($sales as $key => $sale)
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
                                                        @switch($sale->status_ficha)
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
                                                    <td> {{ \Carbon\Carbon::parse($sale->created_at)->format('d/m/Y') }} </td>
                                                    <td class="text-center">
                                                        <a class="btn btn-outline-primary" href="{{ route('invoices', ["id"=> $sale->id]) }}" target="_blank"> <i class="fa fa-credit-card"></i> </a>
                                                    </td>
                                                </tr>
                                            @endforeach --}}
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

    <div class="modal fade" id="modalSaque" tabindex="-1" role="dialog" aria-labelledby="modalSaque" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalSaque">Deseja realizar um Saque?</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @csrf
                        <div class="mb-3">
                            <select name="type" class="form-control">
                                <option value="" selected>Tipo Chave</option>
                                <option value="CPF">CPF</option>
                                <option value="CNPJ">CNPJ</option>
                                <option value="EMAIL">EMAIL</option>
                                <option value="PHONE">TELEFONE</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <input type="text" class="form-control" name="key_pix" placeholder="Chave Pix:" autofocus required/>
                        </div>
                        <div class="mb-3">
                            <input type="text" class="form-control" name="value" placeholder="Valor:" required/>
                        </div>
                        <div class="mb-3">
                            <input type="password" class="form-control" name="password" placeholder="Confirme sua senha:" required/>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>
                        <button class="btn btn-primary" type="submit">Realizar Saque</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
