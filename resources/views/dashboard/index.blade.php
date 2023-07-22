@extends('dashboard/layout')
    @section('conteudo')
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        </div>

        <!-- Relatórios -->
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
                                            @foreach ($vendas as $key =>$venda )
                                            <tr>
                                                <td>{{ $venda->id }}</td>
                                                <td>{{ $venda->nome }}</td>
                                                <td>{{ $venda->id_produto }}</td>
                                                <td>{{ $venda->cpf }}</td>
                                                <td>{{ $venda->status_pay }}</td>
                                                <td>{{ $venda->created_at }}</td>
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
