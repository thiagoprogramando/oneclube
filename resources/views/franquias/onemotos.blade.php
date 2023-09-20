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
                                            <h1 class="h4 text-gray-900 mb-2">Olá, cliente One Motos!</h1>
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
                                        @if(!empty($success))
                                            <div class="alert alert-success">
                                                {{ $success }}
                                            </div>
                                        @endif
                                        <form id="registrer" class="user" method="POST" action="{{ route('vender', ['id' => $id]) }}" enctype="multipart/form-data">
                                            <input type="hidden" value={{  csrf_token() }} name="_token">
                                            <div class="col-sm-12 col-lg-8 offset-lg-2 row">

                                                <div class="form-group col-sm-12 col-lg-6">
                                                    <input type="text" id="cpfInput" oninput="mascaraCpf(this)" maxlength="14" value="{{ old('cpfcnpj') }}" class="form-control form-control-user" name="cpfcnpj" placeholder="CPF/CNPJ">
                                                </div>
                                                
                                                <input type="hidden" name="id_user" value="{{ $id }}">
                                                <input type="hidden" name="produto" value="3">
                                                <input type="hidden" name="franquia" value="onemotos">
                                            
                                                <div class="form-group col-sm-12 col-lg-6">
                                                    <input type="text" id="dataInput" class="form-control form-control-user" name="dataNascimento" value="{{ old('dataNascimento') }}" oninput="mascaraData(this)" maxlength="10" placeholder="Data de Nascimento" required>
                                                </div>
                                                
                                                <div class="form-group col-sm-12 col-lg-12">
                                                    <input type="text" id="cliente" class="form-control form-control-user" name="cliente" value="{{ old('cliente') }}" placeholder="Nome">
                                                </div>
            
                                                <div class="form-group col-sm-12 col-lg-6">
                                                    <input type="text" id="profissao" class="form-control form-control-user" name="profissao" value="{{ old('profissao') }}" placeholder="Profissão" required>
                                                </div>
                                                <div class="form-group col-sm-12 col-lg-6">
                                                    <input type="text" id="rg" class="form-control form-control-user" name="rg" value="{{ old('rg') }}" placeholder="RG">
                                                </div>
            
                                                <div class="form-group col-sm-12 col-lg-12">
                                                    <select class="form-control" name="civil" required>
                                                        <option value="Solteiro">Solteiro</option>
                                                        <option value="Casado"> Casado</option>
                                                        <option value="União Estável">União Estável</option>
                                                    </select>
                                                </div>
            
                                                <div class="form-group col-sm-12 col-lg-6">
                                                    <input type="email" id="email" class="form-control form-control-user" value="{{ old('email') }}" name="email" placeholder="Email" required>
                                                </div>
                                                <div class="form-group col-sm-12 col-lg-6">
                                                    <input type="text" id="telefoneInput" oninput="mascaraTelefone(this)" maxlength="15" value="{{ old('telefone') }}" class="form-control form-control-user" name="telefone" placeholder="WhatsApp" required>
                                                </div>
            
                                                <div class="form-group col-sm-12 col-lg-6">
                                                    <input type="number" onblur="consultarEndereco()" id="cep" class="form-control form-control-user" value="{{ old('cep') }}" name="cep" placeholder="CEP" required>
                                                </div>
                                                <div class="form-group col-sm-12 col-lg-6">
                                                    <input type="text" id="numero" class="form-control form-control-user" value="{{ old('numero') }}" name="numero" placeholder="N°" required>
                                                </div>
            
                                                <div class="form-group col-sm-12 col-lg-6">
                                                    <input type="text" id="estado" class="form-control form-control-user" value="{{ old('estado') }}" name="estado" placeholder="Estado" required>
                                                </div>
                                                <div class="form-group col-sm-12 col-lg-6"> 
                                                    <input type="text" id="cidade" class="form-control form-control-user" value="{{ old('cidade') }}" name="cidade" placeholder="Cidade" required>
                                                </div>
            
                                                <div class="form-group col-sm-12 col-lg-6">
                                                    <input type="text" id="bairro" class="form-control form-control-user" value="{{ old('bairro') }}" name="bairro" placeholder="Bairro" required>
                                                </div>
                                                <div class="form-group col-sm-12 col-lg-6">
                                                    <input type="text" id="endereco" class="form-control form-control-user" value="{{ old('endereco') }}" name="endereco" placeholder="Endereço" required>
                                                </div>
                                                
                                                <div class="form-group col-sm-12 col-lg-12">
                                                   
                                                       
                                                          <input type="file"   name="file">
                                                      
                                                     
                                                </div>

                                                <div class="form-group col-sm-12 col-lg-4 offset-lg-4">
                                                    <div class="form-group">
                                                        <button type="submit" class="btn btn-primary btn-user btn-block"> Contratar </button>
                                                    </div>
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
            
            function mascaraCpf(cpfInput) {
                let cpf = cpfInput.value;
                cpf = cpf.replace(/\D/g, '');
                cpf = cpf.replace(/(\d{3})(\d)/, '$1.$2');
                cpf = cpf.replace(/(\d{3})(\d)/, '$1.$2');
                cpf = cpf.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
                cpfInput.value = cpf;
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

