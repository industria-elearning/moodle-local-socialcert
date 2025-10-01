// local/socialcert/amd/src/copyhtml.js
import Notification from 'core/notification';

/**
 * Copia el texto "visible" del nodo (como si lo seleccionaras con el mouse),
 * forzando portapapeles en text/plain. Evita HTML.
 */
function copySelectionAsPlainText(node) {
    const sel = window.getSelection();
    const saved = [];
    for (let i = 0; i < sel.rangeCount; i++) saved.push(sel.getRangeAt(i));

    sel.removeAllRanges();
    const range = document.createRange();
    range.selectNodeContents(node);
    sel.addRange(range);

    // Captura el "copy" y fuerza text/plain con el contenido seleccionado.
    const oncopy = (ev) => {
        try {
            const text = sel.toString(); // respeta layout (similar a innerText)
            ev.clipboardData.setData('text/plain', text);
            ev.preventDefault(); // evita que el navegador ponga HTML
        } catch (e) {
            // deja seguir si algo falla; el fallback intentará copiar igual
        }
    };

    document.addEventListener('copy', oncopy, {once: true});
    let ok = false;
    try {
        ok = document.execCommand('copy'); // requiere activación de usuario
    } catch (e) {
        ok = false;
    }

    // Limpieza: quitar selección y restaurar selección previa
    sel.removeAllRanges();
    saved.forEach(r => sel.addRange(r));

    return ok;
}

/**
 * Fallback: usa innerText (no HTML) y Clipboard API si está disponible.
 * Útil si el navegador bloquea execCommand por alguna razón.
 */
async function copyInnerTextFallback(node) {
    const text = node.innerText ?? node.textContent ?? '';
    try {
        await navigator.clipboard.writeText(text);
        return true;
    } catch (e) {
        // textarea oculto
        const ta = document.createElement('textarea');
        ta.value = text;
        ta.setAttribute('readonly', '');
        ta.style.position = 'absolute';
        ta.style.left = '-9999px';
        document.body.appendChild(ta);
        ta.select();
        const ok = document.execCommand('copy');
        document.body.removeChild(ta);
        return ok;
    }
}

export const init = (selector = '[data-action="copy-as-html"]') => {
    document.addEventListener('click', async (e) => {
        const btn = e.target.closest(selector);
        if (!btn) return;

        const targetId = btn.dataset.target;
        if (!targetId) {
            Notification.exception({message: 'Falta data-target en el botón.'});
            return;
        }
        const node = document.getElementById(targetId);
        if (!node) {
            Notification.exception({message: `No se encontró #${targetId}.`});
            return;
        }

        // 1º intento: simular selección y copiar SOLO texto plano
        let ok = copySelectionAsPlainText(node);

        // Fallback: innerText → portapapeles
        if (!ok) ok = await copyInnerTextFallback(node);

        if (ok) {
            Notification.addNotification({message: 'Copiado como texto visible.', type: 'success'});
            // Debug opcional:
        } else {
            Notification.exception({message: 'No se pudo copiar. Verifica permisos/HTTPS.'});
        }
    });
};
