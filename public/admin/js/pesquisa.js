function formatarData(data) {
    var dataObj = new Date(data);

    var dia = dataObj.getUTCDate().toString().padStart(2, '0');
    var mes = (dataObj.getUTCMonth() + 1).toString().padStart(2, '0');
    var ano = dataObj.getUTCFullYear().toString();

    return dia + '/' + mes + '/' + ano;
}

function pesquisaCPFCNPJ() {
    let cpfcnpj = $('input[name=cpfcnpj]').val();
    let dataNascimento = $('input[name=dataNascimento]').val();
    if (cpfcnpj.length > 13) {
        $.ajax({
            url: "http://ws.hubdodesenvolvedor.com.br/v2/cnpj/?cnpj=" + cpfcnpj + "&token=124678250wDRJmrCEXu225102800",
            method: 'GET',
            complete: function (xhr) {

                response = xhr.responseJSON;

                if (response.return == 'OK') {
                    response = response.result;
                    $('#pesquisa').addClass('d-none');
                    $('#cadastro').removeClass('d-none');

                    $('#cliente').val(response.fantasia);
                    $('#cpfcnpj').val(response.numero_de_inscricao);
                    $('#situacao').val(response.situacao);
                    $('#dataNascimento').val(response.dt_situacao_cadastral);
                } else {
                    alert('Erro ao pesquisar documento!');
                }
            }
        });
    } else {
        $.ajax({
            url: "https://ws.hubdodesenvolvedor.com.br/v2/cpf/?cpf=" + cpfcnpj + "&data=" + formatarData(dataNascimento) + "&token=124678250wDRJmrCEXu225102800",
            method: 'GET',
            complete: function (xhr) {

                response = xhr.responseJSON;
                console.log(xhr);

                if (response.return == 'OK') {
                    response = response.result;
                    $('#pesquisa').addClass('d-none');
                    $('#cadastro').removeClass('d-none');

                    $('#cliente').val(response.nome_da_pf);
                    $('#cpfcnpj').val(response.numero_de_cpf);
                    $('#situacao').val(response.situacao_cadastral);
                    $('#dataNascimento').val(response.data_nascimento);

                } else {
                    alert('Erro ao pesquisar documento!');
                }
            }
        });
    }
}

function copiaLink(botao) {
    var link = botao.getAttribute('data-link');
    var tempInput = document.createElement('input');
    tempInput.value = link;
    document.body.appendChild(tempInput);
    tempInput.select();
    tempInput.setSelectionRange(0, 99999);
    document.execCommand('copy');
    document.body.removeChild(tempInput);

    Swal.fire(
        'Sucesso!',
        'Link de pagamento copiado!',
        'success'
    )
}

document.addEventListener("DOMContentLoaded", function() {
    const gerarLinks = document.querySelectorAll(".gerar-link");

    gerarLinks.forEach(function(gerarLink) {
        gerarLink.addEventListener("click", function(e) {
            e.preventDefault();

            const url = gerarLink.getAttribute("data-url");
            const produto = gerarLink.getAttribute("data-produto");

            Swal.fire({
                title: 'Valor da entrada',
                input: 'number',
                inputAttributes: {
                    min: produto === '2' ? 970 : (produto === '3' ? 1000 : 1000),
                    placeholder: `Valor mínimo de ${produto === '2' ? 'venda' : 'entrada'} é ${produto === '2' ? 970 : 1000}`
                },
                showCancelButton: true,
                confirmButtonText: 'Gerar Link',
                cancelButtonText: 'Cancelar',
            }).then((result) => {
                if (result.isConfirmed) {
                    const entrada = result.value;
                    const linkGerado = `${url}/${entrada}`;

                    Swal.fire({
                        title: 'Link gerado',
                        html: `<input class="form-control form-control-user mb-2" type="text" value="${linkGerado}" id="linkGeradoInput" readonly>
                                    <button class="btn btn-success" onclick="copiarLink()">Copiar</button>`,
                        showCancelButton: false,
                        showConfirmButton: false,
                    });
                } else {
                    Swal.fire('Operação cancelada!', '', 'info');
                }

            });
        });
    });
});

function copiarLink() {
    const linkInput = document.getElementById("linkGeradoInput");
    linkInput.select();
    document.execCommand("copy");
    Swal.fire({
        text: 'Link copiado para a área de transferência',
        icon: 'success',
    });
}
