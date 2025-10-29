// resources/js/notificacao.js
export function notificacoes() {
    return {
        open: false,
        notificacoes: [],
        count: 0,
        async carregar() {
            const res = await fetch('/notificacoes/lista');
            this.notificacoes = await res.json();
            const c = await fetch('/notificacoes/count');
            const j = await c.json();
            this.count = j.count ?? 0;
        },
        async abrir() {
            this.open = !this.open;
            if (this.open) {
                await this.carregar();
                await fetch('/notificacoes/marcar-todas-lidas', {
                    method: 'POST',
                    headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')}
                });
                await this.carregar();
            }
        },
        fechar() { this.open = false; },
        async ocultar(id) {
            await fetch(`/notificacoes/${id}/ocultar`, {
                method: 'POST',
                headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')}
            });
            await this.carregar();
        }
    };
}
