/**
 * BOYINSURE member session + header auth (shared across promotions & profile)
 */
const BoyInsureAuth = (() => {
  let member = null;
  let rewards = [];
  const listeners = new Set();

  function isLoggedIn() {
    return Boolean(member);
  }

  function getMember() {
    return member;
  }

  function getRewards() {
    return rewards;
  }

  function getSpinsRemaining() {
    return member?.spins_remaining ?? 0;
  }

  function memberDisplayName(m = member) {
    if (!m) return 'โปรไฟล์';
    if (m.first_name) return m.first_name;
    if (m.name) return String(m.name).trim().split(/\s+/)[0];
    if (m.login_id) return m.login_id;
    return 'โปรไฟล์';
  }

  function formatPoints(value) {
    return Number(value || 0).toLocaleString('th-TH');
  }

  function notify() {
    listeners.forEach((fn) => {
      try {
        fn({ member, rewards, isLoggedIn: isLoggedIn() });
      } catch (_) {}
    });
  }

  function setSession(nextMember, nextRewards = []) {
    member = nextMember || null;
    rewards = Array.isArray(nextRewards) ? nextRewards : [];
    updateHeader();
    notify();
  }

  function updateHeader() {
    const loggedIn = isLoggedIn();

    document.querySelectorAll('.js-header-signin-item, .navbar__actions .js-header-signin').forEach((el) => {
      el.hidden = loggedIn;
      el.classList.toggle('is-auth-hidden', loggedIn);
    });
    document.querySelectorAll('.js-header-register-item, .navbar__actions .js-header-register').forEach((el) => {
      el.hidden = loggedIn;
      el.classList.toggle('is-auth-hidden', loggedIn);
    });
    document.querySelectorAll('.js-header-profile-item, .navbar__actions .js-header-profile').forEach((el) => {
      el.hidden = !loggedIn;
      el.classList.toggle('is-auth-hidden', !loggedIn);
    });
    document.querySelectorAll('.js-header-logout-item, .navbar__actions .js-header-logout').forEach((el) => {
      el.hidden = !loggedIn;
      el.classList.toggle('is-auth-hidden', !loggedIn);
    });

    document.querySelectorAll('.js-header-profile-name').forEach((el) => {
      if (loggedIn) el.textContent = memberDisplayName();
    });

    if (window.lucide?.createIcons) lucide.createIcons();
  }

  async function refresh() {
    if (!window.BoyInsureAPI) return false;
    try {
      const data = await BoyInsureAPI.me();
      if (data.logged_in && data.member) {
        setSession(data.member, data.rewards || []);
        return true;
      }
    } catch (_) {}
    setSession(null, []);
    return false;
  }

  async function logout() {
    try {
      if (window.BoyInsureAPI) await BoyInsureAPI.logoutMember();
    } catch (_) {}
    setSession(null, []);
  }

  function subscribe(fn) {
    listeners.add(fn);
    return () => listeners.delete(fn);
  }

  function bindHeaderActions() {
    document.querySelectorAll('.js-header-logout').forEach((btn) => {
      btn.addEventListener('click', async (e) => {
        e.preventDefault();
        await logout();
        if (document.body.dataset.page === 'profile') {
          window.location.href = 'promotions.html';
        }
      });
    });
  }

  async function init() {
    bindHeaderActions();
    updateHeader();
    await refresh();
  }

  return {
    init,
    refresh,
    logout,
    subscribe,
    setSession,
    updateHeader,
    isLoggedIn,
    getMember,
    getRewards,
    getSpinsRemaining,
    memberDisplayName,
    formatPoints,
  };
})();

window.BoyInsureAuth = BoyInsureAuth;

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => BoyInsureAuth.init());
} else {
  BoyInsureAuth.init();
}
