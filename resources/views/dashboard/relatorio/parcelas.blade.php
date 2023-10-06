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
                        <div class="table-responsive">
                            <table class="table table-striped" id="tabela" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>N°</th>
                                        <th>Vencimento</th>
                                        <th>Valor</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($parcelas as $parcela)
                                        <tr>
                                            <td>{{ $parcela->id }}</td>
                                            <td>{{ $parcela->n_parcela }}</td>
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
