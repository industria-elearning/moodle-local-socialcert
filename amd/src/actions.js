// amd/src/actions.js

/**
 * Mapa de acciones: nombre -> handler(ev, el)
 * @type {Map<string, Function>}
 */
const registry = new Map();

/**
 * Delegación de eventos: escucha en `root` y, si el objetivo o un ancestro
 * coincide con `selector`, ejecuta `handler(ev, targetMatch)`.
 * @param {HTMLElement} root     Contenedor raíz donde delegar
 * @param {string} selector      Selector a matchear (con closest)
 * @param {string} type          Tipo de evento (p.ej., 'click')
 * @param {(ev:Event, el:HTMLElement)=>void} handler  Manejador
 * @returns {void}
 */
function on(root, selector, type, handler) {
  root.addEventListener(type, (ev) => {
    const target = ev.target.closest(selector);
    if (!target || !root.contains(target)) {
      return;
    }
    handler(ev, target);
  });
}

/**
 * Muestra/oculta nodos según el estado actual de red en `root.dataset.network`.
 * Cada bloque sensible debe declarar `data-visible-on="linkedin twitter ..."`
 * @param {HTMLElement} root  Contenedor raíz con data-network
 * @returns {void}
 */
function updateVisibility(root) {
  const current = root.dataset.network || '';
  root.querySelectorAll('[data-visible-on]').forEach(el => {
    const allowed = (el.getAttribute('data-visible-on') || '').split(/\s+/);
    if (allowed.includes(current)) {
      el.classList.remove('d-none');
    } else {
      el.classList.add('d-none');
    }
  });
}

/**
 * Selecciona pestaña/red (data-network), actualiza aria-selected y visibilidad.
 * @param {MouseEvent} ev
 * @param {HTMLElement} el  Botón con data-network="..."
 * @returns {void}
 */
export function handleSelectNetwork(ev, el) {
  ev.preventDefault();
  const root = el.closest('.local-socialcert');
  if (!root) {
    return;
  }

  const next = el.dataset.network;
  if (!next) {
    return;
  }

  // Estado
  root.dataset.network = next;

  // Toggle visual en el grupo
  const group = el.closest('.lsc-social-tabs');
  if (group) {
    group.querySelectorAll('[data-action="select-network"]').forEach(b => {
      b.classList.toggle('active', b === el);
      b.setAttribute('aria-selected', b === el ? 'true' : 'false');
    });
  }

  // Mostrar/ocultar secciones
  updateVisibility(root);
}

/**
 * Abre un enlace en nueva pestaña. Usa href o data-url.
 * Marca aria-busy brevemente como feedback.
 * @param {MouseEvent} ev
 * @param {HTMLElement} el
 * @returns {void}
 */
function handleOpenLink(ev, el) {
  // Si es <a> con href, dejamos que el browser lo haga,
  // pero añadimos un pequeño “busy”/tracking si quieres.
  // Si NO quieres prevenir, comenta la línea de preventDefault.
  ev.preventDefault();

  el.setAttribute('aria-busy', 'true');
  // Si el elemento no tiene href (ej. <button>), abrimos data-url:
  const url = el.getAttribute('href') || el.dataset.url;
  if (url) {
    window.open(url, '_blank', 'noopener');
  }
  // Quita busy luego de un tick
  setTimeout(() => el.removeAttribute('aria-busy'), 300);
}

/**
 * Copia el HTML interno del selector dado en data-target.
 * @param {MouseEvent} _ev
 * @param {HTMLElement} el
 * @returns {void}
 */
function handleCopyHtml(_ev, el) {
  const sel = el.dataset.target || '';
  const node = sel ? document.querySelector(sel) : null;
  if (!node) {
    return;
  }
  // Copia HTML
  const html = node.innerHTML;
  navigator.clipboard.writeText(html).catch(() => { /* fallback opcional */ });
}

/**
 * Copia la URL src de la imagen indicada en data-target.
 * @param {MouseEvent} _ev
 * @param {HTMLElement} el
 * @returns {void}
 */
function handleCopyImage(_ev, el) {
  const sel = el.dataset.target || '';
  const img = sel ? document.querySelector(sel) : null;
  if (!img) {
    return;
  }
  // Copiar URL de imagen (sencillo y compatible)
  const src = img.getAttribute('src');
  if (src) {
    navigator.clipboard.writeText(src);
  }
}

/**
 * Registra una acción para usar con data-action="name".
 * @param {string} name
 * @param {(ev:Event, el:HTMLElement)=>void} fn
 * @returns {void}
 */
export function register(name, fn) { registry.set(name, fn); }

/**
 * Punto de entrada: registra handlers base y activa la delegación de clicks.
 * @returns {void}
 */
export function init() {
  const root = document.querySelector('.local-socialcert');
  if (!root) {
    return;
  }

  // ⬇️ FALTABA registrar el selector de red
  register('select-network', handleSelectNetwork);
  updateVisibility(root);

  // Registra acciones base
  register('open-link', handleOpenLink);
  register('copy-html', handleCopyHtml);
  register('copy-image', handleCopyImage);

  // Delegación única para todos los clicks con data-action
  on(root, '[data-action]', 'click', (ev, el) => {
    const action = el.dataset.action;
    const fn = registry.get(action);
    if (fn) {
      fn(ev, el);
    }
  });

  // ⬇️ Calcula visibilidad inicial según data-network del root
  updateVisibility(root);
}
