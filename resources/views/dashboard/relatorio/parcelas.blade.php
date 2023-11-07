@extends('dashboard/layout')
@section('conteudo')
    <div class="container-fluid">

        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Parcelas</h1>
        </div>

        <div class="row">
            <div class="col-xl-12 col-md-12 mb-4">
                <div class="card border-left-dark shadow h-100 py-2">
                    <div class="card-body">
                        <button class="btn btn-outline-primary w-25 mb-3" type="button" id="exportar">Excel</button>

                        <div class="table-responsive">
                            <table class="table table-striped" id="tabela" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>N°</th>
                                        <th class="d-none">ID Venda</th>
                                        <th class="d-none">Código Cliente</th>
                                        <th class="d-none">Número Contrato Cobrança</th>
                                        <th>Nosso Número</th>
                                        <th>Vencimento</th>
                                        <th>Valor</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($parcelas as $parcela)
                                        <tr>
                                            <td>{{ $parcela->n_parcela }}</td>
                                            <td class="d-none">{{ $parcela->id_venda }}</td>
                                            <td class="d-none">{{ $parcela->codigocliente }}</td>
                                            <td class="d-none">{{ $parcela->numerocontratocobranca }}</td>
                                            <td>{{ $parcela->numero }}</td>
                                            <td>{{ \Carbon\Carbon::parse($parcela->vencimento)->format('d/m/Y') }}</td>
                                            <td>{{ 'R$ ' . number_format($parcela->valor, 2, ',', '.') }}</td>
                                            <td>@if($parcela->status == 'PENDING_PAY') Aguardando Pagamento @else Aprovado @endif</td>
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
@endsection
