@extends('layout')
@section('conteudo')

    <style>
        .input-group-text {
            cursor: pointer;
        }

        #eye-icon {
            font-size: 18px;
        }

        #password {
            padding-right: 40px;
        }
    </style>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-login-imagee"></div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Acesso para Associados.</h1>
                                        @if(session('error'))
                                            <div class="alert alert-danger">
                                                {{ session('error') }}
                                            </div>
                                        @endif
                                        @if(session('success'))
                                            <div class="alert alert-success">
                                                {{ session('success') }}
                                            </div>
                                        @endif
                                    </div>
                                    <form class="user" method="POST" action="{{ route('login_action') }}">
                                        <input type="hidden" value={{  csrf_token() }} name="_token">
                                        <div class="form-group">
                                            <input type="text" class="form-control form-control-user" name="email" placeholder="Email">
                                        </div>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <input type="password" class="form-control form-control-user" name="password" id="password" placeholder="Senha">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="password-toggle" onclick="togglePasswordVisibility()">
                                                        <i class="fa fa-eye" id="eye-icon"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-user btn-block">
                                            Login
                                        </button>
                                    </form>
                                    <hr>
                                    <div class="text-center">
                                        <a class="small" href="/forgout">Esqueci minha senha!</a>
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

