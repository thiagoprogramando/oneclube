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
                                            <h1 class="h4 text-gray-900 mb-2">Olá, cliente G7!</h1>
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
                                        <form id="registrer" class="user" method="POST" action="{{ route('sell', ['id' => $id]) }}">
                                            @csrf
                                            <div class="col-sm-12 col-lg-8 offset-lg-2 row">
                                                <input type="hidden" name="id_user" value="{{ $id }}">
                                                <input type="hidden" name="produto" value="1">
                                                <input type="hidden" name="franquia" value="limpanome">
                                                <input type="hidden" value="{{ $valor }}" name="valor">

                                                <div class="form-group col-sm-12 col-lg-6">
                                                    <input type="text" class="form-control form-control-user" name="name" value="{{ old('name') }}" placeholder="Nome" required>
                                                </div>
                                                <div class="form-group col-sm-12 col-lg-6">
                                                    <input type="number" class="form-control form-control-user" name="rg" value="{{ old('rg') }}" placeholder="RG" required>
                                                </div>
                                                <div class="form-group col-sm-12 col-lg-6">
                                                    <input type="email" class="form-control form-control-user" name="email" value="{{ old('email') }}" placeholder="Email" required>
                                                </div>
                                                <div class="form-group col-sm-12 col-lg-6">
                                                    <input type="text" class="form-control form-control-user" name="cpfcnpj" value="{{ old('cpfcnpj') }}" oninput="mascaraCpfCnpj(this)" maxlength="14" placeholder="CPF/CNPJ" required>
                                                </div>
                                                <div class="form-group col-sm-12 col-lg-6">
                                                    <input type="text" class="form-control form-control-user" name="birthDate" value="{{ old('birthDate') }}" oninput="mascaraData(this)" maxlength="10" placeholder="Data de Nascimento" required>
                                                </div>
                                                <div class="form-group col-sm-12 col-lg-6">
                                                    <input type="text" class="form-control form-control-user" name="mobilePhone" oninput="mascaraTelefone(this)" maxlength="15" value="{{ old('mobilePhone') }}" placeholder="WhatsApp" required>
                                                </div>
                                                <div class="form-group col-sm-12 col-lg-6">
                                                    <input type="number" class="form-control form-control-user" name="cep" placeholder="CEP" onBlur="preencherEnderecoPorCEP()" value="{{ old('cep') }}" required>
                                                </div>
                                                <div class="form-group col-sm-12 col-lg-6">
                                                    <input type="number" value="{{ old('numero') }}" class="form-control form-control-user" name="numero" placeholder="Número" required>
                                                </div>
                                                <div class="form-group col-sm-12 col-lg-6">
                                                    <input type="text" value="{{ old('endereco') }}" class="form-control form-control-user" name="endereco" placeholder="Rua" required>
                                                </div>
                                                <div class="form-group col-sm-12 col-lg-6">
                                                    <input type="text" value="{{ old('bairro') }}" class="form-control form-control-user" name="bairro" placeholder="Bairro" required>
                                                </div>
                                                <div class="form-group col-sm-12 col-lg-6">
                                                    <input type="text" value="{{ old('cidade') }}" class="form-control form-control-user" name="cidade" placeholder="Cidade" required>
                                                </div>
                                                <div class="form-group col-sm-12 col-lg-6">
                                                    <input type="text" value="{{ old('estado') }}" class="form-control form-control-user" name="estado" placeholder="Estado" required>
                                                </div>

                                                <div class="form-group col-sm-12 col-lg-6">
                                                    <select name="billingType" class="form-control" id="billingType">
                                                        <option value="PIX">Forma Pagamento</option>
                                                        <option value="CREDIT_CARD">Cartão de Crédito</option>
                                                        <option value="BOLETO">Boleto</option>
                                                        <option value="PIX">PIX</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-sm-12 col-lg-6">
                                                    <select name="installmentCount" class="form-control" id="installmentCount">
                                                        <option value="1">Parcelas</option>
                                                        <option value="1">1X</option>
                                                        <option value="2">2X</option>
                                                        <option value="3">3X</option>
                                                        <option value="4">4X</option>
                                                        <option value="4">5X</option>
                                                        <option value="6">6X</option>
                                                        <option value="7">7X</option>
                                                        <option value="8">8X</option>
                                                        <option value="9">9X</option>
                                                        <option value="10">10X</option>
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
            $(function() {
                $("#datepicker").datepicker({
                    dateFormat: 'dd/mm/yy',
                });
            });
            
            function mascaraCpfCnpj(input) {
                let value = input.value;
                value = value.replace(/\D/g, '');

                if (value.length <= 11) {
                    value = value.replace(/(\d{3})(\d)/, '$1.$2');
                    value = value.replace(/(\d{3})(\d)/, '$1.$2');
                    value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
                } else {
                    value = value.replace(/(\d{2})(\d)/, '$1.$2');
                    value = value.replace(/(\d{3})(\d)/, '$1.$2');
                    value = value.replace(/(\d{3})(\d)/, '$1/$2');
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

            function preencherEnderecoPorCEP() {
                var cep = $('input[name=cep]').val();

                if (/^\d{8}$/.test(cep)) {
                    fetch('https://viacep.com.br/ws/' + cep + '/json/')
                        .then(response => response.json())
                        .then(data => {
                            $('input[name=endereco]').val(data.logradouro);
                            $('input[name=bairro]').val(data.bairro);
                            $('input[name=cidade]').val(data.localidade);
                            $('input[name=estado]').val(data.uf);
                        })
                        .catch(error => console.error('Erro ao buscar o endereço:', error));
                } else {
                    console.error('Formato inválido de CEP');
                }
            }

            $(document).ready(function() {
                $("#billingType").change(function() {
                    var selectedBillingType = $(this).val();
                    $("#installmentCount").find('option').remove();
                    if (selectedBillingType === "CREDIT_CARD") {
                        $("#installmentCount").append('<option value="1">1X</option>');
                        for (var i = 2; i <= 12; i++) {
                            $("#installmentCount").append('<option value="' + i + '">' + i + 'X</option>');
                        }
                    } else if (selectedBillingType === "BOLETO") {
                        $("#installmentCount").append('<option value="1">1X (Entrada min de R$ 300)</option>');
                        for (var i = 2; i <= 3; i++) {
                            $("#installmentCount").append('<option value="' + i + '">' + i + 'X</option>');
                        }
                    } else if (selectedBillingType === "PIX") {
                        $("#installmentCount").append('<option value="1">1X</option>');
                    }
                });
            });
        </script>

    @endsection

