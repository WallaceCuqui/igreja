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
        .then(response => response.json())
        .then(data => console.log('Status atualizado:', data))
        .catch(error => {
            console.error(error);
            alert('Erro ao atualizar status.');
            this.checked = !this.checked;
        });
    });
});
