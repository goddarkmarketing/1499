(() => {
  'use strict';

  const assetRoot = (() => {
    const link = document.createElement('a');
    link.href = 'assets/';
    return link.href;
  })();

  function asset(path) {
    const link = document.createElement('a');
    link.href = assetRoot + path.replace(/^\/+/, '');
    return link.href;
  }

  function prizeIcon(group) {
    return `img/icon/Group ${group}.png`;
  }

  const PRIZES = [
    {
      name: 'บัตรโลตัส',
      short: 'โลตัส',
      detail: 'บัตรของขวัญมูลค่า 500 บาท ใช้ได้ทุกสาขา Lotus’s ทั่วประเทศ',
      color: '#fff9e6',
      logo: prizeIcon(1),
    },
    {
      name: 'บัตรน้ำมันปตท.',
      short: 'ปตท.',
      detail: 'บัตรเติมน้ำมันมูลค่า 500 บาท ใช้ได้ที่ ปตท. และ ฟิตกอล',
      color: '#e8f4fd',
      logo: prizeIcon(2),
    },
    {
      name: 'Voucher ตรวจสุขภาพ\nเครือโรงพยาบาลกรุงเทพ',
      short: 'รพ.กรุงเทพ',
      detail: 'ตรวจสุขภาพพื้นฐาน ครอบคลุมเลือด ปัสสาวะ และ X-Ray ที่เครือโรงพยาบาลกรุงเทพ',
      color: '#fde8f0',
      logo: prizeIcon(3),
    },
    {
      name: 'Voucher Super sport',
      short: 'Supersports',
      detail: 'Voucher มูลค่า 1,000 บาท ใช้ซื้อสินค้ากีฬาและเครื่องแต่งกายที่ Supersports',
      color: '#fff0e8',
      logo: prizeIcon(4),
    },
    {
      name: 'Voucher ทันตกรรม',
      short: 'ทันตกรรม',
      detail: 'ฟอกสีฟัน หรือขูดหินปูน 1 ครั้ง ที่คลินิกทันตกรรมพันธมิตร',
      color: '#e8f8ff',
      logo: prizeIcon(5),
    },
    {
      name: 'Voucher โบท็อกซ์ฟิลเลอร์',
      short: 'โบท็อกซ์',
      detail: 'Voucher โบท็อกซ์หรือฟิลเลอร์ 1 จุด ที่คลินิกความงามพันธมิตร',
      color: '#fdf0ff',
      logo: prizeIcon(6),
    },
    {
      name: 'Voucher Jett Fitness',
      short: 'Jett Fitness',
      detail: 'สมาชิกฟิตเนส 1 เดือน ใช้ได้ทุกสาขา Jetts Fitness 24 ชม.',
      color: '#fff5e8',
      logo: prizeIcon(7),
    },
    {
      name: 'Voucher Big C',
      short: 'Big C',
      detail: 'Voucher มูลค่า 500 บาท ใช้ซื้อสินค้าทุกประเภทที่ Big C',
      color: '#e8fdf0',
      logo: prizeIcon(11),
    },
    {
      name: 'Voucher โอ้กะจู๋',
      short: 'โอ้กะจู๋',
      detail: 'Voucher อาหารออร์แกนิกมูลค่า 500 บาท ใช้ได้ที่ร้านโอ้กะจู๋',
      color: '#f0ffe8',
      logo: 'img/prizes/ohkajhu.png',
    },
    {
      name: 'ประกันรถยนต์ ชั้น 1',
      short: 'ประกันรถ',
      detail: 'ประกันรถยนต์ชั้น 1 คุ้มครองรถ ผู้ขับ และบุคคลภายนอก ตามเงื่อนไขกรมธรรม์',
      color: '#e8eeff',
      logo: prizeIcon(13),
    },
    {
      name: 'ประกันอุบัติเหตุ 400,000',
      short: 'อุบัติเหตุ',
      detail: 'ความคุ้มครองสูงสุด 400,000 บาท กรณีเสียชีวิตหรือทุพพลภาพถาวรจากอุบัติเหตุ',
      color: '#fff8e8',
      logo: prizeIcon(8),
    },
    {
      name: 'ประกันอัคคีภัย ที่อยู่อาศัย',
      short: 'อัคคีภัย',
      detail: 'คุ้มครองบ้านและทรัพย์สินจากไฟไหม้ ฟ้าผ่า และภัยธรรมชาติ ตามเงื่อนไขกรมธรรม์',
      color: '#ffe8e8',
      logo: prizeIcon(9),
    },
    {
      name: 'Voucher ติดตั้ง โซล่าร์เซลล์',
      short: 'โซล่าร์',
      detail: 'ส่วนลดติดตั้งระบบโซล่าร์เซลล์สำหรับบ้าน มูลค่า 5,000 บาท',
      color: '#fffde8',
      logo: prizeIcon(10),
    },
  ];

  /* 12 ช่องวงล้อ (เรียงตามเข็มนาฬิกาจากด้านบน) */
  const WHEEL_SEGMENTS = 12;
  const DEG_PER_SEG = 360 / WHEEL_SEGMENTS;
  const WHEEL_OFFSET = 0;

  /* lotus, ptt, hospital, supersports, dental, botox, jett, ohkajhu, car, accident, fire, solar */
  const WHEEL_TO_PRIZE = [0, 1, 2, 3, 4, 5, 6, 8, 9, 10, 11, 12];

  const PRIZE_CATALOG_DISPLAY = [
    { prizeIndex: 0, label: "บัตร Lotus's มูลค่า 500 บาท", qty: 100 },
    { prizeIndex: 1, label: 'บัตร PTT Station มูลค่า 500 บาท', qty: 100 },
    { prizeIndex: 8, label: 'Voucher โอ้กะจู๋ มูลค่า 500 บาท', qty: 40 },
    { prizeIndex: 9, label: 'ประกันรถยนต์ ชั้น 1', qty: 10 },
    { prizeIndex: 3, label: 'Voucher Supersports มูลค่า 500 บาท', qty: 50 },
  ];

  const PRIZE_STOCK_BY_INDEX = {
    0: 100, 1: 100, 2: 30, 3: 50, 4: 25, 5: 20, 6: 30, 7: 40,
    8: 20, 9: 10, 10: 15, 11: 12,
  };

  const ALL_WINNERS = [
    { name: 'คุณสมชาย ข.', location: 'เชียงราย', prize: "บัตร Lotus's 500 บาท", time: 'เมื่อ 10 นาทีที่แล้ว', type: 'voucher' },
    { name: 'คุณมาลี ว.', location: 'กรุงเทพฯ', prize: 'บัตร PTT Station 500 บาท', time: 'เมื่อ 25 นาทีที่แล้ว', type: 'voucher' },
    { name: 'คุณณัฐ ส.', location: 'เชียงใหม่', prize: 'ประกันอุบัติเหตุส่วนบุคคล', time: 'เมื่อ 45 นาทีที่แล้ว', type: 'insurance' },
    { name: 'คุณพิม พ.', location: 'นนทบุรี', prize: 'ประกันรถยนต์ ชั้น 1', time: 'เมื่อ 1 ชั่วโมงที่แล้ว', type: 'insurance' },
    { name: 'คุณวิชัย ก.', location: 'ขอนแก่น', prize: 'Voucher Supersports 500 บาท', time: 'เมื่อ 2 ชั่วโมงที่แล้ว', type: 'voucher' },
    { name: 'คุณอรุณ ท.', location: 'ภูเก็ต', prize: 'Voucher Big C 500 บาท', time: 'เมื่อ 3 ชั่วโมงที่แล้ว', type: 'voucher' },
    { name: 'คุณจิตรา น.', location: 'สงขลา', prize: 'Voucher ตรวจสุขภาพ', time: 'เมื่อ 4 ชั่วโมงที่แล้ว', type: 'voucher' },
    { name: 'คุณธนา ล.', location: 'นครราชสีมา', prize: 'ประกันอัคคีภัย ที่อยู่อาศัย', time: 'เมื่อ 5 ชั่วโมงที่แล้ว', type: 'insurance' },
    { name: 'คุณแพรว ร.', location: 'อุดรธานี', prize: 'Voucher Jett Fitness', time: 'เมื่อ 6 ชั่วโมงที่แล้ว', type: 'voucher' },
    { name: 'คุณกิตติ ช.', location: 'ระยอง', prize: 'Voucher ทันตกรรม', time: 'เมื่อ 8 ชั่วโมงที่แล้ว', type: 'voucher' },
  ];

  const RECENT_WINNERS = ALL_WINNERS.slice(0, 5);

  const PROMO_START = new Date('2026-06-20T00:00:00');
  const PROMO_END = new Date('2026-06-27T23:59:59');

  const wheelCanvas = document.getElementById('wheelCanvas');
  const wheelCtx = wheelCanvas?.getContext('2d');
  const wheelRotator = document.getElementById('wheelRotator');
  const wheelPin = document.getElementById('wheelPin');
  const wheelLights = document.getElementById('wheelLights');
  const spinBtn = document.getElementById('spinBtn');
  const spinsLeftEl = document.getElementById('spinsLeft');
  const wheelPanelStatus = document.getElementById('wheelPanelStatus');
  const wheelStatusGuest = document.getElementById('wheelStatusGuest');
  const wheelStatusHasSpins = document.getElementById('wheelStatusHasSpins');
  const wheelStatusNoSpins = document.getElementById('wheelStatusNoSpins');
  const myRewardsBtn = document.getElementById('myRewardsBtn');
  const termsBtn = document.getElementById('termsBtn');
  const rewardsOverlay = document.getElementById('rewardsOverlay');
  const rewardsCardGrid = document.getElementById('rewardsCardGrid');
  const rewardsEmpty = document.getElementById('rewardsEmpty');
  const rewardSlider = BoyInsureRewardsUI.createSlider(document.getElementById('rewardSlider'));
  const prizeCatalogSlider = BoyInsureRewardsUI.createSlider(document.getElementById('prizeCatalogSlider'));
  const closeRewards = document.getElementById('closeRewards');
  const termsOverlay = document.getElementById('termsOverlay');
  const closeTerms = document.getElementById('closeTerms');
  const prizesGrid = document.getElementById('prizesGrid');
  const allPrizesOverlay = document.getElementById('allPrizesOverlay');
  const allPrizesList = document.getElementById('allPrizesList');
  const closeAllPrizes = document.getElementById('closeAllPrizes');
  const allWinnersOverlay = document.getElementById('allWinnersOverlay');
  const allWinnersList = document.getElementById('allWinnersList');
  const closeAllWinners = document.getElementById('closeAllWinners');
  const prizeCompare = document.getElementById('prizeCompare');
  const prizeCompareGrid = document.getElementById('prizeCompareGrid');
  const resultOverlay = document.getElementById('resultOverlay');
  const loginOverlay = document.getElementById('loginOverlay');
  const credentialsOverlay = document.getElementById('credentialsOverlay');
  const closeCredentials = document.getElementById('closeCredentials');
  const closeLogin = document.getElementById('closeLogin');
  const authModalLabel = document.getElementById('authModalLabel');
  const authModalMessage = document.getElementById('authModalMessage');
  const authTabRegister = document.getElementById('authTabRegister');
  const authTabLogin = document.getElementById('authTabLogin');
  const memberLoginForm = document.getElementById('memberLoginForm');
  const memberRegisterForm = document.getElementById('memberRegisterForm');
  const registerSubmitBtn = document.getElementById('registerSubmitBtn');
  const resultPrizeLogo = document.getElementById('resultPrizeLogo');
  const resultPrize = document.getElementById('resultPrize');
  const resultPrizeDetail = document.getElementById('resultPrizeDetail');
  const closeResult = document.getElementById('closeResult');
  const confettiCanvas = document.getElementById('confettiCanvas');
  const confettiCtx = confettiCanvas?.getContext('2d');
  const heroConfetti = document.getElementById('heroConfetti');
  const dashSpinsLeft = document.getElementById('dashSpinsLeft');
  const dashPoints = document.getElementById('dashPoints');
  const dashPrizesWon = document.getElementById('dashPrizesWon');
  const dashMemberLevel = document.getElementById('dashMemberLevel');
  const winnersList = document.getElementById('winnersList');

  let isLoggedIn = false;
  let memberProfile = null;
  let spinsLeft = 0;
  let pendingSpinAfterRegister = false;
  let isSpinning = false;

  function syncFromAuth() {
    isLoggedIn = BoyInsureAuth.isLoggedIn();
    memberProfile = BoyInsureAuth.getMember();
    spinsLeft = BoyInsureAuth.getSpinsRemaining();
    if (isLoggedIn && memberProfile) {
      if (dashMemberLevel) dashMemberLevel.textContent = memberProfile.tier_name || 'ทั่วไป';
      wonPrizes.length = 0;
      BoyInsureAuth.getRewards().forEach((r) => {
        wonPrizes.push({
          name: r.name,
          detail: r.detail || '',
          logo: r.logo_path ? asset(r.logo_path.replace(/^assets\//, '')) : '',
          color: r.color || '#fff',
        });
      });
    }
    updateSpinsDisplay();
  }

  function applyMemberSession(member, rewards = []) {
    BoyInsureAuth.setSession(member, rewards);
    syncFromAuth();
  }

  function clearSpinUnlockState() {
    pendingSpinAfterRegister = false;
  }

  async function refreshMemberAfterSpin(spinsRemaining) {
    clearSpinUnlockState();
    if (typeof spinsRemaining === 'number' && memberProfile) {
      spinsLeft = spinsRemaining;
      memberProfile.spins_remaining = spinsRemaining;
    }
    await BoyInsureAuth.refresh();
    syncFromAuth();
  }

  function segmentFromPrizeIndex(prizeIndex) {
    const seg = WHEEL_TO_PRIZE.indexOf(prizeIndex);
    return seg >= 0 ? seg : Math.floor(Math.random() * WHEEL_SEGMENTS);
  }

  function prizeIndexFromApiPrize(apiPrize) {
    let idx = PRIZES.findIndex((p) => p.short === apiPrize.short_name);
    if (idx < 0) idx = PRIZES.findIndex((p) => p.name === apiPrize.name);
    return idx >= 0 ? idx : 0;
  }
  let rotation = 0;
  let lightInterval = null;
  let spinAnimationId = null;
  const wonPrizes = [];
  const sessionWins = [];

  BoyInsureAuth.subscribe(syncFromAuth);

  const tickSound = new Audio(asset('audio/tick.mp3'));
  tickSound.loop = true;

  function playTickSound() {
    tickSound.currentTime = 0;
    tickSound.play().catch(() => {});
  }

  function stopTickSound() {
    tickSound.pause();
    tickSound.currentTime = 0;
  }

  const LIGHT_COUNT = 24;
  const BLUE_RIM_OUTER = 0.968;
  const BLUE_RIM_INNER = 0.878;
  const LIGHT_RIM_RATIO = (BLUE_RIM_OUTER + BLUE_RIM_INNER) / 2;

  const WHEEL_LAYOUT = {
    outerR: 0.48,
    blueOuterR: 0.968,
    blueInnerR: 0.878,
    segOuterR: 0.855,
    segInnerR: 0.18,
    hubR: 0.14,
  };

  const SEGMENT_BLUE = '#c8e6fc';
  const SEGMENT_WHITE = '#ffffff';
  const SEGMENT_TEXT = '#0a2d6e';

  const segmentData = WHEEL_TO_PRIZE.map(i => ({
    label: PRIZES[i].short,
    prizeIndex: i,
  }));

  const prizeLogoCache = {};

  function getPrizeLogo(prizeIndex) {
    if (!prizeLogoCache[prizeIndex]) {
      const img = new Image();
      const redraw = () => drawWheel();
      img.addEventListener('load', redraw);
      img.addEventListener('error', redraw);
      img.src = asset(PRIZES[prizeIndex].logo);
      prizeLogoCache[prizeIndex] = img;
    }
    return prizeLogoCache[prizeIndex];
  }

  function preloadWheelLogos() {
    WHEEL_TO_PRIZE.forEach(i => getPrizeLogo(i));
  }

  function drawSegmentIcon(ctx, logo, maxW, maxH) {
    if (!logo.complete || logo.naturalWidth <= 0) return 0;

    const aspect = logo.naturalWidth / logo.naturalHeight;
    let drawW = maxW;
    let drawH = maxW / aspect;

    if (drawH > maxH) {
      drawH = maxH;
      drawW = maxH * aspect;
    }

    ctx.drawImage(logo, -drawW / 2, -drawH / 2, drawW, drawH);
    return drawH;
  }

  function drawSegmentLabel(ctx, label, size, y) {
    const fontSize = Math.max(9, size * 0.024);
    const maxWidth = size * 0.095;
    ctx.fillStyle = SEGMENT_TEXT;
    ctx.font = `700 ${fontSize}px "Better Together", sans-serif`;
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';

    if (ctx.measureText(label).width <= maxWidth) {
      ctx.fillText(label, 0, y);
      return;
    }

    ctx.save();
    ctx.translate(0, y);
    wrapCanvasText(ctx, label, maxWidth, fontSize * 1.08);
    ctx.restore();
  }

  function getWheelHost() {
    return wheelRotator?.closest('.wheel-wrap') || wheelRotator;
  }

  function getWheelSize() {
    const host = getWheelHost();
    if (!host) return 0;
    const size = host.clientWidth;
    return size > 0 ? size : 0;
  }

  function getWheelMetrics() {
    const host = getWheelHost();
    const wrapW = host?.clientWidth || 0;
    const wrapH = host?.clientHeight || wrapW;
    const size = getWheelSize();
    const cx = wrapW / 2;
    const cy = wrapH / 2;
    const outerR = size * WHEEL_LAYOUT.outerR;
    const r = outerR * LIGHT_RIM_RATIO;

    return { cx, cy, r, dot: outerR * 0.032, size };
  }

  function drawWheel() {
    if (!wheelCanvas || !wheelCtx || !wheelRotator) return;
    const size = getWheelSize();
    if (!size) return;

    const dpr = window.devicePixelRatio || 1;
    wheelCanvas.width = Math.round(size * dpr);
    wheelCanvas.height = Math.round(size * dpr);
    wheelCanvas.style.width = `${size}px`;
    wheelCanvas.style.height = `${size}px`;

    const ctx = wheelCtx;
    ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
    ctx.clearRect(0, 0, size, size);

    const cx = size / 2;
    const cy = size / 2;
    const outerR = size * WHEEL_LAYOUT.outerR;
    const blueOuterR = outerR * WHEEL_LAYOUT.blueOuterR;
    const blueInnerR = outerR * WHEEL_LAYOUT.blueInnerR;
    const segOuterR = outerR * WHEEL_LAYOUT.segOuterR;
    const segInnerR = outerR * WHEEL_LAYOUT.segInnerR;

    for (let i = 0; i < WHEEL_SEGMENTS; i++) {
      const start = (i / WHEEL_SEGMENTS) * Math.PI * 2 - Math.PI / 2;
      const end = ((i + 1) / WHEEL_SEGMENTS) * Math.PI * 2 - Math.PI / 2;
      const seg = segmentData[i];

      ctx.beginPath();
      ctx.moveTo(cx + Math.cos(start) * segInnerR, cy + Math.sin(start) * segInnerR);
      ctx.arc(cx, cy, segOuterR, start, end);
      ctx.arc(cx, cy, segInnerR, end, start, true);
      ctx.closePath();
      const isBlue = i % 2 === 0;
      ctx.fillStyle = isBlue ? SEGMENT_BLUE : SEGMENT_WHITE;
      ctx.fill();
      ctx.strokeStyle = 'rgba(255,255,255,.35)';
      ctx.lineWidth = Math.max(1, size * 0.002);
      ctx.stroke();

      const mid = (start + end) / 2;
      const segSpan = segOuterR - segInnerR;
      const iconR = segInnerR + segSpan * 0.70;
      const labelR = segInnerR + segSpan * 0.54;
      const maxLogoW = size * 0.14;
      const maxLogoH = size * 0.068;
      const logo = getPrizeLogo(seg.prizeIndex);
      const rot = mid + Math.PI / 2;

      ctx.save();
      ctx.translate(cx + Math.cos(mid) * iconR, cy + Math.sin(mid) * iconR);
      ctx.rotate(rot);
      drawSegmentIcon(ctx, logo, maxLogoW, maxLogoH);
      ctx.restore();

      ctx.save();
      ctx.translate(cx + Math.cos(mid) * labelR, cy + Math.sin(mid) * labelR);
      ctx.rotate(rot);
      drawSegmentLabel(ctx, seg.label, size, 0);
      ctx.restore();
    }

    ctx.beginPath();
    ctx.arc(cx, cy, blueOuterR, 0, Math.PI * 2);
    ctx.arc(cx, cy, blueInnerR, 0, Math.PI * 2, true);
    ctx.fillStyle = '#1a4fa0';
    ctx.fill();

    for (let i = 0; i < LIGHT_COUNT; i++) {
      const angle = (i / LIGHT_COUNT) * Math.PI * 2 - Math.PI / 2;
      const lx = cx + Math.cos(angle) * (blueOuterR + blueInnerR) / 2;
      const ly = cy + Math.sin(angle) * (blueOuterR + blueInnerR) / 2;
      const dotR = size * 0.012;
      ctx.beginPath();
      ctx.arc(lx, ly, dotR, 0, Math.PI * 2);
      ctx.fillStyle = '#fff';
      ctx.fill();
      ctx.strokeStyle = 'rgba(255,255,255,.6)';
      ctx.lineWidth = 1;
      ctx.stroke();
    }
  }

  function wrapCanvasText(ctx, text, maxWidth, lineHeight) {
    const words = text.split(/\s+/);
    const lines = [];
    let line = words[0] || '';

    for (let i = 1; i < words.length; i++) {
      const test = `${line} ${words[i]}`;
      if (ctx.measureText(test).width > maxWidth) {
        lines.push(line);
        line = words[i];
      } else {
        line = test;
      }
    }
    lines.push(line);

    const offsetY = -((lines.length - 1) * lineHeight) / 2;
    lines.forEach((ln, idx) => {
      ctx.fillText(ln, 0, offsetY + idx * lineHeight);
    });
  }

  function prizeIndexFromSegment(seg) {
    return WHEEL_TO_PRIZE[seg];
  }

  /* มุมกึ่งกลางช่องบนวงล้อ (องศาจากด้านบน ตามเข็มนาฬิกา) */
  function segmentCenterAngle(seg) {
    return seg * DEG_PER_SEG + DEG_PER_SEG / 2 + WHEEL_OFFSET;
  }

  /* องศาหมุนเพื่อให้กึ่งกลางช่องมาตรงตัวชี้ด้านบน */
  function rotationForSegmentCenter(seg) {
    return (360 - segmentCenterAngle(seg) + 360) % 360;
  }

  function applyWheelRotation(deg, animate = false) {
    if (!wheelRotator) return;
    wheelRotator.style.transition = animate ? '' : 'none';
    wheelRotator.style.transform = `rotate(${deg}deg)`;
  }

  function buildLights(intervalMs = 160) {
    if (!wheelLights || !wheelRotator) return;
    if (lightInterval) clearInterval(lightInterval);
    wheelLights.innerHTML = '';

    if (!getWheelSize()) return;

    const { cx, cy, r, dot } = getWheelMetrics();

    for (let i = 0; i < LIGHT_COUNT; i++) {
      const el = document.createElement('span');
      el.className = 'wheel-light';
      el.style.setProperty('--i', i);
      const angle = (i / LIGHT_COUNT) * 2 * Math.PI - Math.PI / 2;
      el.style.left = `${cx + Math.cos(angle) * r}px`;
      el.style.top = `${cy + Math.sin(angle) * r}px`;
      el.style.width = `${dot}px`;
      el.style.height = `${dot}px`;
      el.innerHTML = '<span class="wheel-light__core"></span>';
      wheelLights.appendChild(el);
    }

    let step = 0;
    const chaseLen = 5;
    const tick = () => {
      wheelLights.querySelectorAll('.wheel-light').forEach((el, i) => {
        const dist = (i - step + LIGHT_COUNT) % LIGHT_COUNT;
        el.classList.toggle('wheel-light--on', dist < chaseLen);
      });
    };
    tick();
    lightInterval = setInterval(() => {
      step = (step + 1) % LIGHT_COUNT;
      tick();
    }, intervalMs);
  }

  function restartLightAnimation() {
    buildLights(isSpinning ? 100 : 160);
  }

  function buildHeroConfetti() {
    if (!heroConfetti) return;
    const colors = ['#f5d060', '#d4af37', '#fff', '#ffcc00', '#f5a060'];
    for (let i = 0; i < 30; i++) {
      const el = document.createElement('div');
      el.className = 'confetti-piece';
      el.style.left = `${Math.random() * 100}%`;
      el.style.top = `${Math.random() * 100}%`;
      el.style.background = colors[Math.floor(Math.random() * colors.length)];
      el.style.animationDuration = `${6 + Math.random() * 8}s`;
      el.style.animationDelay = `${Math.random() * 8}s`;
      el.style.width = `${4 + Math.random() * 8}px`;
      el.style.height = `${4 + Math.random() * 8}px`;
      heroConfetti.appendChild(el);
    }
  }

  function getPrizeTag(index) {
    return [9, 10, 11].includes(index) ? 'ประกันภัย' : 'VOUCHER';
  }

  function buildPrizeCatalogCardHTML(p) {
    return `
      <div class="prize-catalog-card__logo-ring">
        <img src="${asset(p.logo)}" alt="${p.short}" class="prize-catalog-card__logo" loading="lazy" />
      </div>
      <p class="prize-catalog-card__title">${p.short}</p>
    `;
  }

  function addPrizeWonHighlight(index) {
    document.querySelector(`.prize-list__item[data-index="${index}"], .prize-catalog-card[data-index="${index}"]`)
      ?.classList.add('prize-list__item--won', 'prize-catalog-card--won');
  }

  function clearPrizeWonHighlight() {
    document.querySelectorAll('.prize-list__item, .prize-catalog-card').forEach(c => {
      c.classList.remove('prize-list__item--won', 'prize-catalog-card--won');
    });
  }

  function hidePrizeCompare() {
    if (prizeCompare) prizeCompare.hidden = true;
    if (prizeCompareGrid) prizeCompareGrid.innerHTML = '';
  }

  function renderPrizeCompare() {
    if (!prizeCompare || !prizeCompareGrid || sessionWins.length === 0) return;

    const compareRows = [
      { label: '', type: 'logo' },
      { label: 'ชื่อรางวัล', type: 'short' },
      { label: 'รายละเอียด', type: 'detail' },
      { label: 'ประเภท', type: 'tag' },
    ];

    const headerCells = sessionWins.map((_, i) =>
      `<th class="prize-compare__th prize-compare__th--spin">ครั้งที่ ${i + 1}</th>`
    ).join('');

    const bodyRows = compareRows.map(row => {
      const cells = sessionWins.map(prizeIndex => {
        const p = PRIZES[prizeIndex];
        if (row.type === 'logo') {
          return `<td class="prize-compare__td prize-compare__td--logo">
            <div class="prize-compare__logo-ring">
              <img src="${asset(p.logo)}" alt="${p.short}" loading="lazy" />
            </div>
          </td>`;
        }
        if (row.type === 'detail') {
          const detail = p.detail || p.name.replace('\n', ' ');
          return `<td class="prize-compare__td">${detail}</td>`;
        }
        if (row.type === 'tag') {
          return `<td class="prize-compare__td"><span class="prize-compare__tag">${getPrizeTag(prizeIndex)}</span></td>`;
        }
        return `<td class="prize-compare__td prize-compare__td--title">${p.short}</td>`;
      }).join('');
      return `<tr><th class="prize-compare__th prize-compare__th--label" scope="row">${row.label}</th>${cells}</tr>`;
    }).join('');

    prizeCompareGrid.innerHTML = `
      <table class="prize-compare__table">
        <thead>
          <tr>
            <th class="prize-compare__th prize-compare__th--corner" scope="col">รายการ</th>
            ${headerCells}
          </tr>
        </thead>
        <tbody>${bodyRows}</tbody>
      </table>
    `;

    prizeCompare.hidden = false;
  }

  /* ไม่แสดงในรายการการ์ดด้านล่าง (แถวละ 6 ใบ) */
  const CATALOG_EXCLUDE = new Set([12]);

  function getPrizeLabel(index) {
    const custom = PRIZE_CATALOG_DISPLAY.find((d) => d.prizeIndex === index);
    if (custom) return custom.label;
    const p = PRIZES[index];
    return p ? p.name.replace('\n', ' ') : '';
  }

  function getPrizeQty(index) {
    if (PRIZE_STOCK_BY_INDEX[index] != null) return PRIZE_STOCK_BY_INDEX[index];
    const custom = PRIZE_CATALOG_DISPLAY.find((d) => d.prizeIndex === index);
    return custom ? custom.qty : 10;
  }

  function buildCatalogItem(prizeIndex) {
    const p = PRIZES[prizeIndex];
    if (!p) return null;
    return {
      prizeIndex,
      name: getPrizeLabel(prizeIndex),
      short: p.short,
      detail: p.detail || p.name.replace('\n', ' '),
      logo: asset(p.logo),
      qty: getPrizeQty(prizeIndex),
    };
  }

  function getAllCatalogItems() {
    return PRIZES
      .map((_, i) => i)
      .filter((i) => !CATALOG_EXCLUDE.has(i))
      .map((i) => buildCatalogItem(i))
      .filter(Boolean);
  }

  function getPanelCatalogItems() {
    return PRIZE_CATALOG_DISPLAY
      .map(({ prizeIndex }) => buildCatalogItem(prizeIndex))
      .filter(Boolean);
  }

  function getHeroCatalogItems() {
    return [...new Set(WHEEL_TO_PRIZE)]
      .map((i) => buildCatalogItem(i))
      .filter(Boolean);
  }

  function buildAllPrizesList() {
    if (!allPrizesList) return;
    allPrizesList.innerHTML = getAllCatalogItems()
      .map((item) => buildPrizeListItemHTML(item.prizeIndex))
      .join('');
  }

  function openAllPrizesModal() {
    if (!allPrizesOverlay) return;
    buildAllPrizesList();
    allPrizesOverlay.hidden = false;
    document.body.style.overflow = 'hidden';
    if (window.lucide?.createIcons) lucide.createIcons();
  }

  function closeAllPrizesModal() {
    if (!allPrizesOverlay) return;
    allPrizesOverlay.hidden = true;
    if (!isModalOpen()) {
      document.body.style.overflow = '';
    }
  }

  function buildPrizeListItemHTML(prizeIndex) {
    const p = PRIZES[prizeIndex];
    if (!p) return '';
    return `
      <div class="prize-list__item" data-index="${prizeIndex}">
        <div class="prize-list__prize">
          <div class="prize-list__logo">
            <img src="${asset(p.logo)}" alt="${p.short}" width="44" height="44" loading="lazy" />
          </div>
          <span class="prize-list__name">${getPrizeLabel(prizeIndex)}</span>
        </div>
        <span class="prize-list__qty">${getPrizeQty(prizeIndex)} รางวัล</span>
      </div>
    `;
  }

  function buildPrizeGrid() {
    if (!prizesGrid) return;
    prizesGrid.innerHTML = PRIZE_CATALOG_DISPLAY
      .map(({ prizeIndex }) => buildPrizeListItemHTML(prizeIndex))
      .join('');
  }

  function initHeroPrizeCards() {
    const container = document.getElementById('heroPrizeCards');
    if (!container) return;
    BoyInsureRewardsUI.bindCatalogGrid(container, getHeroCatalogItems(), prizeCatalogSlider);
  }

  function buildWinnerItemHTML(w) {
    const prizeClass = w.type === 'insurance' ? 'winners-list__prize-name--insurance' : 'winners-list__prize-name--voucher';
    return `
      <li class="winners-list__item">
        <span class="winners-list__avatar" aria-hidden="true"><i data-lucide="user"></i></span>
        <div class="winners-list__main">
          <div class="winners-list__user">
            <span class="winners-list__name">${w.name}</span>
            <span class="winners-list__location">${w.location}</span>
          </div>
          <p class="winners-list__result">ได้รับ <em class="winners-list__prize-name ${prizeClass}">${w.prize}</em></p>
        </div>
        <time class="winners-list__time">${w.time}</time>
      </li>
    `;
  }

  function buildWinnersList() {
    if (!winnersList) return;
    winnersList.innerHTML = RECENT_WINNERS.map(buildWinnerItemHTML).join('');
    if (window.lucide?.createIcons) lucide.createIcons();
  }

  function buildAllWinnersList() {
    if (!allWinnersList) return;
    allWinnersList.innerHTML = ALL_WINNERS.map(buildWinnerItemHTML).join('');
  }

  function openAllWinnersModal() {
    if (!allWinnersOverlay) return;
    allWinnersOverlay.hidden = false;
    document.body.style.overflow = 'hidden';
    if (window.lucide?.createIcons) lucide.createIcons();
  }

  function closeAllWinnersModal() {
    if (!allWinnersOverlay) return;
    allWinnersOverlay.hidden = true;
    if (!isModalOpen()) {
      document.body.style.overflow = '';
    }
  }

  function padCount(n) {
    return String(Math.max(0, n)).padStart(2, '0');
  }

  function formatPromoEndDate(date) {
    return date.toLocaleDateString('th-TH', {
      day: 'numeric',
      month: 'long',
      year: 'numeric',
    });
  }

  function initCountdownMeta() {
    const daysLabel = document.getElementById('promoDurationDays');
    const hintEl = document.getElementById('promoCountdownHint');
    const campaignDays = Math.max(1, Math.ceil((PROMO_END - PROMO_START) / 86400000));

    if (daysLabel) daysLabel.textContent = String(campaignDays);
    if (hintEl) {
      hintEl.textContent = `เหลือเวลาร่วมกิจกรรมก่อนปิดรับสิทธิ์ — สิ้นสุด ${formatPromoEndDate(PROMO_END)}`;
    }
  }

  function updateCountdown() {
    const daysEl = document.getElementById('countdownDays');
    const hoursEl = document.getElementById('countdownHours');
    const minutesEl = document.getElementById('countdownMinutes');
    const secondsEl = document.getElementById('countdownSeconds');
    if (!daysEl) return;

    const diff = PROMO_END - Date.now();
    if (diff <= 0) {
      daysEl.textContent = '00';
      hoursEl.textContent = '00';
      minutesEl.textContent = '00';
      secondsEl.textContent = '00';
      const hintEl = document.getElementById('promoCountdownHint');
      if (hintEl) hintEl.textContent = 'กิจกรรมสิ้นสุดแล้ว — ขอบคุณที่ร่วมสนุกกับ BOYINSURE';
      return;
    }

    const totalSec = Math.floor(diff / 1000);
    const days = Math.floor(totalSec / 86400);
    const hours = Math.floor((totalSec % 86400) / 3600);
    const minutes = Math.floor((totalSec % 3600) / 60);
    const seconds = totalSec % 60;

    daysEl.textContent = padCount(days);
    hoursEl.textContent = padCount(hours);
    minutesEl.textContent = padCount(minutes);
    secondsEl.textContent = padCount(seconds);
  }

  function initCountdown() {
    initCountdownMeta();
    updateCountdown();
    setInterval(updateCountdown, 1000);
  }

  function resumePromoAnimations() {
    restartLightAnimation();
    updateCountdown();
  }

  window.addEventListener('pageshow', (event) => {
    if (event.persisted) resumePromoAnimations();
  });

  function initViewAllPrizes() {
    const btn = document.getElementById('viewAllPrizesBtn');
    if (btn) {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        openAllPrizesModal();
      });
    }

    document.querySelectorAll('.js-open-all-prizes').forEach((link) => {
      link.addEventListener('click', (e) => {
        e.preventDefault();
        openAllPrizesModal();
      });
    });

    closeAllPrizes?.addEventListener('click', (e) => {
      e.preventDefault();
      closeAllPrizesModal();
    });

    allPrizesOverlay?.addEventListener('click', (e) => {
      if (e.target === allPrizesOverlay) closeAllPrizesModal();
    });

    allPrizesOverlay?.querySelector('.result-modal--prizes')?.addEventListener('click', (e) => {
      e.stopPropagation();
    });
  }

  function initViewAllWinners() {
    document.querySelectorAll('.js-open-all-winners').forEach((link) => {
      link.addEventListener('click', (e) => {
        e.preventDefault();
        openAllWinnersModal();
      });
    });

    closeAllWinners?.addEventListener('click', (e) => {
      e.preventDefault();
      closeAllWinnersModal();
    });

    allWinnersOverlay?.addEventListener('click', (e) => {
      if (e.target === allWinnersOverlay) closeAllWinnersModal();
    });

    allWinnersOverlay?.querySelector('.result-modal--winners')?.addEventListener('click', (e) => {
      e.stopPropagation();
    });
  }

  function updateDashboard() {
    if (dashSpinsLeft) dashSpinsLeft.textContent = isLoggedIn ? String(spinsLeft) : '0';
    if (dashPrizesWon) dashPrizesWon.textContent = String(isLoggedIn ? wonPrizes.length : 0);
    if (dashPoints) dashPoints.textContent = isLoggedIn ? BoyInsureAuth.formatPoints(memberProfile?.points) : '0';
    if (dashMemberLevel) {
      dashMemberLevel.textContent = isLoggedIn
        ? (memberProfile?.tier_name || 'ทั่วไป')
        : 'สมาชิก';
    }
  }

  function canParticipate() {
    return isLoggedIn && spinsLeft > 0;
  }

  function updateSpinsDisplay() {
    spinBtn.disabled = isSpinning;

    if (spinsLeftEl) spinsLeftEl.textContent = String(spinsLeft);

    if (isLoggedIn) {
      if (wheelStatusGuest) wheelStatusGuest.hidden = true;
      if (spinsLeft > 0) {
        if (wheelStatusHasSpins) wheelStatusHasSpins.hidden = false;
        if (wheelStatusNoSpins) wheelStatusNoSpins.hidden = true;
        if (wheelPanelStatus) wheelPanelStatus.classList.remove('wheel-panel__status--empty');
      } else {
        if (wheelStatusHasSpins) wheelStatusHasSpins.hidden = true;
        if (wheelStatusNoSpins) wheelStatusNoSpins.hidden = false;
        if (wheelPanelStatus) wheelPanelStatus.classList.add('wheel-panel__status--empty');
      }
    } else {
      if (wheelStatusGuest) wheelStatusGuest.hidden = false;
      if (wheelStatusHasSpins) wheelStatusHasSpins.hidden = true;
      if (wheelStatusNoSpins) wheelStatusNoSpins.hidden = true;
      if (wheelPanelStatus) wheelPanelStatus.classList.remove('wheel-panel__status--empty');
    }

    updateDashboard();
  }

  function isModalOpen() {
    const catalogSlider = document.getElementById('prizeCatalogSlider');
    const memberRewardSlider = document.getElementById('rewardSlider');
    return !loginOverlay.hidden || !credentialsOverlay?.hidden || !resultOverlay.hidden || !rewardsOverlay.hidden || !termsOverlay.hidden || (catalogSlider && !catalogSlider.hidden) || (memberRewardSlider && !memberRewardSlider.hidden) || (allPrizesOverlay && !allPrizesOverlay.hidden) || (allWinnersOverlay && !allWinnersOverlay.hidden);
  }

  function showCredentialsModal(credentials, sentEmail = false) {
    return new Promise((resolve) => {
      if (!credentialsOverlay || !credentials?.login_id) {
        resolve();
        return;
      }
      const loginEl = document.getElementById('credLoginId');
      const passEl = document.getElementById('credPassword');
      const hintEl = document.getElementById('credEmailHint');
      if (loginEl) loginEl.textContent = credentials.login_id;
      if (passEl) passEl.textContent = credentials.password || '';
      if (hintEl) hintEl.hidden = !sentEmail;
      credentialsOverlay.hidden = false;
      document.body.style.overflow = 'hidden';

      const done = () => {
        credentialsOverlay.hidden = true;
        closeCredentials?.removeEventListener('click', onClose);
        credentialsOverlay.removeEventListener('click', onBackdrop);
        if (!isModalOpen()) {
          document.body.style.overflow = '';
        }
        resolve();
      };
      const onClose = (e) => {
        e.preventDefault();
        done();
      };
      const onBackdrop = (e) => {
        if (e.target === credentialsOverlay) done();
      };
      closeCredentials?.addEventListener('click', onClose);
      credentialsOverlay.addEventListener('click', onBackdrop);
      credentialsOverlay.querySelector('.result-modal--credentials')?.addEventListener('click', (e) => e.stopPropagation());
    });
  }

  function setAuthModalMode(mode = 'register') {
    const isLogin = mode === 'login';

    if (authTabRegister) {
      authTabRegister.classList.toggle('is-active', !isLogin);
      authTabRegister.setAttribute('aria-selected', String(!isLogin));
    }
    if (authTabLogin) {
      authTabLogin.classList.toggle('is-active', isLogin);
      authTabLogin.setAttribute('aria-selected', String(isLogin));
    }
    if (memberRegisterForm) memberRegisterForm.hidden = isLogin;
    if (memberLoginForm) memberLoginForm.hidden = !isLogin;

    if (authModalLabel) {
      authModalLabel.textContent = isLogin ? 'เข้าสู่ระบบ' : 'ลงทะเบียนก่อนหมุน';
    }
    if (authModalMessage) {
      authModalMessage.textContent = isLogin
        ? 'เข้าสู่ระบบด้วยไอดีและรหัสผ่านที่ตั้งไว้ตอนลงทะเบียน'
        : 'กรุณากรอกข้อมูลให้ครบถ้วนเพื่อสมัครสมาชิกและหมุนวงล้อ';
    }
    if (registerSubmitBtn) {
      registerSubmitBtn.textContent = pendingSpinAfterRegister
        ? 'ลงทะเบียนและหมุนวงล้อ'
        : 'ลงทะเบียน';
    }
  }

  function openAuthModal(mode = 'register', forSpin = false) {
    pendingSpinAfterRegister = forSpin;
    setAuthModalMode(mode);
    loginOverlay.hidden = false;
    document.body.style.overflow = 'hidden';
    if (mode === 'login') {
      document.getElementById('loginId')?.focus();
    } else {
      document.getElementById('registerFirstName')?.focus();
    }
  }

  function openRegisterModal(forSpin = false) {
    openAuthModal('register', forSpin);
  }

  function closeLoginModal() {
    loginOverlay.hidden = true;
    pendingSpinAfterRegister = false;
    if (!isModalOpen()) {
      document.body.style.overflow = '';
    }
  }

  function collectRegisterPayload() {
    return {
      first_name: document.getElementById('registerFirstName')?.value?.trim(),
      last_name: document.getElementById('registerLastName')?.value?.trim(),
      phone: document.getElementById('registerPhone')?.value?.trim(),
      national_id: document.getElementById('registerNationalId')?.value?.trim(),
      birth_date: document.getElementById('registerBirthDate')?.value,
      email: document.getElementById('registerEmail')?.value?.trim(),
      login_id: document.getElementById('registerLoginId')?.value?.trim(),
      password: document.getElementById('registerPassword')?.value ?? '',
      password_confirm: document.getElementById('registerPasswordConfirm')?.value ?? '',
      consent: document.getElementById('registerConsent')?.checked,
    };
  }

  async function submitRegistration(shouldSpinAfter = false) {
    if (!window.BoyInsureAPI) {
      alert('ระบบไม่พร้อมใช้งาน กรุณาลองใหม่ภายหลัง');
      return false;
    }

    const payload = collectRegisterPayload();
    const hasPasswordInput = payload.password !== '' || payload.password_confirm !== '' || payload.login_id !== '';
    if (hasPasswordInput) {
      if (!payload.login_id) {
        alert('กรุณากรอกไอดีสำหรับเข้าสู่ระบบ');
        return false;
      }
      if (payload.password.length < 6) {
        alert('กรุณาตั้งรหัสผ่านอย่างน้อย 6 ตัวอักษร');
        return false;
      }
      if (payload.password !== payload.password_confirm) {
        alert('รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน');
        return false;
      }
    }

    const submitBtn = document.getElementById('registerSubmitBtn');
    if (submitBtn) submitBtn.disabled = true;

    try {
      const data = await BoyInsureAPI.registerMember(payload);
      applyMemberSession(data.member);
      spinsLeft = data.member.spins_remaining ?? 0;
      closeLoginModal();
      if (data.credentials) {
        await showCredentialsModal(data.credentials, Boolean(data.credentials_sent_email));
      } else if (!shouldSpinAfter) {
        alert('ลงทะเบียนสำเร็จ');
      }
      if (shouldSpinAfter) {
        await executeSpin();
      }
      return true;
    } catch (err) {
      alert(err.message || 'ลงทะเบียนไม่สำเร็จ');
      return false;
    } finally {
      if (submitBtn) submitBtn.disabled = false;
    }
  }

  function getRewardsForUi() {
    if (isLoggedIn && BoyInsureAuth.getRewards().length) {
      return BoyInsureAuth.getRewards();
    }
    return wonPrizes.map((item) => ({
      name: item.name,
      detail: item.detail || '',
      logo: item.logo || '',
      status: 'won',
    }));
  }

  function renderRewardsList() {
    const rewards = getRewardsForUi();
    const hasRewards = rewards.length > 0;
    rewardsEmpty.hidden = hasRewards;
    if (rewardsCardGrid) rewardsCardGrid.hidden = !hasRewards;
    if (!hasRewards) {
      if (rewardsCardGrid) rewardsCardGrid.innerHTML = '';
      return;
    }
    BoyInsureRewardsUI.bindGrid(rewardsCardGrid, rewards, rewardSlider);
  }

  async function openRewardsModal() {
    if (isLoggedIn) {
      await BoyInsureAuth.refresh();
      syncFromAuth();
    }
    if (!getRewardsForUi().length) {
      alert(isLoggedIn
        ? 'ยังไม่มีรางวัล — ลงทะเบียนและหมุนวงล้อเพื่อลุ้นรับของรางวัล'
        : 'ยังไม่มีรางวัล — เข้าสู่ระบบและหมุนวงล้อเพื่อลุ้นรับของรางวัล');
      return;
    }
    renderRewardsList();
    rewardsOverlay.hidden = false;
    document.body.style.overflow = 'hidden';
  }

  function closeRewardsModal() {
    rewardsOverlay.hidden = true;
    if (!isModalOpen()) {
      document.body.style.overflow = '';
    }
  }

  function openTermsModal() {
    termsOverlay.hidden = false;
    document.body.style.overflow = 'hidden';
  }

  function closeTermsModal() {
    termsOverlay.hidden = true;
    if (!isModalOpen()) {
      document.body.style.overflow = '';
    }
  }

  function easeOutCubic(t) {
    return 1 - Math.pow(1 - t, 3);
  }

  async function executeSpin() {
    if (!canParticipate()) return;
    if (isSpinning || spinsLeft <= 0) return;
    if (!window.BoyInsureAPI) {
      alert('ระบบไม่พร้อมใช้งาน กรุณาลองใหม่ภายหลัง');
      return;
    }

    wheelPin.classList.remove('bounce');
    cancelAnimationFrame(spinAnimationId);

    let winSegment = Math.floor(Math.random() * WHEEL_SEGMENTS);
    let prizeIndex = prizeIndexFromSegment(winSegment);
    let apiPrize = null;

    try {
      spinBtn.disabled = true;
      const result = await BoyInsureAPI.spin();
      apiPrize = result.prize;
      prizeIndex = prizeIndexFromApiPrize(apiPrize);
      winSegment = segmentFromPrizeIndex(prizeIndex);
      spinsLeft = result.spins_remaining ?? 0;
    } catch (err) {
      alert(err.message || 'หมุนไม่สำเร็จ');
      updateSpinsDisplay();
      spinBtn.disabled = false;
      return;
    }

    const targetMod = rotationForSegmentCenter(winSegment);
    const currentMod = ((rotation % 360) + 360) % 360;
    const delta = (targetMod - currentMod + 360) % 360;
    const extraSpins = 3 + Math.floor(Math.random() * 2);
    const totalRotation = rotation + extraSpins * 360 + delta;
    const duration = 7000 + Math.random() * 2000;

    isSpinning = true;
    wheelRotator.classList.add('wheel-rotator--spinning', 'blur');
    wheelRotator.style.setProperty('--spin-duration', `${duration / 1000}s`);
    wheelRotator.style.transition = 'none';
    restartLightAnimation();
    updateSpinsDisplay();
    resultOverlay.hidden = true;
    playTickSound();

    const startRot = rotation;
    const startTime = performance.now();

    function animate(now) {
      const progress = Math.min((now - startTime) / duration, 1);
      rotation = startRot + (totalRotation - startRot) * easeOutCubic(progress);
      applyWheelRotation(rotation);

      if (progress < 1) {
        spinAnimationId = requestAnimationFrame(animate);
      } else {
        rotation = totalRotation % 360;
        applyWheelRotation(rotation);
        wheelRotator.classList.remove('wheel-rotator--spinning', 'blur');
        stopTickSound();
        isSpinning = false;
        restartLightAnimation();
        updateSpinsDisplay();
        wheelPin.classList.add('bounce');
        if (apiPrize) {
          showResultFromApi(apiPrize, prizeIndex);
        } else {
          showResult(prizeIndex);
        }
        refreshMemberAfterSpin(spinsLeft);
        launchConfetti();
      }
    }

    spinAnimationId = requestAnimationFrame(animate);
  }

  async function spin() {
    if (isSpinning) return;

    await BoyInsureAuth.refresh();
    syncFromAuth();

    if (!isLoggedIn) {
      openRegisterModal(true);
      return;
    }

    if (spinsLeft <= 0) {
      alert('คุณใช้สิทธิ์หมุนวงล้อครบแล้ว');
      return;
    }

    if (!window.BoyInsureAPI) {
      alert('ระบบไม่พร้อมใช้งาน กรุณาลองใหม่ภายหลัง');
      return;
    }

    try {
      const data = await BoyInsureAPI.prepareSpin();
      if (data.member) applyMemberSession(data.member);
      await executeSpin();
    } catch (err) {
      alert(err.message || 'ไม่สามารถหมุนได้');
    }
  }

  function showResultFromApi(apiPrize, fallbackIndex) {
    if (resultPrizeLogo && apiPrize.logo) {
      resultPrizeLogo.src = asset(String(apiPrize.logo).replace(/^assets\//, ''));
      resultPrizeLogo.alt = apiPrize.short_name;
    }
    resultPrize.textContent = String(apiPrize.name).replace('\n', ' ');
    if (resultPrizeDetail) {
      resultPrizeDetail.textContent = apiPrize.detail || '';
      resultPrizeDetail.hidden = !apiPrize.detail;
    }
    closeResult.textContent = 'ตกลง';
    resultOverlay.hidden = false;
    document.body.style.overflow = 'hidden';
    sessionWins.push(fallbackIndex);
    wonPrizes.push({
      name: apiPrize.name,
      detail: apiPrize.detail || '',
      logo: apiPrize.logo ? asset(String(apiPrize.logo).replace(/^assets\//, '')) : '',
      color: apiPrize.color || '#fff',
    });
    updateDashboard();
  }

  function showResult(index) {
    const prize = PRIZES[index];
    const prizeName = prize.name.replace('\n', ' ');

    if (resultPrizeLogo) {
      resultPrizeLogo.src = asset(prize.logo);
      resultPrizeLogo.alt = prize.short;
    }
    resultPrize.textContent = prizeName;
    if (resultPrizeDetail) {
      resultPrizeDetail.textContent = prize.detail || '';
      resultPrizeDetail.hidden = !prize.detail;
    }
    closeResult.textContent = 'ตกลง';

    resultOverlay.hidden = false;
    document.body.style.overflow = 'hidden';

    sessionWins.push(index);
    addPrizeWonHighlight(index);

    wonPrizes.push({ index, name: prize.name, short: prize.short });
    updateDashboard();

    if (spinsLeft === 0) {
      renderPrizeCompare();
    }
  }

  let confettiParticles = [];

  function resizeConfetti() {
    if (!confettiCanvas || !confettiCtx) return;
    confettiCanvas.width = window.innerWidth;
    confettiCanvas.height = window.innerHeight;
  }

  function launchConfetti() {
    if (!confettiCanvas || !confettiCtx) return;
    const colors = ['#f5d060', '#d4af37', '#ffcc00', '#1a4fa0', '#fff', '#ff6b9d'];
    confettiParticles = [];
    const cx = window.innerWidth / 2;
    for (let i = 0; i < 150; i++) {
      const angle = Math.random() * Math.PI * 2;
      const speed = 4 + Math.random() * 8;
      confettiParticles.push({
        x: cx + (Math.random() - 0.5) * 200,
        y: window.innerHeight * 0.35,
        w: 5 + Math.random() * 7,
        h: 4 + Math.random() * 5,
        color: colors[Math.floor(Math.random() * colors.length)],
        vx: Math.cos(angle) * speed,
        vy: Math.sin(angle) * speed - 4,
        rot: Math.random() * 360,
        rotV: (Math.random() - 0.5) * 12,
        opacity: 1,
      });
    }
    animateConfetti();
  }

  function animateConfetti() {
    if (!confettiCtx || !confettiCanvas) return;
    confettiCtx.clearRect(0, 0, confettiCanvas.width, confettiCanvas.height);
    let alive = false;
    confettiParticles.forEach(p => {
      p.x += p.vx;
      p.y += p.vy;
      p.vy += 0.15;
      p.rot += p.rotV;
      p.opacity -= 0.006;
      if (p.opacity > 0) {
        alive = true;
        confettiCtx.save();
        confettiCtx.translate(p.x, p.y);
        confettiCtx.rotate((p.rot * Math.PI) / 180);
        confettiCtx.globalAlpha = Math.max(0, p.opacity);
        confettiCtx.fillStyle = p.color;
        confettiCtx.fillRect(-p.w / 2, -p.h / 2, p.w, p.h);
        confettiCtx.restore();
      }
    });
    if (alive) requestAnimationFrame(animateConfetti);
    else confettiCtx.clearRect(0, 0, confettiCanvas.width, confettiCanvas.height);
  }

  function closeResultModal() {
    resultOverlay.hidden = true;

    if (!isModalOpen()) {
      document.body.style.overflow = '';
    }

    if (sessionWins.length > 0 && prizeCompare && !prizeCompare.hidden) {
      prizeCompare.scrollIntoView({ behavior: 'smooth', block: 'center' });
    } else {
      document.getElementById('privileges')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
  }

  spinBtn?.addEventListener('click', spin);
  myRewardsBtn?.addEventListener('click', () => {
    if (isLoggedIn) {
      window.location.href = 'profile.html#rewards';
      return;
    }
    openRewardsModal();
  });
  termsBtn?.addEventListener('click', openTermsModal);
  closeRewards?.addEventListener('click', e => {
    e.preventDefault();
    closeRewardsModal();
  });
  closeTerms?.addEventListener('click', e => {
    e.preventDefault();
    closeTermsModal();
  });
  rewardsOverlay?.addEventListener('click', e => {
    if (e.target === rewardsOverlay) closeRewardsModal();
  });
  termsOverlay?.addEventListener('click', e => {
    if (e.target === termsOverlay) closeTermsModal();
  });
  closeResult?.addEventListener('click', e => {
    e.preventDefault();
    e.stopPropagation();
    closeResultModal();
  });
  resultOverlay?.addEventListener('click', e => {
    if (e.target === resultOverlay) closeResultModal();
  });
  resultOverlay.querySelector('.result-modal--win')?.addEventListener('click', e => e.stopPropagation());

  document.querySelectorAll('.js-header-signin').forEach((btn) => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      document.getElementById('navbarMenu')?.classList.remove('open');
      document.getElementById('navToggle')?.setAttribute('aria-expanded', 'false');
      openAuthModal('login', false);
    });
  });

  document.querySelectorAll('.js-header-register').forEach((btn) => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      document.getElementById('navbarMenu')?.classList.remove('open');
      document.getElementById('navToggle')?.setAttribute('aria-expanded', 'false');
      openAuthModal('register', false);
    });
  });

  authTabRegister?.addEventListener('click', () => setAuthModalMode('register'));
  authTabLogin?.addEventListener('click', () => setAuthModalMode('login'));
  closeLogin?.addEventListener('click', e => {
    e.preventDefault();
    closeLoginModal();
  });
  loginOverlay?.addEventListener('click', e => {
    if (e.target === loginOverlay) closeLoginModal();
  });
  loginOverlay?.querySelector('.result-modal--auth')?.addEventListener('click', e => e.stopPropagation());

  memberRegisterForm?.addEventListener('submit', async (e) => {
    e.preventDefault();
    await submitRegistration(pendingSpinAfterRegister);
  });

  memberLoginForm?.addEventListener('submit', async (e) => {
    e.preventDefault();
    if (!window.BoyInsureAPI) {
      alert('ระบบไม่พร้อมใช้งาน กรุณาลองใหม่ภายหลัง');
      return;
    }

    const submitBtn = document.getElementById('loginSubmitBtn');
    if (submitBtn) submitBtn.disabled = true;

    try {
      const data = await BoyInsureAPI.loginMember({
        login_id: document.getElementById('loginId')?.value?.trim(),
        password: document.getElementById('loginPassword')?.value ?? '',
      });
      applyMemberSession(data.member);
      spinsLeft = data.member.spins_remaining ?? 0;
      closeLoginModal();
    } catch (err) {
      alert(err.message || 'เข้าสู่ระบบไม่สำเร็จ');
    } finally {
      if (submitBtn) submitBtn.disabled = false;
    }
  });

  window.addEventListener('resize', () => {
    resizeConfetti();
    drawWheel();
    restartLightAnimation();
  });

  function initWheel() {
    if (!wheelCanvas || !wheelRotator) return;
    preloadWheelLogos();
    const wheelHost = getWheelHost();
    const paintWheel = () => {
      drawWheel();
      restartLightAnimation();
    };
    requestAnimationFrame(() => {
      paintWheel();
      requestAnimationFrame(paintWheel);
    });
    window.setTimeout(paintWheel, 120);
    window.setTimeout(paintWheel, 400);
    if (typeof ResizeObserver !== 'undefined' && wheelHost) {
      const wheelObserver = new ResizeObserver(() => {
        drawWheel();
        restartLightAnimation();
      });
      wheelObserver.observe(wheelHost);
    }
  }

  if (document.fonts && document.fonts.ready) {
    document.fonts.ready.then(initWheel);
  }
  initWheel();
  window.addEventListener('load', initWheel);

  resizeConfetti();
  buildHeroConfetti();
  buildPrizeGrid();
  buildAllPrizesList();
  buildAllWinnersList();
  buildWinnersList();
  initCountdown();
  initHeroPrizeCards();
  initViewAllPrizes();
  initViewAllWinners();
  applyWheelRotation(0);
  updateSpinsDisplay();

  BoyInsureAuth.refresh().then(() => {
    syncFromAuth();
    const authHash = location.hash.replace('#', '');
    if (authHash === 'login') openAuthModal('login', false);
    if (authHash === 'register') openAuthModal('register', false);
  });
})();
