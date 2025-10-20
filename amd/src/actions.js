// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Plugin version and other meta-data are defined here.
 *
 * @package
 * @copyright   2025 Manuel Bojaca <manuel@buendata.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Local SocialCert — frontend helpers.
 *
 * Responsibilities:
 * - Lightweight event delegation utility for click actions.
 * - Handlers for: opening external links, copying generated text, and AI “typewriter” effect.
 * - Triggering the AI request and streaming the response into the DOM.
 * - Public API: { init, register, runAiHandler, typewriter }.
 *
 * Notes:
 * - This module is loaded via $PAGE->requires->js_call_amd('local_socialcert/actions', 'init').
 * - The HTML is rendered by the Mustache template and provides the data-* hooks.
 */

import {get_string as getString} from 'core/str';
import Ajax from 'core/ajax';
import Notification from 'core/notification';

/* ============================================================================
 * Action registry
 * ==========================================================================*/

/**
 * Action registry mapping: actionName -> handler(event, element).
 * Handlers are registered once in {@link init} and invoked via delegation.
 * @type {Map<string, Function>}
 */
const registry = new Map();

/* ============================================================================
 * Event delegation
 * ==========================================================================*/

/**
 * Simple event delegation helper.
 * Listens on a root element and, when the event target or an ancestor matches
 * the selector, calls the provided handler with the matched element.
 *
 * @param {HTMLElement} root   Root container where the listener is attached.
 * @param {string} selector    CSS selector to match using Element.closest().
 * @param {string} type        Event type (e.g., "click").
 * @param {(ev: Event, el: HTMLElement) => void} handler  Callback invoked with the event and the matched element.
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

/* ============================================================================
 * Handlers: open link / copy to clipboard
 * ==========================================================================*/

/**
 * Opens a link in a new tab (using element href or data-url) and briefly sets aria-busy for feedback.
 *
 * @param {MouseEvent} ev
 * @param {HTMLElement} el Element with an href or data-url attribute.
 * @returns {void}
 */
function handleOpenLink(ev, el) {

  ev.preventDefault();

  el.setAttribute('aria-busy', 'true');

  const url = el.getAttribute('href') || el.dataset.url;

  if (url) {
    window.open(url, '_blank', 'noopener');
  }

  setTimeout(() => el.removeAttribute('aria-busy'), 300);
}

/**
 * Copies the content of a target node to the clipboard.
 * - data-target: selector pointing to the container (e.g., "#ai-response").
 * - data-copy: "text" (default) or "html".
 *   - In both modes, temporary caret spans (.lsc-caret) are removed before copying.
 *
 * @param {MouseEvent} _ev
 * @param {HTMLElement} el Button element with data-target (and optional data-copy).
 * @returns {void}
 */
async function handleCopyHtml(_ev, el) {
  const sel = el.dataset.target || '';
  const node = sel ? document.querySelector(sel) : null;
  if (!node) {
    return;
  }

  const clone = node.cloneNode(true);
  const carets = clone.querySelectorAll('.lsc-caret');
  carets.forEach(c => c.remove());

  const mode = (el.dataset.copy === 'html') ? 'html' : 'text';
  const content = (mode === 'html')
    ? clone.innerHTML
    : (clone.innerText || clone.textContent || '');

  navigator.clipboard.writeText(content)
    .then(() => {

      const btn = el.querySelector('[data-action="copy-html"]');

      if (!btn) {return;}

      const originalHTML = btn.innerHTML;
      btn.textContent = '✔';
      setTimeout(() => { btn.textContent = originalHTML; }, 1200);
    })
    .catch(() => {

    });
}

/* ============================================================================
 * Typewriter (stream mock) utilities
 * ==========================================================================*/

/**
 * Inserts a visual caret at the end of the target element and returns it.
 * Used to simulate live typing.
 *
 * @param {HTMLElement} el Target container where the caret is appended.
 * @returns {HTMLSpanElement} The inserted <span> with class "lsc-caret".
 */
