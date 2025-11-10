document.querySelectorAll('.status-toggle').forEach(checkbox => {
    checkbox.addEventListener('change', function () {
        const url = this.checked ? this.dataset.urlAtivar : this.dataset.urlRemover;
        const method = this.checked ? 'POST' : 'DELETE';

        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro ao atualizar status');
            }
            return response.json();
        })
        .then(data => {
            console.log('✅ Status atualizado:', data);

            // 🔄 Recarrega a página após breve delay (0.3s para UX melhor)
            setTimeout(() => location.reload(), 300);
        })
        .catch(error => {
            console.error('❌ Erro:', error);
            alert('Erro ao atualizar status.');
            // volta o checkbox pro estado anterior se der erro
            this.checked = !this.checked;
        });
    });
});

