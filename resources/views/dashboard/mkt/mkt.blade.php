@extends('dashboard.layout')
    @section('conteudo')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Material e MKT</h1>
        </div>

        <div class="row">
            <div class="col-xl-12 col-md-12 mb-2">
                <div class="card border-left-dark shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">

                                <button class="btn btn-outline-success w-25 mb-5" type="button" data-toggle="modal" data-target="#exampleModal">Criar</button>

                                <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form method="POST" action="{{ route('createMkt') }}" enctype="multipart/form-data">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLabel">Filtros:</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
                                                </div>
                                                <div class="modal-body">
                                                    @csrf
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <input type="text" class="form-control" name="title" placeholder="Título">
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <input type="text" class="form-control" name="description" placeholder="Descrição">
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <input type="file" class="form-control" name="arquivo" placeholder="Arquivo">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-danger"
                                                        data-dismiss="modal">Fechar</button>
                                                    <button type="submit" class="btn btn-success">Criar</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-striped" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Título</th>
                                                <th>Descrição</th>
                                                <th class="text-center">Opções</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($mkts as $key => $mkt)
                                                <tr>
                                                    <td>{{ $mkt->id }}</td>
                                                    <td>{{ $mkt->title }}</td>
                                                    <td>{{ $mkt->description }}</td>
                                                    <td class="text-center">
                                                        <form action="{{ route('deleteMkt') }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="id" value="{{ $mkt->id }}">
                                                            <a href="{{ url("storage/{$mkt->file}") }}" download class="btn btn-outline-success"><i class="fas fa-file-download"></i></a>
                                                            <button type="submit" class="btn btn-outline-danger"><i class="fas fa-trash"></i></button>
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