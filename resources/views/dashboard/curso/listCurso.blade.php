@extends('dashboard.layout')
@section('conteudo')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Listagem de Cursos</h1>
        </div>

        <div class="row">
            @if (Auth::user()->type == 1)
                <div class="col-xl-12 col-md-12 mb-2">
                    <button type="button" data-toggle="modal" data-target="#modalCreateCurso" class="btn btn-outline-primary">Cadastrar</button>
                </div>
            @endif
            <div class="col-xl-12 col-md-12 mb-2">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row p-2">

                            @foreach ($cursos as $curso)
                                <div class="col-xl-4 col-md-4 mb-4">
                                    <a href="{{ route('viewCurso', ['id' => $curso->id]) }}">
                                    <div class="card border-left-dark shadow py-2">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="font-weight-bold text-primary mb-1">
                                                        {{ $curso->title }}
                                                    </div>
                                                    <div class="mb-0 font-weight-bold text-dark">
                                                        {{ $curso->description }}
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fas fa-user-graduate fa-2x text-gray-300"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    </a>
                                </div>
                            @endforeach
                
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalCreateCurso" tabindex="-1" role="dialog" aria-labelledby="labelCreateUser" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('createCurso') }}" method="POST" class="user">
                    <div class="modal-header">
                        <h5 class="modal-title" id="labelCreateUser">Cadastro de Curso</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">×</span> </button>
                    </div>
                    <div class="modal-body">
                        @csrf
                        <div class="form-group">
                            <input type="text" class="form-control" name="title" placeholder="Título:" required>
                        </div>
                        <div class="form-group">
                            <textarea class="form-control" name="description" rows="3" placeholder="Descrição:"></textarea>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" name="value" placeholder="Valor:" oninput="mascaraReal(this)" required>
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
