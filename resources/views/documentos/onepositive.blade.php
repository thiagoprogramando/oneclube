<?php setlocale(LC_TIME, 'pt_BR.utf8', 'pt_BR', 'portuguese'); $dataAtual = date('Y-m-d'); ?>

<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Content-Style-Type" content="text/css" />
    <meta name="generator" content="Aspose.Words for .NET 23.7.0" />
    <title></title>
    <style type="text/css">
      body {
        line-height: 108%;
        font-family: Calibri;
        font-size: 11pt;
      }
      p {
        margin: 0pt 0pt 8pt;
      }
    </style>
  </head>
  <body>
    <div>
      <p><span style="font-weight: bold">NOME: <?= $data['cliente'] ?></span><span> </span></p>
      <p><span style="font-weight: bold">CPF/CNPJ: <?= $data['cpfcnpj'] ?></span><span> </span></p>
      <p style="line-height: 108%; font-size: 12pt">
        <span style="-aw-import: ignore">&#xa0;</span>
      </p>
      <p style="text-align: center">
        <span style="font-weight: bold">FICHA DE INSCRIÇÃO ASSOCIATIVA / DECLARAÇÃO / AUTORIZAÇÃO</span>
      </p>
      <p><span style="-aw-import: ignore">&#xa0;</span></p>
      <p style="text-align: justify">
        <span> Por meio da presente, venho requerer a minha inscrição como associado
          (a), desta associação. Ao assinar este instrumento, declaro estar
          ciente do inteiro teor do estatuto social da Associação, bem como dos
          direitos e deveres impostos aos membros desta instituição. Declaro que
          consinto com a propositura de Ação de Obrigação de Fazer com Pedido de
          Tutela de Urgência e Indenização por Danos Morais, para defesa de
          direito difuso ou coletivo, em meu nome, movida por esta associação,
          bem como, que me responsabilizo a efetuar os pagamentos acertados
          previamente.</span>
      </p>
      <p style="text-align: justify">
        <span style="-aw-import: ignore">&#xa0;</span>
      </p>
      <p><span style="-aw-import: ignore">&#xa0;</span></p>
      <p>
        <span>Brasil, </span>
        <span style="-aw-import: spaces">&#xa0; </span>
        <span style="-aw-import: spaces">&#xa0; </span>
        <span><?= strftime('%A', strtotime($dataAtual)); ?>, <?= strftime('%d de %B de %Y', strtotime($dataAtual)); ?></span>
      </p>
      <p><span style="-aw-import: ignore">&#xa0;</span></p>
      <p><span style="-aw-import: ignore">&#xa0;</span></p>
      <p><span style="-aw-import: ignore">&#xa0;</span></p>
      <p><span>Assinatura Digital</span></p>
    </div>
  </body>
</html>
