@extends('layout')
@section('conteudo')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-10 col-md-8">

            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <!-- Nested Row within Card Body -->
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="p-5">
                                <div class="text-center">
                                    <h1 class="h4 text-gray-900 mb-2">Obrigado!</h1>
                                    <p class="mb-4">Enviaremos para o seu whatsapp os dados de Pagamento. Até breve!</p>
                                </div>
                                @if (!empty($error))
                                    <div class="alert alert-danger">
                                        <ul>
                                            <li>{{ $error }}</li>
                                        </ul>
                                    </div>
                                @endif
                                @if(!empty($success))
                                    <div class="alert alert-success">
                                        {{ $success }}
                                    </div>
                                 @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>


@endsection

