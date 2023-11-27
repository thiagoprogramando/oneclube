@extends('dashboard/layout')
@section('conteudo')
    <div class="container-fluid">

        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Usuários</h1>
        </div>

        <div class="row">
            @if (isset($msg))
                {{ $msg }}
            @endif
            <div class="col-xl-12 col-md-12 mb-2">
                <button type="button" data-toggle="modal" data-target="#logoutModal" class="btn btn-outline-primary">Cadastrar</button>
                @if ($errors->any())
                    <div class="mt-2" style="background-color: rgb(136, 16, 20); color:white; text-align: center; border-radius:5px;">
                        <ul class="alert alert-error">
                            @foreach ($errors->all() as $error)
                                {{ $error }}
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
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
                                                <th>Nome</th>
                                                <th>Email</th>
                                                <th>CPF</th>
                                                <th>Tipo</th>
                                                <th class="text-center">Mudar para:</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($users as $key => $user)
                                                <tr>
                                                    <td>{{ $user->id }}</td>
                                                    <td>{{ $user->nome }}</td>
                                                    <td>{{ $user->email }}</td>
                                                    <td>{{ $user->cpf }}</td>
                                                    <td>
                                                        @switch($user->tipo)
                                                            @case(1)
                                                                Padrão
                                                            @break

                                                            @case(2)
                                                                Administrador
                                                            @break

                                                            @default
                                                                Tipo Desconhecido
                                                        @endswitch
                                                    </td>
                                                    <td class="text-center">
                                                        <form method="POST" action="{{ route('relatorioUsuarios') }}">
                                                            <input type="hidden" value={{ csrf_token() }} name="_token">
                                                            <input type="hidden" value="{{ $user->id }}"
                                                                name="id_usuario">
                                                            @if ($user->tipo == 1)
                                                                <input type="hidden" value="2" name="tipo">
                                                                <button type="submit" class="btn btn-outline-primary">
                                                                    Administrador </button>
                                                            @else
                                                                <input type="hidden" value="1" name="tipo">
                                                                <button type="submit" class="btn btn-outline-primary">
                                                                    Padrão </button>
                                                            @endif
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

    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('register_action') }}" method="POST" class="user">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Cadastro de Usuários</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" value={{ csrf_token() }} name="_token">
                        <div class="form-group">
                            <input type="text" class="form-control" name="name" placeholder="Nome">
                        </div>
                        <div class="form-group">
                            <input type="number" class="form-control" name="cpf" placeholder="CPF">
                        </div>
                        <div class="form-group">
                            <input type="email" class="form-control" name="email" placeholder="Email">
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-control" name="password" placeholder="Senha">
                        </div>
                        <div class="form-group">
                            <select name="tipo" class="form-control">
                                <option value="1" selected>Tipo</option>
                                <option value="1">Padrão</option>
                                <option value="2">Administrador</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-danger" type="button" data-dismiss="modal">Cancelar</button>
                        <button class="btn btn-success" type="submit">Cadastrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