function addCaret(el) {
  const caret = document.createElement('span');
  caret.className = 'lsc-caret';
  caret.textContent = ' ';
  el.appendChild(caret);
  return caret;
}

/**
 * Removes the visual caret (if present) from a given element.
 *
 * @param {HTMLElement} el Container to clean up.
 * @returns {void}
 */
function removeCaret(el) {
  const caret = el.querySelector('.lsc-caret');
  if (caret) { caret.remove(); }
}

/**
 * Tracks active streams per target so we can stop/replace them.
 * @type {WeakMap<HTMLElement, {stop: Function}>}
 */
const streams = new WeakMap();

/**
 * Streams text into an element in "char" or "word" units with a configurable delay.
 *
 * @param {HTMLElement} el   Target element to receive the text.
 * @param {string} text      Full text to stream.
 * @param {'char'|'word'} mode Unit size used while streaming.
 * @param {number} speedMs   Interval between units (ms).
 * @returns {{stop: Function, done: Promise<void>}} Control handle with a stop() method and a completion promise.
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

/* ============================================================================
 * AI request
 * ==========================================================================*/

/**
 * Fetches an AI-generated response for the given context using the
 * `local_socialcert_get_ai_response` web service.
 *
 * @function ai_response
 * @async
 * @param {string} certname   Certificate (or student) name used in the prompt.
 * @param {string} course     Course name used in the prompt.
 * @param {string} org        Issuing organization name.
 * @param {string} socialmedia Target social network (e.g., "LinkedIn").
 * @returns {Promise<string>} Resolves to the AI textual reply.
 * @throws {SyntaxError} If the backend JSON is invalid.
 * @throws {Error} If the AJAX call fails (also reported via Notification.exception).
 *
 * @example
 * ai_response("Analytics Certificate", "BUEN DATA", "LinkedIn")
 *   .then(reply => console.log("AI reply:", reply))
 *   .catch(err => console.error("AI error:", err));
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


/* ============================================================================
 * Action: run AI
 * ==========================================================================*/

/**
 * Starts/stops the “AI” streaming flow.
 * Reads data attributes from the trigger button:
 *  - data-target: CSS selector for the output node.
 *  - data-mode: "char" | "word" (streaming unit).
 *  - data-speed: interval in ms.
 *
 * Also manages a loader, ARIA states, and reveals the Copy button when done.
 *
 * @param {MouseEvent} ev
 * @param {HTMLElement} btn Triggering button element.
 * @returns {void}
 */
export function runAiHandler(ev, btn) {

  ev.preventDefault();

  const sel = btn.dataset.target;
  let target = null;
  if (sel) {
    target = document.querySelector(sel);
  } else {
    const wrap = btn.closest('.lsc-response-wrap');
    target = wrap ? wrap.querySelector('.lsc-response') : null;
  }
  if (!target) { return; }

  if (streams.has(target)) {
    const s = streams.get(target);
    if (s && s.stop) { s.stop(); }
    btn.disabled = false;
    btn.textContent = getString('airesponsebtn', 'local_socialcert');
    return;
  }

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


/* ============================================================================
 * Public API
 * ==========================================================================*/

/**
 * Registers an action handler that can be invoked via data-action="name".
 *
 * @param {string} name
 * @param {(ev: Event, el: HTMLElement) => void} fn
 * @returns {void}
 */
export function register(name, fn) { registry.set(name, fn); }

/**
 * Entry point: registers base actions and sets up click delegation.
 * Called once when the AMD module is loaded.
 *
 * @returns {void}
 */
export function init() {
  const root = document.querySelector('.local-socialcert');
  if (!root) {
    return;
  }

  register('open-link', handleOpenLink);
  register('copy-html', handleCopyHtml);
  register('run-ai', runAiHandler);

  on(root, '[data-action]', 'click', (ev, el) => {
    const action = el.dataset.action;
    const fn = registry.get(action);
    if (fn) {
      fn(ev, el);
    }
  });
}
