@extends('dashboard/layout')
@section('conteudo')
    <div class="container-fluid">

        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Painel Principal</h1>
        </div>

        <div class="row">
            <div class="col-xl-6 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1"> Link de vendas R$ 1.997,00 </div>
                                <div class="row no-gutters align-items-center">
                                    <div class="col-auto">
                                        <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                            <button class="btn btn-outline-primary" data-link="{{ url('/limpanome/' . auth()->id() . '/1997') }}" onclick="copiaLink(this)"><i class="fas fa-copy"></i></button>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <a style="font-size: 15px;" class="text-blue" id="copyLink" href="{{ url('/limpanome/' . auth()->id() . '/1997') }}" target="_blank">{{ url('/limpanome/' . auth()->id() . '/1997') }}</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-check fa-2x text-blue"></i>
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
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1"> Link de vendas R$ 1.497,00 </div>
                                <div class="row no-gutters align-items-center">
                                    <div class="col-auto">
                                        <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                            <button class="btn btn-outline-primary" data-link="{{ url('/limpanome/' . auth()->id() . '/1497') }}" onclick="copiaLink(this)"><i class="fas fa-copy"></i></button>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <a style="font-size: 15px;" class="text-blue" id="copyLink" href="{{ url('/limpanome/' . auth()->id() . '/1497') }}" target="_blank">{{ url('/limpanome/' . auth()->id() . '/1497') }}</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-check fa-2x text-blue"></i>
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
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1"> Link de vendas R$ 1.197,00 </div>
                                <div class="row no-gutters align-items-center">
                                    <div class="col-auto">
                                        <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                            <button class="btn btn-outline-primary" data-link="{{ url('/limpanome/' . auth()->id() . '/1197') }}" onclick="copiaLink(this)"><i class="fas fa-copy"></i></button>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <a style="font-size: 15px;" class="text-blue" id="copyLink" href="{{ url('/limpanome/' . auth()->id() . '/1197') }}" target="_blank">{{ url('/limpanome/' . auth()->id() . '/1197') }}</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-check fa-2x text-blue"></i>
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
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1"> Link de vendas R$ 997,00 </div>
                                <div class="row no-gutters align-items-center">
                                    <div class="col-auto">
                                        <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                            <button class="btn btn-outline-primary" data-link="{{ url('/limpanome/' . auth()->id() . '/997') }}" onclick="copiaLink(this)"><i class="fas fa-copy"></i></button>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <a style="font-size: 15px;" class="text-blue" id="copyLink" href="{{ url('/limpanome/' . auth()->id() . '/997') }}" target="_blank">{{ url('/limpanome/' . auth()->id() . '/997') }}</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-check fa-2x text-blue"></i>
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
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Últimas Vendas
                                    <hr>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="tabela" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Cliente</th>
                                                <th>Produto</th>
                                                <th>Status</th>
                                                <th>Data venda</th>
                                                <th class="text-center">Opções</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($vendas as $key => $venda)
                                                <tr>
                                                    <td>{{ $venda->id }}</td>
                                                    <td>{{ $venda->nome }}</td>
                                                    <td>
                                                        @switch($venda->id_produto)
                                                            @case(1)
                                                                Limpa Nome
                                                            @break
                                                            @default
                                                                Produto Desconhecido
                                                        @endswitch
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
                                                    <td> {{ \Carbon\Carbon::parse($venda->created_at)->format('d/m/Y') }} </td>
                                                    <td class="text-center">
                                                        <a class="btn btn-outline-success" href="{{ $venda->file }}" target="_blank"><i class="fa fa-file"></i></a>
                                                        <?php $id_pay = str_replace('pay_', '', $venda->id_pay); ?>
                                                        <a class="btn btn-outline-primary" href="https://www.asaas.com/i/{{ $id_pay }}" target="_blank"> <i class="fa fa-credit-card"></i> </a>
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
