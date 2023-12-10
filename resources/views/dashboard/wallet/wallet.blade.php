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
                                <div class="h5 mb-0 font-weight-bold text-green">R$ {{ number_format($balance, 2, ',', '.') }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6 col-md-6 mb-4">
                <div class="card border-left-dark shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                    Saldo (Recebíveis)</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">R$ {{ number_format($statistics, 2, ',', '.') }}</div>
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
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1"> Transações </div>
                            </div>
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-hover" id="tabela" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Tipo</th>
                                                <th>Descrição</th>
                                                <th class="text-justify">Valor</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($extracts as $extract)
                                                <tr>
                                                    <td>{{ $extract['id'] }}</td>
                                                    <td>
                                                        @if($extract['value'] < 0)
                                                            Saída
                                                        @else
                                                            Entrada
                                                        @endif
                                                    </td>
                                                    <td>{{ $extract['description'] }}</td>
                                                    <td class="text-justify">R$ {{ number_format($extract['value'], 2, ',', '.') }}</td>
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

    <div class="modal fade" id="modalSaque" tabindex="-1" role="dialog" aria-labelledby="modalSaque" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('withdraw') }}" method="POST">
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
