/**
 * BOYINSURE API client
 */
const BoyInsureAPI = (() => {
  const base = (() => {
    const link = document.createElement('a');
    link.href = 'api/';
    return link.href.replace(/\/?$/, '/');
  })();

  async function request(path, options = {}) {
    const res = await fetch(base + path, {
      credentials: 'same-origin',
      headers: { 'Content-Type': 'application/json', ...(options.headers || {}) },
      ...options,
    });
    const data = await res.json().catch(() => ({}));
    if (!res.ok) {
      throw new Error(data.error || 'เกิดข้อผิดพลาด');
    }
    return data;
  }

  return {
    submitContact(payload) {
      return request('contact.php', { method: 'POST', body: JSON.stringify(payload) });
    },
    registerMember(payload) {
      return request('member/register.php', { method: 'POST', body: JSON.stringify(payload) });
    },
    loginMember(payload) {
      return request('member/login.php', { method: 'POST', body: JSON.stringify(payload) });
    },
    logoutMember() {
      return request('member/logout.php', { method: 'POST', body: '{}' });
    },
    prepareSpin() {
      return request('member/prepare-spin.php', { method: 'POST', body: '{}' });
    },
    selectReward(payload) {
      return request('member/select-reward.php', { method: 'POST', body: JSON.stringify(payload) });
    },
    claimReward(payload) {
      return request('member/claim-reward.php', { method: 'POST', body: JSON.stringify(payload) });
    },
    dashboard() {
      return request('member/dashboard.php');
    },
    me() {
      return request('member/me.php');
    },
    spin() {
      return request('spin/spin.php', { method: 'POST', body: '{}' });
    },
    prizes() {
      return request('spin/prizes.php');
    },
    winners(limit = 10) {
      return request(`spin/winners.php?limit=${limit}`);
    },
    insuranceCategories() {
      return request('insurance/categories.php');
    },
    insuranceDetail(slug) {
      return request(`insurance/detail.php?slug=${encodeURIComponent(slug)}`);
    },
    sitePublic() {
      return request('site/public.php');
    },
    articleCategories() {
      return request('articles/categories.php');
    },
    articleDetail(slug) {
      return request(`articles/detail.php?slug=${encodeURIComponent(slug)}`);
    },
  };
})();

window.BoyInsureAPI = BoyInsureAPI;
