@extends('dashboard.layout')
@section('conteudo')
    <div class="container-fluid">

        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        </div>

        <div class="row">
            <div class="col-xl-12 col-md-12 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1"> Limpa Nome</div>
                                <div class="row no-gutters align-items-center">
                                    <div class="col-auto">
                                        <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                            <button class="btn btn-outline-success" onclick="copyToClipboard()"><i class="fas fa-copy"></i></button>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <a class="text-success" id="copyLink" href="{{ url('/limpanome/' . auth()->id() .'/'. Auth::user()->cupom)  }}" target="_blank">{{ url('/limpanome/' . auth()->id()) }}</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-check fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12 col-md-12 mb-4">
                <div class="card border-left-dark shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 mb-4">
                                <div class="text-xs font-weight-bold text-success text-uppercase">
                                    Últimas Vendas
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>Cliente</th>
                                                <th>CPF/CNPJ</th>
                                                <th>Contrato</th>
                                                <th>Data venda</th>
                                                <th class="text-center">Opções</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($vendas as $key => $venda)
                                                <tr>
                                                    <td>{{ $venda->nome }}</td>
                                                    <td>{{ $venda->cpf }}</td>
                                                    <td>{{ $venda->status_contrato }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($venda->created_at)->format('d/m/Y') }} </td>
                                                    <td class="text-center">
                                                        <a class="btn btn-outline-dark" href="{{ asset('contratos/2' . $venda->cpf . '.pdf') }}" download><i class="fa fa-file"></i></a>
                                                        <a class="btn btn-outline-success" href="{{ route('parcelas', ['id' => $venda->id]) }}"><i class="fa fa-credit-card"></i></a>
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
