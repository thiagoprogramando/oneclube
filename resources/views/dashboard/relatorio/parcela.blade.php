@extends('dashboard.layout')
    @section('conteudo')
    <div class="container-fluid">
        @if(Session::has('erro'))
        <div class="alert alert-danger">
            {{ Session::get('erro') }}
        </div>
    @endif

    @if(Session::has('sucesso'))
        <div class="alert alert-success">
            {{ Session::get('sucesso') }}
        </div>
    @endif

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Vendas</h1>
        </div>

        <!-- Minhas Vendas -->
        <div class="row">
            <div class="col-xl-12 col-md-12 mb-4">
                <div class="card border-left-dark shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">

                            </div>
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="tabela" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>CPF</th>
                                                <th>N°</th>
                                                <th>VALOR</th>
                                                <th>STATUS</th>
                                                <th>VENCIMENTO</th>
                                                <th class="text-center">Opções</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($parcela as $parcelas)
                                            <tr>
                                                <td>{{ $parcelas->id }}</td>
                                                <td>{{ $parcelas->cpf }}</td>
                                                <td>{{ $parcelas->numero_parcela }}</td>
                                                <td>{{ $parcelas->valor }}</td>
                                                <td>
                                                    @switch($parcelas->status)
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
                                                <td>{{ \Carbon\Carbon::parse($parcelas->vencimento)->format('d/m/Y') }}</td>
                                                <td class="text-center">
                                                    <form action="{{ route('relatorioParcelasAdminn') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="parcela_id" value="{{ $parcelas->id }}">
                                                        <button type="submit" class="btn btn-outline-info">Alterar Status</button>
                                                    </form>
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
