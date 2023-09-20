@extends('dashboard/layout')
    @section('conteudo')
    <div class="container-fluid">

        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Vendas Premiadas</h1>
        </div>

        <div class="row">
            <div class="col-xl-12 col-md-12 mb-4">
                <div class="card border-left-dark shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    <button class="btn btn-outline-success w-25" type="button" data-toggle="modal" data-target="#premiadoModal">Premiar Venda</button>
                                    <div class="modal fade" id="premiadoModal" tabindex="-1" role="dialog" aria-labelledby="premiadoModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <form method="POST" action="{{ route('relatorioPremiados') }}">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="premiadoModalLabel">Escolha uma venda!</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="hidden" value={{  csrf_token() }} name="_token">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="form-group">
                                                                    <input type="text" class="form-control" name="venda" id="searchInput" placeholder="Digite o ID ou nome do cliente">
                                                                    <input type="hidden" name="id" id="id">
                                                                </div>
                                                                <ul id="searchResults"></ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                                                        <button type="submit" class="btn btn-success">Premiar</button>
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
                                                <th>Data venda</th>
                                                <th class="text-center">Opções</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($vendasPremiadas as $key =>$venda)
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
                                                <td>{{ \Carbon\Carbon::parse($venda->created_at)->format('d/m/Y') }}</td>
                                                <td class="text-center">
                                                    <div class="btn-group">
                                                        <form action="{{ route('relatorioPremiadosUp') }}" method="post">
                                                            <input type="hidden" value={{  csrf_token() }} name="_token">
                                                            @switch($venda->id_produto)
                                                                @case(1)
                                                                    <a class="btn btn-outline-success" href="{{ asset('contratos/1' . $venda->cpf . '.pdf') }}" download><i class="fa fa-file"></i></a>
                                                                    @break
                                                                @case(2)
                                                                    <a class="btn btn-outline-success" href="{{ asset('contratos/2' . $venda->cpf . '.pdf') }}" download><i class="fa fa-file"></i></a>
                                                                    @break
                                                                @case(3)
                                                                    <a class="btn btn-outline-success" href="{{ asset('contratos/3' . $venda->cpf . '.pdf') }}" download><i class="fa fa-file"></i></a>
                                                                    @break
                                                                @case(8)
                                                                    <a class="btn btn-outline-success" href="{{ asset('contratos/8' . $venda->cpf . '.pdf') }}" download><i class="fa fa-file"></i></a>
                                                                    @break
                                                                @case(11)
                                                                    <a class="btn btn-outline-success" href="{{ asset('contratos/11' . $venda->cpf . '.pdf') }}" download><i class="fa fa-file"></i></a>
                                                                    @break
                                                                @case(12)
                                                                    <a class="btn btn-outline-success" href="{{ asset('contratos/12' . $venda->cpf . '.pdf') }}" download><i class="fa fa-file"></i></a>
                                                                    @break
                                                                @default
                                                            @endswitch

                                                            <input type="hidden" name="id" value="{{ $venda->id }}">
                                                            <button type="submit" class="btn btn-outline-danger"><i class="fa fa-trash"></i></button>
                                                        </form>
                                                    </div>
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
        </div>
    </div>

    <script>
        const vendas = @json($vendas);

        const searchInput = document.getElementById('searchInput');
        const searchResults = document.getElementById('searchResults');
        const idInput = document.getElementById('id');

        searchInput.addEventListener('input', function () {
            const searchTerm = searchInput.value.toLowerCase();
            const filteredVendas = vendas.filter(venda =>
                venda.id.toString().includes(searchTerm) ||
                (venda.nome && venda.nome.toLowerCase().includes(searchTerm))
            );

            renderResults(filteredVendas);
        });

        function renderResults(results) {
            searchResults.innerHTML = '';
            results.forEach(venda => {
                const listItem = document.createElement('li');
                listItem.textContent = `ID: ${venda.id} - Cliente: ${venda.nome}`;
                listItem.addEventListener('click', () => {
                    searchInput.value = venda.nome;
                    idInput.value = venda.id; // Preenche o campo id com o ID da venda
                });
                searchResults.appendChild(listItem);
            });
        }
    </script>

    @endsection
