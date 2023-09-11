@extends('dashboard/layout')
    @section('conteudo')
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Usuários</h1>
        </div>

        <!-- Minhas Vendas -->
        <div class="row">
            @if(isset($msg))
                {{ $msg }}
            @endif
            <div class="col-xl-12 col-md-12 mb-4">
                <div class="card border-left-dark shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Nome</th>
                                                <th>Código/Cupom</th>
                                                <th>Email</th>
                                                <th>CPF</th>
                                                <th class="text-center">Alterar para:</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($users as $key =>$user)
                                            <tr>
                                                <td>{{ $user->id }}</td>
                                                <td>{{ $user->nome }}</td>
                                                <td>{{ $user->cupom }}</td>
                                                <td>{{ $user->email }}</td>
                                                <td>{{ $user->cpf }}</td>
                                                <td class="text-center">
                                                    <form method="POST" action="{{ route('relatorioUsuarios') }}">
                                                        <input type="hidden" value={{  csrf_token() }} name="_token">
                                                        <input type="hidden" value="{{  $user->id }}" name="id_usuario">
                                                        @if($user->tipo == 1) <input type="hidden" value="2" name="tipo"> @else <input type="hidden" value="1" name="tipo">  @endif
                                                        <button class="btn btn-outline-primary"> @if($user->tipo == 1) Administrador @else Comum  @endif</button>
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
