import Alpine from "alpinejs";
import { route } from "ziggy-js"; // importação nomeada

window.Alpine = Alpine;
window.route = route; // expõe globalmente para outros arquivos

// Importa outros módulos JS que usam o route diretamente
import "./notificacao";
import "./buscaIgreja";

Alpine.start();
