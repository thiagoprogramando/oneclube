<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/png" href="{{ asset('admin/assets/logo.png') }}" />

    <title>G7 - Assessoria</title>

    <link href="{{ asset('admin/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('admin/css/sb-admin-2.css') }}" rel="stylesheet">
    <script src="{{ asset('admin/vendor/jquery/jquery.min.js') }}"></script>

    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.2/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body id="page-top">
    <div id="wrapper">

        <ul class="navbar-nav bg-gradient-success sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="/dashboard">
                <div class="sidebar-brand-icon d-md-none d-lg-none d-xl-none">
                    <img class="img-responsive w-100" src="{{ asset('admin/assets/logo_menu.png') }}">
                </div>
                <div class="sidebar-brand-text mx-3">
                    <img class="img-responsive w-100" src="{{ asset('admin/assets/logo_menu.png') }}">
                </div>
            </a>

            <hr class="sidebar-divider my-0">

            <li class="nav-item active">
                <a class="nav-link" href="/dashboard"> <i class="fas fa-fw fa-chart-area"></i> <span>Painel Principal</span> </a>
            </li>

            <hr class="sidebar-divider">

            <div class="sidebar-heading"> Negócios </div>

            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePositive" aria-expanded="true" aria-controls="collapsePositive">
                    <i class="fa fa-check"></i>
                    <span>Limpa Nome</span>
                </a>
                <div id="collapsePositive" class="collapse" aria-labelledby="headingPositive" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item dinamic-sale" href="" data-produto="1" data-trava="390">Limpa Nome</a>
                        <a class="collapse-item" href="{{ route('sales', ['produto' => 1]) }}">Minhas Vendas</a>
                        <a class="collapse-item" href="{{ route('listMkt') }}">Material e MKT</a>
                    </div>
                </div>
            </li>

            <div class="sidebar-heading"> Financeiro </div>

            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseWallet" aria-expanded="true" aria-controls="collapseWallet">
                    <i class="fas fa-wallet"></i>
                    <span>Carteira</span>
                </a>
                <div id="collapseWallet" class="collapse" aria-labelledby="headingWallet" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="{{ route('wallet') }}">Carteira Digital</a>
                    </div>
                </div>
            </li>

            <!--<li class="nav-item">
                <a class="nav-link" href="{{ route('cursos') }}"><i class="fas fa-fw fa-user-graduate"></i> <span>Cursos</span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('listUsers') }}"><i class="fas fa-fw fa-user"></i> <span>Indicações</span></a>
            </li>-->

            <li class="nav-item">
                <a class="nav-link" href="https://consultas.grupo7assessoria.com" target="_blank"><i class="fas fa-fw fa-clipboard-list"></i> <span>Consultas</span></a>
            </li>

            @if (Auth::user()->type == 1)
                <hr class="sidebar-divider">

                <div class="sidebar-heading">
                    Gestão
                </div>

                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseGestao" aria-expanded="true" aria-controls="collapseGestao">
                        <i class="fa fa-clipboard-list"></i>
                        <span>Módulos</span>
                    </a>
                    <div id="collapseGestao" class="collapse" aria-labelledby="headingGestao" data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            <a class="collapse-item" href="{{ route('saleManager') }}">Vendas</a>
                            <a class="collapse-item" href="{{ route('listUsers') }}">Usuários</a>
                            <a class="collapse-item" href="{{ route('mkt') }}">Material e MKT</a>
                            <a class="collapse-item" href="{{ route('lista') }}">Lista</a>
                        </div>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseVenda" aria-expanded="true" aria-controls="collapseVenda">
                        <i class="fa fa-check"></i>
                        <span>Vendas Dinâmicas</span>
                    </a>
                    <div id="collapseVenda" class="collapse" aria-labelledby="headingGestao" data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            <a class="collapse-item dinamic-open" href="" data-produto="1" data-trava-mae="300">Limpa Nome</a>
                        </div>
                    </div>
                </li>
            @endif

            <hr class="sidebar-divider d-none d-md-block">

            <div class="text-center d-none d-md-inline"> <button class="rounded-circle border-0" id="sidebarToggle"></button> </div>
        </ul>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">

                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3"> <i class="fa fa-bars"></i> </button>

                    <div class="d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                        <h4 class="small font-weight-bold mt-2">
                            <i class="fas fa-gem text-primary"></i>
                            R$ @if(isset($accumulated)) {{ number_format($accumulated, 2, ',', '.') }} @else 0 @endif -> 
                            R$ {{ number_format($ranking['alvo'], 2, ',', '.') }} 
                            <span class="float-right d-none">{{ number_format($ranking['porcentagem'], 2, ',', '.') }}%</span>
                        </h4>
                        <div class="progress mb-4">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $ranking['porcentagem'] }}%" aria-valuenow="{{ $ranking['porcentagem'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>  
                    </div>

                    <ul class="navbar-nav ml-auto">
                        <div class="topbar-divider d-none d-sm-block"></div>
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img class="img-profile rounded-circle" src="{{ asset('admin/assets/perfil_padrao.svg') }}">
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="{{ route('profile') }}"> <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i> Perfil </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('logout') }}"> <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i> Sair </a>
                            </div>
                        </li>
                    </ul>
                </nav>

                @yield('conteudo')

            </div>

            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto"> <span>Copyright &copy; G7</span> </div>
                </div>
            </footer>

        </div>
    </div>

    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Sucesso!',
                text: `{{ session('success') }}`,
            })
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Atenção',
                text: `{{ session('error') }}`,
            })
        </script>
    @endif

    <script src="{{ asset('admin/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('admin/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('admin/js/sb-admin-2.min.js') }}"></script>
    <script src="{{ asset('admin/js/consulta.js') }}"></script>
    <script>
        $('#gerarExcel').click(function() {

            var tabela = document.getElementById('tabela');
            var wb = XLSX.utils.table_to_book(tabela, {
                sheet: 'Sheet 1'
            });
            var wbout = XLSX.write(wb, {
                bookType: 'xlsx',
                type: 'binary'
            });

            function s2ab(s) {
                var buf = new ArrayBuffer(s.length);
                var view = new Uint8Array(buf);
                for (var i = 0; i < s.length; i++) {
                    view[i] = s.charCodeAt(i) & 0xFF;
                }
                return buf;
            }

            var blob = new Blob([s2ab(wbout)], {
                type: 'application/octet-stream'
            });
            var url = URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = 'tabela.xlsx';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            setTimeout(function() {
                URL.revokeObjectURL(url);
            }, 100);
        });

        document.addEventListener('DOMContentLoaded', function() {
            var vendaLink = document.querySelector('.dinamic-sale');

            vendaLink.addEventListener('click', function(e) {
                e.preventDefault();

                var dataTrava = parseFloat(vendaLink.getAttribute('data-trava'));

                Swal.fire({
                    title: 'Informe o valor',
                    input: 'text',
                    showCancelButton: true,
                    confirmButtonText: 'Próximo',
                    cancelButtonText: 'Cancelar',
                }).then((result) => {
                    if (result.isConfirmed) {
                        var valorInserido = parseFloat(result.value);

                        // Verifica se data-trava existe e se o valor é maior ou igual
                        if (!isNaN(valorInserido) && (!dataTrava || valorInserido >= dataTrava)) {
                            var novaURL = '{{ url("/limpanome/") }}/{{ auth()->id() }}/' + valorInserido;

                            Swal.fire({
                                title: 'Nova URL gerada:',
                                input: 'text',
                                inputValue: novaURL,
                                showCancelButton: true,
                                confirmButtonText: 'Copiar URL',
                                cancelButtonText: 'Cancelar',
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    var inputElement = document.querySelector('.swal2-input');
                                    inputElement.select();
                                    document.execCommand('copy');
                                    Swal.fire('Link copiado com sucesso!', '', 'success');
                                }
                            });
                        } else {
                            Swal.fire('Valor inserido inválido.', '', 'error');
                        }
                    }
                });
            });

            var vendaLivre = document.querySelector('.dinamic-open');

            vendaLivre.addEventListener('click', function(e) {
                e.preventDefault();

                var dataTrava = parseFloat(vendaLink.getAttribute('data-trava-mae'));

                Swal.fire({
                    title: 'Informe o valor',
                    input: 'text',
                    showCancelButton: true,
                    confirmButtonText: 'Próximo',
                    cancelButtonText: 'Cancelar',
                }).then((result) => {
                    if (result.isConfirmed) {
                        var valorInserido = parseFloat(result.value);

                        if (!isNaN(valorInserido) && (!dataTrava || valorInserido >= dataTrava)) {
                            var novaURL = '{{ url("/limpanome/") }}/{{ auth()->id() }}/' + valorInserido;

                            Swal.fire({
                                title: 'Nova URL gerada:',
                                input: 'text',
                                inputValue: novaURL,
                                showCancelButton: true,
                                confirmButtonText: 'Copiar URL',
                                cancelButtonText: 'Cancelar',
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    var inputElement = document.querySelector('.swal2-input');
                                    inputElement.select();
                                    document.execCommand('copy');
                                    Swal.fire('Link copiado com sucesso!', '', 'success');
                                }
                            });
                        } else {
                            Swal.fire('Valor inserido inválido.', '', 'error');
                        }
                    }
                });
            });
        });

    </script>

</body>

</html>
