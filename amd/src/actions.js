// amd/src/actions.js
import {get_string as getString} from 'core/str';
import Ajax from 'core/ajax';
import Notification from 'core/notification';
// import {ready as domReady} from 'core/ready';

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
    const origin = /** @type {Element} */(ev.target instanceof Element ? ev.target : root);
    const target = origin.closest(selector);
    if (!target || !root.contains(target)) {
      return;
    }
    handler(ev, /** @type {HTMLElement} */(target));
  });
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
 * Copia el contenido del cuadro de respuesta al portapapeles.
 * - data-target: selector del contenedor (ej. "#ai-response")
 * - data-copy: "text" (por defecto) o "html"
 *   - En ambos modos se eliminan spans .lsc-caret antes de copiar.
 *
 * @param {MouseEvent} _ev
 * @param {HTMLElement} el
 * @returns {void}
 */
async function handleCopyHtml(_ev, el) {
  const sel = el.dataset.target || '';
  const node = sel ? document.querySelector(sel) : null;
  if (!node) {
    return;
  }

  // Clonamos para poder limpiar elementos auxiliares (caret, etc.)
  const clone = node.cloneNode(true);
  const carets = clone.querySelectorAll('.lsc-caret');
  carets.forEach(c => c.remove());

  const mode = (el.dataset.copy === 'html') ? 'html' : 'text';
  const content = (mode === 'html')
    ? clone.innerHTML
    : (clone.innerText || clone.textContent || '');

  navigator.clipboard.writeText(content)
    .then(() => {
      // feedback opcional: cambiar el texto del botón brevemente
      const btn = el.querySelector('[data-action="copy-html"]');
      if (!btn) {return;}

      const originalHTML = btn.innerHTML;
      btn.textContent = '✔';
      setTimeout(() => { btn.textContent = originalHTML; }, 1200);
    })
    .catch(() => {
      // fallback opcional (silencioso)
    });
}



// --- STREAM MOCK + TYPEWRITER -----------------------------------------------

/**
 * Inserta y devuelve un cursor visual (caret) al final del elemento destino.
 * Se usa para simular escritura en vivo.
 *
 * @param {HTMLElement} el - Contenedor donde se añadirá el caret.
 * @returns {HTMLSpanElement} caret - El nodo <span> insertado con la clase "lsc-caret".
 */
function addCaret(el) {
  const caret = document.createElement('span');
  caret.className = 'lsc-caret';
  caret.textContent = ' ';
  el.appendChild(caret);
  return caret;
}

/**
 * Elimina, si existe, el cursor visual (caret) dentro del elemento destino.
 *
 * @param {HTMLElement} el - Contenedor desde el que se eliminará el caret.
 * @returns {void}
 */
function removeCaret(el) {
  const caret = el.querySelector('.lsc-caret');
  if (caret) { caret.remove(); }
}

// Control de streams por destino (para detener si se pulsa de nuevo)
const streams = new WeakMap();

/**
 * Escribe texto en unidades (char|word) con un intervalo.
 * @param {HTMLElement} el
 * @param {string} text
 * @param {'char'|'word'} mode
 * @param {number} speedMs
 * @returns {{stop:Function, done:Promise<void>}}
 */
export function typewriter(el, text, mode, speedMs) {
  const caret = addCaret(el);
  const units = mode === 'char' ? text.split('') : text.split(/\s+/);
  let i = 0;
  let stopped = false;
  el.innerHTML = '';
  el.appendChild(caret);

  const done = new Promise((resolve) => {
    const timer = setInterval(() => {
      if (stopped) {
        clearInterval(timer);
        removeCaret(el);
        resolve();
        return;
      }
      if (i >= units.length) {
        clearInterval(timer);
        removeCaret(el);
        resolve();
        return;
      }
      const chunk = units[i++];
      caret.insertAdjacentText('beforebegin', mode === 'word' ? (chunk + ' ') : chunk);
    }, Math.max(10, speedMs || 30));
    streams.set(el, { stop: () => { stopped = true; } });
  });

  return { stop() { const s = streams.get(el); if (s) { s.stop(); streams.delete(el); } }, done };
}

