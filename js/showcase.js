(() => {
  'use strict';
  document.querySelectorAll('[data-showcase]').forEach(root => {
    const track = root.querySelector('.showcase-track');
    const viewport = root.querySelector('.showcase-viewport');
    const originals = [...track.children];
    const prev = root.querySelector('.showcase-prev'), next = root.querySelector('.showcase-next');
    const status = root.querySelector('[data-showcase-status]');
    const reduced = matchMedia('(prefers-reduced-motion: reduce)');
    const count = originals.length;
    if (!count) return;
    let index = 0, busy = false, timer, start;
    const mod = n => (n % count + count) % count;
    function position(slot = 2) {
      const card = track.children[slot];
      track.style.transform = `translateX(${(viewport.clientWidth - card.offsetWidth) / 2 - card.offsetLeft}px)`;
    }
    function render() {
      const focused = track.contains(document.activeElement);
      track.replaceChildren();
      for (let offset = -2; offset <= 2; offset++) {
        if (count === 1 && offset !== 0) continue;
        const node = originals[mod(index + offset)].cloneNode(true);
        node.removeAttribute('id');
        node.querySelectorAll('[id]').forEach(el => el.removeAttribute('id'));
        node.inert = offset !== 0;
        if (offset !== 0) node.setAttribute('aria-hidden', 'true');
        node.classList.toggle('is-center', offset === 0);
        track.append(node);
      }
      track.style.transition = 'none';
      position(count === 1 ? 0 : 2);
      if (focused) track.focus({preventScroll:true});
      status.textContent = `${index + 1} / ${count}`;
    }
    function finish(direction) { clearTimeout(timer); index = mod(index + direction); busy = false; render(); }
    function move(direction) {
      if (busy || count < 2) return;
      busy = true;
      if (reduced.matches) { finish(direction); return; }
      track.style.transition = 'transform 300ms ease';
      position(2 + direction);
      timer = setTimeout(() => finish(direction), 330);
    }
    root.classList.add('is-ready');
    prev.hidden = next.hidden = count < 2;
    prev.addEventListener('click', () => move(-1)); next.addEventListener('click', () => move(1));
    track.addEventListener('keydown', e => {
      if (e.key === 'ArrowLeft' || e.key === 'ArrowRight') { e.preventDefault(); move(e.key === 'ArrowLeft' ? -1 : 1); }
    });
    let suppressUntil = 0;
    viewport.addEventListener('pointerdown', e => { if (e.pointerType !== 'mouse') start = {x:e.clientX,y:e.clientY,id:e.pointerId}; });
    viewport.addEventListener('pointerup', e => {
      if (!start || start.id !== e.pointerId) return;
      const dx=e.clientX-start.x, dy=e.clientY-start.y; start=null;
      if (Math.abs(dx)>40 && Math.abs(dx)>Math.abs(dy)*1.3) { suppressUntil=Date.now()+400; move(dx<0?1:-1); }
    });
    viewport.addEventListener('pointercancel',()=>{start=null;});
    viewport.addEventListener('click',e=>{if(Date.now()<suppressUntil){e.preventDefault();e.stopPropagation();}},true);
    new ResizeObserver(() => { if (!busy) position(count===1?0:2); }).observe(viewport);
    render();
  });
})();
