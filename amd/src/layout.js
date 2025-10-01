/**
 * Módulo de UI para el panel de IA del plugin.
 * Proporciona utilidades para copiar HTML con formato, copiar una imagen
 * y enlazar los botones del template Mustache.
 *
 * @module local_socialcert/aiview
 */

define([], function() {

    /**
     * Copia el contenido de un elemento al portapapeles.
     * Intenta copiar como HTML con formato (text/html) y, si no es posible,
     * cae a copiar texto plano.
     *
     * @async
     * @function copyHTMLFrom
     * @param {HTMLElement} targetEl - Elemento contenedor cuyo innerHTML/innerText se copiará.
     * @returns {Promise<void>} Promesa que se resuelve cuando finaliza la operación de copiado.
     * @throws {Error} Si el navegador bloquea el acceso al portapapeles o falla la escritura.
     *
     * @example
     * const box = document.querySelector('#ai-response');
     * await copyHTMLFrom(box);
     */
    async function copyHTMLFrom(targetEl) {
        const html = targetEl.innerHTML;
        const plain = targetEl.innerText;

        if (navigator.clipboard && window.ClipboardItem) {
            const item = new ClipboardItem({
                'text/html': new Blob([html], {type: 'text/html'}),
                'text/plain': new Blob([plain], {type: 'text/plain'})
            });
            await navigator.clipboard.write([item]);
            return;
        }

        // Fallback: texto plano con execCommand (legacy).
        const ta = document.createElement('textarea');
        ta.value = plain;
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        ta.remove();
    }

    /**
     * Copia una imagen al portapapeles si el navegador lo permite.
     * Si no es posible, ofrece descarga del recurso como alternativa.
     *
     * Requiere contexto seguro (HTTPS) y permisos del portapapeles para copiar como imagen.
     *
     * @async
     * @function copyImageFrom
     * @param {HTMLImageElement} imgEl - La imagen a copiar/descargar.
     * @returns {Promise<void>} Promesa que se resuelve al terminar la acción.
     * @throws {Error} Si falla la obtención del blob o la escritura en el portapapeles.
     *
     * @example
     * const img = document.querySelector('#ai-image');
     * await copyImageFrom(img);
     */
    async function copyImageFrom(imgEl) {
        if (navigator.clipboard && window.ClipboardItem) {
            const res = await fetch(imgEl.src, {mode: 'cors'});
            const blob = await res.blob();
            const item = new ClipboardItem({ [blob.type || 'image/png']: blob });
            await navigator.clipboard.write([item]);
            return;
        }
        // Fallback: descarga del archivo.
        const a = document.createElement('a');
        a.href = imgEl.src;
        a.download = 'image';
        document.body.appendChild(a);
        a.click();
        a.remove();
    }

    /**
     * @typedef {Object} InitOptions
     * @property {string} [target] - Selector CSS del botón principal (opcional). Si no se provee, se usa '#btn-cert'.
     * @property {string} [verifyurl] - Ejemplo de dato a pasar al botón/acción (opcional).
     */

    /**
     * Inicializa listeners para:
     *  - Copiar el contenido con formato del cuadro de respuesta (data-action="copy-html")
     *  - Copiar/descargar la imagen (data-action="copy-image")
     *  - Simular escritura incremental al presionar el botón de IA (data-action="run-ai")
     *
     * Esta función asume el marcado generado por el template Mustache del plugin.
     *
     * @function init
     * @param {InitOptions} [opts] - Opciones iniciales para ajustar selectores/valores.
     * @returns {void}
     *
     * @example
     * // PHP:
     * // $PAGE->requires->js_call_amd('local_socialcert/aiview', 'init', [ ['target' => '#btn-cert'] ]);
     *
     * // JS:
     * // require(['local_socialcert/aiview'], m => m.init({ target: '#btn-cert' }));
     */
    function init(opts) {
        // Copiar HTML con formato
        document.querySelectorAll('[data-action="copy-html"]').forEach(btn => {
            btn.addEventListener('click', async () => {
                const sel = btn.getAttribute('data-target');
                const el = document.querySelector(sel);
                if (!el) return;
                try {
                    await copyHTMLFrom(el);
                    // eslint-disable-next-line no-alert
                    alert('Copiado al portapapeles.');
                } catch (e) {
                    alert('No se pudo copiar.');
                }
            });
        });

        // Copiar imagen
        document.querySelectorAll('[data-action="copy-image"]').forEach(btn => {
            btn.addEventListener('click', async () => {
                const sel = btn.getAttribute('data-target');
                const img = document.querySelector(sel);
                if (!img) return;
                try {
                    await copyImageFrom(img);
                    // eslint-disable-next-line no-alert
                    alert('Imagen copiada/descargada.');
                } catch (e) {
                    alert('No se pudo copiar la imagen.');
                }
            });
        });

        // Botón “activar IA”: demo de escritura incremental
        document.querySelectorAll('[data-action="run-ai"]').forEach(btn => {
            btn.addEventListener('click', async () => {
                const wrap = btn.closest('.local-socialcert');
                const out = wrap && wrap.querySelector('.lsc-response');
                if (!out) return;

                // Ejemplo: escribir con formato en fragmentos
                out.innerHTML = '';
                const chunks = [
                    '<p><strong>Generando resumen…</strong></p>',
                    '<p>1) Introducción <em>(ok)</em></p>',
                    '<p>2) Desarrollo</p>',
                    '<p><u>3) Cierre</u></p>'
                ];
                for (const c of chunks) {
                    out.insertAdjacentHTML('beforeend', c);
                    await new Promise(r => setTimeout(r, 300));
                }
            });
        });
    }

    return { init };
});
