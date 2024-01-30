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
                                                        <a href="{{ url("storage/{$mkt->file}") }}" download class="btn btn-outline-success"><i class="fas fa-file-download"></i></a>
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