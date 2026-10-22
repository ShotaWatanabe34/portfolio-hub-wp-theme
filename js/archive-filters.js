(() => {
  'use strict';
  document.querySelectorAll('.archive-year-toggle').forEach((button) => {
    const list = document.getElementById(button.getAttribute('aria-controls'));
    if (!list) return;
    const icon = button.querySelector('span');
    function setOpen(open) {
      button.setAttribute('aria-expanded', String(open));
      list.hidden = !open;
      icon.textContent = open ? '▼' : '◀';
    }
    setOpen(button.dataset.initialOpen === 'true');
    button.hidden = false;
    button.addEventListener('click', () => setOpen(button.getAttribute('aria-expanded') !== 'true'));
  });
})();
