@extends('dashboard/layout')
    @section('conteudo')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Minhas Parcelas</h1>
        </div>

        @if (session('link'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Sucesso!',
                    html: '<input type="text" id="linkInput" value="{{ session('link') }}" readonly><button id="openLinkButton" class="btn btn-success">Abrir Link</button>',
                    showConfirmButton: false,
                    showCancelButton: false,
                    focusConfirm: false,
                });

                const openLinkButton = document.getElementById('openLinkButton');
                openLinkButton.addEventListener('click', () => {
                    const linkInput = document.getElementById('linkInput').value;
                    window.open(linkInput, '_blank');
                });
            </script>
        @endif

        @if (session('mensagemErro'))
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: '{{ session('mensagemErro') }}',
                    showConfirmButton: true
                });
            </script>
        @endif

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
                                                <th>Vencimento</th>
                                                <th>Status</th>
                                                <th>Valor</th>
                                                <th>Opções</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($parcelas as $key => $parcela)
                                            <tr>
                                                {{-- <td class="text-center" type="hidden"> {{ $parcela->venda_id }} </td> --}}
                                                <td> {{ \Carbon\Carbon::parse($parcela->vencimento)->format('d/m/Y') }} </td>
                                                <td>
                                                    @switch($parcela->status)
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
                                                <td> R$ {{ number_format($parcela->valor, 2, ',', '.') }} </td>
                                                <td>
                                                    @if($parcela->status == 'PENDING_PAY' && $parcela->id_assas == null)
                                                        <form action="{{ route('geraAssasParcela') }}" method="post">
                                                            <input type="hidden" value={{  csrf_token() }} name="_token">
                                                            <input type="hidden" name="id" value="{{ $parcela->id }}">
                                                            <input type="hidden" name="valor" value="{{ $parcela->valor }}">
                                                            <input type="hidden" name="cpf" value="{{ $parcela->cpf }}">
                                                            <button type="submit" class="btn btn-outline-success">Pagar</button>
                                                        </form>
                                                    @else
                                                        <?php
                                                            $id_pay = str_replace('pay_', '', $parcela->id_assas);
                                                        ?>
                                                        <a href="https://www.asaas.com/i/{{ $id_pay }}" target="_blank" class="btn btn-outline-success">Ver Fatura</a>
                                                    @endif
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
