document.addEventListener('DOMContentLoaded', () => {
  if (window.lucide?.createIcons) lucide.createIcons();

  const sidebar = document.getElementById('adminSidebar');
  const overlay = document.getElementById('sidebarOverlay');
  const toggle = document.getElementById('sidebarToggle');

  function closeSidebar() {
    sidebar?.classList.remove('is-open');
    overlay?.classList.remove('is-visible');
    if (!document.querySelector('.admin-modal.is-open')) {
      document.body.style.overflow = '';
    }
  }

  function openSidebar() {
    sidebar?.classList.add('is-open');
    overlay?.classList.add('is-visible');
    document.body.style.overflow = 'hidden';
  }

  toggle?.addEventListener('click', () => {
    if (sidebar?.classList.contains('is-open')) closeSidebar();
    else openSidebar();
  });

  overlay?.addEventListener('click', closeSidebar);

  document.querySelectorAll('.admin-nav__link').forEach((link) => {
    link.addEventListener('click', () => {
      if (window.innerWidth <= 900) closeSidebar();
    });
  });

  document.querySelectorAll('.admin-flash').forEach((flash) => {
    const closeBtn = flash.querySelector('.admin-flash__close');
    closeBtn?.addEventListener('click', () => {
      flash.style.opacity = '0';
      flash.style.transform = 'translateY(-6px)';
      setTimeout(() => flash.remove(), 200);
    });
    setTimeout(() => {
      if (flash.parentNode) {
        flash.style.transition = 'opacity 0.2s, transform 0.2s';
        flash.style.opacity = '0';
        flash.style.transform = 'translateY(-6px)';
        setTimeout(() => flash.remove(), 200);
      }
    }, 5000);
  });

  function openModal(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    modal.hidden = false;
    modal.classList.add('is-open');
    document.body.style.overflow = 'hidden';
    if (window.lucide?.createIcons) lucide.createIcons();
    initAdminSelects(modal);
    modal.querySelector('.admin-select__trigger, input, textarea')?.focus();
  }

  function closeModal(modal) {
    modal.classList.remove('is-open');
    modal.hidden = true;
    if (!document.querySelector('.admin-modal.is-open')) {
      document.body.style.overflow = '';
    }
  }

  document.querySelectorAll('[data-admin-modal-open]').forEach((btn) => {
    btn.addEventListener('click', () => {
      openModal(btn.getAttribute('data-admin-modal-open'));
    });
  });

  document.querySelectorAll('[data-admin-modal-close]').forEach((el) => {
    el.addEventListener('click', () => {
      const modal = el.closest('.admin-modal');
      if (modal) closeModal(modal);
    });
  });

  document.addEventListener('keydown', (e) => {
    if (e.key !== 'Escape') return;
    closeAllSelects();
    document.querySelectorAll('.admin-modal.is-open').forEach((modal) => closeModal(modal));
  });

  const openSelects = new Set();

  function closeAllSelects(except) {
    openSelects.forEach((wrapper) => {
      if (wrapper !== except) {
        wrapper.classList.remove('is-open');
        wrapper.querySelector('.admin-select__menu')?.setAttribute('hidden', '');
        wrapper.querySelector('.admin-select__trigger')?.setAttribute('aria-expanded', 'false');
        openSelects.delete(wrapper);
      }
    });
  }

  function enhanceSelect(select) {
    if (select.dataset.adminSelectInit) return;
    select.dataset.adminSelectInit = '1';

    const wrapper = document.createElement('div');
    wrapper.className = 'admin-select';

    select.classList.add('admin-select__native');
    select.parentNode?.insertBefore(wrapper, select);
    wrapper.appendChild(select);

    const trigger = document.createElement('button');
    trigger.type = 'button';
    trigger.className = 'admin-select__trigger';
    trigger.setAttribute('aria-haspopup', 'listbox');
    trigger.setAttribute('aria-expanded', 'false');
    trigger.innerHTML =
      '<span class="admin-select__label"></span>' +
      '<svg class="admin-select__chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">' +
      '<path d="m6 9 6 6 6-6"/></svg>';

    const menu = document.createElement('ul');
    menu.className = 'admin-select__menu';
    menu.setAttribute('role', 'listbox');
    menu.hidden = true;

    function buildOptions() {
      menu.innerHTML = '';
      Array.from(select.options).forEach((opt) => {
        const li = document.createElement('li');
        li.className = 'admin-select__option';
        li.dataset.value = opt.value;
        li.textContent = opt.textContent;
        li.setAttribute('role', 'option');
        if (opt.selected) li.classList.add('is-selected');
        li.addEventListener('click', (e) => {
          e.stopPropagation();
          select.value = opt.value;
          select.dispatchEvent(new Event('change', { bubbles: true }));
          updateUI();
          closeMenu();
        });
        menu.appendChild(li);
      });
    }

    function updateUI() {
      const opt = select.options[select.selectedIndex];
      trigger.querySelector('.admin-select__label').textContent = opt?.textContent || '—';
      menu.querySelectorAll('.admin-select__option').forEach((li) => {
        li.classList.toggle('is-selected', li.dataset.value === select.value);
      });
    }

    function openMenu() {
      closeAllSelects(wrapper);
      buildOptions();
      wrapper.classList.add('is-open');
      menu.hidden = false;
      trigger.setAttribute('aria-expanded', 'true');
      openSelects.add(wrapper);
    }

    function closeMenu() {
      wrapper.classList.remove('is-open');
      menu.hidden = true;
      trigger.setAttribute('aria-expanded', 'false');
      openSelects.delete(wrapper);
    }

    trigger.addEventListener('click', (e) => {
      e.stopPropagation();
      if (wrapper.classList.contains('is-open')) closeMenu();
      else openMenu();
    });

    select.addEventListener('change', updateUI);
    wrapper.appendChild(trigger);
    wrapper.appendChild(menu);
    updateUI();
  }

  function initAdminSelects(root = document) {
    root.querySelectorAll('select').forEach(enhanceSelect);
    if (window.lucide?.createIcons) lucide.createIcons();
  }

  document.addEventListener('click', () => closeAllSelects());
  initAdminSelects();
});
