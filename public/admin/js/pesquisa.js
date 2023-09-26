function formatarData(data) {
    var dataObj = new Date(data);

    var dia = dataObj.getUTCDate().toString().padStart(2, '0');
    var mes = (dataObj.getUTCMonth() + 1).toString().padStart(2, '0');
    var ano = dataObj.getUTCFullYear().toString();

    return dia + '/' + mes + '/' + ano;
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

function consultarEndereco() {
    const cep = document.getElementById('cep').value;
    fetch(`https://viacep.com.br/ws/${cep}/json/`)
        .then(response => response.json())
        .then(data => {
            if (data.erro) {
                console.log('CEP não encontrado');
            } else {
                const cidade    = data.localidade + '/' + data.uf;
                const endereco  = data.logradouro;
                const bairro    = data.bairro;
                const estado    = data.uf;

                // Preencher campos de cidade e endereço
                document.getElementById('cidade').value = cidade;
                document.getElementById('endereco').value = endereco;
                document.getElementById('bairro').value = bairro;
                document.getElementById('estado').value = estado;
            }
        })
        .catch(error => console.log(error));
}
