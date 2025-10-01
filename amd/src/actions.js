// amd/src/actions.js

const registry = new Map();

function on(root, selector, type, handler) {
  root.addEventListener(type, (ev) => {
    const target = ev.target.closest(selector);
    if (!target || !root.contains(target)) return;
    handler(ev, target);
  });
}

// Handlers de ejemplo
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

function handleCopyHtml(_ev, el) {
  const sel = el.dataset.target || '';
  const node = sel ? document.querySelector(sel) : null;
  if (!node) return;
  // Copia HTML
  const html = node.innerHTML;
  navigator.clipboard.writeText(html).catch(() => {/* fallback opcional */});
}

function handleCopyImage(_ev, el) {
  const sel = el.dataset.target || '';
  const img = sel ? document.querySelector(sel) : null;
  if (!img) return;
  // Copiar URL de imagen (sencillo y compatible)
  const src = img.getAttribute('src');
  if (src) navigator.clipboard.writeText(src);
}

// API simple para registrar acciones por nombre
export function register(name, fn) { registry.set(name, fn); }

export function init() {
  const root = document.querySelector('.local-socialcert');
  if (!root) return;

  // Registra acciones base
  register('open-link', handleOpenLink);
  register('copy-html', handleCopyHtml);
  register('copy-image', handleCopyImage);

  // Delegación única para todos los clicks con data-action
  on(root, '[data-action]', 'click', (ev, el) => {
    const action = el.dataset.action;
    const fn = registry.get(action);
    if (fn) fn(ev, el);
  });
}
