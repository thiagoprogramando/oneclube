@extends('dashboard/layout')
@section('conteudo')
    <div class="container-fluid">

        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Painel Principal</h1>
        </div>

        @if (Auth::user()->tipo == 1 || Auth::user()->tipo == 2)
            <div class="row">
                <div class="col-xl-6 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1"> Link de vendas
                                        One Motos </div>
                                    <div class="row no-gutters align-items-center">
                                        <div class="col-auto">
                                            <div class="h5 mb-0 mr-3 font-weight-bold text-warning">
                                                <button class="btn btn-outline-warning"
                                                    data-link="{{ url('/onemotos/' . auth()->id()) }}"
                                                    onclick="copiaLink(this)"><i class="fas fa-copy"></i></button>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <a style="font-size: 15px;" class="text-warning" id="copyLink"
                                                href="{{ url('/onemotos/' . auth()->id()) }}"
                                                target="_blank">{{ url('/onemotos/' . auth()->id()) }}</a>
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
                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1"> Link de vendas
                                        One Beauty </div>
                                    <div class="row no-gutters align-items-center">
                                        <div class="col-auto">
                                            <div class="h5 mb-0 mr-3 font-weight-bold text-danger">
                                                <button class="btn btn-outline-danger"
                                                    data-link="{{ url('/onebeauty/' . auth()->id()) }}"
                                                    onclick="copiaLink(this)"><i class="fas fa-copy"></i></button>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <a style="font-size: 15px;" class="text-danger" id="copyLink"
                                                href="{{ url('/onebeauty/' . auth()->id()) }}"
                                                target="_blank">{{ url('/onebeauty/' . auth()->id()) }}</a>
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
                <div class="col-xl-12 col-md-12 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-green text-uppercase mb-1"> Link de vendas One
                                        Positive </div>
                                    <div class="row no-gutters align-items-center">
                                        <div class="col-auto">
                                            <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                                <button class="btn btn-outline-green"
                                                    data-link="{{ url('/onepositive/' . auth()->id()) }}"
                                                    onclick="copiaLink(this)"><i class="fas fa-copy"></i></button>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <a style="font-size: 15px;" class="text-green" id="copyLink"
                                                href="{{ url('/onepositive/' . auth()->id()) }}"
                                                target="_blank">{{ url('/onepositive/' . auth()->id()) }}</a>
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
            </div>
        @endif

        @if (Auth::user()->tipo == 1 || Auth::user()->tipo == 3 || Auth::user()->tipo == 2)
            <div class="row">
                <div class="col-xl-12 col-md-12 mb-4">
                    <div class="card border-left-dark shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Últimas Vendas
                                        <hr>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table class="table table-striped" id="tabela" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>

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
                                                                One Motos
                                                                @break
                                                            @case(11)
                                                                One Motos
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
            </div>
        @endif

        @if (Auth::user()->tipo == 4)
            <div class="row">
                <div class="col-xl-8 col-lg-7">
                    <div style="height: 60vh; overflow: auto;" class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-success">Meus Contratos</h6>
                            <div class="dropdown no-arrow">
                                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                                    aria-labelledby="dropdownMenuLink" style="">
                                    <a class="dropdown-item"  id="exportarContratos">Gerar Excel</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped" id="tabelaContratos" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Produto</th>
                                            <th>Data</th>
                                            <th>Opções</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($vendas as $key => $venda)
                                            <tr>
                                                <td>
                                                    @switch($venda->id_produto)
                                                        @case(2)
                                                            One Positive
                                                        @break

                                                        @case(12)
                                                            One Positive
                                                        @break

                                                        @case(3)
                                                            One Motos
                                                        @break

                                                        @case(11)
                                                            One Motos
                                                        @break

                                                        @case(8)
                                                            One Serviços
                                                        @break

                                                        @default
                                                            Produto Desconhecido
                                                    @endswitch
                                                </td>
                                                <td> {{ \Carbon\Carbon::parse($venda->created_at)->format('d/m/Y') }} </td>
                                                <td>
                                                    <form action="" method="POST">
                                                        <a href="{{ route('relatorioParcelas', ['id' => $venda->id]) }}" class="btn btn-outline-info">Extrato</a>
                                                        @if ($venda->id_produto == 3 || $venda->id_produto == 12)

                                                        @if($venda->total_parcelas_confirmadas >= 3) <button type="button" class="btn btn-outline-success" data-toggle="modal" data-target="#lanceModal{{ $venda->id }}">Ofertar Quitação</button> @endif
                                                        @endif

                                                        @if($venda->total_parcelas_confirmadas <= 2)<button type="button" class="btn btn-outline-success" data-toggle="modal" data-target="#avisoModal">Ofertar Quitação</button>    @endif

                                                    </form>
                                                    <div class="modal fade" id="lanceModal{{ $venda->id }}"
                                                        tabindex="-1" role="dialog" aria-labelledby="lanceModalLabel"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content">
                                                                <form method="POST" action="{{ route('lance') }}">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="lanceModalLabel"> Filtros:</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <input type="hidden" value={{ csrf_token() }} name="_token">
                                                                        <input type="hidden" name="totalPago" value="{{ $venda->total_parcelas_confirmadas_valor }}">
                                                                        <input type="hidden" value="{{ $venda->id }}" name="id">
                                                                        <input type="hidden" id="totalPago{{ $venda->id }}" value="{{ $venda->total_parcelas_confirmadas_valor }}">

                                                                        <div class="row">
                                                                            <div class="col-12">
                                                                                <div class="form-group">
                                                                                    <input type="text" class="form-control" value="Contrato N°: {{ $venda->id }}" readonly>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-12">
                                                                                <div class="form-group">
                                                                                    <input type="text" class="form-control" value="Valor total Pago: {{ number_format($venda->total_parcelas_confirmadas_valor, 2, ',', '.') }}" readonly>
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-12">
                                                                                <div class="form-group">
                                                                                    <input type="number" class="form-control" name="oferta" id="oferta{{$venda->id}}" placeholder="Valor adicional da oferta:" oninput="valorLance({{ $venda->id }})">
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-12">
                                                                                <div class="form-group">
                                                                                    <input type="text" class="form-control" name="lance" id="lance{{ $venda->id }}" placeholder="Oferta Válida" readonly>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-danger m-1" data-dismiss="modal">Fechar</button>
                                                                        <button type="submit" class="btn btn-success m-1">Ofertar Quitação</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="modal fade" id="avisoModal"
                                                        tabindex="-1" role="dialog" aria-labelledby="lanceModalLabel"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content">
                                                                 <div class="modal-header">
                                                                    <h5 class="modal-title" id="lanceModalLabel">Informação</h5>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
                                                                  </div>
                                                                  <div  style="margin:30px;">
                                                                        Caro cliente, você somente poderá fazer uma OFERTA DE QUITAÇÃO, caso a entrada e as três primeiras parcelas de seu contrato estejam pagas.
                                                                </div>
                                                            </div>
                                                        </div>
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

                <div class="col-xl-4 col-lg-5">
                    <div style="height: 60vh; overflow: auto;" class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-success">Oferta de Quitação Válida</h6>
                            <div class="dropdown no-arrow">
                                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                                    aria-labelledby="dropdownMenuLink">
                                    <div class="dropdown-header">Opções:</div>
                                    <a class="dropdown-item" id="exportarLances">Gerar Excel</a>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            @if (session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif
                            <div class="table-responsive">
                                <table class="table table-striped text-center" id="tabelaLances" width="100%"
                                    cellspacing="0">
                                    <thead>
                                        <tr>

                                            <th>Data</th>
                                            <th>Valor da Oferta</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($lances as $key => $lance)
                                            <tr>

                                                <td>{{ $lance->created_at->format('d/m/Y') }}</td>
                                                <td> R$ {{ number_format($lance->pago + $lance->oferta, 2, ',', '.') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!--<div class="col-xl-12 col-lg-12">
                    <div style="height: 50vh; overflow: auto;" class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-success">Últimos Contratos Quitados Por Antecipação</h6>
                            <div class="dropdown no-arrow">
                                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                                    aria-labelledby="dropdownMenuLink" style="">
                                    <a class="dropdown-item"  id="exportarContratos">Gerar Excel</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped" id="tabelaContratos" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Premiação</th>
                                            <th>Cliente</th>
                                            <th>Oferta/Parcelas</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>-->
            </div>

            <script>
                function valorLance(id) {
                    pago = parseFloat($('#totalPago' + id).val());
                    oferta = parseFloat($('#oferta' + id).val());

                    var totalLance = pago + oferta;

                    if(totalLance < 1){
                        $('#lance' + id).val(0);
                    } else {
                        $('#lance' + id).val(totalLance.toFixed(2));
                    }
                }

                $(document).ready(function() {
                    $('#exportarContratos').click(function() {
                        var tabela = document.getElementById('tabelaContratos');
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
                    $('#exportarLances').click(function() {
                        var tabela = document.getElementById('tabelaLances');
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
        @endif
    </div>

@endsection
