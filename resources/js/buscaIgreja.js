import { route } from "ziggy-js";

document.addEventListener("DOMContentLoaded", function () {
    const inputBusca = document.getElementById("igreja_busca");
    const lista = document.getElementById("lista-igrejas");
    const inputHidden = document.getElementById("igreja_id");

    if (!inputBusca || !lista || !inputHidden) return;

    let timeout = null;

    inputBusca.addEventListener("input", function () {
        clearTimeout(timeout);
        const termo = this.value.trim();
        lista.innerHTML = "";
        lista.classList.add("hidden");
        inputHidden.value = "";

        if (termo.length < 2) return;

        timeout = setTimeout(() => {
            fetch(route("igrejas.buscar") + `?q=${encodeURIComponent(termo)}`)
                .then((res) => {
                    if (!res.ok) throw new Error("Erro na requisição");
                    return res.json();
                })
                .then((data) => {
                    lista.innerHTML = "";

                    if (data.length > 0) {
                        data.forEach((igreja) => {
                            const li = document.createElement("li");
                            li.textContent = igreja.name;
                            li.classList.add(
                                "px-3",
                                "py-2",
                                "hover:bg-gray-100",
                                "cursor-pointer"
                            );
                            li.addEventListener("click", () => {
                                inputBusca.value = igreja.name;
                                inputHidden.value = igreja.id;
                                lista.classList.add("hidden");
                            });
                            lista.appendChild(li);
                        });
                    } else {
                        const li = document.createElement("li");
                        li.textContent = "Nenhum resultado encontrado";
                        li.classList.add("px-3", "py-2", "text-gray-500");
                        lista.appendChild(li);
                    }

                    lista.classList.remove("hidden");
                })
                .catch((err) => {
                    console.error(err);
                    lista.innerHTML = `<li class="px-3 py-2 text-red-600">Erro ao buscar igrejas</li>`;
                    lista.classList.remove("hidden");
                });
        }, 300);
    });

    document.addEventListener("click", function (e) {
        if (!lista.contains(e.target) && e.target !== inputBusca) {
            lista.classList.add("hidden");
        }
    });
});
