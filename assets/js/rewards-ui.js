/**
 * Reward cards (home-news-prize-card style) + detail slider
 */
const BoyInsureRewardsUI = (() => {
  'use strict';

  const STATUS = {
    won: 'ได้รับรางวัล',
    pending_verify: 'รอตรวจสอบ',
    approved: 'อนุมัติแล้ว',
    shipping: 'กำลังจัดส่ง',
    sent: 'จัดส่งแล้ว',
    redeemed: 'ใช้สิทธิ์แล้ว',
    rejected: 'ไม่ผ่านเงื่อนไข',
    expired: 'หมดอายุ',
  };

  function prizeLogo(path) {
    if (!path) return '';
    const clean = String(path).replace(/^assets\//, '').replace(/^https?:\/\/[^/]+\/[^/]+\/assets\//, '');
    const link = document.createElement('a');
    link.href = clean.startsWith('http') ? clean : `assets/${clean.replace(/^\/+/, '')}`;
    return link.href;
  }

  function normalizeReward(r) {
    const logo = r.logo || (r.logo_path ? prizeLogo(r.logo_path) : '');
    const qty = r.qty != null ? Number(r.qty) : null;
    return {
      name: String(r.name || r.label || r.short_name || 'รางวัล').replace(/\n/g, ' '),
      shortName: r.short_name || r.short || '',
      detail: r.detail || '',
      logo,
      qty,
      status: r.status || 'won',
      createdAt: r.created_at || r.createdAt || '',
    };
  }

  function cardFooter(r) {
    if (r.qty != null && !Number.isNaN(r.qty)) return `${r.qty} รางวัล`;
    return statusLabel(r.status);
  }

  function formatDate(value) {
    if (!value) return '';
    const d = new Date(String(value).replace(' ', 'T'));
    if (Number.isNaN(d.getTime())) return value;
    return d.toLocaleString('th-TH', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    });
  }

  function statusLabel(status) {
    return STATUS[status] || status || 'รางวัล';
  }

  function renderCard(reward, index) {
    const r = normalizeReward(reward);
    return `
      <button type="button" class="home-news-prize-card member-reward-picker" data-reward-index="${index}">
        <div class="home-news-prize-card__logo">
          ${r.logo
            ? `<img src="${r.logo}" alt="${r.shortName || r.name}" width="56" height="56" loading="lazy" />`
            : '<i data-lucide="gift" aria-hidden="true"></i>'}
        </div>
        <strong class="home-news-prize-card__name">${r.name}</strong>
        <span class="home-news-prize-card__qty">${cardFooter(r)}</span>
      </button>
    `;
  }

  function renderCards(container, rewards) {
    if (!container) return [];
    const list = Array.isArray(rewards) ? rewards : [];
    container.innerHTML = list.map((reward, index) => renderCard(reward, index)).join('');
    if (window.lucide?.createIcons) lucide.createIcons();
    return list.map(normalizeReward);
  }

  function createSlider(modal) {
    if (!modal) return null;

    const track = modal.querySelector('.reward-slider__track');
    const counter = modal.querySelector('.reward-slider__counter');
    const dots = modal.querySelector('.reward-slider__dots');
    const prevBtn = modal.querySelector('.reward-slider__nav--prev');
    const nextBtn = modal.querySelector('.reward-slider__nav--next');
    const viewport = modal.querySelector('.reward-slider__viewport');
    let rewards = [];
    let index = 0;

    function update() {
      if (!track) return;
      track.style.transform = `translate3d(-${index * 100}%, 0, 0)`;
      if (counter) counter.textContent = rewards.length ? `${index + 1} / ${rewards.length}` : '';
      if (prevBtn) prevBtn.disabled = index <= 0;
      if (nextBtn) nextBtn.disabled = index >= rewards.length - 1;
      dots?.querySelectorAll('.reward-slider__dot').forEach((dot, i) => {
        dot.classList.toggle('is-active', i === index);
        dot.setAttribute('aria-selected', i === index ? 'true' : 'false');
      });
    }

    function buildSlides(list) {
      rewards = list.map(normalizeReward);
      if (!track) return;
      track.innerHTML = rewards.map((r) => `
        <article class="reward-slider__slide">
          <div class="reward-slider__logo">
            ${r.logo
              ? `<img src="${r.logo}" alt="${r.shortName || r.name}" loading="lazy" />`
              : '<i data-lucide="gift" aria-hidden="true"></i>'}
          </div>
          <h2 class="reward-slider__title">${r.name}</h2>
          <span class="reward-slider__status">${cardFooter(r)}</span>
          ${r.createdAt ? `<time class="reward-slider__date">${formatDate(r.createdAt)}</time>` : ''}
          ${r.detail ? `<p class="reward-slider__detail">${r.detail}</p>` : ''}
        </article>
      `).join('');

      if (dots) {
        dots.innerHTML = rewards.map((_, i) => `
          <button type="button" class="reward-slider__dot${i === index ? ' is-active' : ''}" data-slide="${i}" aria-label="รางวัลที่ ${i + 1}" aria-selected="${i === index ? 'true' : 'false'}"></button>
        `).join('');
        dots.querySelectorAll('.reward-slider__dot').forEach((dot) => {
          dot.addEventListener('click', () => goTo(parseInt(dot.dataset.slide, 10)));
        });
      }

      if (window.lucide?.createIcons) lucide.createIcons();
      update();
    }

    function goTo(next) {
      if (!rewards.length) return;
      index = Math.max(0, Math.min(next, rewards.length - 1));
      update();
    }

    function open(startIndex, list) {
      index = Math.max(0, startIndex || 0);
      buildSlides(list || []);
      modal.hidden = false;
      document.body.classList.add('reward-slider-open');
      modal.querySelector('.reward-slider__close')?.focus();
    }

    function close() {
      modal.hidden = true;
      document.body.classList.remove('reward-slider-open');
    }

    prevBtn?.addEventListener('click', () => goTo(index - 1));
    nextBtn?.addEventListener('click', () => goTo(index + 1));
    modal.querySelectorAll('[data-reward-slider-close]').forEach((el) => {
      el.addEventListener('click', close);
    });

    document.addEventListener('keydown', (e) => {
      if (modal.hidden) return;
      if (e.key === 'Escape') close();
      if (e.key === 'ArrowLeft') goTo(index - 1);
      if (e.key === 'ArrowRight') goTo(index + 1);
    });

    let touchX = 0;
    viewport?.addEventListener('touchstart', (e) => {
      touchX = e.changedTouches[0].clientX;
    }, { passive: true });
    viewport?.addEventListener('touchend', (e) => {
      const diff = e.changedTouches[0].clientX - touchX;
      if (diff > 48) goTo(index - 1);
      if (diff < -48) goTo(index + 1);
    }, { passive: true });

    return { open, close, goTo };
  }

  const rowSliderState = new WeakMap();

  function stopRowSlider(container) {
    const state = rowSliderState.get(container);
    if (!state) return;
    if (state.timer) clearInterval(state.timer);
    rowSliderState.delete(container);
  }

  function initRowSlider(container) {
    if (!container) return;
    const wrap = container.closest('.prize-cards-row-wrap');
    if (!wrap) return;

    stopRowSlider(container);

    let dotsEl = wrap.querySelector('.prize-cards-row__dots');
    if (!dotsEl) {
      dotsEl = document.createElement('div');
      dotsEl.className = 'prize-cards-row__dots';
      dotsEl.setAttribute('role', 'tablist');
      dotsEl.setAttribute('aria-label', 'ตำแหน่งสไลด์รางวัล');
      wrap.appendChild(dotsEl);
    }

    const state = {
      activeIndex: 0,
      timer: null,
      dotsEl,
      wrap,
      container,
      getCards: () => [...container.querySelectorAll('.home-news-prize-card')],
    };

    state.setActive = (index) => {
      state.activeIndex = index;
      dotsEl.querySelectorAll('.prize-cards-row__dot').forEach((dot, i) => {
        const on = i === index;
        dot.classList.toggle('is-active', on);
        dot.setAttribute('aria-selected', on ? 'true' : 'false');
      });
    };

    state.scrollToIndex = (index) => {
      const cards = state.getCards();
      if (!cards.length) return;
      const next = ((index % cards.length) + cards.length) % cards.length;
      const card = cards[next];
      const left = card.offsetLeft - container.offsetLeft;
      container.scrollTo({ left, behavior: 'smooth' });
      state.setActive(next);
    };

    state.buildDots = () => {
      const cards = state.getCards();
      dotsEl.innerHTML = cards.map((_, i) => `
        <button type="button" class="prize-cards-row__dot${i === 0 ? ' is-active' : ''}" data-index="${i}" aria-label="รางวัลที่ ${i + 1}" aria-selected="${i === 0 ? 'true' : 'false'}"></button>
      `).join('');
      dotsEl.querySelectorAll('.prize-cards-row__dot').forEach((dot) => {
        dot.addEventListener('click', () => state.scrollToIndex(parseInt(dot.dataset.index, 10)));
      });
      return cards;
    };

    const autoMs = 3500;
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    state.startAuto = () => {
      if (reducedMotion || state.timer) return;
      state.timer = setInterval(() => {
        if (wrap.matches(':hover') || document.hidden) return;
        state.scrollToIndex(state.activeIndex + 1);
      }, autoMs);
    };

    state.pauseAuto = () => {
      if (!state.timer) return;
      clearInterval(state.timer);
      state.timer = null;
    };

    if (!container.dataset.rowSliderBound) {
      container.dataset.rowSliderBound = '1';
      let scrollTimer;
      container.addEventListener('scroll', () => {
        const s = rowSliderState.get(container);
        if (!s) return;
        clearTimeout(scrollTimer);
        scrollTimer = setTimeout(() => {
          const cards = s.getCards();
          if (!cards.length) return;
          let closest = 0;
          let minDist = Infinity;
          cards.forEach((card, i) => {
            const dist = Math.abs(card.offsetLeft - container.scrollLeft);
            if (dist < minDist) {
              minDist = dist;
              closest = i;
            }
          });
          s.setActive(closest);
        }, 100);
      }, { passive: true });

      wrap.addEventListener('mouseenter', () => rowSliderState.get(container)?.pauseAuto());
      wrap.addEventListener('mouseleave', () => rowSliderState.get(container)?.startAuto());
      wrap.addEventListener('focusin', () => rowSliderState.get(container)?.pauseAuto());
      wrap.addEventListener('focusout', (e) => {
        if (wrap.contains(e.relatedTarget)) return;
        rowSliderState.get(container)?.startAuto();
      });
    }

    const cards = state.buildDots();
    rowSliderState.set(container, state);

    if (cards.length <= 1) {
      dotsEl.innerHTML = '';
      container.scrollLeft = 0;
      return;
    }

    container.scrollLeft = 0;
    state.setActive(0);
    state.startAuto();
  }

  function bindGrid(container, items, slider, pickerClass = 'member-reward-picker') {
    renderCards(container, items);
    container.querySelectorAll(`.${pickerClass}`).forEach((btn) => {
      btn.addEventListener('click', () => {
        slider?.open(parseInt(btn.dataset.rewardIndex, 10), items);
      });
    });
    const wrap = container.closest('.prize-cards-row-wrap');
    const isStaticGrid = wrap?.classList.contains('prize-cards-row-wrap--grid')
      || Boolean(container.closest('#panel-rewards'));
    if (isStaticGrid) {
      stopRowSlider(container);
      wrap?.querySelector('.prize-cards-row__dots')?.remove();
      return;
    }
    initRowSlider(container);
  }

  function renderCatalogCard(item, index) {
    const r = normalizeReward(item);
    return `
      <button type="button" class="home-news-prize-card prize-catalog-picker" data-reward-index="${index}">
        <div class="home-news-prize-card__logo">
          ${r.logo
            ? `<img src="${r.logo}" alt="${r.shortName || r.name}" width="56" height="56" loading="lazy" />`
            : '<i data-lucide="gift" aria-hidden="true"></i>'}
        </div>
        <strong class="home-news-prize-card__name">${r.name}</strong>
        <span class="home-news-prize-card__qty">${cardFooter(r)}</span>
      </button>
    `;
  }

  function bindCatalogGrid(container, items, slider) {
    if (!container) return;
    const list = Array.isArray(items) ? items : [];
    container.innerHTML = list.map((item, index) => renderCatalogCard(item, index)).join('');
    if (window.lucide?.createIcons) lucide.createIcons();
    container.querySelectorAll('.prize-catalog-picker').forEach((btn) => {
      btn.addEventListener('click', () => {
        slider?.open(parseInt(btn.dataset.rewardIndex, 10), list);
      });
    });
    initRowSlider(container);
  }

  return {
    STATUS,
    normalizeReward,
    formatDate,
    statusLabel,
    cardFooter,
    renderCards,
    createSlider,
    bindGrid,
    bindCatalogGrid,
    initRowSlider,
  };
})();

window.BoyInsureRewardsUI = BoyInsureRewardsUI;
