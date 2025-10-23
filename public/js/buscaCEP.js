document.addEventListener('DOMContentLoaded', function() {
    const cepInput = document.getElementById('cep');
    const statusText = document.getElementById('cep-status');

    cepInput.addEventListener('input', async function() {
        let cep = cepInput.value.replace(/\D/g, '');
        if (cep.length < 8) {
            statusText.textContent = '';
            return;
        }

        statusText.textContent = 'Buscando endereço...';
        
        try {
            const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
            const data = await response.json();

            if (data.erro) {
                statusText.textContent = 'CEP não encontrado.';
                return;
            }

            // Preenche os campos
            document.getElementById('endereco').value = data.logradouro || '';
            document.getElementById('bairro').value = data.bairro || '';
            document.getElementById('cidade').value = data.localidade || '';
            document.getElementById('estado').value = data.uf || '';

            statusText.textContent = 'Endereço carregado com sucesso!';
            setTimeout(() => statusText.textContent = '', 2000);
        } catch (error) {
            statusText.textContent = 'Erro ao buscar o CEP.';
            console.error(error);
        }
    });
});

