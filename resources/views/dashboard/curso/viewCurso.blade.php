@extends('dashboard.layout')
@section('conteudo')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Curso {{ $curso->title }}</h1>
        </div>

        <div class="row">
            @if (Auth::user()->type == 1)
                <div class="col-xl-12 col-md-12 mb-2">
                    <button type="button" data-toggle="modal" data-target="#modalCreateMaterial" class="btn btn-outline-primary">Cadastrar Material</button>
                </div>
            @endif
            <div class="col-xl-12 col-md-12 mb-2">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row p-2">

                            @foreach ($materiais as $material)
                                <div class="col-12 col-xl-4 col-md-4 mb-4">
                                    <div class="card shadow py-2">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="font-weight-bold text-primary mb-1">
                                                        {{ $material->title }}
                                                    </div>
                                                    <div class="mb-0 font-weight-bold text-dark">
                                                        {{ $material->description }}
                                                    </div>

                                                    @switch($material->type)
                                                        @case(1)
                                                            <video width="100%" controls>
                                                                <source src="{{ url("storage/{$material->file}") }}" type="video/mp4">
                                                                Your browser does not support the video tag.
                                                            </video>                                                        
                                                            @break
                                                        @case(2)
                                                            <a download href="{{ url("storage/{$material->file}") }}" class="btn btn-primary">Baixar Arquivo</a>
                                                            @break
                                                        @case(3)
                                                            <a style="text-decoration: none !important;" download href="{{ url("storage/{$material->file}") }}">
                                                                <img style="max-height: 200px; widht: 100%;" class="img-fluid" src="{{ url("storage/{$material->file}") }}">
                                                            </a>
                                                            @break
                                                        @default
                                                    @endswitch

                                                </div>
                                            </div>
                                            @if (Auth::user()->type == 1)
                                            <div class="row mt-5">
                                                <div class="col-12 mt-5">
                                                    <a href="{{ route('deleteMaterial', ["id" => $material->id]) }}" class="btn btn-google btn-block">Excluir</a>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalCreateMaterial" tabindex="-1" role="dialog" aria-labelledby="labelCreateUser" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('createMaterial') }}" method="POST" class="user" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="labelCreateUser">Cadastro de Material</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">×</span> </button>
                    </div>
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="id_curso" value="{{ $curso->id }}">
                        <div class="form-group">
                            <input type="text" class="form-control" name="title" placeholder="Título:" required>
                        </div>
                        <div class="form-group">
                            <textarea class="form-control" name="description" rows="3" placeholder="Descrição:" required></textarea>
                        </div>
                        <div class="form-group">
                            <select name="type" class="form-control" required>
                                <option value="1" selected>Video</option>
                                <option value="2">PDF</option>
                                <option value="3">Imagem</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <input type="file" class="form-control" name="file" required>
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
