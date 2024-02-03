function mascaraCpfCnpj(input) {
    let value = input.value;
    value = value.replace(/\D/g, '');

    if (value.length <= 11) {
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    } else {
        value = value.replace(/^(\d{2})(\d)/, '$1.$2');
        value = value.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
        value = value.replace(/\.(\d{3})(\d)/, '.$1/$2');
        value = value.replace(/(\d{4})(\d)/, '$1-$2');
    }

    input.value = value;
}

function mascaraData(dataInput) {
    let data = dataInput.value;
    data = data.replace(/\D/g, '');
    data = data.replace(/(\d{2})(\d)/, '$1-$2')
    data = data.replace(/(\d{2})(\d)/, '$1-$2');
    dataInput.value = data;
}

function mascaraTelefone(telefoneInput) {
    let telefone = telefoneInput.value;
    telefone = telefone.replace(/\D/g, '');
    telefone = telefone.replace(/(\d{2})(\d)/, '($1) $2');
    telefone = telefone.replace(/(\d{5})(\d)/, '$1-$2');
    telefoneInput.value = telefone;
}

function mascaraReal(input) {
    let value = input.value;
    
    value = value.replace(/\D/g, '');
    value = (parseInt(value) / 100).toFixed(2);
    value = value.replace('.', ',');
    value = value.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');

    input.value = value;
}

function consultaCEP() {
    var cep = $('[name="postalCode"]').val();

    cep = cep.replace(/\D/g, '');

    if (/^\d{8}$/.test(cep)) {

        cep = cep.replace(/(\d{5})(\d{3})/, '$1-$2');
        $.get(`https://viacep.com.br/ws/${cep}/json/`, function (data) {
            $('[name="address"]').val(data.logradouro);
            $('[name="complement"]').val(data.complemento);
            $('[name="province"]').val(data.bairro);
            $('[name="city"]').val(data.localidade);
            $('[name="state"]').val(data.uf);
        })
            .fail(function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Atenção!',
                    text: `Não encontramos dados com o CEP informado, verifique!`,
                })
            });
    } else {
        Swal.fire({
            icon: 'error',
            title: 'Atenção!',
            text: `Formato de CEP inválido!`,
        })
    }
}