@extends('layout')
@section('conteudo')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9 mt-5">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-6 min-h-100 d-none d-lg-block bg-login-image"></div>
                            <div class="col-lg-6 mt-5">
                                <div class="p-5 mx-auto">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Bem vindo(a)!</h1>
                                    </div>
                                    <form class="user" method="POST" action="{{ route('logarClient') }}">
                                        @csrf
                                        <div class="form-group">
                                            <input type="number" class="form-control form-control-user" name="cpfcnpj" placeholder="CPF ou CNPJ">
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-user btn-block"> Acessar </button>
                                    </form>
                                    <hr>
                                    <div class="text-center">
                                        <a class="small" href="#">V 0.0.1</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

 