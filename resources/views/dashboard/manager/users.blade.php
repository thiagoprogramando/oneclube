@extends('dashboard.layout')
@section('conteudo')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Listagem de Usuários</h1>
        </div>

        <div class="row">
            <div class="col-xl-12 col-md-12 mb-2">
                <button type="button" data-toggle="modal" data-target="#modalCreateUser" class="btn btn-outline-primary">Cadastrar</button>
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
                                                <th>Tipo</th>
                                                <th>Situação</th>
                                                @if (Auth::user()->type == 1)
                                                    <th class="text-center">Wallet</th>
                                                    <th class="text-center">ApiKey</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($users as $key => $user)
                                                <tr>
                                                    <td>{{ $user->id }}</td>
                                                    <td>{{ $user->name }}</td>
                                                    <td><span class="badge badge-dark">{{ $user->type_user}}</span></td>
                                                    <td><span class="badge badge-success">{{ $user->status_user}}</span></td>
                                                    @if (Auth::user()->type == 1)
                                                        <td class="text-center"><button class="btn btn-outline-success" data-copia="{{ $user->walletId }}"><i class="fas fa-wallet"></i></button></td>
                                                        <td class="text-center"><button class="btn btn-outline-success" data-copia="{{ $user->apiKey }}"><i class="fas fa-key"></i></button></td>
                                                    @endif
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

    <div class="modal fade" id="modalCreateUser" tabindex="-1" role="dialog" aria-labelledby="labelCreateUser" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('createUser') }}" method="POST" class="user">
                    <div class="modal-header">
                        <h5 class="modal-title" id="labelCreateUser">Cadastro de Usuários</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">×</span> </button>
                    </div>
                    <div class="modal-body">
                        @csrf
                        <div class="form-group">
                            <input type="text" class="form-control" name="name" placeholder="Nome" required>
                        </div>
                        <div class="row">
                            <div class="col-6 form-group">
                                <input type="text" class="form-control" name="cpfcnpj" placeholder="CPF ou CNPJ" oninput="mascaraCpfCnpj(this)" required>
                            </div>
                            <div class="col-6 form-group">
                                <input type="text" class="form-control" name="birthDate" placeholder="Data de Nascimento" oninput="mascaraData(this)" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6 form-group">
                                <input type="email" class="form-control" name="email" placeholder="Email" required>
                            </div>
                            <div class="col-6 form-group">
                                <input type="text" class="form-control" name="mobilePhone" placeholder="Celular (Whatsapp)" oninput="mascaraTelefone(this)" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6 form-group">
                                <input type="number" class="form-control" name="postalCode" placeholder="CEP" onblur="consultaCEP()" required>
                            </div>
                            <div class="col-6 form-group">
                                <input type="text" class="form-control" name="address" placeholder="Rua, Travessa, Logradouro..." readonly>
                            </div>
                            <div class="col-6 form-group">
                                <input type="number" class="form-control" name="addressNumber" placeholder="Número" required>
                            </div>
                            <div class="col-6 form-group">
                                <input type="text" class="form-control" name="complement" placeholder="Complemento" required>
                            </div>
                            <div class="col-6 form-group">
                                <input type="text" class="form-control" name="province" placeholder="Bairro" readonly>
                            </div>
                            <div class="col-6 form-group">
                                <input type="text" class="form-control" name="city" placeholder="Cidate" readonly>
                            </div>
                            <div class="col-12 form-group">
                                <input type="text" class="form-control" name="state" placeholder="Estado" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6 form-group">
                                <select name="tipo" class="form-control" required>
                                    <option value="2" selected>Tipo</option>
                                    <option value="2">Assinante</option>
                                    @if (Auth::user()->type == 1) <option value="1">Administrador</option> @endif
                                </select>
                            </div>
                            <div class="col-6 form-group">
                                <select name="companyType" class="form-control" required>
                                    <option value="PF" selected>Porte</option>
                                    <option value="PF">PF</option>
                                    <option value="MEI">MEI</option>
                                    <option value="LIMITED">LIMITED</option>
                                    <option value="INDIVIDUAL">INDIVIDUAL</option>
                                    <option value="ASSOCIATION">ASSOCIATION</option>
                                </select>
                            </div>
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
