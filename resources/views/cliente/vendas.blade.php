@extends('cliente.layout')
@section('conteudo')

    <div class="container-fluid">

        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        </div>

        <div class="row">
            <div class="col-xl-12 col-md-12 mb-4">
                <div class="card border-left-dark shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 mb-4">
                                <div class="text-xs font-weight-bold text-success text-uppercase">
                                    Meus Contratos
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
                                                        <a class="btn btn-outline-success" href="{{ route('parcelaCliente', ['id' => $venda->id]) }}"><i class="fa fa-credit-card"></i></a>
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
