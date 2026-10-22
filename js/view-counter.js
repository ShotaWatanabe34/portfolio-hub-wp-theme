(() => {
  'use strict';
  const root = document.querySelector('[data-view-counter]');
  if (!root || typeof stageCounter === 'undefined') return;
  const randomId = () => Array.from(crypto.getRandomValues(new Uint8Array(16)), n => n.toString(16).padStart(2, '0')).join('');
  let token;
  try {
    token = sessionStorage.getItem('stage-a-counter-tab');
    if (!/^[a-f0-9]{32}$/.test(token || '')) { token = randomId(); sessionStorage.setItem('stage-a-counter-tab', token); }
  } catch (_) { token = randomId(); }
  const view = randomId();
  let counted = false, busy = false, lastSent = 0;
  async function refresh() {
    if (document.hidden || busy || Date.now() - lastSent < 25000) return;
    busy = true; lastSent = Date.now();
    const controller = new AbortController();
    const timeout = setTimeout(() => controller.abort(), 8000);
    try {
      const options = {cache: 'no-store', credentials: 'same-origin', signal: controller.signal};
      if (!stageCounter.readOnly) {
        options.method = 'POST';
        options.headers = {'Content-Type': 'application/json'};
        options.body = JSON.stringify({token, view, heartbeat: counted});
      }
      const response = await fetch(stageCounter.url, options);
      if (!response.ok) throw new Error('unavailable');
      const data = await response.json();
      for (const key of ['total', 'month', 'online']) {
        if (!Number.isSafeInteger(data[key]) || data[key] < 0) throw new Error('invalid');
      }
      for (const key of ['total', 'month', 'online']) root.querySelector(`[data-count="${key}"]`).textContent = data[key].toLocaleString('ja-JP');
      counted = true;
    } catch (_) {
      // エラーを0件と誤表示しない。
      for (const node of root.querySelectorAll('[data-count]')) node.textContent = '—';
    } finally { clearTimeout(timeout); busy = false; }
  }
  refresh();
  setInterval(refresh, 30000);
  document.addEventListener('visibilitychange', refresh);
})();
