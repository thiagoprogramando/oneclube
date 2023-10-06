@extends('layout')
@section('conteudo')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-10 col-md-8">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-2">Olá, cliente Positivo Brasil!</h1>
                                        <p class="mb-4">Preencha com os seus dados todas às informações.</p>
                                    </div>

                                    @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                    @if (!empty($success))
                                        <div class="alert alert-success">
                                            {{ $success }}
                                        </div>
                                    @endif

                                    <form id="registrer" class="user" method="POST" action="{{ route('vender', ['id' => $id]) }}">
                                        <input type="hidden" value={{ csrf_token() }} name="_token">
                                        <div class="col-sm-12 col-lg-8 offset-lg-2 row">

                                            <div class="form-group col-sm-12 col-lg-6">
                                                <input type="text" id="cliente" class="form-control" name="cliente" value="{{ old('cliente') }}" placeholder="Nome">
                                            </div>

                                            <div class="form-group col-sm-12 col-lg-6">
                                                <input type="text" id="cliente" class="form-control" name="cupom" value="{{ $cupom }}" placeholder="Cupom (opcional)">
                                            </div>

                                            <div class="form-group col-sm-12 col-lg-6">
                                                <input type="email" id="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="Email">
                                            </div>

                                            <div class="form-group col-sm-12 col-lg-6">
                                                <input type="text" id="cpfInput" oninput="mascaraCpfCnpj(this)" value="{{ old('cpfcnpj') }}" class="form-control " name="cpfcnpj" placeholder="CPF/CNPJ">
                                            </div>

                                            <input type="hidden" name="id_user" value="{{ $id }}">
                                            <input type="hidden" name="produto" value="2">
                                            <input type="hidden" name="franquia" value="limpanome">
                                            <input type="hidden" name="uf" id="estado">
                                            <input type="hidden" name="bairro" id="bairro">
                                            <input type="hidden" name="cidade" id="cidade">

                                            <div class="form-group col-sm-12 col-lg-6">
                                                <input type="text" id="dataInput" class="form-control" name="dataNascimento" value="{{ old('dataNascimento') }}" oninput="mascaraData(this)" maxlength="10" placeholder="Data de Nascimento" required>
                                            </div>

                                            <div class="form-group col-sm-12 col-lg-6">
                                                <input type="text" id="telefoneInput" oninput="mascaraTelefone(this)" maxlength="15" value="{{ old('telefone') }}" class="form-control " name="telefone" placeholder="WhatsApp" required>
                                            </div>

                                            <div class="form-group col-sm-12 col-lg-6">
                                                <input type="number" id="cep" class="form-control " name="cep" placeholder="CEP" onchange="consultarEndereco()" required>
                                            </div>

                                            <div class="form-group col-sm-12 col-lg-6">
                                                <input type="text" id="endereco" class="form-control " name="endereco" placeholder="Endereço" required>
                                            </div>

                                            <div class="form-group col-sm-12 col-lg-6">
                                                <select name="forma_pagamento" class="form-control">
                                                    <option value="PIX">Forma de Pagamento</option>
                                                    <option value="PIX">PIX</option>
                                                    <option value="BOLETO">BOLETO</option>
                                                    <option value="CARTÃO">CARTÃO</option>
                                                </select>
                                            </div>

                                            <div class="form-group col-sm-12 col-lg-6">
                                                <select name="parcela" class="form-control">
                                                    <option value="1">Parcelas</option>
                                                    <option value="1">1x</option>
                                                    <option value="2">2x</option>
                                                    <option value="3">3x</option>
                                                    <option value="4">4x</option>
                                                    <option value="5">5x</option>
                                                    <option value="6">6x</option>
                                                    <option value="7">7x</option>
                                                    <option value="8">8x</option>
                                                    <option value="9">9x</option>
                                                    <option value="10">10x</option>
                                                    <option value="11">11x</option>
                                                    <option value="12">12x</option>
                                                </select>
                                            </div>

                                            <div class="form-group col-sm-12 col-lg-4 offset-lg-4">
                                                <button type="submit" class="btn btn-primary btn-user btn-block"> Contratar </button>
                                            </div>

                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function mascaraCpfCnpj(input) {
            let value = input.value;
            value = value.replace(/\D/g, '');

            if (value.length <= 11) {
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            } else {
                value = value.replace(/^(\d{2})(\d)/, '$1.$2');
                value = value.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
                value = value.replace(/\.(\d{3})(\d)/, '.$1/$2');
                value = value.replace(/(\d{4})(\d)/, '$1-$2');
            }

            input.value = value;
        }

        function mascaraData(dataInput) {
            let data = dataInput.value;
            data = data.replace(/\D/g, '');
            data = data.replace(/(\d{2})(\d)/, '$1-$2')
            data = data.replace(/(\d{2})(\d)/, '$1-$2');
            dataInput.value = data;
        }

        function mascaraTelefone(telefoneInput) {
            let telefone = telefoneInput.value;
            telefone = telefone.replace(/\D/g, '');
            telefone = telefone.replace(/(\d{2})(\d)/, '($1) $2');
            telefone = telefone.replace(/(\d{5})(\d)/, '$1-$2');
            telefoneInput.value = telefone;
        }
    </script>

@endsection
