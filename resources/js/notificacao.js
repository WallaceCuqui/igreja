export function notificacoes() {
    return {
        open: false,
        notificacoes: [],
        count: 0,

        async carregar() {
            try {
                const res = await route('notificacoes.lista'); // retorna URL
                const json = await fetch(res).then(r => r.json());
                this.notificacoes = json;

                const c = await fetch(route('notificacoes.count')).then(r => r.json());
                this.count = c.count ?? 0;
            } catch (e) {
                console.error('Erro ao carregar notificações:', e);
            }
        },

        async abrir() {
            this.open = !this.open;

            if (this.open) {
                await this.carregar();

                await fetch(route('notificacoes.marcarTodasLidas'), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                await this.carregar();
            }
        },

        fechar() {
            this.open = false;
        },

        async ocultar(id) {
            await fetch(route('notificacoes.ocultar', id), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            await this.carregar();
        },
    };
}

window.notificacoes = notificacoes;
