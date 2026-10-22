(() => {
  'use strict';
  document.querySelectorAll('[data-post-slider]').forEach((slider) => {
    const track = slider.querySelector('.top-post-track');
    const viewport = slider.querySelector('.slider-viewport');
    const cards = Array.from(track.querySelectorAll('.top-post-card'));
    const controls = slider.querySelector('.slider-controls');
    const prev = slider.querySelector('.top-post-prev');
    const next = slider.querySelector('.top-post-next');
    if (!cards.length) return;
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
    let shown = 1, moving = false, pendingDirection = 0, timeout;
    slider.classList.add('is-loop-ready');
    function update() {
      const ordered = Array.from(track.children);
      ordered.forEach((card, index) => {
        const hidden = index >= shown;
        if (hidden && card.contains(document.activeElement)) track.focus({preventScroll: true});
        card.inert = hidden;
        if (hidden) card.setAttribute('aria-hidden', 'true');
        else card.removeAttribute('aria-hidden');
      });
      controls.hidden = cards.length < 2;
      // 画像だけでなく、タイトル・日付を含む表示枠の縦中央。
      slider.style.setProperty('--slider-button-top', `${viewport.getBoundingClientRect().height / 2}px`);
      viewport.scrollLeft = 0;
    }
    function finish() {
      if (!moving) return;
      clearTimeout(timeout);
      track.style.transition = 'none';
      if (pendingDirection === 1) track.append(track.firstElementChild);
      track.style.transform = 'translateX(0)';
      pendingDirection = 0;
      moving = false;
      update();
    }
    function layout() {
      finish();
      const siteWidth = document.getElementById('site-shell')?.clientWidth || window.innerWidth;
      const desired = siteWidth <= 767 ? 1 : siteWidth <= 1023 ? 2 : 3;
      // 2件以上なら常に1枚分の循環余地を残す。記事の複製は不要。
      shown = cards.length > 1 ? Math.min(desired, cards.length - 1) : 1;
      slider.style.setProperty('--slides-visible', shown);
      slider.style.setProperty('--slider-gap', getComputedStyle(track).columnGap);
      update();
    }
    function move(direction) {
      if (moving || cards.length < 2) return;
      moving = true;
      pendingDirection = direction;
      const step = track.firstElementChild.getBoundingClientRect().width + (parseFloat(getComputedStyle(track).columnGap) || 0);
      track.style.transition = 'none';
      if (direction === -1) {
        track.prepend(track.lastElementChild);
        track.style.transform = `translateX(-${step}px)`;
      }
      // 開始位置を確定してからアニメーションする。
      void track.offsetWidth;
      track.style.transition = reduced.matches ? 'none' : 'transform 320ms ease';
      track.style.transform = direction === 1 ? `translateX(-${step}px)` : 'translateX(0)';
      if (reduced.matches) finish();
      else timeout = setTimeout(finish, 380);
    }
    track.addEventListener('transitionend', (event) => {
      if (event.target === track && event.propertyName === 'transform') finish();
    });
    prev.addEventListener('click', () => move(-1));
    next.addEventListener('click', () => move(1));
    track.addEventListener('keydown', (event) => {
      if (event.target !== track) return;
      if (event.key === 'ArrowLeft' || event.key === 'ArrowRight') {
        event.preventDefault(); move(event.key === 'ArrowLeft' ? -1 : 1);
      }
    });
    let touch = null, suppressClickUntil = 0;
    viewport.addEventListener('pointerdown', (event) => {
      if (event.pointerType === 'mouse') return;
      touch = {x: event.clientX, y: event.clientY, id: event.pointerId};
    });
    viewport.addEventListener('pointerup', (event) => {
      if (!touch || touch.id !== event.pointerId) return;
      const dx = event.clientX - touch.x, dy = event.clientY - touch.y;
      touch = null;
      if (Math.abs(dx) > 40 && Math.abs(dx) > Math.abs(dy) * 1.3) {
        suppressClickUntil = Date.now() + 300;
        move(dx < 0 ? 1 : -1);
      }
    });
    viewport.addEventListener('pointercancel', () => { touch = null; });
    viewport.addEventListener('click', (event) => {
      if (Date.now() < suppressClickUntil) { event.preventDefault(); event.stopPropagation(); }
    }, true);
    if ('ResizeObserver' in window) new ResizeObserver(layout).observe(viewport);
    else window.addEventListener('resize', layout);
    layout();
  });
})();