/**
 * Obtiene una respuesta generada por IA para un certificado/curso y red social dada,
 * utilizando la llamada AJAX `local_socialcert_get_ai_response`.
 *
 * La función envía un payload con `certname`, el curso fijo "Curso de Python",
 * la organización y la red social, y resuelve con el texto de la respuesta (`reply`)
 * devuelta por el backend.
 *
 * @function ai_response
 * @async
 * @param {string} certname - Nombre del certificado (o del estudiante) a incluir en el prompt.
 * @param {string} course - Nombre del certificado (o del estudiante) a incluir en el prompt.
 * @param {string} org - Nombre de la organización o institución emisora.
 * @param {string} socialmedia - Red social objetivo (p. ej., "LinkedIn") o lista de redes.
 * @returns {Promise<string>} Promesa que se resuelve con el contenido textual de la respuesta de IA.
 * @throws {SyntaxError} Si el JSON devuelto por el backend no es válido.
 * @throws {Error} Si la llamada AJAX falla por cualquier motivo (se notificará con `Notification.exception`).
 *
 * @example
 * ai_response("Certificado de Analítica", "BUEN DATA", "LinkedIn")
 *   .then((reply) => {
 *     console.log("Respuesta IA:", reply);
 *   })
 *   .catch((err) => {
 *     console.error("Error obteniendo la respuesta de IA:", err);
 *   });
 */
function ai_response (certname, course, org, socialmedia) {
  return new Promise((resolve, reject) => {
    Ajax.call([{
      methodname: 'local_socialcert_get_ai_response',
      args: {
        body: {
          certname: certname,
          course: course,
          org: org,
          socialmedia: socialmedia
        }
      },
    }])[0].then((response) => {
      try {
        const parsed = JSON.parse(response.json);
        return resolve(parsed.reply);
      } catch (e) {
        reject(e);
      }
    }).catch((err) => {
      Notification.exception(err);
      reject(err);
    });
  });
}


/**
 * Ejecuta/Detiene la “IA” (streaming).
 * Lee data-target, data-mode ("char"|"word"), data-speed (ms).
 * @param {MouseEvent} ev
 * @param {HTMLElement} btn
 */
export function runAiHandler(ev, btn) {

  ev.preventDefault();

  // Localiza el destino
  const sel = btn.dataset.target;
  let target = null;
  if (sel) {
    target = document.querySelector(sel);
  } else {
    const wrap = btn.closest('.lsc-response-wrap');
    target = wrap ? wrap.querySelector('.lsc-response') : null;
  }
  if (!target) { return; }

  // Si ya hay stream en ese destino: detener y restaurar
  if (streams.has(target)) {
    const s = streams.get(target);
    if (s && s.stop) { s.stop(); }
    btn.disabled = false;
    btn.textContent = getString('airesponsebtn', 'local_socialcert');
    return;
  }

  // Preparar UI
  const mode = (btn.dataset.mode === 'char') ? 'char' : 'word';
  const speed = parseInt(btn.dataset.speed || '40', 10);
  const certname = btn.dataset.certname || '';
  const course = btn.dataset.course || '';
  const org = btn.dataset.org || '';
  const socialmedia = btn.dataset.socialmedia || '';
  const id_servicio = btn.dataset.id_servicio || '';
  const original = btn.textContent;
  btn.disabled = true;
  btn.textContent = 'Generating';
  target.setAttribute('aria-busy', 'true');
  target.setAttribute('role', 'status');

  const loader = document.getElementById('ai-card');
  const copyBtn = document.getElementById('copyBtn');

  ai_response(certname, course, org, socialmedia, id_servicio).then((fulltext) => {
    loader.classList.add('hidden');
    loader.setAttribute('aria-busy', 'false');
    const stream = typewriter(target, fulltext , mode, speed);
    stream.done.then(() => {
      btn.disabled = false;
      btn.textContent = original;
      target.removeAttribute('aria-busy');
      streams.delete(target);
    })
    .finally(() => {
      copyBtn.hidden=false;
    });
  }).catch(() => {
    btn.disabled = false;
    btn.textContent = original;
    target.removeAttribute('aria-busy');
  });
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

  // Registra acciones base
  register('open-link', handleOpenLink);
  register('copy-html', handleCopyHtml);
  register('run-ai', runAiHandler);

  // Delegación única para todos los clicks con data-action
  on(root, '[data-action]', 'click', (ev, el) => {
    const action = el.dataset.action;
    const fn = registry.get(action);
    if (fn) {
      fn(ev, el);
    }
  });

}
