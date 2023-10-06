function formatarData(data) {
    var dataObj = new Date(data);

    var dia = dataObj.getUTCDate().toString().padStart(2, '0');
    var mes = (dataObj.getUTCMonth() + 1).toString().padStart(2, '0');
    var ano = dataObj.getUTCFullYear().toString();

    return dia + '/' + mes + '/' + ano;
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

                $('#cidade').val(cidade);
                $('#endereco').val(endereco);
                $('#bairro').val(bairro);
                $('#estado').val(estado);
            }
        })
        .catch(error => console.log(error));
}
