@extends('dashboard/layout')
    @section('conteudo')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Meus Contratos</h1>
        </div>

        <div class="row">
            <div class="col-xl-12 col-md-12 mb-4">
                <div class="card border-left-dark shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    <button class="btn btn-outline-primary w-25" type="button" id="exportar">Excel</button>
                                    <hr>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="tabela" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th class="text-center">N° Contrato</th>
                                                <th>Produto</th>
                                                <th class="text-center">Opções</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($vendas as $key => $venda)
                                            <tr>
                                                <td class="text-center"> {{ $venda->id }} </td>
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
                                                <td class="text-center">
                                                    <a class="btn btn-outline-success" href="{{ asset('contratos/'.$venda->id_produto.$venda->cpf.'.pdf') }}" download><i class="fa fa-file"></i></a>
                                                    <a href="{{ route('relatorioParcelas', ['id' => $venda->id]) }}" class="btn btn-outline-success">Extrato/Parcelas</a>
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
