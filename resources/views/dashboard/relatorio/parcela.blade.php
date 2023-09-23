@if (Auth::user()->tipo == 2)
@extends('dashboard/layout')
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
                                                <th>PARCELAS</th>
                                                <th>VALOR</th>
                                                <th>STATUS</th>
                                                <th>VENCIMENTO</th>
                                                <th class="text-center">Opções</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($parcela as $parcelas)
                                            <tr>
                                                <td>{{ $parcelas->venda_id }}</td>
                                                <td>{{ $parcelas->cpf }}</td>
                                                <td>{{ $parcelas->numero_parcela }}</td>
                                                <td>{{ $parcelas->valor }}</td>
                                                <td>{{ $parcelas->status }}</td>
                                                <td>{{ \Carbon\Carbon::parse($parcelas->vencimento)->format('d/m/Y') }}</td>
                                                <td>
                                                    <form action="{{ route('relatorioAction') }}" method="POST" onsubmit="return confirm('Tem certeza que deseja Atualizar o status do CPF : {{ $parcelas->cpf }}?')">
                                                        @csrf
                                                        <input type="hidden" name="parcela_id" value="{{ $parcelas->id }}">
                                                        <button type="submit" class="btn btn-outline-primary" style="border: 2px solid black;">
                                                            <i><img src="{{ asset('icone/relatorio.png') }}" alt="icone" style="max-width:20px;"></i>
                                                        </button>
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
            <!-- Fim Vendas -->
        </div>

    </div>

   

    @endsection
    @endif