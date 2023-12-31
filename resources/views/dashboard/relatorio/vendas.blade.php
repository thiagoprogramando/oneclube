@extends('dashboard/layout')
    @section('conteudo')
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Vendas</h1>
        </div>

        <!-- Minhas Vendas -->
        <div class="row">
            <div class="col-xl-12 col-md-12 mb-4">
                <div class="card border-left-dark shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    <button class="btn btn-outline-success w-25" type="button" data-toggle="modal" data-target="#exampleModal">Filtros</button>
                                    <button class="btn btn-outline-primary w-25" type="button" id="exportar">Excel</button>
                                    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <form method="POST" action="{{ route('relatorioVendas') }}">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLabel">Filtros:</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="hidden" value={{  csrf_token() }} name="_token">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="form-group">
                                                                    <select class="form-control"  name="produto">
                                                                        <option value="ALL">Todos os Produtos</option>
                                                                        <option value="3">One Motos</option>
                                                                        <option value="11">One Motos Associados</option>
                                                                        <option value="3">One Beauty</option>
                                                                        <option value="2">One Positive</option>
                                                                        <option value="12">One Positive Associados</option>
                                                                        <option value="8">One Serviços</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-12">
                                                                <div class="form-group">
                                                                    <select class="form-control"  name="status">
                                                                        <option value="ALL">Todos os Status</option>
                                                                        <option value="PAYMENT_CONFIRMED">Aprovados</option>
                                                                        <option value="PENDING_PAY">Pendentes de Pagamento</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-12">
                                                                <div class="form-group">
                                                                    <select class="form-control"  name="usuario">
                                                                        <option value="ALL">Todos os Usuários</option>
                                                                        @foreach ($users as $key =>$user)
                                                                        <option value="{{ $user->id }}">{{ $user->nome }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <input type="date" class="form-control" name="data_inicio" placeholder="Data inicial">
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <input type="date" class="form-control" name="data_fim" placeholder="Data Final">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                                                        <button type="submit" class="btn btn-success">Filtrar</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <hr>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="tabela" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Cliente</th>
                                                <th>Produto</th>
                                                <th>Status</th>
                                                <th>Data venda</th>
                                                <th class="text-center">Opções</th>
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
                                                            One Positive
                                                            @break
                                                        @case(12)
                                                            One Positive
                                                            @break
                                                        @case(3)
                                                            One Motos/Beauty
                                                            @break
                                                        @case(11)
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
                                                    @switch($venda->status_pay)
                                                        @case('PAYMENT_CONFIRMED')
                                                            Aprovado
                                                            @break
                                                        @case('PENDING_PAY')
                                                            Aguardando Pagamento
                                                            @break
                                                        @default
                                                            Status Desconhecido
                                                    @endswitch
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($venda->created_at)->format('d/m/Y') }}</td>
                                                <td class="text-center">
                                                    <a class="btn btn-outline-success" href="{{ asset('contratos/'.$venda->id_produto.$venda->cpf.'.pdf') }}" download><i class="fa fa-file"></i></a>
                                                    <?php
                                                        $id_pay = str_replace('pay_', '', $venda->id_pay);
                                                    ?>
                                                    <a class="btn btn-outline-primary" href="https://www.asaas.com/i/{{ $id_pay }}" target="_blank">
                                                        <i class="fa fa-credit-card"></i>
                                                    </a>
                                                </td>
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

    <script>
        $(document).ready(function() {
            $('#exportar').click(function() {
                var tabela = document.getElementById('tabela');
                var wb = XLSX.utils.table_to_book(tabela, { sheet: 'Sheet 1' });
                var wbout = XLSX.write(wb, { bookType: 'xlsx', type: 'binary' });

                function s2ab(s) {
                    var buf = new ArrayBuffer(s.length);
                    var view = new Uint8Array(buf);
                    for (var i = 0; i < s.length; i++) {
                        view[i] = s.charCodeAt(i) & 0xFF;
                    }
                    return buf;
                }

                var blob = new Blob([s2ab(wbout)], { type: 'application/octet-stream' });
                var url = URL.createObjectURL(blob);
                var a = document.createElement('a');
                a.href = url;
                a.download = 'tabela.xlsx';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                setTimeout(function() { URL.revokeObjectURL(url); }, 100);
            });
        });
    </script>

    @endsection
