(() => {
  'use strict';

  const layout = document.getElementById('memberLayout');
  const guest = document.getElementById('memberGuest');
  const rewardSlider = BoyInsureRewardsUI.createSlider(document.getElementById('rewardSlider'));
  let currentRewards = [];

  function formatDate(value) {
    if (!value) return '—';
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

  function formatDateOnly(value) {
    if (!value) return '—';
    const d = new Date(String(value).replace(' ', 'T'));
    if (Number.isNaN(d.getTime())) return value;
    return d.toLocaleDateString('th-TH', { year: 'numeric', month: 'long', day: 'numeric' });
  }

  function maskNationalId(id) {
    const digits = String(id || '').replace(/\D/g, '');
    if (digits.length !== 13) return id || '—';
    return `${digits.slice(0, 1)}-${digits.slice(1, 5)}-${digits.slice(5, 10)}-${digits.slice(10, 12)}-${digits.slice(12)}`;
  }

  function showPanel(name) {
    document.querySelectorAll('.member-panel').forEach((panel) => {
      const active = panel.dataset.panel === name;
      panel.hidden = !active;
      panel.classList.toggle('is-active', active);
    });
    document.querySelectorAll('.member-sidebar__link[data-panel]').forEach((link) => {
      link.classList.toggle('is-active', link.dataset.panel === name);
    });
  }

  function initSidebarNav() {
    document.querySelectorAll('.member-sidebar__link[data-panel]').forEach((link) => {
      link.addEventListener('click', (e) => {
        e.preventDefault();
        const panel = link.dataset.panel;
        showPanel(panel);
        history.replaceState(null, '', `#${panel}`);
      });
    });

    const hash = (location.hash || '#overview').replace('#', '');
    showPanel(['overview', 'info', 'activities', 'rewards'].includes(hash) ? hash : 'overview');
  }

  function renderOverview(data) {
    const m = data.member;
    const stats = data.stats || {};
    document.getElementById('sidebarName').textContent = m.name || BoyInsureAuth.memberDisplayName(m);
    document.getElementById('sidebarTier').textContent = m.tier_name || 'ทั่วไป';
    document.getElementById('statSpins').textContent = String(m.spins_remaining ?? 0);
    document.getElementById('statPoints').textContent = BoyInsureAuth.formatPoints(m.points);
    document.getElementById('statTotalSpins').textContent = String(stats.total_spins ?? 0);
    document.getElementById('statTotalRewards').textContent = String(stats.total_rewards ?? 0);

    const statusEl = document.getElementById('overviewSpinStatus');
    const spinBtn = document.getElementById('overviewSpinBtn');
    if ((m.spins_remaining ?? 0) > 0) {
      statusEl.textContent = `คุณมีสิทธิ์หมุนวงล้อ ${m.spins_remaining} ครั้ง — กดปุ่มด้านล่างเพื่อไปหมุน`;
      spinBtn.hidden = false;
    } else {
      statusEl.textContent = 'คุณใช้สิทธิ์หมุนวงล้อครบแล้ว';
      spinBtn.hidden = true;
    }
  }

  function renderInfo(m) {
    const rows = [
      ['ชื่อ-นามสกุล', m.name || '—'],
      ['ไอดีเข้าสู่ระบบ', m.login_id || '—'],
      ['เบอร์โทรศัพท์', m.phone || '—'],
      ['อีเมล', m.email || '—'],
      ['เลขบัตรประชาชน', maskNationalId(m.national_id)],
      ['วันเกิด', formatDateOnly(m.birth_date)],
      ['ระดับสมาชิก', m.tier_name || 'ทั่วไป'],
      ['สมัครเมื่อ', formatDate(m.created_at)],
    ];
    document.getElementById('memberInfoList').innerHTML = rows.map(([label, value]) => `
      <div class="member-info__row">
        <dt>${label}</dt>
        <dd>${value}</dd>
      </div>
    `).join('');
  }

  function renderSpins(spins) {
    const tbody = document.querySelector('#spinsTable tbody');
    const empty = document.getElementById('spinsEmpty');
    if (!spins.length) {
      tbody.innerHTML = '';
      empty.hidden = false;
      return;
    }
    empty.hidden = true;
    tbody.innerHTML = spins.map((row) => `
      <tr>
        <td>${formatDate(row.created_at)}</td>
        <td>${row.game_name || 'วงล้อโชคดี'}</td>
        <td>${row.prize_name || row.short_name || '—'}</td>
      </tr>
    `).join('');
  }

  function renderRegistrations(regs) {
    const tbody = document.querySelector('#regsTable tbody');
    const empty = document.getElementById('regsEmpty');
    if (!regs.length) {
      tbody.innerHTML = '';
      empty.hidden = false;
      return;
    }
    empty.hidden = true;
    tbody.innerHTML = regs.map((row) => `
      <tr>
        <td>${formatDate(row.created_at)}</td>
        <td>${row.spin_log_id ? 'หมุนแล้ว' : 'ลงทะเบียนแล้ว'}</td>
      </tr>
    `).join('');
  }

  function renderRewards(rewards) {
    const grid = document.getElementById('rewardsGrid');
    const empty = document.getElementById('rewardsEmpty');
    currentRewards = Array.isArray(rewards) ? rewards : [];
    if (!currentRewards.length) {
      if (grid) grid.innerHTML = '';
      if (empty) empty.hidden = false;
      return;
    }
    if (empty) empty.hidden = true;
    BoyInsureRewardsUI.bindGrid(grid, currentRewards, rewardSlider);
  }

  function renderDashboard(data) {
    renderOverview(data);
    renderInfo(data.member);
    renderSpins(data.spins || []);
    renderRegistrations(data.registrations || []);
    renderRewards(data.rewards || []);
    BoyInsureAuth.setSession(data.member, data.rewards || []);
    if (window.lucide?.createIcons) lucide.createIcons();
  }

  async function loadDashboard() {
    try {
      const data = await BoyInsureAPI.dashboard();
      if (!data.logged_in) throw new Error('not logged in');
      layout.hidden = false;
      guest.hidden = true;
      renderDashboard(data);
    } catch (_) {
      layout.hidden = true;
      guest.hidden = false;
    }
  }

  BoyInsureAuth.subscribe(() => {
    if (!BoyInsureAuth.isLoggedIn()) {
      layout.hidden = true;
      guest.hidden = false;
    }
  });

  initSidebarNav();
  loadDashboard();
})();
