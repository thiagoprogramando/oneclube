@extends('dashboard.layout')
@section('conteudo')
    <div class="container-fluid">

        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Painel Principal</h1>
        </div>

        <div class="row">
            <div class="col-xl-3 col-md-3 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-green text-uppercase mb-1">
                                    Saldo (Disponível para saque)</div>
                                <div class="h5 mb-0 font-weight-bold text-green">R$ {{ number_format($balance, 2, ',', '.') }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-3 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Saldo (Recebíveis)</div>
                                <div class="h5 mb-0 font-weight-bold text-warning">R$ {{ number_format($statistics, 2, ',', '.') }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-3 mb-4">
                <div class="card border-left-dark shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                    Saldo (Acumulado)</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">R$ {{ number_format($accumulated, 2, ',', '.') }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-3 mb-4">
                <div class="card border-left-dark shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                   Próxima Lista @if(isset($lista)) ({{ \Carbon\Carbon::parse($lista->dateEnd)->format('d/m/Y') }}) @endif</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="contador">

                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-stopwatch fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-8 col-md-8 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Últimas Vendas</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="tabela" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Cliente</th>
                                                <th class="text-center">Situação Contrato</th>
                                                <th class="text-center">Status</th>
                                                <th class="text-center">Data</th>
                                                <th class="text-center">Opções</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($sales as $key => $sale)
                                                <tr>
                                                    <td>{{ $sale->id }}</td>
                                                    <td title="{{ $sale->name }}">{{ strlen($sale->name) > 15 ? substr($sale->name, 0, 15) . '...' : $sale->name }}</td>
                                                    <td class="text-center">
                                                        @switch($sale->status_produto)
                                                            @case('doc_signed')
                                                                Assinado
                                                                @break
                                                            @case('null')
                                                                Aguardando Assinatura
                                                                @break
                                                            @default
                                                                Aguardando Assinatura
                                                                @break
                                                        @endswitch
                                                    </td>
                                                    <td class="text-center">
                                                        @switch($sale->status_pay)
                                                            @case('PAYMENT_CONFIRMED')
                                                                Aprovado
                                                            @break

                                                            @case('PENDING_PAY')
                                                                Aguardando Pagamento
                                                            @break

                                                            @default
                                                                Aguardando Pagamento
                                                        @endswitch
                                                    </td>
                                                    <td class="text-center"> {{ \Carbon\Carbon::parse($sale->created_at)->format('d/m/Y') }} </td>
                                                    <td class="text-center">
                                                        <a class="btn btn-outline-primary" href="{{ route('invoices', ["id"=> $sale->id]) }}" target="_blank"> <i class="fa fa-credit-card"></i> </a>
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

            <div class="col-xl-4 col-md-4 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">RANKING</h6>
                    </div>
                    <div class="card-body">
                        <h4 class="small font-weight-bold">R$ 0 - R$ 11.000,00 <span class="float-right">{{ number_format($ranking['eleventhousand'], 2, ',', '.') }}%</span></h4>
                        <div class="progress mb-4">
                            <div class="progress-bar bg-danger" role="progressbar" style="width: {{$ranking['eleventhousand']}}%" aria-valuenow="{{$ranking['eleventhousand']}}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <h4 class="small font-weight-bold">R$ 11.000,00 - R$ 30.000,00 <span class="float-right">{{ number_format($ranking['thirtythousand'], 2, ',', '.') }}%</span></h4>
                        <div class="progress mb-4">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{$ranking['thirtythousand']}}%" aria-valuenow="{{$ranking['thirtythousand']}}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <h4 class="small font-weight-bold">R$ 31.000,00 - R$ 50.000,00 <span class="float-right">{{ number_format($ranking['fiftythousand'], 2, ',', '.') }}%</span></h4>
                        <div class="progress mb-4">
                            <div class="progress-bar bg-info" role="progressbar" style="width: {{$ranking['fiftythousand']}}%" aria-valuenow="{{$ranking['fiftythousand']}}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <h4 class="small font-weight-bold">R$ 51.000,00 - R$ 100.000,00 <span class="float-right">{{ number_format($ranking['hundredthousand'], 2, ',', '.') }}%</span></h4>
                        <div class="progress mb-4">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{$ranking['hundredthousand']}}%" aria-valuenow="{{$ranking['hundredthousand']}}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <h4 class="small font-weight-bold">R$ 101.000,00 - R$ 300.000,00 <span class="float-right">{{ number_format($ranking['threehundredthousand'], 2, ',', '.') }}%</span></h4>
                        <div class="progress mb-4">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{$ranking['threehundredthousand']}}%" aria-valuenow="{{$ranking['threehundredthousand']}}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <h4 class="small font-weight-bold">R$ 301.000,00 - R$ 500.000,00 <span class="float-right">{{ number_format($ranking['fivehundredthousand'], 2, ',', '.') }}%</span></h4>
                        <div class="progress mb-4">
                            <div class="progress-bar bg-info" role="progressbar" style="width: {{$ranking['fivehundredthousand']}}%" aria-valuenow="{{$ranking['fivehundredthousand']}}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <h4 class="small font-weight-bold">R$ 500.000,00 - R$ 1000.000,00 <span class="float-right">{{ number_format($ranking['amillion'], 2, ',', '.') }}%</span></h4>
                        <div class="progress mb-4">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{$ranking['amillion']}}%" aria-valuenow="{{$ranking['amillion']}}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    @if(isset($lista))
    <script>
        // Data final da contagem regressiva
        var dataFinal = moment("{{ $lista->dateEnd }}");
    
        function atualizarContador() {
            // Data atual
            var dataAtual = moment();
    
            // Calcula a diferença entre as datas
            var diferenca = moment.duration(dataFinal.diff(dataAtual));
    
            // Atualiza a exibição com dias e horas
            var dias = diferenca.days();
            var horas = diferenca.hours();
    
            // Atualiza a exibição
            document.getElementById('contador').innerHTML = dias + 'D & ' + horas + 'H';
        }
    
        // Atualiza o contador a cada segundo
        setInterval(atualizarContador, 1000);
    
        // Chama a função inicialmente para exibir o valor imediatamente
        atualizarContador();
    </script>
    @endif
    

@endsection
