@extends('cliente.layout')
@section('conteudo')
    <div class="container-fluid">

        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Faturas</h1>
        </div>

        <div class="row">
            <div class="col-xl-12 col-md-12 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1"> Faturas </div>
                            </div>
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="tabela" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Título</th>
                                                <th>Descrição</th>
                                                <th>Situação</th>
                                                <th class="text-center">Vencimento</th>
                                                <th class="text-center">Opções</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($invoices as $key => $invoice)
                                                <tr>
                                                    <td>{{ $invoice->id }}</td>
                                                    <td>{{ $invoice->name }}</td>
                                                    <td>{{ $invoice->description }}</td>
                                                    <td>
                                                        @switch($invoice->status)
                                                            @case('PAYMENT_CONFIRMED')
                                                                Aprovado
                                                                @break
                                                            @default
                                                                Pendente
                                                                @break
                                                        @endswitch
                                                    </td>
                                                    <td class="text-center">{{ \Carbon\Carbon::parse($invoice->dueDate)->format('d/m/Y') }}</td>
                                                    <td class="text-center">
                                                        <a class="btn btn-outline-primary" href="{{ $invoice->url }}" target="_blank"> <i class="fa fa-credit-card"></i> </a>
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
