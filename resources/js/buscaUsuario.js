document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".busca-dinamica").forEach((input) => {
        const resultsContainer = document.querySelector(input.dataset.results);
        const hiddenInputSelector = input.dataset.targetInput;
        const endpoint = input.dataset.endpoint;
        const hiddenInput = hiddenInputSelector ? document.querySelector(hiddenInputSelector) : null;
        const ativarUrlTemplate = input.dataset.ativarUrlTemplate; // usado só no caso de ministérios

        let timeout = null;

        input.addEventListener("input", function () {
            const query = this.value.trim();
            clearTimeout(timeout);

            if (query.length < 2) {
                resultsContainer.innerHTML = "";
                return;
            }

            timeout = setTimeout(() => {
                fetch(`${endpoint}?q=${encodeURIComponent(query)}`)
                    .then((res) => res.json())
                    .then((items) => {
                        resultsContainer.innerHTML = "";

                        if (!items.length) {
                            resultsContainer.innerHTML =
                                '<p class="p-2 text-gray-500 text-sm">Nenhum resultado encontrado</p>';
                            return;
                        }

                        items.forEach((item) => {
                            const option = document.createElement("div");
                            option.className =
                                "p-2 cursor-pointer hover:bg-indigo-100 border-b";
                            option.textContent = item.name;
                            option.addEventListener("click", async () => {
                                input.value = item.name;
                                if (hiddenInput) hiddenInput.value = item.id;
                                resultsContainer.innerHTML = "";

                                // 🔹 Se tiver o data-ativar-url-template, é busca de integrante
                                if (ativarUrlTemplate) {
                                    const url = ativarUrlTemplate.replace("MEMBRO_ID", item.id);
                                    const res = await fetch(url, {
                                        method: "POST",
                                        headers: {
                                            "X-CSRF-TOKEN": document
                                                .querySelector('meta[name="csrf-token"]')
                                                .getAttribute("content"),
                                        },
                                    });

                                    if (res.ok) {
                                        location.reload();
                                    } else {
                                        alert("Erro ao adicionar membro ao ministério.");
                                    }
                                }
                            });

                            resultsContainer.appendChild(option);
                        });
                    })
                    .catch((err) => console.error("Erro na busca:", err));
            }, 300);
        });
    });
});
