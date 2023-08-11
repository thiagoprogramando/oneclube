@extends('dashboard/layout')
    @section('conteudo')
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        </div>

        <!-- Relatórios -->
        <div class="row">
            <div class="col-xl-6 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1"> Link de vendas One Motos </div>
                                <div class="row no-gutters align-items-center">
                                    <div class="col-auto">
                                        <div class="h5 mb-0 mr-3 font-weight-bold text-warning">
                                            <button class="btn btn-outline-warning" onclick="copyToClipboard()"><i class="fas fa-copy"></i></button></div>
                                    </div>
                                    <div class="col">
                                        <a style="font-size: 15px;" class="text-warning" id="copyLink" href="{{ url('/onemotos/' . auth()->id()) }}" target="_blank">{{ url('/onemotos/' . auth()->id()) }}</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-motorcycle fa-2x text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-md-6 mb-4">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1"> Link de vendas One Beauty </div>
                                <div class="row no-gutters align-items-center">
                                    <div class="col-auto">
                                        <div class="h5 mb-0 mr-3 font-weight-bold text-danger">
                                            <button class="btn btn-outline-danger" onclick="copyToClipboard()"><i class="fas fa-copy"></i></button></div>
                                    </div>
                                    <div class="col">
                                        <a style="font-size: 15px;" class="text-danger" id="copyLink" href="{{ url('/onebeauty/' . auth()->id()) }}" target="_blank">{{ url('/onebeauty/' . auth()->id()) }}</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-flask fa-2x text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-green text-uppercase mb-1"> Link de vendas One Positive </div>
                                <div class="row no-gutters align-items-center">
                                    <div class="col-auto">
                                        <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                            <button class="btn btn-outline-green" onclick="copyToClipboard()"><i class="fas fa-copy"></i></button></div>
                                    </div>
                                    <div class="col">
                                        <a style="font-size: 15px;" class="text-green" id="copyLink" href="{{ url('/onepositive/' . auth()->id()) }}" target="_blank">{{ url('/onepositive/' . auth()->id()) }}</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-check fa-2x text-green"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-md-6 mb-4">
                <div class="card border-left-dark shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-dark text-uppercase mb-1"> Link de vendas One Serviços </div>
                                <div class="row no-gutters align-items-center">
                                    <div class="col-auto">
                                        <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                            <button class="btn btn-outline-dark" onclick="copyToClipboard()"><i class="fas fa-copy"></i></button></div>
                                    </div>
                                    <div class="col">
                                        <a style="font-size: 15px;" class="text-dark" id="copyLink" href="{{ url('/oneservicos/' . auth()->id()) }}" target="_blank">{{ url('/oneservicos/' . auth()->id()) }}</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-briefcase fa-2x text-dark"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <!-- Minhas Vendas -->
            <div class="col-xl-12 col-md-12 mb-4">
                <div class="card border-left-dark shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Últimas Vendas <hr>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Cliente</th>
                                                <th>Produto</th>
                                                <th>Contrato</th>
                                                <th>Status</th>
                                                <th>Data venda</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($vendas as $key =>$venda)
                                            <tr>
                                                <td>{{ $venda->id }}</td>
                                                <td>{{ $venda->nome }}</td>
                                                <td>
                                                    @switch($venda->id_produto)
                                                        @case(2)
                                                            Limpa Nome
                                                            @break
                                                        @case(3)
                                                            One Motos/Beauty
                                                            @break
                                                        @case(8)
                                                            One Serviços
                                                            @break
                                                        @default
                                                            Produto Desconhecido
                                                    @endswitch
                                                </td>
                                                <td>
                                                @switch($venda->id_produto)
                                                        @case(1)
                                                            <a class="btn btn-outline-success" href="{{ asset('contratos/1' . $venda->cpf . '.pdf') }}" download>Contrato</a>
                                                            @break
                                                        @case(2)
                                                            <a class="btn btn-outline-success" href="{{ asset('contratos/2' . $venda->cpf . '.pdf') }}" download>Contrato</a>
                                                            @break
                                                        @case(3)
                                                            <a class="btn btn-outline-success" href="{{ asset('contratos/3' . $venda->cpf . '.pdf') }}" download>Contrato</a>
                                                            @break
                                                        @case(4)
                                                            <a class="btn btn-outline-success" href="{{ asset('contratos/4' . $venda->cpf . '.pdf') }}" download>Contrato</a>
                                                            @break
                                                        @default
                                                            Produto Desconhecido
                                                    @endswitch
                                                </td>
                                                <td>
                                                    @switch($venda->status_pay)
                                                        @case('PAYMENT_CONFIRMED')
                                                            Aprovado
                                                            @break
                                                        @case('PENDING_PAY')
                                                            Aguardando Pagamento
                                                            @break
                                                        @default
                                                            Pendente
                                                    @endswitch
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($venda->created_at)->format('d/m/Y') }}</td>
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
            <!-- Fim Vendas -->
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function copyToClipboard() {
            const link = document.getElementById('copyLink').href;
            const tempInput = document.createElement('input');
            tempInput.setAttribute('value', link);
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand('copy');
            document.body.removeChild(tempInput);
            Swal.fire('Copiado!', 'Link copiado para a área de transferência!', 'success');
        }
    </script>

    @endsection
