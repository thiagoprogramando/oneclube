@extends('dashboard.layout')
    @section('conteudo')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Faturas/Cobranças</h1>
        </div>

        <div class="row">
            <div class="col-xl-12 col-md-12 mb-2">
                <div class="card border-left-dark shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Fatura</th>
                                                <th>Descrição</th>
                                                <th>Situação</th>
                                                <th>Valor</th>
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
                                                        @if($invoice->status == 'PENDING_PAY')
                                                            <span class="badge badge-danger">Pendente</span>
                                                        @else
                                                            <span class="badge badge-success">Aprovado</span>
                                                        @endif
                                                    </td>
                                                    <td>R$ {{ number_format($invoice->value, 2, ',', '.') }}</td>
                                                    <td class="text-center">
                                                        @if($invoice->status == 'PENDING_PAY' && $invoice->token == null)
                                                            <form action="{{ route('invoiceCreate') }}" method="POST">
                                                                @csrf
                                                                <input type="hidden" name="id" value="{{ $invoice->id }}">
                                                                <input type="hidden" name="type" value="{{ $invoice->type }}">
                                                                <input type="hidden" name="value" value="{{ $invoice->value }}">
                                                                <input type="hidden" name="description" value="{{ $invoice->description }}">
                                                                <input type="hidden" name="billingType" value="PIX">
                                                                <button type="submit" class="btn btn-outline-success"><i class="fas fa-credit-card"></i></button>
                                                            </form>
                                                        @else
                                                            <a target="_blank" href="{{ $invoice->url }}" class="btn btn-outline-success"><i class="fas fa-receipt"></i></a>
                                                        @endif
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