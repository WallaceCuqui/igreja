document.addEventListener("DOMContentLoaded", function () {
    const inputBusca = document.getElementById("igreja_busca");
    const lista = document.getElementById("lista-igrejas");
    const inputHidden = document.getElementById("igreja_id");

    let timeout = null;

    inputBusca.addEventListener("input", function () {
        clearTimeout(timeout);
        const termo = this.value.trim();
        lista.innerHTML = "";
        lista.classList.add("hidden");
        inputHidden.value = "";

        if (termo.length < 2) return;

        timeout = setTimeout(async () => {
            try {
                console.log("Buscando igrejas para:", termo);

                const url = route("igrejas.buscar") + "?q=" + encodeURIComponent(termo);
                const res = await fetch(url);

                if (!res.ok) throw new Error("Erro na requisição: " + res.status);

                const data = await res.json();
                console.log("Resultado da busca:", data);

                lista.innerHTML = "";

                if (data.length > 0) {
                    data.forEach((igreja) => {
                        const li = document.createElement("li");
                        li.textContent = igreja.nome;
                        li.classList.add("px-3", "py-2", "hover:bg-gray-100", "cursor-pointer");

                        li.addEventListener("click", () => {
                            inputBusca.value = igreja.nome;
                            inputHidden.value = igreja.id;
                            lista.classList.add("hidden");
                        });

                        lista.appendChild(li);
                    });

                    lista.classList.remove("hidden");
                } else {
                    const li = document.createElement("li");
                    li.textContent = "Nenhuma igreja encontrada";
                    li.classList.add("px-3", "py-2", "text-gray-500");
                    lista.appendChild(li);
                    lista.classList.remove("hidden");
                }
            } catch (err) {
                console.error(err);
            }
        }, 300);
    });

    // Fechar lista se clicar fora
    document.addEventListener("click", function (e) {
        if (!lista.contains(e.target) && e.target !== inputBusca) {
            lista.classList.add("hidden");
        }
    });
});
