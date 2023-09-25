@extends('layout')
    @section('conteudo')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-password-image"></div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-2">Esqueceu seu acesso?</h1>
                                        <p class="mb-4">Iremos te ajudar a redefinir!</p>
                                    </div>
                                    @if(session('error'))
                                        <div class="alert alert-danger">
                                            {{ session('error') }}
                                        </div>
                                    @endif
                                    @if(isset($codigoAleatorio))
                                        <form class="user" method="POST" action="{{ route('forgout_token') }}">
                                            <input type="hidden" value={{  csrf_token() }} name="_token">
                                            <div class="form-group">
                                                <input type="number" class="form-control form-control-user" name="token" placeholder="Token">
                                            </div>
                                            <div class="form-group">
                                                <input type="text" class="form-control form-control-user" name="senha" placeholder="Novo Senha">
                                            </div>
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary btn-user btn-block"> Confirmar </button>
                                            </div>
                                        </form>
                                    @else
                                        <form class="user" method="POST" action="{{ route('forgout') }}">
                                            <input type="hidden" value={{  csrf_token() }} name="_token">
                                            <div class="form-group">
                                                <input type="email" class="form-control form-control-user" name="email" placeholder="Informe seu E-mail">
                                            </div>
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary btn-user btn-block"> Enviar </button>
                                            </div>
                                        </form>
                                    @endif
                                    <hr>
                                    <div class="text-center">
                                        <p><a href="/" class="small">Acessar minha conta.</a></p>
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

