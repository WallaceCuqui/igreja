document.addEventListener("DOMContentLoaded", () => {
    const input = document.querySelector("#documento");
    const semCnpjCheckbox = document.querySelector("#sem_cnpj");

    if (!input) return;

    // Mensagem de erro
    const errorMsg = document.createElement("p");
    errorMsg.classList.add("text-sm", "text-red-600", "mt-1");
    input.insertAdjacentElement("afterend", errorMsg);

    // Campos a controlar
    const campoNomeFantasia = document.querySelector("#campo-nome-fantasia");
    const campoGenero = document.querySelector("#campo-genero");
    const campoDataNascimento = document.querySelector(
        "#campo-data-nascimento"
    );
    const campoIgreja = document.querySelector("#campo-igreja");

    // Funções de validação
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

    if (semCnpjCheckbox && semCnpjCheckbox.checked) {
        input.disabled = true;
        input.placeholder = "Sem CNPJ";
    }

    // Função unificada para validar e mostrar/ocultar campos
    function atualizarCamposDocumento() {
        const semCnpj = semCnpjCheckbox && semCnpjCheckbox.checked;
        const valor = input.value.replace(/\D/g, "");
        errorMsg.textContent = "";

        // Validação
        if (!valor) return;
        if (!semCnpj) {
            if (valor.length === 11 && !validaCPF(valor)) {
                errorMsg.textContent = "CPF inválido.";
            } else if (valor.length === 14 && !validaCNPJ(valor)) {
                errorMsg.textContent = "CNPJ inválido.";
            } else if (![11, 14].includes(valor.length)) {
                errorMsg.textContent = "Informe um CPF ou CNPJ válido.";
            }
        }

        // Mostrar/ocultar campos
        const isCNPJ = valor.length === 14 && !semCnpj;

        if (campoNomeFantasia) {
            campoNomeFantasia.style.display = isCNPJ ? "block" : "none";
            if (!isCNPJ) {
                const nomeInput = campoNomeFantasia.querySelector("input");
                if (nomeInput) nomeInput.value = "";
            }
        }

        if (campoGenero) {
            campoGenero.style.display = isCNPJ ? "none" : "block";
            if (isCNPJ) {
                const generoInput = campoGenero.querySelector("select");
                if (generoInput) generoInput.value = "";
            }
        }

        if (campoDataNascimento) {
            campoDataNascimento.style.display = isCNPJ ? "none" : "block";
            if (isCNPJ) {
                const dataInput = campoDataNascimento.querySelector("input");
                if (dataInput) dataInput.value = "";
            }
        }

        if (campoIgreja) {
            campoIgreja.style.display = isCNPJ ? "none" : "block";
            if (isCNPJ) {
                const igrejaInput =
                    campoIgreja.querySelector("input[type='text']");
                const igrejaHidden = campoIgreja.querySelector(
                    "input[type='hidden']"
                );
                if (igrejaInput) igrejaInput.value = "";
                if (igrejaHidden) igrejaHidden.value = "";
            }
        }
    }

    if (semCnpjCheckbox) {
        semCnpjCheckbox.addEventListener("change", () => {
            if (semCnpjCheckbox.checked) {
                input.value = "";
                input.disabled = true;
                input.placeholder = "Sem CNPJ";
                errorMsg.textContent = "";
            } else {
                input.disabled = false;
                input.placeholder = "Digite o CNPJ da igreja";
            }
            atualizarCamposDocumento();
        });
    }

    // Executa ao carregar, ao digitar e ao sair do campo
    atualizarCamposDocumento();
    input.addEventListener("input", atualizarCamposDocumento);
    input.addEventListener("blur", atualizarCamposDocumento);
});
