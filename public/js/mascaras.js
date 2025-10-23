document.addEventListener("DOMContentLoaded", () => {
    // CPF / CNPJ
    const documentoInput = document.querySelector("#documento");
    if (documentoInput) {
        const maskOptions = {
            mask: [
                {
                    mask: "000.000.000-00",
                    maxLength: 11,
                },
                {
                    mask: "00.000.000/0000-00",
                    maxLength: 14,
                },
            ],
            dispatch: function (appended, dynamicMasked) {
                const value = (dynamicMasked.value + appended).replace(
                    /\D/g,
                    ""
                );
                return dynamicMasked.compiledMasks.find(
                    (m) => value.length <= m.maxLength
                );
            },
        };
        IMask(documentoInput, maskOptions);

        // Validação automática ao sair do campo
        const errorMsg = document.createElement("p");
        errorMsg.classList.add("text-sm", "text-red-600", "mt-1");
        documentoInput.insertAdjacentElement("afterend", errorMsg);

        documentoInput.addEventListener("blur", () => {
            const valor = documentoInput.value.replace(/\D/g, "");
            errorMsg.textContent = "";

            if (!valor) return;

            if (valor.length === 11 && !validaCPF(valor)) {
                errorMsg.textContent = "CPF inválido.";
            } else if (valor.length === 14 && !validaCNPJ(valor)) {
                errorMsg.textContent = "CNPJ inválido.";
            } else if (![11, 14].includes(valor.length)) {
                errorMsg.textContent = "Informe um CPF ou CNPJ válido.";
            }
        });
    }

    // CEP
    const cepInput = document.querySelector("#cep");
    if (cepInput) {
        IMask(cepInput, { mask: "00000-000" });
    }

    // Telefone (aceita 8 ou 9 dígitos)
    const telefoneInput = document.querySelector("#telefone");
    if (telefoneInput) {
        IMask(telefoneInput, {
            mask: [{ mask: "(00) 0000-0000" }, { mask: "(00) 00000-0000" }],
        });
    }

    // Funções auxiliares para validação de CPF e CNPJ
    function validaCPF(cpf) {
        if (/^(\d)\1+$/.test(cpf)) return false;
        let soma = 0;
        for (let i = 0; i < 9; i++) soma += parseInt(cpf.charAt(i)) * (10 - i);
        let resto = 11 - (soma % 11);
        if (resto >= 10) resto = 0;
        if (resto !== parseInt(cpf.charAt(9))) return false;

        soma = 0;
        for (let i = 0; i < 10; i++) soma += parseInt(cpf.charAt(i)) * (11 - i);
        resto = 11 - (soma % 11);
        if (resto >= 10) resto = 0;
        return resto === parseInt(cpf.charAt(10));
    }

    function validaCNPJ(cnpj) {
        if (/^(\d)\1+$/.test(cnpj)) return false;
        let tamanho = cnpj.length - 2;
        let numeros = cnpj.substring(0, tamanho);
        let digitos = cnpj.substring(tamanho);
        let soma = 0;
        let pos = tamanho - 7;
        for (let i = tamanho; i >= 1; i--) {
            soma += numeros.charAt(tamanho - i) * pos--;
            if (pos < 2) pos = 9;
        }
        let resultado = soma % 11 < 2 ? 0 : 11 - (soma % 11);
        if (resultado != digitos.charAt(0)) return false;

        tamanho = tamanho + 1;
        numeros = cnpj.substring(0, tamanho);
        soma = 0;
        pos = tamanho - 7;
        for (let i = tamanho; i >= 1; i--) {
            soma += numeros.charAt(tamanho - i) * pos--;
            if (pos < 2) pos = 9;
        }
        resultado = soma % 11 < 2 ? 0 : 11 - (soma % 11);
        return resultado == digitos.charAt(1);
    }
});
