import Alpine from 'alpinejs';
import { route } from 'ziggy-js'; // importação nomeada

window.Alpine = Alpine;
window.route = route; // expõe globalmente para outros arquivos

import './notificacao';

Alpine.start();
