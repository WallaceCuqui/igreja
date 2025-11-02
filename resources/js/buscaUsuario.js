document.addEventListener("DOMContentLoaded", function () {
    const input = document.getElementById("busca_relacao");
    const resultsContainer = document.getElementById("resultados_busca");
    const hiddenUserId = document.getElementById("user_id");

    let timeout = null;

    input.addEventListener("input", function () {
        const query = this.value.trim();
        clearTimeout(timeout);

        if (query.length < 2) {
            resultsContainer.innerHTML = "";
            return;
        }

        timeout = setTimeout(() => {
            fetch(`/profile/relacoes/buscar?q=${encodeURIComponent(query)}`)
                .then((response) => response.json())
                .then((users) => {
                    resultsContainer.innerHTML = "";

                    if (users.length === 0) {
                        resultsContainer.innerHTML =
                            '<p class="p-2 text-gray-500 text-sm">Nenhum resultado encontrado</p>';
                        return;
                    }

                    users.forEach((user) => {
                        const option = document.createElement("div");
                        option.className =
                            "p-2 cursor-pointer hover:bg-gray-100 border-b";
                        option.textContent = `${user.name} (${user.email})`;
                        option.addEventListener("click", () => {
                            input.value = `${user.name} (${user.email})`;
                            hiddenUserId.value = user.id;
                            resultsContainer.innerHTML = "";
                        });
                        resultsContainer.appendChild(option);
                    });
                })
                .catch((err) => {
                    console.error("Erro na busca:", err);
                });
        }, 400);
    });
});
