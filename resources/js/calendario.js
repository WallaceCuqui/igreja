import { Calendar } from '@fullcalendar/core'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import interactionPlugin from '@fullcalendar/interaction'
import ptLocale from '@fullcalendar/core/locales/pt-br'

document.addEventListener('DOMContentLoaded', () => {
    const calendarEl = document.getElementById('calendar')
    if (!calendarEl) return

    const modal = document.getElementById('eventoModal')
    const fecharModal = document.getElementById('fecharModal')
    const modalTitulo = document.getElementById('modalTitulo')
    const modalDescricao = document.getElementById('modalDescricao')

    let eventos = []
    try {
        const data = calendarEl.dataset.eventos
        eventos = JSON.parse(data)
    } catch (e) {
        console.error('Erro ao carregar eventos:', e)
    }

    const calendar = new Calendar(calendarEl, {
        plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
        initialView: 'dayGridMonth',
        locale: ptLocale,
        height: 'auto',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: eventos,
        eventClick: function (info) {
            const props = info.event.extendedProps

            // Preenche o conteúdo do modal
            modalTitulo.textContent = info.event.title
            modalDescricao.textContent = `
                📅 Início: ${info.event.start.toLocaleString()}
                ${info.event.end ? `🏁 Fim: ${info.event.end.toLocaleString()}` : ''}
                ${props.local ? `\n📍 Local: ${props.local}` : ''}
                ${props.tipo_evento ? `\n🎭 Tipo: ${props.tipo_evento}` : ''}
                ${props.descricao ? `\n📝 ${props.descricao}` : ''}
                ${props.criado_por ? `\n✍️ Criado por: ${props.criado_por}` : ''}

            `.trim()

            // Mostra o modal
            modal.classList.remove('hidden')
        },
    })

    calendar.render()

    // Fecha o modal ao clicar no X ou fora da caixa
    fecharModal.addEventListener('click', () => modal.classList.add('hidden'))
    modal.addEventListener('click', (e) => {
        if (e.target === modal) modal.classList.add('hidden')
    })
})
