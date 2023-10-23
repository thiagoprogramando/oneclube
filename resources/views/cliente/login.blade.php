<html lang="pt-br">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="icon" type="image/png" href="{{ asset('admin/assets/logo.png') }}" />
        <title>Positivo Brasil - Clientes</title>
        <link href="{{ asset('admin/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
        <link
            href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
            rel="stylesheet">
        <link href="{{ asset('admin/css/sb-admin-2.css') }}" rel="stylesheet">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    </head>

    <body style="min-height: 150vh !important;" class="bg-gradient-success">

        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-10 col-lg-12 col-md-9">

                    <div class="card o-hidden border-0 shadow-lg my-5">
                        <div class="card-body p-0">
                            <div class="row">
                                <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
                                <div class="col-lg-6">
                                    <div class="p-5">
                                        <div class="text-center">
                                            <h1 class="h4 text-gray-900 mb-4">Acesso para Clientes.</h1>
                                            @if ($errors->has('error'))
                                                <div class="alert alert-danger">
                                                    {{ $errors->first('error') }}
                                                </div>
                                            @endif
                                        </div>
                                        <form class="user" method="POST" action="{{ route('cliente') }}">
                                            <input type="hidden" value={{ csrf_token() }} name="_token">
                                            <div class="form-group">
                                                <input type="number" class="form-control form-control-user" name="cpfcnpj"
                                                    placeholder="CPF ou CNPJ">
                                            </div>
                                            <button type="submit" class="btn btn-primary btn-user btn-block"> Acessar
                                            </button>
                                        </form>
                                        <hr>
                                        <div class="text-center">
                                            V 0.0.1
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <script src="{{ asset('admin/vendor/jquery/jquery.min.js') }}"></script>
        <script src="{{ asset('admin/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('admin/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
        <script src="{{ asset('admin/js/sb-admin-2.min.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </body>
</html>
