import Alpine from "alpinejs";
import { route } from "ziggy-js";

window.Alpine = Alpine;
window.route = route;

import "./notificacao";
import "./buscaIgreja";
import "./validaDocumento";
import "./buscaCEP";
import "./mascaras";
import "./integrantes";

Alpine.start();
