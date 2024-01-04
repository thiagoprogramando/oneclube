@extends('dashboard.layout')
    @section('conteudo')
    <div class="container-fluid">

        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Perfil</h1>
        </div>

        @if(count($myDocuments) > 0)
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-success">Documentos Pendentes</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Situação</th>
                                        <th>Documento</th>
                                        <th>Descrição</th>
                                        <th class="text-center">Opções</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($myDocuments as $key => $myDocument)
                                        <tr>
                                            <td>
                                                @switch($myDocument['status'])
                                                    @case('NOT_SENT')
                                                        <span class="badge badge-dark">Não enviado</span>
                                                        @break
                                                    @case('PENDING')
                                                        <span class="badge badge-warning">Em Análise</span>
                                                        @break
                                                    @case('APPROVED')
                                                        <span class="badge badge-success">Aprovado</span>
                                                        @break
                                                    @case('REJECTED')
                                                        <span class="badge badge-danger">Rejeitado</span>
                                                        @break
                                                    @default
                                                @endswitch
                                            </td>
                                            <td>{{ $myDocument['title'] }}</td>
                                            <td>Para enviar/reenviar esse documento utilize o botão ao lado.</td>
                                            <td class="text-center">
                                                @switch($myDocument['status'])
                                                    @case('NOT_SENT')
                                                    <a class="btn btn-outline-primary" target="_blank" href="{{ $myDocument['onboardingUrl'] }}"><i class="far fa-paper-plane"></i></a>
                                                        @break
                                                    @case('PENDING')
                                                        <span class="badge badge-warning">Em Análise</span>
                                                        @break
                                                    @case('APPROVED')
                                                        <span class="badge badge-success">Aprovado</span>
                                                        @break
                                                    @case('REJECTED')
                                                        <a class="btn btn-outline-primary" target="_blank" href="{{ $myDocument['onboardingUrl'] }}"><i class="far fa-paper-plane"></i></a>
                                                        @break
                                                    @default
                                                @endswitch
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
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">

                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-success">Meus dados</h6>
                    </div>

                    <div class="card-body">
                        <form id="cadastro" class="user" method="POST" action="{{ route('profileUpdate') }}">
                            @csrf
                            <div class="row">
                                <div class="col-sm-12 col-lg-8 offset-lg-2 row">

                                    <div class="form-group col-sm-12 col-lg-6">
                                        <input type="text" class="form-control form-control-user" name="name" value="{{$dados->name}}">
                                    </div>
                                    <div class="form-group col-sm-12 col-lg-6">
                                        <input type="text" class="form-control form-control-user" name="mobilePhone" oninput="mascaraTelefone(this)" value="{{ $dados->mobilePhone }}">
                                    </div>
                                    <div class="form-group col-sm-12 col-lg-6">
                                        <input type="text" class="form-control form-control-user" name="email" value="{{ $dados->email }}">
                                    </div>
                                    <div class="form-group col-sm-12 col-lg-6">
                                        <input type="password" class="form-control form-control-user" name="password" placeholder="Alterar senha: (Opcional)">
                                    </div>

                                    <div class="form-group col-sm-12 col-lg-4 offset-lg-4">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary btn-user btn-block"> Atualizar </button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>

    </div>
    @endsection
