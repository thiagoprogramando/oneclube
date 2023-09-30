<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/png" href="{{ asset('admin/assets/icon.png') }}" />

    <title>One Clube</title>

    <link href="{{ asset('admin/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="{{ asset('admin/css/sb-admin-2.css') }}" rel="stylesheet">
    <script src="{{ asset('admin/vendor/jquery/jquery.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.2/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body id="page-top">
    <div id="wrapper">

        <ul class="navbar-nav bg-gradient-success sidebar sidebar-dark accordion" id="accordionSidebar">

            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="/dashboard">
                <div class="sidebar-brand-text mx-3"> @if(Auth::check() && Auth::user()->tipo == 4)
                    One Autos
                @else
                    One Clube
                @endif
                </div>
            </a>

            <hr class="sidebar-divider my-0">

            <li class="nav-item active">
                <a class="nav-link" href="/dashboard"> <i class="fas fa-fw fa-chart-area"></i>
                    <span>Painel Principal</span></a>
            </li>

            @if (Auth::user()->tipo == 3)
                <hr class="sidebar-divider">

                <div class="sidebar-heading">
                    Negócios
                </div>

                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseMotos"
                        aria-expanded="true" aria-controls="collapseMotos">
                        <i class="fa fa-motorcycle"></i>
                        <span>One Motos</span>
                    </a>
                    <div id="collapseMotos" class="collapse" aria-labelledby="headingMotos"
                        data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            <a class="collapse-item" href="/vendas/11">Minhas Vendas</a>
                            <a class="collapse-item gerar-link-one-motos" href="#" data-produto="3"
                                data-url="{{ url('/associadonemotos/' . auth()->id()) }}">Gerar Link de Venda</a>
                        </div>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePositive"
                        aria-expanded="true" aria-controls="collapsePositive">
                        <i class="fa fa-check"></i>
                        <span>One Positive</span>
                    </a>
                    <div id="collapsePositive" class="collapse" aria-labelledby="headingPositive"
                        data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            <a class="collapse-item" href="/vendas/12">Minhas Vendas</a>
                            <a class="collapse-item gerar-link" href="#" data-produto="2"
                                data-url="{{ url('/associadonepositive/' . auth()->id()) }}">Gerar Link de Venda</a>
                        </div>
                    </div>
                </li>
            @elseif (Auth::user()->tipo == 1 || Auth::user()->tipo == 2)
                <hr class="sidebar-divider">

                <div class="sidebar-heading">
                    Negócios
                </div>

                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseMotos"
                        aria-expanded="true" aria-controls="collapseMotos">
                        <i class="fa fa-motorcycle"></i>
                        <span>One Motos</span>
                    </a>
                    <div id="collapseMotos" class="collapse" aria-labelledby="headingMotos"
                        data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            <a class="collapse-item" href="/vendas/3">Minhas Vendas</a>
                            <a class="collapse-item" href="{{ url('/onemotos/' . auth()->id()) }}"
                                target="_BLANK">Vender</a>
                        </div>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseBeauty"
                        aria-expanded="true" aria-controls="collapseBeauty">
                        <i class="fa fa-flask"></i>
                        <span>One Beauty</span>
                    </a>
                    <div id="collapseBeauty" class="collapse" aria-labelledby="headingBeauty"
                        data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            <a class="collapse-item" href="/vendas/1">Minhas Vendas</a>
                            <a class="collapse-item" href="{{ url('/onebeauty/' . auth()->id()) }}"
                                target="_BLANK">Vender</a>
                        </div>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse"
                        data-target="#collapsePositive" aria-expanded="true" aria-controls="collapsePositive">
                        <i class="fa fa-check"></i>
                        <span>One Positive</span>
                    </a>
                    <div id="collapsePositive" class="collapse" aria-labelledby="headingPositive"
                        data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            <a class="collapse-item" href="/vendas/2">Minhas Vendas</a>
                            <a class="collapse-item" href="{{ url('/onepositive/' . auth()->id()) }}"
                                target="_BLANK">Vender</a>
                        </div>
                    </div>
                </li>
            @endif

            @if (Auth::user()->tipo == 4)
                <hr class="sidebar-divider">

                <div class="sidebar-heading">
                    Extrato e Faturas
                </div>

                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse"
                        data-target="#collapseGestao" aria-expanded="true" aria-controls="collapseGestao">
                        <i class="fa fa-clipboard-list"></i>
                        <span>Contratos</span>
                    </a>
                    <div id="collapseGestao" class="collapse" aria-labelledby="headingGestao"
                        data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            <a class="collapse-item" href="/relatorioContratos">Meus Contratos</a>
                            <a class="collapse-item" href="/relatorioParcelas">Minhas Parcelas</a>
                            <a class="collapse-item" href="/dashboard">Ofertar Quitação</a>
                        </div>
                    </div>
                </li>
            @endif

            @if (Auth::user()->tipo == 4)
                <hr class="sidebar-divider">

                <div class="sidebar-heading">
                    Clube de Benefícios
                </div>

                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseBenefícios" aria-expanded="true" aria-controls="collapseBenefícios">
                        <i class="fa fa-handshake"></i>
                        <span>Benefícios</span>
                    </a>
                    <div id="collapseBenefícios" class="collapse" aria-labelledby="headingBenefícios" data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            <a class="collapse-item" data-toggle="modal" data-target="#telemedicinaModal">Telemedicina | ONE MED</a>
                            <a class="collapse-item" data-toggle="modal" data-target="#medicamentosModal">Medicamentos | ONE MED</a>
                            <a class="collapse-item" data-toggle="modal" data-target="#descontosModal">Descontos | ONE GOOD</a>
                        </div>
                    </div>
                </li>
            @endif

            @if (Auth::user()->tipo == 2)
                <hr class="sidebar-divider">

                <div class="sidebar-heading">
                    Gestão
                </div>

                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse"
                        data-target="#collapseGestao" aria-expanded="true" aria-controls="collapseGestao">
                        <i class="fa fa-clipboard-list"></i>
                        <span>Relatórios</span>
                    </a>
                    <div id="collapseGestao" class="collapse" aria-labelledby="headingGestao"
                        data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            <a class="collapse-item" href="/relatorioVendas">Vendas</a>
                            <a class="collapse-item" href="/relatorioUsuarios">Usuários</a>
                            <a class="collapse-item" href="/relatorioPremiados">Premiados</a>
                           
                        </div>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseLinks"
                        aria-expanded="true" aria-controls="collapseLinks">
                        <i class="fa fa-link"></i>
                        <span>Links</span>
                    </a>
                    <div id="collapseLinks" class="collapse" aria-labelledby="headingGestao"
                        data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            <a class="collapse-item" target="_blank" href="/registerAssociado">Associados</a>
                        </div>
                    </div>
                </li>
            @endif

            <hr class="sidebar-divider d-none d-md-block">

            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">

                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <div
                        class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                        <p>Olá,
                            @if (Auth::user()->login)
                                {{ Auth::user()->login }}
                            @else
                                {{ Auth::user()->nome }}
                            @endif
                            . Bem-vindo(a)!
                        </p>
                    </div>

                    <ul class="navbar-nav ml-auto">
                        <div class="topbar-divider d-none d-sm-block"></div>

                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img class="img-profile rounded-circle"
                                    src="{{ asset('admin/assets/perfil_padrao.svg') }}">
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="{{ route('perfil') }}">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Perfil
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('logout') }}">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Sair
                                </a>
                            </div>
                        </li>
                    </ul>

                </nav>

                @yield('conteudo')

            </div>

            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto"> <span>Copyright &copy; One Clube</span> </div>
                </div>
            </footer>

        </div>

    </div>

    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <div class="modal fade" id="telemedicinaModal" tabindex="-1" role="dialog" aria-labelledby="telemedicinaModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="telemedicinaModal">Telemedicina</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
                </div>
                <div class="modal-body">
                    <p class="p">
                        Olá! Associado <strong>ONE CLUBE!</strong>  <br> <br>

                        Seu plano de assistência saúde <strong>ONE MED</strong> agora tem muito mais benefícios a lhe oferecer! <br> <br>
                        Também melhoramos a forma como você será atendido e como solucionar suas eventuais dúvidas, com uma equipe pronta para lhe atender via WhatsApp ou 0800. <br> <br>
                        Agora, além das consultas via <strong>TELEMEDICINA</strong>, você terá apoio para marcar consultas presenciais, sempre com muito desconto! <br> <br>
                        E novidade, incluímos nas especialidades médicas as consultas com <strong>PSICÓLOGO e PSIQUIATRA.</strong> <br> <br>
                        Conheça todas as novidades acessando nosso novo site da <strong>ONE MED!</strong>
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                    <a href="https://clubecertosaude.com.br/saude/onemed/index.php" target="_blank" class="btn btn-success">Acessar Agora</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="medicamentosModal" tabindex="-1" role="dialog" aria-labelledby="medicamentosModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="medicamentosModal">Medicamentos</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
                </div>
                <div class="modal-body">
                    <p class="p">
                        Olá Associado <strong>ONE CLUBE!</strong> <br> <br>
                        Agora você conta com mais um serviço essencial para seu dia a dia, que é a possibilidade de <strong>COMPRAR MEDICAMENTOS GENÉRICOS PELO PREÇO DE CUSTO!</strong> <br> <br>
                        Para ter acesso ao serviço, basta manter em dia a manutenção mensal de nosso clube e falar com um de nossos atendentes no <strong>WhatsApp (11) 5196-6368</strong> <br> <br>
                        Nesse número, após a confirmação de seus dados, você vai fornecer o nome do medicamento que precisa, receberá a cotação, valor do frete e, tomando a decisão de comprar, receberá o link para pagamento, tudo rápido e sem burocracia. <br> <br>
                        OBS.: <strong>O atendimento acontece em dias úteis, das 8:30 às 17:00 horas.</strong> Em horários de pico, a cotação final dos medicamentos pode levar até 25 min para ser respondida. <br> <br>
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="descontosModal" tabindex="-1" role="dialog" aria-labelledby="descontosModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="descontosModal">Descontos</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
                </div>
                <div class="modal-body">
                    <p class="p">
                        Olá ! <br> <br>
                        Bem vindo ao nosso serviço ONE GOOD, aqui você poderá usufruir de DESCONTOS e RECEBER CASHBACK em milhares de produtos! <br> <br>
                        Para isso você precisa baixar o APP da CLUBE CERTO e fazer seu cadastro. <br> <br>
                        Com o APP CLUBE CERTO sua experiência ficará muito melhor, caso tenha dúvida sobre a forma de cadastrar no APP, em tutoriais há um vídeo explicativo do passo a passo. <br> <br>
                        Você também pode acessar os serviços através do site abaixo. <br> <br>
                        <a href="https://clubecerto.com.br/hotsite/?utm_cc=acessodireto&ent=oneclube" target="_blank"> Seu Clube de Descontos - Clube Certo </a> <br> <br>
                        Links para baixar o APP CLUBE CERTO: <br> <br>
                        <a href="https://apps.apple.com/br/app/clube-certo/id1662239139" target="_blank">iOS</a> | <a href="https://play.google.com/store/apps/details?id=com.devusama.clubecerto" target="_blank">Android</a> <br> <br>
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('admin/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('admin/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('admin/js/sb-admin-2.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('admin/js/pesquisa.js') }}"></script>

</body>

</html>
