const FOOTER_HTML = `<div class="site-footer__inner">
  <div class="site-footer__brand">
    <a href="index.html" class="site-footer__name">BOYINSURE</a>
    <p class="site-footer__tagline">คุ้มครองทุกช่วงชีวิต ด้วยใจ</p>
    <p class="site-footer__desc">พันธมิตรด้านประกันชีวิตและประกันภัย วิเคราะห์และเปรียบเทียบแผนให้ก่อนตัดสินใจ ดูแลลูกค้าต่อเนื่องจนจบทุกเคส</p>
    <p class="site-footer__note">พันธมิตรด้านประกันภัย</p>
    <div class="site-footer__social">
      <a href="tel:0627878968" class="site-footer__social-link" aria-label="โทรศัพท์"><i data-lucide="phone" aria-hidden="true"></i></a>
      <a href="contact.html" class="site-footer__social-link" aria-label="แชทสอบถาม"><i data-lucide="message-circle" aria-hidden="true"></i></a>
      <a href="mailto:contact@boyinsure.com" class="site-footer__social-link" aria-label="อีเมล"><i data-lucide="mail" aria-hidden="true"></i></a>
    </div>
  </div>
  <div class="site-footer__col">
    <h4 class="site-footer__heading">เมนูหลัก</h4>
    <nav class="site-footer__nav" aria-label="เมนูหลัก">
      <a href="index.html">หน้าแรก</a>
      <a href="promotions.html">โปรโมชั่นและของรางวัล</a>
      <a href="insurance.html">แบบประกันของเรา</a>
      <a href="about.html">เกี่ยวกับ BOYINSURE</a>
      <a href="articles.html">บทความ</a>
      <a href="contact.html">ติดต่อเรา</a>
    </nav>
  </div>
  <div class="site-footer__col">
    <h4 class="site-footer__heading">บริการประกัน</h4>
    <nav class="site-footer__nav" aria-label="บริการประกัน">
      <a href="insurance.html">ประกันสุขภาพ</a>
      <a href="insurance.html">ประกันชีวิต</a>
      <a href="insurance.html">ประกันสะสมทรัพย์ / บำนาญ</a>
      <a href="insurance.html">ประกันโรคร้ายแรง</a>
      <a href="insurance.html">ประกันอุบัติเหตุ</a>
      <a href="insurance.html">ประกันรถยนต์ / เดินทาง</a>
    </nav>
  </div>
  <div class="site-footer__col site-footer__col--contact">
    <h4 class="site-footer__heading">ติดต่อเรา</h4>
    <ul class="site-footer__contact-list">
      <li><i data-lucide="phone" aria-hidden="true"></i><a href="tel:0627878968">062-787-8968</a></li>
      <li><i data-lucide="clock" aria-hidden="true"></i><span>จันทร์–ศุกร์ 09:00–18:00 น.</span></li>
      <li><i data-lucide="map-pin" aria-hidden="true"></i><span>ให้บริการทั่วประเทศ</span></li>
    </ul>
    <a href="contact.html" class="btn btn--gold site-footer__cta">ปรึกษาฟรี</a>
  </div>
</div>
<div class="site-footer__bottom">
  <p class="site-footer__copy">© 2026 BOYINSURE — สงวนลิขสิทธิ์</p>
  <nav class="site-footer__legal" aria-label="ข้อกำหนดทางกฎหมาย">
    <a href="contact.html">นโยบายความเป็นส่วนตัว</a>
    <a href="contact.html">เงื่อนไขการใช้งาน</a>
  </nav>
</div>`;

function loadFooter() {
  const el = document.getElementById('siteFooter');
  if (!el) return;
  el.innerHTML = buildFooterHtml();
  if (window.lucide?.createIcons) lucide.createIcons();
}

function initHomeHeroSlideshow() {
  const container = document.getElementById('homeHeroSlides');
  const dotsContainer = document.getElementById('homeHeroDots');
  if (!container || !dotsContainer) return;
  const slides = [...container.querySelectorAll('.home-hero__slide')];
  if (slides.length < 2) return;

  let current = 0;
  let timer = null;
  let dots = [];

  slides.forEach((_, i) => {
    const dot = document.createElement('button');
    dot.type = 'button';
    dot.className = 'home-hero__dot' + (i === 0 ? ' is-active' : '');
    dot.setAttribute('role', 'tab');
    dot.setAttribute('aria-label', `ภาพที่ ${i + 1}`);
    dot.setAttribute('aria-selected', i === 0 ? 'true' : 'false');
    dot.addEventListener('click', () => {
      show(i, true);
    });
    dotsContainer.appendChild(dot);
    dots.push(dot);
  });

  function updateDots() {
    dots.forEach((dot, i) => {
      const active = i === current;
      dot.classList.toggle('is-active', active);
      dot.setAttribute('aria-selected', active ? 'true' : 'false');
    });
  }

  function show(index, manual) {
    slides[current].classList.remove('is-active');
    current = (index + slides.length) % slides.length;
    slides[current].classList.add('is-active');
    updateDots();
    if (manual) restart();
  }

  function start() {
    stop();
    timer = setInterval(() => show(current + 1), 5500);
  }

  function stop() {
    if (timer) {
      clearInterval(timer);
      timer = null;
    }
  }

  function restart() {
    stop();
    start();
  }

  const hero = container.closest('.home-hero');
  hero?.addEventListener('mouseenter', stop);
  hero?.addEventListener('mouseleave', start);
  start();
}

function fbVideoHref(url) {
  const reelMatch = String(url).match(/reel\/(\d+)/);
  if (reelMatch) return `https://www.facebook.com/watch/?v=${reelMatch[1]}`;
  return url;
}

function fbVideoEmbedUrl(href, width) {
  const height = Math.round(width * 16 / 9);
  const params = new URLSearchParams({
    href: fbVideoHref(href),
    show_text: 'false',
    width: String(width),
    height: String(height),
  });
  return `https://www.facebook.com/plugins/video.php?${params}`;
}

function loadHighlightVideo(iframe, href, fallbackWidth) {
  if (!iframe || !href) return;
  const shell = iframe.closest('.site-modal__video');
  const width = Math.max(280, Math.round(shell?.getBoundingClientRect().width || fallbackWidth || 360));
  iframe.hidden = false;
  iframe.src = fbVideoEmbedUrl(href, width);
}

function getHighlightPoster(item) {
  if (!item) return '';
  return item.mainVideoPoster || item.image || item.thumbVideoPoster || item.thumb || '';
}

function highlightHasVideo(item) {
  return Boolean(item && (item.mainVideoFile || item.mainVideo));
}

const HIGHLIGHTS_DATA = [
  {
    brandInitial: 'B',
    brandName: 'BOYINSURE',
    brandTagline: 'คลิปไฮไลท์ล่าสุด',
    tags: ['คลิปใหม่', 'อัปเดตล่าสุด', 'ดูแลจริง'],
    title: 'ไฮไลท์ล่าสุดจาก BOYINSURE',
    text: 'อัปเดตคลิปไฮไลท์ล่าสุดจากทีม BOYINSURE พร้อมเล่าเรื่องการดูแลลูกค้าและการวางแผนประกันแบบเข้าใจง่าย กดเล่นเพื่อรับชมเต็มคลิป และเลื่อนดูไฮไลท์อื่น ๆ ได้ต่อเนื่อง',
    image: 'assets/img/highlights/insure-con3-poster.jpg',
    imageAlt: 'คลิปไฮไลท์ล่าสุดจาก BOYINSURE',
    mainVideoFile: 'assets/video/insure-con3.mp4',
    mainVideoPoster: 'assets/img/highlights/insure-con3-poster.jpg',
    caption: 'คลิปไฮไลท์ล่าสุดจากทีม BOYINSURE',
    link: 'about.html',
  },
  {
    brandInitial: 'B',
    brandName: 'BOYINSURE',
    brandTagline: 'พันธมิตรด้านประกันภัย',
    tags: ['วิเคราะห์แผน', 'เปรียบเทียบเบี้ย', 'ไม่บังคับซื้อ'],
    title: 'วางแผนความคุ้มครองอย่างมืออาชีพ',
    text: 'BOYINSURE ช่วยวิเคราะห์และเปรียบเทียบแผนประกันจากบริษัทชั้นนำ อธิบายให้เข้าใจง่ายก่อนตัดสินใจ ไม่บังคับซื้อ และดูแลต่อเนื่องตลอดอายุกรมธรรม์ เพื่อให้คุณและครอบครัวอุ่นใจในทุกช่วงชีวิต',
    image: 'assets/img/highlights/reel-main.jpg',
    imageAlt: 'BOYINSURE วางแผนประกันครอบครัว',
    mainVideo: 'https://www.facebook.com/reel/1316213417025596/',
    mainVideoPoster: 'assets/img/highlights/reel-main.jpg',
    caption: 'BOYINSURE วางแผนประกันครอบครัวอย่างมืออาชีพ',
    link: 'about.html',
  },
  {
    brandInitial: 'B',
    brandName: 'BOYINSURE',
    brandTagline: 'ดูแลเคลมจนจบทุกขั้นตอน',
    tags: ['ดูแลเคลม', 'ตอบไว', 'จบทุกเคส'],
    title: 'ดูแลเคลมประกันสุขภาพจนจบ',
    text: 'ทีม BOYINSURE ดูแลเรื่องเคลมประกันสุขภาพให้ตั้งแต่ต้นจนจบ ประสานงานกับโรงพยาบาลและบริษัทประกัน ติดตามผลให้ครบทุกขั้นตอน เพื่อให้คุณไม่ต้องกังวลในวันที่ต้องใช้สิทธิ์',
    image: 'assets/img/highlights/reel-thumb.jpg',
    imageAlt: 'ทีม BOYINSURE ดูแลเคลมประกันสุขภาพจนจบทุกขั้นตอน',
    mainVideo: 'https://www.facebook.com/reel/2393152494542455/',
    mainVideoPoster: 'assets/img/highlights/reel-thumb.jpg',
    caption: 'ทีม BOYINSURE ดูแลเคลมประกันสุขภาพจนจบทุกขั้นตอน',
    link: 'about.html',
  },
];

function initHighlights() {
  const block = document.getElementById('highlightsBlock');
  const moreBtn = document.getElementById('highlightMoreBtn');
  if (!block || !moreBtn || ACTIVE_HIGHLIGHTS_DATA.length === 0) return;

  const modal = document.getElementById('highlightVideoModal');
  const modalFrame = document.getElementById('highlightVideoModalFrame');
  const modalPlayer = document.getElementById('highlightVideoModalPlayer');
  const modalTitle = document.getElementById('highlightVideoModalTitle');
  const modalDialog = modal?.querySelector('.site-modal__dialog');
  let lastVideoTrigger = null;

  function closeVideoModal() {
    if (!modal || modal.hidden) return;
    modal.hidden = true;
    document.body.classList.remove('site-modal-open');
    if (modalFrame) {
      modalFrame.hidden = true;
      modalFrame.removeAttribute('src');
    }
    if (modalPlayer) {
      modalPlayer.pause?.();
      modalPlayer.hidden = true;
      modalPlayer.removeAttribute('src');
      modalPlayer.load?.();
    }
    lastVideoTrigger?.focus();
    lastVideoTrigger = null;
  }

  function openVideoModal(item, trigger) {
    if (!modal || !highlightHasVideo(item)) return;
    lastVideoTrigger = trigger || null;
    const title = item.imageAlt || item.caption || 'BOYINSURE';
    if (modalTitle) modalTitle.textContent = title;
    modal.hidden = false;
    document.body.classList.add('site-modal-open');

    if (item.mainVideoFile && modalPlayer) {
      if (modalFrame) {
        modalFrame.hidden = true;
        modalFrame.removeAttribute('src');
      }
      modalPlayer.hidden = false;
      modalPlayer.src = item.mainVideoFile;
      modalPlayer.currentTime = 0;
      modalPlayer.play?.().catch(() => {});
    } else if (item.mainVideo && modalFrame) {
      if (modalPlayer) {
        modalPlayer.pause?.();
        modalPlayer.hidden = true;
        modalPlayer.removeAttribute('src');
      }
      modalFrame.title = title;
      loadHighlightVideo(modalFrame, item.mainVideo, 360);
    }

    modal.querySelector('.site-modal__close')?.focus();
    if (window.lucide?.createIcons) lucide.createIcons();
  }

  if (modal) {
    modal.querySelectorAll('[data-video-modal-close]').forEach((el) => {
      el.addEventListener('click', closeVideoModal);
    });
    modalDialog?.addEventListener('click', (e) => e.stopPropagation());
    document.addEventListener('keydown', (e) => {
      if (modal.hidden) return;
      if (e.key === 'Escape') closeVideoModal();
    });
  }

  const els = {
    content: block.querySelector('.highlights__content'),
    clips: block.querySelector('.highlights__clips'),
    media: block.querySelector('.highlights__media'),
    avatar: document.getElementById('highlightAvatar'),
    name: document.getElementById('highlightName'),
    tagline: document.getElementById('highlightTagline'),
    tags: document.getElementById('highlightTags'),
    title: document.getElementById('highlightTitle'),
    text: document.getElementById('highlightText'),
    image: document.getElementById('highlightImage'),
    mainPlayer: document.getElementById('highlightMainPlayer'),
    mainPlay: document.getElementById('highlightMainPlay'),
    thumb: document.getElementById('highlightThumb'),
    thumbWrap: document.getElementById('highlightThumbWrap'),
    thumbPlay: document.getElementById('highlightThumbPlay'),
    caption: document.getElementById('highlightCaption'),
    thumbLink: document.getElementById('highlightThumbLink'),
    nav: block.querySelector('.highlights__nav'),
    nextBtn: document.getElementById('highlightNextBtn'),
  };

  let current = 0;

  function resetMainPlayer() {
    if (els.mainPlayer) els.mainPlayer.classList.remove('is-playing', 'has-video');
    if (els.image) els.image.hidden = false;
    if (els.mainPlay) els.mainPlay.hidden = true;
  }

  function resetThumbPlayer() {
    if (els.thumbWrap) els.thumbWrap.classList.remove('is-playing', 'has-video');
    if (els.thumb) els.thumb.hidden = false;
    if (els.thumbPlay) els.thumbPlay.hidden = true;
    if (els.thumbLink) els.thumbLink.hidden = true;
  }

  function setMainMedia(item) {
    resetMainPlayer();
    if (!els.image) return;

    els.image.src = getHighlightPoster(item);
    els.image.alt = item.imageAlt || item.caption || '';

    if (highlightHasVideo(item) && els.mainPlay) {
      els.mainPlay.hidden = false;
      els.mainPlayer?.classList.add('has-video');
    }
  }

  // Thumb previews the NEXT highlight; advancing promotes it into the main player.
  function setThumbPreview(nextItem) {
    resetThumbPlayer();
    els.thumbWrap?.classList.remove('has-video');
    if (!els.thumb || !nextItem) return;

    els.thumb.src = getHighlightPoster(nextItem);
    els.thumb.alt = nextItem.imageAlt || nextItem.caption || '';
    els.thumbWrap?.classList.add('is-preview');

    if (els.thumbLink) {
      els.thumbLink.hidden = false;
      els.thumbLink.href = '#';
    }
  }

  function render(index) {
    const total = ACTIVE_HIGHLIGHTS_DATA.length;
    const item = ACTIVE_HIGHLIGHTS_DATA[index];
    if (!item) return;
    const nextItem = ACTIVE_HIGHLIGHTS_DATA[(index + 1) % total];
    els.avatar.textContent = item.brandInitial;
    els.name.textContent = item.brandName;
    els.tagline.textContent = item.brandTagline;
    if (els.tags) {
      els.tags.innerHTML = item.tags.map((tag) => `<span>${tag}</span>`).join('');
    }
    els.title.textContent = item.title;
    els.text.textContent = item.text;
    setMainMedia(item);
    setThumbPreview(nextItem);
    els.caption.textContent = item.caption;

    const multiple = total > 1;
    if (els.nav) els.nav.hidden = !multiple;
  }

  function fadeSwitch(nextIndex) {
    closeVideoModal();
    [els.content, els.clips, els.caption, els.nav].forEach((el) => el?.classList.add('is-fading'));
    setTimeout(() => {
      current = nextIndex;
      render(current);
      [els.content, els.clips, els.caption, els.nav].forEach((el) => el?.classList.remove('is-fading'));
      if (window.lucide?.createIcons) lucide.createIcons();
    }, 320);
  }

  function goTo(offset) {
    const total = ACTIVE_HIGHLIGHTS_DATA.length;
    if (total < 2) return;
    fadeSwitch(((current + offset) % total + total) % total);
  }

  const AUTO_MS = 5000;
  let autoTimer = null;
  let autoPaused = false;
  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  function stopAuto() {
    if (autoTimer) {
      clearInterval(autoTimer);
      autoTimer = null;
    }
  }

  function startAuto() {
    stopAuto();
    if (reducedMotion || ACTIVE_HIGHLIGHTS_DATA.length < 2) return;
    autoTimer = setInterval(() => {
      // หยุดเมื่อเอาเมาส์/โฟกัสไปวาง หรือกำลังเปิดดูวิดีโอ
      if (autoPaused || (modal && !modal.hidden) || document.hidden) return;
      goTo(1);
    }, AUTO_MS);
  }

  // กดเองแล้วเริ่มจับเวลาใหม่ ไม่ให้เลื่อนซ้ำทันที
  function manualGo(offset) {
    goTo(offset);
    startAuto();
  }

  moreBtn.addEventListener('click', () => manualGo(1));
  els.nextBtn?.addEventListener('click', () => manualGo(1));

  els.mainPlayer?.addEventListener('click', () => {
    openVideoModal(ACTIVE_HIGHLIGHTS_DATA[current], els.mainPlayer);
  });

  // Clicking the preview thumb advances to it (it becomes the main video).
  els.thumbWrap?.addEventListener('click', (e) => {
    e.preventDefault();
    manualGo(1);
  });

  block.addEventListener('mouseenter', () => { autoPaused = true; });
  block.addEventListener('mouseleave', () => { autoPaused = false; });
  block.addEventListener('focusin', () => { autoPaused = true; });
  block.addEventListener('focusout', (e) => {
    if (!block.contains(e.relatedTarget)) autoPaused = false;
  });

  render(current);
  if (window.lucide?.createIcons) lucide.createIcons();
  startAuto();
}

const INSURANCE_CATEGORIES = [
  {
    id: 'savings',
    title: 'ออมเงินและวางแผนอนาคต',
    tagline: 'ออมวันนี้ สบายวันหน้า — สร้างความมั่นคงระยะยาว',
    icon: 'piggy-bank',
    plans: [
      {
        id: 'savings-fund',
        name: 'ประกันสะสมทรัพย์',
        desc: 'ออมเงินพร้อมรับความคุ้มครองชีวิต วางแผนเป้าหมายระยะยาว',
        image: 'assets/img/products/savings.jpg',
        featured: true,
        features: ['ออมสะสมตามแผนที่กำหนด', 'คุ้มครองชีวิตตลอดสัญญา', 'ปรับเบี้ยตามงบประมาณ'],
      },
      {
        id: 'pension',
        name: 'ประกันบำนาญ',
        desc: 'สร้างรายได้หลังเกษียณอย่างมั่นคง ไม่เป็นภาระลูกหลาน',
        image: 'assets/img/products/pension.jpg',
        features: ['รับเงินบำนาญรายเดือน', 'วางแผนเกษียณล่วงหน้า', 'เลือกอายุรับผลประโยชน์ได้'],
      },
      {
        id: 'tax-shield',
        name: 'ประกันลดหย่อนภาษี',
        desc: 'วางแผนภาษีอย่างชาญฉลาด ลดหย่อนได้ตามเงื่อนไข',
        image: 'assets/img/products/tax.jpg',
        features: ['ช่วยวางแผนภาษีประจำปี', 'คุ้มครองชีวิตควบคู่การออม', 'เปรียบเทียบหลายบริษัท'],
      },
    ],
  },
  {
    id: 'health',
    title: 'สุขภาพและค่ารักษา',
    tagline: 'เจ็บป่วยไม่สะเทือนเงินเก็บ — ดูแลสุขภาพครอบครัว',
    icon: 'heart-pulse',
    plans: [
      {
        id: 'health',
        name: 'ประกันสุขภาพ',
        desc: 'ครอบคลุมค่ารักษาพยาบาล ผู้ใหญ่และเด็ก ทั้ง IPD และ OPD',
        image: 'assets/img/products/health.jpg',
        featured: true,
        features: ['ค่ารักษาผู้ป่วยใน–นอก', 'เลือกวงเงินตามต้องการ', 'เครือข่ายโรงพยาบาลชั้นนำ'],
      },
      {
        id: 'critical-illness',
        name: 'ประกันโรคร้ายแรง',
        desc: 'รับเงินก้อนเมื่อวินิจฉัยโรคร้ายแรง ใช้ดูแลตัวเองได้ทันที',
        image: 'assets/img/products/critical.jpg',
        features: ['ครอบคลุมโรคร้ายแรงหลัก', 'จ่ายผลประโยชน์ครั้งเดียว', 'ไม่ต้องส่งใบเสร็จ'],
      },
      {
        id: 'senior-health',
        name: 'ประกันผู้สูงอายุ',
        desc: 'ดูแลสุขภาพวัยเกษียณอย่างมั่นใจ เน้นความคุ้มครองที่ใช้จริง',
        image: 'assets/img/products/senior-health.jpg',
        features: ['ออกแบบสำหรับวัย 50+', 'ค่ารักษาและการเฝ้าไข้', 'เบี้ยเหมาะกับวัยทำงาน'],
      },
    ],
  },
  {
    id: 'risk',
    title: 'อุบัติเหตุและความเสี่ยง',
    tagline: 'อุ่นใจทุกสถานการณ์ — คุ้มครองเหตุไม่คาดฝัน',
    icon: 'shield',
    plans: [
      {
        id: 'accident',
        name: 'ประกันอุบัติเหตุ',
        desc: 'คุ้มครองกรณีเกิดเหตุไม่คาดคิด ทั้งเสียชีวิตและทุพพลภาพ',
        image: 'assets/img/products/accident.jpg',
        featured: true,
        features: ['คุ้มครอง 24 ชั่วโมง', 'ค่ารักษาจากอุบัติเหตุ', 'เบี้ยเริ่มต้นไม่สูง'],
      },
      {
        id: 'life',
        name: 'ประกันชีวิต',
        desc: 'ดูแลคนที่คุณรัก แม้วันที่คุณไม่อยู่ สร้างความมั่นใจให้ครอบครัว',
        image: 'assets/img/products/life.jpg',
        features: ['ทุนประกันปรับได้', 'คุ้มครองชีวิตระยะยาว', 'ผลประโยชน์แก่ทายาท'],
      },
      {
        id: 'personal-accident',
        name: 'ประกันอุบัติเหตุส่วนบุคคล',
        desc: 'เสริมความคุ้มครองรายวัน เหมาะกับอาชีพที่เดินทางบ่อย',
        image: 'assets/img/products/accident.jpg',
        features: ['สมัครง่าย ไม่ต้องตรวจสุขภาพ', 'คุ้มครองทั่วโลก', 'ต่ออายุสะดวก'],
      },
    ],
  },
  {
    id: 'travel',
    title: 'เดินทางและยานพาหนะ',
    tagline: 'ไปไหนก็มั่นใจ — คุ้มครองทริปและยานพาหนะ',
    icon: 'plane',
    plans: [
      {
        id: 'travel',
        name: 'ประกันการเดินทาง',
        desc: 'คุ้มครองทั้งในและต่างประเทศ กระเป๋าเดินทางและค่ารักษา',
        image: 'assets/img/products/travel.jpg',
        featured: true,
        features: ['คุ้มครองต่างประเทศ', 'ค่ารักษาและกระเป๋าหาย', 'ซื้อออนไลน์ก่อนเดินทาง'],
      },
      {
        id: 'car',
        name: 'ประกันรถยนต์ / EV',
        desc: 'ปกป้องรถจากอุบัติเหตุและความเสียหาย รองรับรถ EV',
        image: 'assets/img/products/car.jpg',
        features: ['ชั้น 1 / 2+ / 3+', 'รถ EV และรถส่วนบุคคล', 'เปรียบเทียบเบี้ยหลายบริษัท'],
      },
      {
        id: 'travel-domestic',
        name: 'ประกันเดินทางในประเทศ',
        desc: 'เหมาะสำหรับทริปสั้น คุ้มครองอุบัติเหตุและค่าใช้จ่ายฉุกเฉิน',
        image: 'assets/img/products/travel.jpg',
        features: ['เริ่มต้นรายวัน', 'คุ้มครองครอบครัว', 'เคลมง่ายผ่านทีม BOYINSURE'],
      },
    ],
  },
  {
    id: 'property',
    title: 'ทรัพย์สินและธุรกิจ',
    tagline: 'ปกป้องสิ่งที่คุณสร้างมา — บ้าน ธุรกิจ และทีมงาน',
    icon: 'building-2',
    plans: [
      {
        id: 'fire',
        name: 'ประกันอัคคีภัย',
        desc: 'คุ้มครองบ้าน คอนโด และทรัพย์สินภายในจากไฟไหม้และภัยพิบัติ',
        image: 'assets/img/products/fire.jpg',
        featured: true,
        features: ['คุ้มครองอาคารและเฟอร์นิเจอร์', 'ภัยธรรมชาติเสริมได้', 'ประเมินทุนตามมูลค่าจริง'],
      },
      {
        id: 'group',
        name: 'ประกันกลุ่ม',
        desc: 'สวัสดิการดูแลพนักงานทั้งองค์กร สร้างความมั่นใจให้ทีม',
        image: 'assets/img/products/group.jpg',
        features: ['ออกแบบตามขนาดองค์กร', 'สุขภาพและอุบัติเหตุ', 'บริหารจัดการง่าย'],
      },
      {
        id: 'business-liability',
        name: 'ประกันความรับผิดทางธุรกิจ',
        desc: 'ลดความเสี่ยงจากความรับผิดต่อบุคคลภายนอกและทรัพย์สิน',
        image: 'assets/img/products/group.jpg',
        features: ['เหมาะ SME และร้านค้า', 'คุ้มครองค่าเสียหายต่อบุคคลที่สาม', 'ปรึกษาแผนกับทีม'],
      },
    ],
  },
];

const PLAN_PROCESS_STEPS = [
  { icon: 'message-circle', title: 'ปรึกษาและแชร์ความต้องการ', desc: 'บอกเป้าหมาย งบประมาณ และสถานการณ์ปัจจุบัน' },
  { icon: 'list-checks', title: 'วิเคราะห์และเปรียบเทียบ', desc: 'ทีมช่วยสรุปแผนจากหลายบริษัทที่เหมาะกับคุณ' },
  { icon: 'clipboard-check', title: 'เลือกแผนและทำสัญญา', desc: 'ตัดสินใจได้อย่างมั่นใจ ไม่มีการบังคับซื้อ' },
  { icon: 'headphones', title: 'ดูแลหลังขาย', desc: 'ช่วยเรื่องเคลม ต่ออายุ และปรับแผนเมื่อชีวิตเปลี่ยน' },
];

let ACTIVE_INSURANCE_CATEGORIES = INSURANCE_CATEGORIES;
let ACTIVE_HIGHLIGHTS_DATA = HIGHLIGHTS_DATA;
let ACTIVE_PLAN_PROCESS_STEPS = PLAN_PROCESS_STEPS;
let SITE_PUBLIC = null;

function isStaticDemoHost() {
  return location.hostname.endsWith('github.io');
}

function siteApiUrl(phpPath) {
  if (!isStaticDemoHost()) return phpPath;
  const map = {
    'api/site/public.php': 'static-api/site/public.json',
    'api/insurance/categories.php': 'static-api/insurance/categories.json',
    'api/articles/categories.php': 'static-api/articles/categories.json',
  };
  return map[phpPath] || phpPath;
}

function sitePublicSettings() {
  return SITE_PUBLIC?.settings || {
    site_name: 'BOYINSURE',
    site_tagline: 'คุ้มครองทุกช่วงชีวิต ด้วยใจ',
    contact_email: 'contact@boyinsure.com',
    phone: '0627878968',
    phone_display: '062-787-8968',
    business_hours: 'จันทร์–ศุกร์ 09:00–18:00 น.',
    address: 'ให้บริการทั่วประเทศ',
    footer_note: 'พันธมิตรด้านประกันภัย',
    facebook_url: 'https://www.facebook.com/',
    tiktok_url: 'https://www.tiktok.com/',
    line_url: 'https://line.me/R/ti/p/@boyinsure',
  };
}

function getContactFabConfig() {
  const s = sitePublicSettings();
  const phone = (s.phone || '0627878968').replace(/\D/g, '');
  return {
    phoneHref: `tel:${phone}`,
    facebookHref: s.facebook_url || 'https://www.facebook.com/',
    tiktokHref: s.tiktok_url || 'https://www.tiktok.com/',
    lineHref: s.line_url || 'https://line.me/R/ti/p/@boyinsure',
    contactHref: 'contact.html',
  };
}

const CONTACT_FAB_ICONS = {
  phone: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>',
  facebook: '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>',
  tiktok: '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/></svg>',
  line: '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 2C6.48 2 2 5.58 2 10.02c0 3.95 3.5 7.26 8.22 7.9.32.07.75.21.86.49.1.24.06.62.03.87l-.14.84c-.04.24-.19.94.82.51 1.01-.43 5.45-3.21 7.43-5.49C21.96 13.77 22 11.93 22 10.02 22 5.58 17.52 2 12 2z"/></svg>',
  contact: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>',
};

const CONTACT_FAB_TOGGLE_ICON = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>';
const CONTACT_FAB_CLOSE_ICON = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>';

function buildContactFabHtml(cfg) {
  const items = [
    { href: cfg.contactHref, class: 'contact', label: 'สอบถาม', icon: CONTACT_FAB_ICONS.contact, external: false, i: 0 },
    { href: cfg.lineHref, class: 'line', label: 'LINE', icon: CONTACT_FAB_ICONS.line, external: true, i: 1 },
    { href: cfg.tiktokHref, class: 'tiktok', label: 'TikTok', icon: CONTACT_FAB_ICONS.tiktok, external: true, i: 2 },
    { href: cfg.facebookHref, class: 'facebook', label: 'Facebook', icon: CONTACT_FAB_ICONS.facebook, external: true, i: 3 },
    { href: cfg.phoneHref, class: 'phone', label: 'โทรศัพท์', icon: CONTACT_FAB_ICONS.phone, external: false, i: 4 },
  ];

  const links = items.map((item) => {
    const ext = item.external ? ' target="_blank" rel="noopener noreferrer"' : '';
    return `<a href="${item.href}" class="contact-fab__btn contact-fab__btn--${item.class}" style="--i:${item.i}" aria-label="${item.label}"${ext}>${item.icon}</a>`;
  }).join('');

  return `
    <div class="contact-fab__backdrop" data-fab-backdrop aria-hidden="true"></div>
    <div class="contact-fab__menu" id="contactFabMenu" aria-hidden="true">${links}</div>
    <button type="button" class="contact-fab__toggle" aria-expanded="false" aria-controls="contactFabMenu" aria-label="เปิดช่องทางติดต่อ">
      <span class="contact-fab__toggle-icon contact-fab__toggle-icon--open">${CONTACT_FAB_TOGGLE_ICON}</span>
      <span class="contact-fab__toggle-icon contact-fab__toggle-icon--close">${CONTACT_FAB_CLOSE_ICON}</span>
    </button>
  `;
}

function setContactFabOpen(fab, open) {
  const menu = fab.querySelector('#contactFabMenu');
  const toggle = fab.querySelector('.contact-fab__toggle');
  const backdrop = fab.querySelector('[data-fab-backdrop]');
  if (!menu || !toggle) return;

  fab.classList.toggle('is-open', open);
  toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
  toggle.setAttribute('aria-label', open ? 'ปิดช่องทางติดต่อ' : 'เปิดช่องทางติดต่อ');
  menu.setAttribute('aria-hidden', open ? 'false' : 'true');
  backdrop?.setAttribute('aria-hidden', open ? 'false' : 'true');
  document.body.classList.toggle('contact-fab-open', open);
}

function bindContactFabEvents(fab) {
  const toggle = fab.querySelector('.contact-fab__toggle');
  const backdrop = fab.querySelector('[data-fab-backdrop]');

  toggle?.addEventListener('click', (e) => {
    e.stopPropagation();
    setContactFabOpen(fab, !fab.classList.contains('is-open'));
  });

  backdrop?.addEventListener('click', () => setContactFabOpen(fab, false));

  fab.querySelectorAll('.contact-fab__btn').forEach((link) => {
    link.addEventListener('click', () => setContactFabOpen(fab, false));
  });

  if (fab.dataset.docBound === '1') return;
  fab.dataset.docBound = '1';

  document.addEventListener('click', (e) => {
    const activeFab = document.getElementById('contactFab');
    if (!activeFab?.classList.contains('is-open')) return;
    if (e.target.closest('#contactFab')) return;
    setContactFabOpen(activeFab, false);
  });

  document.addEventListener('keydown', (e) => {
    const activeFab = document.getElementById('contactFab');
    if (e.key === 'Escape' && activeFab?.classList.contains('is-open')) {
      setContactFabOpen(activeFab, false);
    }
  });
}

function initContactFab() {
  let fab = document.getElementById('contactFab');
  const cfg = getContactFabConfig();
  const wasOpen = fab?.classList.contains('is-open');

  if (!fab) {
    fab = document.createElement('aside');
    fab.id = 'contactFab';
    fab.className = 'contact-fab';
    fab.setAttribute('aria-label', 'ช่องทางติดต่อ');
    document.body.appendChild(fab);
  }

  fab.innerHTML = buildContactFabHtml(cfg);
  bindContactFabEvents(fab);
  if (wasOpen) setContactFabOpen(fab, true);
}

async function loadSitePublicApi() {
  try {
    const res = await fetch(siteApiUrl('api/site/public.php'));
    if (!res.ok) return;
    const data = await res.json();
    if (data.settings || data.content) {
      SITE_PUBLIC = data;
      applySitePublic(data);
    }
  } catch (_) {
    /* fallback ข้อมูลใน site.js */
  }
}

async function loadInsuranceApi() {
  try {
    const res = await fetch(siteApiUrl('api/insurance/categories.php'));
    if (!res.ok) return;
    const data = await res.json();
    if (Array.isArray(data.categories) && data.categories.length > 0) {
      ACTIVE_INSURANCE_CATEGORIES = data.categories;
    }
  } catch (_) {}
}

function applySitePublic(data) {
  const settings = data.settings || {};
  const content = data.content || {};
  if (content.highlights?.items?.length) {
    ACTIVE_HIGHLIGHTS_DATA = content.highlights.items;
  }
  if (content.plan_process?.steps?.length) {
    ACTIVE_PLAN_PROCESS_STEPS = content.plan_process.steps;
  }
  const page = document.body.dataset.page;
  if (page === 'about' && content.about) applyAboutPage(content.about, settings);
  if (page === 'contact' && content.contact) applyContactPage(content.contact, settings);
}

function applyAboutPage(about, settings) {
  const hero = document.querySelector('.page-hero--about .page-hero__inner');
  if (hero && about.hero) {
    const eyebrow = hero.querySelector('.page-hero__eyebrow');
    const title = hero.querySelector('h1');
    const lead = hero.querySelector('.page-hero__lead');
    if (eyebrow && about.hero.eyebrow) eyebrow.textContent = about.hero.eyebrow;
    if (title && about.hero.title) title.textContent = about.hero.title;
    if (lead && about.hero.lead) lead.textContent = about.hero.lead;
  }
  const textCol = document.querySelector('.about-grid__text');
  if (textCol && Array.isArray(about.paragraphs)) {
    textCol.innerHTML = about.paragraphs.map((p) => `<p>${p}</p>`).join('');
  }
  const highlight = document.querySelector('.about-highlight');
  if (highlight && about.highlight) {
    const h = highlight.querySelector('h3');
    const p = highlight.querySelector('p');
    if (h && about.highlight.title) h.textContent = about.highlight.title;
    if (p && about.highlight.text) p.textContent = about.highlight.text;
  }
  if (about.services?.items?.length) {
    const head = document.querySelector('.site-section--alt .section-head h2');
    if (head && about.services.heading) head.textContent = about.services.heading;
    const grid = document.querySelector('.site-section--alt .feature-grid');
    if (grid) {
      grid.innerHTML = about.services.items.map((item) => `
        <article class="feature-card">
          <div class="feature-card__icon" aria-hidden="true"><i data-lucide="${item.icon || 'circle'}"></i></div>
          <h3>${item.title || ''}</h3>
          <p>${item.text || ''}</p>
        </article>
      `).join('');
    }
  }
  const cta = document.querySelector('.cta-banner');
  if (cta && about.cta) {
    const h2 = cta.querySelector('h2');
    const p = cta.querySelector('p');
    if (h2 && about.cta.title) h2.textContent = about.cta.title;
    if (p && (about.cta.text || about.cta.subtitle)) p.textContent = about.cta.text || about.cta.subtitle;
    const actions = cta.querySelector('.cta-banner__actions');
    if (actions && about.cta.actions?.length) {
      actions.innerHTML = about.cta.actions.map((a) => {
        const cls = a.style === 'gold' ? 'btn btn--gold' : 'btn btn--outline';
        return `<a href="${a.href || '#'}" class="${cls}">${a.label || ''}</a>`;
      }).join('');
    } else if (settings.phone_display) {
      const tel = cta.querySelector('a[href^="tel:"]');
      if (tel) {
        tel.href = `tel:${settings.phone || settings.phone_display}`;
        tel.textContent = `โทร ${settings.phone_display}`;
      }
    }
  }
  if (window.lucide?.createIcons) lucide.createIcons();
}

function applyContactPage(contact, settings) {
  const hero = document.querySelector('.page-hero--contact .page-hero__inner');
  if (hero && contact.hero) {
    const eyebrow = hero.querySelector('.page-hero__eyebrow');
    const title = hero.querySelector('h1');
    const lead = hero.querySelector('.page-hero__lead');
    if (eyebrow && contact.hero.eyebrow) eyebrow.textContent = contact.hero.eyebrow;
    if (title && contact.hero.title) title.textContent = contact.hero.title;
    if (lead && contact.hero.lead) lead.textContent = contact.hero.lead;
  }
  const cardsCol = document.querySelector('.contact-grid > div:first-child');
  if (cardsCol && contact.cards?.length) {
    cardsCol.innerHTML = contact.cards.map((card, i) => {
      const margin = i < contact.cards.length - 1 ? ' style="margin-bottom:20px;"' : '';
      const valueHtml = card.href && card.value
        ? `<a href="${card.href}">${card.value}</a>`
        : (card.value ? `<span>${card.value}</span>` : '');
      const extra = card.text ? `<p style="margin-top:12px;font-size:.92rem;color:#5a6b85;">${card.text}</p>` : '';
      return `<div class="contact-card"${margin}>
        <h3><i data-lucide="${card.icon || 'circle'}" class="contact-card__heading-icon" aria-hidden="true"></i> ${card.title || ''}</h3>
        ${valueHtml}${extra}
      </div>`;
    }).join('');
  } else if (settings.phone_display) {
    const phoneCard = cardsCol?.querySelector('.contact-card a[href^="tel:"]');
    if (phoneCard) {
      phoneCard.href = `tel:${settings.phone || settings.phone_display}`;
      phoneCard.textContent = settings.phone_display;
    }
  }
  if (contact.interests?.length) {
    const interest = document.getElementById('interest');
    if (interest) {
      interest.innerHTML = '<option value="">-- เลือก --</option>' +
        contact.interests.map((opt) => `<option>${opt}</option>`).join('');
    }
  }
  if (contact.features?.length) {
    const section = document.querySelector('.page-main .site-section:not(.site-section--alt) .feature-grid');
    if (section) {
      section.innerHTML = contact.features.map((item) => `
        <article class="feature-card">
          <div class="feature-card__icon" aria-hidden="true"><i data-lucide="${item.icon || 'circle'}"></i></div>
          <h3>${item.title || ''}</h3>
          <p>${item.text || ''}</p>
        </article>
      `).join('');
    }
  }
  if (window.lucide?.createIcons) lucide.createIcons();
}

function buildFooterHtml() {
  const s = sitePublicSettings();
  const footer = SITE_PUBLIC?.content?.footer || {};
  const siteName = s.site_name || 'BOYINSURE';
  const tagline = footer.tagline || s.site_tagline || 'คุ้มครองทุกช่วงชีวิต ด้วยใจ';
  const desc = footer.description || 'พันธมิตรด้านประกันชีวิตและประกันภัย วิเคราะห์และเปรียบเทียบแผนให้ก่อนตัดสินใจ ดูแลลูกค้าต่อเนื่องจนจบทุกเคส';
  const note = footer.note || s.footer_note || 'พันธมิตรด้านประกันภัย';
  const phone = s.phone || '0627878968';
  const phoneDisplay = s.phone_display || '062-787-8968';
  const email = s.contact_email || 'contact@boyinsure.com';
  const hours = s.business_hours || 'จันทร์–ศุกร์ 09:00–18:00 น.';
  const address = s.address || 'ให้บริการทั่วประเทศ';
  const copyright = footer.copyright || `© ${new Date().getFullYear()} ${siteName} — สงวนลิขสิทธิ์`;

  return `<div class="site-footer__inner">
  <div class="site-footer__brand">
    <a href="index.html" class="site-footer__name">${siteName}</a>
    <p class="site-footer__tagline">${tagline}</p>
    <p class="site-footer__desc">${desc}</p>
    <p class="site-footer__note">${note}</p>
    <div class="site-footer__social">
      <a href="tel:${phone}" class="site-footer__social-link" aria-label="โทรศัพท์"><i data-lucide="phone" aria-hidden="true"></i></a>
      <a href="contact.html" class="site-footer__social-link" aria-label="แชทสอบถาม"><i data-lucide="message-circle" aria-hidden="true"></i></a>
      <a href="mailto:${email}" class="site-footer__social-link" aria-label="อีเมล"><i data-lucide="mail" aria-hidden="true"></i></a>
    </div>
  </div>
  <div class="site-footer__col">
    <h4 class="site-footer__heading">เมนูหลัก</h4>
    <nav class="site-footer__nav" aria-label="เมนูหลัก">
      <a href="index.html">หน้าแรก</a>
      <a href="promotions.html">โปรโมชั่นและของรางวัล</a>
      <a href="insurance.html">แบบประกันของเรา</a>
      <a href="about.html">เกี่ยวกับ BOYINSURE</a>
      <a href="articles.html">บทความ</a>
      <a href="contact.html">ติดต่อเรา</a>
    </nav>
  </div>
  <div class="site-footer__col">
    <h4 class="site-footer__heading">บริการประกัน</h4>
    <nav class="site-footer__nav" aria-label="บริการประกัน">
      <a href="insurance.html">ประกันสุขภาพ</a>
      <a href="insurance.html">ประกันชีวิต</a>
      <a href="insurance.html">ประกันสะสมทรัพย์ / บำนาญ</a>
      <a href="insurance.html">ประกันโรคร้ายแรง</a>
      <a href="insurance.html">ประกันอุบัติเหตุ</a>
      <a href="insurance.html">ประกันรถยนต์ / เดินทาง</a>
    </nav>
  </div>
  <div class="site-footer__col site-footer__col--contact">
    <h4 class="site-footer__heading">ติดต่อเรา</h4>
    <ul class="site-footer__contact-list">
      <li><i data-lucide="phone" aria-hidden="true"></i><a href="tel:${phone}">${phoneDisplay}</a></li>
      <li><i data-lucide="clock" aria-hidden="true"></i><span>${hours}</span></li>
      <li><i data-lucide="map-pin" aria-hidden="true"></i><span>${address}</span></li>
    </ul>
    <a href="contact.html" class="btn btn--gold site-footer__cta">ปรึกษาฟรี</a>
  </div>
</div>
<div class="site-footer__bottom">
  <p class="site-footer__copy">${copyright}</p>
  <nav class="site-footer__legal" aria-label="ข้อกำหนดทางกฎหมาย">
    <a href="contact.html">นโยบายความเป็นส่วนตัว</a>
    <a href="contact.html">เงื่อนไขการใช้งาน</a>
  </nav>
</div>`;
}

const CATEGORY_DETAIL_DEFAULTS = {
  savings: {
    facts: { age: '0–65 ปี', term: '10–20 ปี', premium: 'เริ่มต้น ~1,000–5,000 บ./เดือน', processDays: '3–7 วันทำการ' },
    exclusions: ['การฆ่าตัวตายภายใน 1 ปีแรก (ตามเงื่อนไขกรมธรรม์)', 'โรคที่มีอยู่ก่อนทำสัญญา (ขึ้นกับการรับประกัน)', 'การไม่ชำระเบี้ยตามกำหนด', 'การให้ข้อมูลไม่ตรงความจริง'],
    faq: [
      { q: 'ต้องตรวจสุขภาพไหม?', a: 'ขึ้นกับอายุ ทุนประกัน และบริษัทที่เลือก บางแผนไม่ต้องตรวจสุขภาพ' },
      { q: 'ยกเลิกกลางคันได้ไหม?', a: 'ยกเลิกได้ตามเงื่อนไขกรมธรรม์ อาจได้รับมูลค่าเงินคืนตามระยะเวลาที่ชำระ' },
      { q: 'BOYINSURE บังคับซื้อไหม?', a: 'ไม่บังคับซื้อ เราให้คำปรึกษาและเปรียบเทียบแผนให้ฟรีก่อนตัดสินใจ' },
    ],
  },
  health: {
    facts: { age: '0–75 ปี', term: 'ต่ออายุรายปี', premium: 'เริ่มต้น ~3,000–20,000 บ./ปี', processDays: '1–5 วันทำการ' },
    exclusions: ['โรคที่มีอยู่ก่อนทำสัญญา (ตามเงื่อนไขรับประกัน)', 'การรักษาเพื่อความสวยงาม', 'อุบัติเหตุจากกีฬาเสี่ยง (ถ้าไม่ได้ซื้อความคุ้มครองเสริม)', 'การรักษานอกเหนือวงเงินหรือข้อยกเว้นในกรมธรรม์'],
    faq: [
      { q: 'เคลมค่ารักษายังไง?', a: 'แจ้งเคลมผ่าน BOYINSURE หรือบริษัทประกันโดยตรง ทีมช่วยติดตามจนจบเคส' },
      { q: 'OPD คุ้มครองไหม?', a: 'ขึ้นกับแผนที่เลือก บางแผนครอบคลุมทั้ง IPD และ OPD' },
      { q: 'เปลี่ยนแผนได้ไหม?', a: 'ปรับแผนหรือเพิ่มความคุ้มครองได้เมื่อครบกำหนดต่ออายุ หรือตามเงื่อนไขบริษัท' },
    ],
  },
  risk: {
    facts: { age: '1–70 ปี', term: '1–99 ปี (แล้วแต่แผน)', premium: 'เริ่มต้น ~500–3,000 บ./เดือน', processDays: '1–5 วันทำการ' },
    exclusions: ['การฆ่าตัวตาย', 'อุบัติเหตุจากสงครามหรือก่อการร้าย', 'การกระทำผิดกฎหมายโดยเจตนา', 'โรคที่มีอยู่ก่อน (สำหรับบางแผน)'],
    faq: [
      { q: 'ต้องตรวจสุขภาพไหม?', a: 'ประกันอุบัติเหตุส่วนใหญ่ไม่ต้องตรวจ ประกันชีวิตขึ้นกับทุนและอายุ' },
      { q: 'คุ้มครองตลอด 24 ชม. ไหม?', a: 'ประกันอุบัติเหตุคุ้มครองทั่วโลกตลอด 24 ชม. (ตามเงื่อนไขกรมธรรม์)' },
      { q: 'ผู้รับผลประโยชน์เปลี่ยนได้ไหม?', a: 'เปลี่ยนได้ตามขั้นตอนที่บริษัทกำหนด' },
    ],
  },
  travel: {
    facts: { age: '0–80 ปี', term: 'ตามวันเดินทาง', premium: 'เริ่มต้น ~100–2,000 บ./ทริป', processDays: 'ทันที–1 วัน' },
    exclusions: ['การเดินทางไปพื้นที่ที่มีคำเตือนห้ามเดินทาง', 'การเจ็บป่วยที่มีอยู่ก่อน', 'กีฬาเสี่ยง (ถ้าไม่ได้ซื้อความคุ้มครองเสริม)', 'ความเสียหายจากการดื่มสุราเกินขีดจำกัด'],
    faq: [
      { q: 'ซื้อก่อนเดินทางกี่วัน?', a: 'แนะนำซื้อก่อนออกเดินทางอย่างน้อย 1 วัน บางแผนซื้อได้ในวันเดินทาง' },
      { q: 'เคลมกระเป๋าหายยังไง?', a: 'แจ้งเคลมพร้อมเอกสารจากสายการบินหรือตำรวจตามเงื่อนไขกรมธรรม์' },
      { q: 'ประกันรถต่ออายุเมื่อไหร่?', a: 'ต่ออายุก่อนหมดอายุกรมธรรม์ ทีม BOYINSURE ช่วยเตือนและเปรียบเทียบเบี้ยใหม่' },
    ],
  },
  property: {
    facts: { age: 'เจ้าของทรัพย์สิน/นิติบุคคล', term: '1 ปี (ต่ออายุได้)', premium: 'ขึ้นกับมูลค่าทรัพย์สิน', processDays: '3–10 วันทำการ' },
    exclusions: ['การสึกหรอตามปกติ', 'ความเสียหายจากการก่อสร้างหรือรื้อถอน', 'สงคราม จลาจล หรือก่อการร้าย (ตามเงื่อนไข)', 'การให้ข้อมูลมูลค่าทรัพย์สินไม่ถูกต้อง'],
    faq: [
      { q: 'ประเมินทุนประกันอย่างไร?', a: 'ประเมินจากมูลค่าอาคารและทรัพย์สินภายใน ทีมช่วยแนะนำให้คุ้มครองพอดี' },
      { q: 'ประกันกลุ่มเหมาะกับบริษัทขนาดไหน?', a: 'ออกแบบได้ตั้งแต่ SME ไปจนถึงองค์กรขนาดใหญ่' },
      { q: 'เคลมอัคคีภัยใช้เวลานานไหม?', a: 'ขึ้นกับความเสียหายและเอกสาร ทีม BOYINSURE ช่วยประสานจนจบเคส' },
    ],
  },
};

const INSURANCE_PLAN_DETAILS = {
  'savings-fund': {
    coverageDetails: ['ออมเงินตามระยะเวลาที่กำหนด พร้อมดอกเบี้ย/ผลตอบแทนตามแผน', 'คุ้มครองชีวิตตลอดอายุสัญญา', 'รับเงินคืนตามกำหนดหรือเมื่อครบสัญญา', 'ปรับทุนประกันและเบี้ยให้เหมาะกับงบประมาณ'],
    useCase: 'คุณอายุ 35 ปี อยากออมเงิน 10–15 ปี พร้อมคุ้มครองชีวิตให้ครอบครัว งบประมาณ ~3,000 บ./เดือน — ทีม BOYINSURE ช่วยเปรียบเทียบแผนที่ให้ทั้งการออมและความคุ้มครอง',
    faq: [{ q: 'ได้เงินคืนเมื่อไหร่?', a: 'ตามระยะเวลาในกรมธรรม์ เช่น ราย 3–5 ปี หรือเมื่อครบสัญญา ขึ้นกับแผนที่เลือก' }],
  },
  pension: {
    facts: { premium: 'เริ่มต้น ~2,000–10,000 บ./เดือน' },
    coverageDetails: ['รับเงินบำนาญรายเดือนหลังเกษียณ', 'เลือกอายุเริ่มรับผลประโยชน์ได้', 'คุ้มครองชีวิตระหว่างสะสม', 'วางแผนรายได้หลังเกษียณล่วงหน้า'],
    useCase: 'คุณอายุ 45 ปี วางแผนเกษียณใน 15 ปี อยากมีรายได้มั่นคง ~15,000 บ./เดือนหลังเกษียณ — เราช่วยคำนวณและเปรียบเทียบแผนบำนาญจากหลายบริษัท',
  },
  'tax-shield': {
    facts: { premium: 'เริ่มต้น ~1,500–6,000 บ./เดือน' },
    coverageDetails: ['ลดหย่อนภาษีได้ตามเงื่อนไข ก.ค.ศ.', 'คุ้มครองชีวิตควบคู่การออม', 'เลือกระยะเวลาชำระเบี้ยให้เหมาะกับแผนภาษี', 'เปรียบเทียบแผนจากหลายบริษัทก่อนตัดสินใจ'],
    useCase: 'พนักงานเงินเดือนที่ต้องการลดหย่อนภาษีสูงสุด พร้อมมีความคุ้มครองชีวิต — ทีมช่วยจับคู่แผนกับช่วงรายได้และวงเงินลดหย่อนที่ใช้ได้',
    faq: [{ q: 'ลดหย่อนได้เท่าไหร่?', a: 'ขึ้นกับประเภทกรมธรรม์และเพดานลดหย่อนประจำปี ทีมช่วยคำนวณให้ตามสถานการณ์จริง' }],
  },
  health: {
    coverageDetails: ['ค่ารักษาผู้ป่วยใน (IPD) และผู้ป่วยนอก (OPD) ตามแผน', 'เลือกวงเงินและความคุ้มครองเสริมได้', 'เครือข่ายโรงพยาบาลชั้นนำทั่วประเทศ', 'ต่ออายุได้ตามเงื่อนไขบริษัท'],
    useCase: 'ครอบครัวมีลูกเล็ก 1 คน ต้องการคุ้มครองค่ารักษา IPD/OPD งบ ~8,000 บ./ปี — เปรียบเทียบแผนที่ครอบคลุมทั้งผู้ใหญ่และเด็ก',
  },
  'critical-illness': {
    facts: { premium: 'เริ่มต้น ~1,000–5,000 บ./เดือน' },
    coverageDetails: ['จ่ายเงินก้อนเมื่อวินิจฉัยโรคร้ายแรงที่กำหนด', 'ไม่ต้องส่งใบเสร็จค่ารักษา', 'ใช้เงินได้อิสระ ไม่จำกัดการใช้', 'ครอบคลุมโรคร้ายแรงหลัก เช่น มะเร็ง หัวใจ ไตวาย'],
    useCase: 'ผู้ที่มีประกันสุขภาพอยู่แล้ว แต่ต้องการเงินก้อนสำรองเมื่อป่วยหนัก — เสริมความคุ้มครองโรคร้ายแรงให้ครอบคลุมช่วงพักฟื้น',
  },
  'senior-health': {
    facts: { age: '50–85 ปี', premium: 'เริ่มต้น ~5,000–25,000 บ./ปี' },
    coverageDetails: ['ออกแบบสำหรับวัยเกษียณและผู้สูงอายุ', 'ค่ารักษา การเฝ้าไข้ และค่าใช้จ่ายที่เกี่ยวข้อง', 'เบี้ยเหมาะกับวัยและสุขภาพ', 'ลดภาระค่ารักษาให้ลูกหลาน'],
    useCase: 'พ่อแม่วัย 60 ปี มีโรคประจำตัวเล็กน้อย ต้องการประกันที่รับได้และคุ้มครองค่ารักษาจริง — ทีมช่วยหาแผนที่เหมาะกับอายุและสุขภาพ',
  },
  accident: {
    coverageDetails: ['คุ้มครองอุบัติเหตุ 24 ชั่วโมงทั่วโลก', 'เสียชีวิต ทุพพลภาพ และค่ารักษาจากอุบัติเหตุ', 'เบี้ยเริ่มต้นไม่สูง สมัครง่าย', 'เหมาะเสริมความคุ้มครองรายวัน'],
    useCase: 'พนักงานออฟฟิศที่เดินทางบ่อย อยากมีความคุ้มครองอุบัติเหตุเสริมจากประกันหลัก งบ ~500 บ./เดือน',
  },
  life: {
    facts: { premium: 'เริ่มต้น ~800–5,000 บ./เดือน' },
    coverageDetails: ['ทุนประกันชีวิตปรับได้ตามความต้องการ', 'ผลประโยชน์แก่ผู้รับผลประโยชน์ที่กำหนด', 'คุ้มครองระยะยาวหรือตามระยะเวลา', 'วางแผนทางการเงินให้ครอบครัวเมื่อไม่อยู่'],
    useCase: 'หัวหน้าครอบครัววัย 40 ปี มีภาระผ่อนบ้านและค่าเล่าเรียนลูก ต้องการทุนชีวิต ~2 ล้านบาท — เปรียบเทียบเบี้ยและแผนที่คุ้มค่า',
  },
  'personal-accident': {
    facts: { premium: 'เริ่มต้น ~300–1,500 บ./เดือน' },
    coverageDetails: ['สมัครง่าย ไม่ต้องตรวจสุขภาพ (ตามเงื่อนไข)', 'คุ้มครองทั่วโลกตลอด 24 ชม.', 'ต่ออายุสะดวก', 'เสริมจากประกันหลักได้'],
    useCase: 'Freelance หรืออาชีพที่เดินทางบ่อย ต้องการความคุ้มครองอุบัติเหตุเบา ๆ ไม่ซับซ้อน สมัครได้เร็ว',
  },
  travel: {
    coverageDetails: ['คุ้มครองทั้งในประเทศและต่างประเทศ', 'ค่ารักษา อุบัติเหตุ และกระเป๋าเดินทาง', 'ความล่าช้าของเที่ยวบิน (ตามแผน)', 'ซื้อออนไลน์ก่อนเดินทางได้'],
    useCase: 'ทริปญี่ปุ่น 7 วัน ครอบครัว 4 คน ต้องการคุ้มครองค่ารักษาและกระเป๋าหาย — เปรียบเทียบแผนรายทริปที่คุ้มค่า',
  },
  car: {
    facts: { term: '1 ปี (ต่ออายุได้)', premium: 'ขึ้นกับรุ่นรถและประวัติเคลม' },
    coverageDetails: ['ชั้น 1 / 2+ / 3+ เลือกได้ตามความต้องการ', 'รองรับรถ EV และรถส่วนบุคคล', 'เปรียบเทียบเบี้ยจากหลายบริษัท', 'ความคุ้มครองรถยนต์และบุคคลภายนอก'],
    useCase: 'เจ้าของรถ EV ใหม่ ต้องการชั้น 1 ครอบคลุมแบตเตอรี่และอุปกรณ์ — ทีมช่วยเปรียบเทียบเบี้ยและเงื่อนไขจากหลายบริษัท',
  },
  'travel-domestic': {
    facts: { premium: 'เริ่มต้น ~50–500 บ./วัน' },
    coverageDetails: ['คุ้มครองรายวันหรือรายทริป', 'อุบัติเหตุและค่าใช้จ่ายฉุกเฉิน', 'ครอบครัวสมัครพร้อมกันได้', 'เคลมง่ายผ่านทีม BOYINSURE'],
    useCase: 'ทริปเที่ยวภูเก็ต 3 วัน กับเพื่อน อยากมีประกันเบา ๆ คุ้มอุบัติเหตุและค่ารักษา — ซื้อได้ก่อนออกเดินทาง',
  },
  fire: {
    coverageDetails: ['คุ้มครองอาคาร คอนโด และเฟอร์นิเจอร์ภายใน', 'ไฟไหม้ ฟ้าผ่า และภัยพิบัติ (ตามแผน)', 'ภัยธรรมชาติเสริมได้', 'ประเมินทุนตามมูลค่าจริง'],
    useCase: 'เจ้าของบ้านที่ซื้อใหม่ ต้องการคุ้มครองทั้งตัวบ้านและของใช้ภายใน ~3 ล้านบาท — ช่วยเลือกแผนที่ครอบคลุมพอดี',
  },
  group: {
    facts: { premium: 'ขึ้นกับจำนวนพนักงานและแผน' },
    coverageDetails: ['สวัสดิการสุขภาพและอุบัติเหตุสำหรับพนักงาน', 'ออกแบบตามขนาดและงบองค์กร', 'บริหารจัดการและต่ออายุง่าย', 'เพิ่มความมั่นใจและดึงดูดบุคลากร'],
    useCase: 'บริษัท SME 50 คน ต้องการสวัสดิการประกันกลุ่มสำหรับพนักงาน งบจำกัด — ออกแบบแผนที่สมดุลระหว่างความคุ้มครองและต้นทุน',
  },
  'business-liability': {
    coverageDetails: ['คุ้มครองความรับผิดต่อบุคคลภายนอก', 'ค่าเสียหายต่อทรัพย์สินบุคคลที่สาม', 'เหมาะ SME ร้านค้า และธุรกิจบริการ', 'ลดความเสี่ยงจากค่าเสียหายที่ไม่คาดคิด'],
    useCase: 'ร้านอาหารหรือคลินิกที่มีลูกค้าเข้าใช้บริการ ต้องการคุ้มครองความรับผิดหากเกิดอุบัติเหตุกับลูกค้า',
  },
};

function getPlanDetailContent(plan, category) {
  const catDefaults = CATEGORY_DETAIL_DEFAULTS[category.id] || CATEGORY_DETAIL_DEFAULTS.savings;
  const planData = INSURANCE_PLAN_DETAILS[plan.id] || {};
  return {
    facts: { ...catDefaults.facts, ...planData.facts },
    coverageDetails: planData.coverageDetails || plan.coverage || plan.features,
    exclusions: planData.exclusions || catDefaults.exclusions,
    useCase: planData.useCase || `เหมาะสำหรับผู้ที่สนใจ${plan.name} และต้องการคำปรึกษาแบบไม่บังคับซื้อ ทีม BOYINSURE ช่วยวิเคราะห์และเปรียบเทียบแผนจากหลายบริษัทให้เหมาะกับงบและเป้าหมายของคุณ`,
    faq: [...(catDefaults.faq || []), ...(planData.faq || [])],
  };
}

function findInsurancePlan(planId) {
  if (!planId) return null;
  for (const category of ACTIVE_INSURANCE_CATEGORIES) {
    const plan = category.plans.find((item) => item.id === planId);
    if (plan) return { category, plan };
  }
  return null;
}

function initSidebarNavDots(nav) {
  if (!nav) return null;

  const sidebar = nav.closest('.insurance-sidebar');
  if (!sidebar) return null;

  let dotsEl = sidebar.querySelector('.insurance-sidebar__dots');
  if (!dotsEl) {
    dotsEl = document.createElement('div');
    dotsEl.className = 'insurance-sidebar__dots';
    dotsEl.setAttribute('role', 'tablist');
    dotsEl.setAttribute('aria-label', 'เลื่อนดูหมวดหมู่');
    nav.insertAdjacentElement('afterend', dotsEl);
  }

  const mq = window.matchMedia('(max-width: 1024px)');

  function getItems() {
    return [...nav.querySelectorAll('.insurance-sidebar__item')];
  }

  function updateActiveDot() {
    const items = getItems();
    const dots = [...dotsEl.querySelectorAll('.insurance-sidebar__dot')];
    if (!dots.length) return;

    const activeBtn = nav.querySelector('.insurance-sidebar__item.is-active');
    const activeIndex = activeBtn ? items.indexOf(activeBtn) : 0;

    dots.forEach((dot, i) => {
      const active = i === activeIndex;
      dot.classList.toggle('is-active', active);
      dot.setAttribute('aria-selected', active ? 'true' : 'false');
    });
  }

  function refresh() {
    const items = getItems();
    const overflow = nav.scrollWidth > nav.clientWidth + 2;

    if (!mq.matches || items.length <= 1 || !overflow) {
      dotsEl.hidden = true;
      return;
    }

    dotsEl.hidden = false;

    if (dotsEl.children.length !== items.length) {
      dotsEl.innerHTML = items.map((_, i) => `
        <button
          type="button"
          class="insurance-sidebar__dot${i === 0 ? ' is-active' : ''}"
          role="tab"
          aria-selected="${i === 0 ? 'true' : 'false'}"
          aria-label="หมวดที่ ${i + 1}"
          data-index="${i}"
        ></button>
      `).join('');

      dotsEl.querySelectorAll('.insurance-sidebar__dot').forEach((dot) => {
        dot.addEventListener('click', () => {
          const item = getItems()[Number(dot.dataset.index)];
          if (!item) return;
          item.click();
          item.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
        });
      });
    }

    updateActiveDot();
  }

  if (!nav.dataset.dotsBound) {
    nav.dataset.dotsBound = '1';
    nav.addEventListener('scroll', updateActiveDot, { passive: true });
    window.addEventListener('resize', refresh, { passive: true });
    mq.addEventListener('change', refresh);
  }

  refresh();

  return { refresh, updateActiveDot };
}

function initInsuranceCatalog() {
  const nav = document.getElementById('insuranceCategoryNav');
  const head = document.getElementById('insuranceCategoryHead');
  const grid = document.getElementById('insurancePlanGrid');
  if (!nav || !head || !grid) return;

  let activeId = ACTIVE_INSURANCE_CATEGORIES[0]?.id;
  const hashCategory = window.location.hash.replace('#', '');
  if (ACTIVE_INSURANCE_CATEGORIES.some((cat) => cat.id === hashCategory)) {
    activeId = hashCategory;
  }

  nav.innerHTML = ACTIVE_INSURANCE_CATEGORIES.map((cat) => `
    <button
      type="button"
      class="insurance-sidebar__item${cat.id === activeId ? ' is-active' : ''}"
      data-category="${cat.id}"
      aria-current="${cat.id === activeId ? 'true' : 'false'}"
    >
      <i data-lucide="${cat.icon}" class="insurance-sidebar__icon" aria-hidden="true"></i>
      <span>${cat.title}</span>
    </button>
  `).join('');

  const sidebarDots = initSidebarNavDots(nav);

  function renderPlans(categoryId) {
    const cat = ACTIVE_INSURANCE_CATEGORIES.find((c) => c.id === categoryId);
    if (!cat) return;

    head.innerHTML = `
      <span class="insurance-catalog__badge">
        <i data-lucide="${cat.icon}" aria-hidden="true"></i>
        ${cat.plans.length} แผนในหมวดนี้
      </span>
      <h2>${cat.title}</h2>
      <p>${cat.tagline}</p>
    `;

    grid.innerHTML = cat.plans.map((plan) => `
      <a href="insurance-plan.html?plan=${plan.id}" class="insurance-plan-card${plan.featured ? ' insurance-plan-card--featured' : ''}">
        <div class="insurance-plan-card__media">
          ${plan.featured ? '<span class="insurance-plan-card__badge">แนะนำ</span>' : ''}
          <img src="${plan.image}" alt="${plan.name}" width="640" height="400" loading="lazy" />
        </div>
        <div class="insurance-plan-card__body">
          <h3>${plan.name}</h3>
          <p>${plan.desc}</p>
          <ul class="insurance-plan-card__features">
            ${plan.features.map((f) => `<li>${f}</li>`).join('')}
          </ul>
          <span class="insurance-plan-card__cta">
            ดูรายละเอียด
            <i data-lucide="arrow-right" aria-hidden="true"></i>
          </span>
        </div>
      </a>
    `).join('');

    if (window.lucide?.createIcons) lucide.createIcons();
  }

  function selectCategory(categoryId) {
    if (categoryId === activeId) return;
    activeId = categoryId;

    nav.querySelectorAll('.insurance-sidebar__item').forEach((btn) => {
      const isActive = btn.dataset.category === categoryId;
      btn.classList.toggle('is-active', isActive);
      btn.setAttribute('aria-current', isActive ? 'true' : 'false');
    });

    sidebarDots?.updateActiveDot();
    nav.querySelector('.insurance-sidebar__item.is-active')
      ?.scrollIntoView({ inline: 'center', block: 'nearest', behavior: 'smooth' });

    grid.classList.add('is-fading');
    setTimeout(() => {
      renderPlans(categoryId);
      grid.classList.remove('is-fading');
    }, 180);
  }

  nav.addEventListener('click', (e) => {
    const btn = e.target.closest('.insurance-sidebar__item');
    if (!btn) return;
    selectCategory(btn.dataset.category);
  });

  renderPlans(activeId);
  if (window.lucide?.createIcons) lucide.createIcons();
  requestAnimationFrame(() => sidebarDots?.refresh());
}

const ARTICLE_CATEGORIES = [
  {
    id: 'life',
    title: 'ประกันชีวิต',
    tagline: 'สร้างความมั่นคงให้ครอบครัวเมื่อเกิดเหตุไม่คาดคิด',
    icon: 'heart',
    articles: [
      {
        id: 'why-life-insurance',
        title: 'ทำไมต้องมีประกันชีวิต?',
        excerpt: 'ประกันชีวิตช่วยสร้างความมั่นคงให้ครอบครัว เมื่อเกิดเหตุไม่คาดคิด การวางแผนล่วงหน้าช่วยลดภาระทางการเงินและให้คนที่คุณรักได้ดูแลต่อ',
        image: 'assets/img/products/pension.jpg',
        readTime: '4 นาที',
        featured: true,
        link: 'insurance.html#risk',
        linkLabel: 'ดูแบบประกัน',
      },
      {
        id: 'life-coverage-amount',
        title: 'ทุนประกันชีวิตกี่ล้านถึงจะพอ?',
        excerpt: 'ไม่มีตัวเลขเดียวที่ถูกสำหรับทุกคน เริ่มจากภาระหนี้ ค่าใช้จ่ายครอบครัว และเป้าหมายระยะยาว แล้วค่อยปรับทุนให้พอดี',
        image: 'assets/img/products/accident.jpg',
        readTime: '5 นาที',
        link: 'contact.html',
        linkLabel: 'ปรึกษาฟรี',
      },
    ],
  },
  {
    id: 'health',
    title: 'สุขภาพและการรักษา',
    tagline: 'เจ็บป่วยไม่สะเทือนเงินเก็บ — ดูแลสุขภาพครอบครัว',
    icon: 'heart-pulse',
    articles: [
      {
        id: 'health-savings',
        title: 'เจ็บป่วยไม่สะเทือนเงินเก็บ',
        excerpt: 'ค่ารักษาพยาบาลสูงขึ้นทุกวัน ประกันสุขภาพช่วยครอบคลุมค่าใช้จ่ายเมื่อป่วย ลดภาระทางการเงินของครอบครัว',
        image: 'assets/img/products/elderly.jpg',
        readTime: '4 นาที',
        featured: true,
        link: 'insurance.html#health',
        linkLabel: 'ดูแบบประกัน',
      },
      {
        id: 'ipd-opd',
        title: 'IPD กับ OPD ต่างกันอย่างไร?',
        excerpt: 'IPD คือการรักษาผู้ป่วยใน OPD คือผู้ป่วยนอก แต่ละแผนคุ้มครองไม่เหมือนกัน เลือกให้ตรงกับพฤติกรรมการใช้บริการของคุณ',
        image: 'assets/img/products/tax.jpg',
        readTime: '3 นาที',
        link: 'insurance-plan.html?plan=health',
        linkLabel: 'อ่านเพิ่มเติม',
      },
      {
        id: 'critical-illness-guide',
        title: 'ประกันโรคร้ายแรง จำเป็นไหม?',
        excerpt: 'เมื่อวินิจฉัยโรคร้ายแรง เงินก้อนจากประกันช่วยให้พักฟื้นได้โดยไม่ต้องกังวลค่าใช้จ่ายรายวัน',
        image: 'assets/img/products/group.jpg',
        readTime: '4 นาที',
        link: 'insurance-plan.html?plan=critical-illness',
        linkLabel: 'ดูแบบประกัน',
      },
    ],
  },
  {
    id: 'planning',
    title: 'วางแผนประกัน',
    tagline: 'เลือกแผนที่พอดีกับชีวิตและงบประมาณจริง',
    icon: 'clipboard-list',
    articles: [
      {
        id: 'budget-planning',
        title: 'เลือกแผนประกันอย่างไรให้เหมาะกับงบประมาณ?',
        excerpt: 'ไม่จำเป็นต้องซื้อแผนใหญ่ที่สุด เริ่มจากความต้องการจริงและงบประมาณที่จ่ายได้สบาย เปรียบเทียบแผนก่อนตัดสินใจ',
        image: 'assets/img/products/tax.jpg',
        readTime: '5 นาที',
        featured: true,
        link: 'contact.html',
        linkLabel: 'ปรึกษาฟรี',
      },
      {
        id: 'compare-plans',
        title: 'เปรียบเทียบประกันก่อนซื้อ ทำอย่างไร?',
        excerpt: 'ดูทุนประกัน ข้อยกเว้น เบี้ย และผลประโยชน์คู่กัน BOYINSURE ช่วยสรุปให้เข้าใจง่ายก่อนตัดสินใจ',
        image: 'assets/img/products/savings.jpg',
        readTime: '4 นาที',
        link: 'insurance.html',
        linkLabel: 'ดูแบบประกัน',
      },
    ],
  },
  {
    id: 'savings',
    title: 'ออมเงินและอนาคต',
    tagline: 'ออมวันนี้ สบายวันหน้า — วางแผนระยะยาวอย่างมั่นใจ',
    icon: 'piggy-bank',
    articles: [
      {
        id: 'save-today',
        title: 'ออมวันนี้ สบายวันหน้า',
        excerpt: 'ประกันสะสมทรัพย์และบำนาญช่วยสร้างเงินก้อนในอนาคต พร้อมรับความคุ้มครองชีวิตไปพร้อมกัน',
        image: 'assets/img/products/savings.jpg',
        readTime: '4 นาที',
        featured: true,
        link: 'insurance.html#savings',
        linkLabel: 'ดูแบบประกัน',
      },
      {
        id: 'pension-planning',
        title: 'วางแผนเกษียณด้วยประกันบำนาญ',
        excerpt: 'สร้างรายได้หลังเกษียณอย่างมั่นคง ไม่เป็นภาระลูกหลาน เริ่มวางแผนได้ตั้งแต่วันนี้',
        image: 'assets/img/products/pension.jpg',
        readTime: '5 นาที',
        link: 'insurance-plan.html?plan=pension',
        linkLabel: 'อ่านเพิ่มเติม',
      },
    ],
  },
  {
    id: 'tips',
    title: 'เคล็ดลับและ FAQ',
    tagline: 'คำตอบที่พบบ่อยและเคล็ดลับจากทีม BOYINSURE',
    icon: 'lightbulb',
    articles: [
      {
        id: 'agent-tips',
        title: 'พูดไม่เก่ง จะเป็นตัวแทนประกันได้ไหม?',
        excerpt: 'สิ่งสำคัญกว่าคือการเข้าใจข้อมูล และสื่อสารประโยชน์ที่ลูกค้าจะได้รับให้ชัดเจน BOYINSURE มีระบบและการซัพพอร์ตอย่างเป็นขั้นตอน',
        image: 'assets/img/products/group.jpg',
        readTime: '4 นาที',
        featured: true,
        link: 'contact.html',
        linkLabel: 'สอบถามรายละเอียด',
      },
      {
        id: 'claim-guide',
        title: 'เคลมประกันต้องเตรียมอะไรบ้าง?',
        excerpt: 'เตรียมเอกสารให้ครบ แจ้งเคลมตั้งแต่เนิ่น ๆ ทีม BOYINSURE ช่วยติดตามจนจบทุกเคส',
        image: 'assets/img/products/fire.jpg',
        readTime: '3 นาที',
        link: 'contact.html',
        linkLabel: 'ปรึกษาฟรี',
      },
    ],
  },
  {
    id: 'promo',
    title: 'โปรโมชั่น',
    tagline: 'กิจกรรม สิทธิพิเศษ และของรางวัลจาก BOYINSURE',
    icon: 'gift',
    articles: [
      {
        id: 'promo-rewards',
        title: 'โปรโมชั่นและของรางวัล BOYINSURE',
        excerpt: 'ลูกค้าที่ทำประกันกับ BOYINSURE รับสิทธิ์ลุ้นของรางวัลและวอเชอร์จากพาร์ทเนอร์ชั้นนำ ผ่านกิจกรรมวงล้อของรางวัล',
        image: 'assets/img/home-hero-2.png',
        readTime: '2 นาที',
        featured: true,
        link: 'promotions.html',
        linkLabel: 'ดูโปรโมชั่น',
      },
    ],
  },
];

let ACTIVE_ARTICLE_CATEGORIES = ARTICLE_CATEGORIES;

async function loadArticleCategoriesApi() {
  try {
    const res = await fetch(siteApiUrl('api/articles/categories.php'));
    if (!res.ok) return;
    const data = await res.json();
    if (Array.isArray(data.categories) && data.categories.length > 0) {
      ACTIVE_ARTICLE_CATEGORIES = data.categories;
    }
  } catch (_) {
    /* ใช้ข้อมูลใน site.js */
  }
}

function findArticle(articleId) {
  if (!articleId) return null;
  for (const category of ACTIVE_ARTICLE_CATEGORIES) {
    const article = category.articles.find((item) => item.id === articleId);
    if (article) return { category, article };
  }
  return null;
}

const ARTICLE_BODY = {
  'why-life-insurance': {
    date: '5 มิ.ย. 2026',
    takeaways: ['ประกันชีวิตช่วยลดภาระทางการเงินของครอบครัว', 'เริ่มวางแผนได้ตั้งแต่มีรายได้สม่ำเสมอ', 'ไม่จำเป็นต้องซื้อทุนสูงสุดตั้งแต่แรก'],
    sections: [
      { type: 'p', text: 'หลายคนรู้ว่าควรมีประกันชีวิต แต่ยังไม่แน่ใจว่าจำเป็นแค่ไหน หรือควรเริ่มเมื่อไหร่ บทความนี้สรุปให้เข้าใจง่ายว่าประกันชีวิตช่วยอะไร และควรคิดอย่างไรก่อนตัดสินใจ' },
      { type: 'h2', text: 'ประกันชีวิตช่วยอะไร?' },
      { type: 'p', text: 'เมื่อเกิดเหตุไม่คาดคิด ประกันชีวิตช่วยให้ครอบครัวมีเงินก้อนสำหรับใช้จ่ายสำคัญ เช่น ค่าครองชีพ หนี้สิน หรือค่าใช้จ่ายด้านการศึกษา โดยไม่ต้องใช้เงินเก็บทั้งหมดในคราวเดียว' },
      { type: 'ul', items: ['สร้างความมั่นคงให้คนที่คุณรัก', 'ช่วยปิดหนี้หรือภาระทางการเงินที่ค้างอยู่', 'เป็นส่วนหนึ่งของแผนการเงินครอบครัวระยะยาว'] },
      { type: 'h2', text: 'ควรเริ่มเมื่อไหร่?' },
      { type: 'p', text: 'ยิ่งเริ่มเร็ว โดยทั่วไปเบี้ยประกันมักต่ำกว่าเมื่ออายุน้อยและสุขภาพดี แต่ถ้ายังไม่พร้อม สิ่งสำคัญคือเริ่มจากงบที่จ่ายได้สบาย แล้วค่อยปรับทุนเมื่อรายได้หรือภาระเปลี่ยน' },
      { type: 'p', text: 'BOYINSURE ช่วยวิเคราะห์และเปรียบเทียบแผนจากหลายบริษัทให้ฟรี ไม่บังคับซื้อ — ปรึกษาเพื่อหาแผนที่เหมาะกับชีวิตจริงของคุณ' },
    ],
  },
  'life-coverage-amount': {
    date: '3 มิ.ย. 2026',
    takeaways: ['คำนวณจากภาระหนี้ + ค่าใช้จ่ายครอบครัว', 'ไม่มีตัวเลขเดียวที่ถูกสำหรับทุกคน', 'ทบทวนทุนประกันทุก 2–3 ปี'],
    sections: [
      { type: 'p', text: 'คำถาม "ทุนประกันกี่ล้านพอ?" ไม่มีคำตอบเดียว แต่มีวิธีคิดที่ช่วยให้ใกล้เคียงความต้องการจริงมากขึ้น' },
      { type: 'h2', text: 'วิธีประเมินทุนประกันเบื้องต้น' },
      { type: 'ul', items: ['รวมหนี้ที่ต้องชำระ (บ้าน รถ สินเชื่อ)', 'ประมาณค่าใช้จ่ายครอบครัวต่อปี × จำนวนปีที่ต้องการดูแล', 'บวกค่าใช้จ่ายพิเศษ เช่น การศึกษาลูก'] },
      { type: 'h2', text: 'อย่าลืมทบทวนเป็นระยะ' },
      { type: 'p', text: 'เมื่อแต่งงาน มีลูก หรือซื้อบ้าน ภาระทางการเงินเปลี่ยน ทุนประกันที่เคยพออาจไม่เพียงพอ ควรทบทวนทุก 2–3 ปี หรือเมื่อมีเหตุการณ์สำคัญในชีวิต' },
    ],
  },
  'health-savings': {
    date: '8 มิ.ย. 2026',
    takeaways: ['ค่ารักษาแพงขึ้นเรื่อย ๆ ประกันช่วยกันเงินเก็บหมด', 'เลือกวงเงินให้สมดุลกับงบ', 'ดูทั้ง IPD และ OPD ตามพฤติกรรมการใช้'],
    sections: [
      { type: 'p', text: 'การป่วยครั้งใหญ่อาจทำให้เงินเก็บหลายปีหายไปในคืนเดียว ประกันสุขภาพจึงเป็นเครื่องมือสำคัญในการปกป้องแผนการเงินครอบครัว' },
      { type: 'h2', text: 'ประกันสุขภาพช่วยอะไร?' },
      { type: 'p', text: 'ครอบคลุมค่ารักษาผู้ป่วยใน ผู้ป่วยนอก หรือทั้งสองอย่าง ขึ้นกับแผนที่เลือก ช่วยให้คุณได้รับการรักษาโดยไม่ต้องกังวลเรื่องเงินสดทันที' },
      { type: 'h2', text: 'เลือกแผนอย่างไร?' },
      { type: 'ul', items: ['ดูพฤติกรรมการใช้บริการสุขภาพของครอบครัว', 'เปรียบเทียบวงเงิน ข้อยกเว้น และเครือข่ายโรงพยาบาล', 'เลือกเบี้ยที่จ่ายได้สม่ำเสมอ ไม่ใช่แผนใหญ่ที่สุดเสมอไป'] },
    ],
  },
  'ipd-opd': {
    date: '1 มิ.ย. 2026',
    takeaways: ['IPD = ผู้ป่วยใน, OPD = ผู้ป่วยนอก', 'ไม่ใช่ทุกแผนคุ้มครองทั้งสอง', 'เลือกตามพฤติกรรมการรักษาจริง'],
    sections: [
      { type: 'p', text: 'เวลาเลือกประกันสุขภาพ มักเจอคำว่า IPD และ OPD บ่อยครั้ง ทั้งสองคือรูปแบบการรักษาที่แตกต่างกัน และส่งผลต่อความคุ้มครองในกรมธรรม์' },
      { type: 'h2', text: 'IPD คืออะไร?' },
      { type: 'p', text: 'In-Patient Department หรือการรักษาผู้ป่วยใน ต้องนอนโรงพยาบาลอย่างน้อย 1 คืน ครอบคลุมค่าห้อง ค่าผ่าตัด ยา และค่ารักษาที่เกี่ยวข้อง' },
      { type: 'h2', text: 'OPD คืออะไร?' },
      { type: 'p', text: 'Out-Patient Department หรือการรักษาผู้ป่วยนอก ไม่ต้องนอนโรงพยาบาล เช่น ไปพบแพทย์ ตรวจเลือด หรือรับยากลับบ้าน' },
      { type: 'p', text: 'ถ้าคุณหรือครอบครัวไปรพ. บ่อยแบบไม่นอน แผนที่มี OPD อาจสำคัญกว่า แต่ถ้ากังวลเรื่องการผ่าตัดหรืออุบัติเหตุ IPD วงเงินสูงก็เป็นสิ่งที่ควรมี' },
    ],
  },
  'critical-illness-guide': {
    date: '28 พ.ค. 2026',
    takeaways: ['จ่ายเงินก้อนเมื่อวินิจฉัย ไม่ต้องส่งใบเสร็จ', 'เสริมจากประกันสุขภาพได้', 'ช่วยช่วงพักฟื้นและรายได้หายไป'],
    sections: [
      { type: 'p', text: 'ประกันโรคร้ายแรงทำงานต่างจากประกันสุขภาพทั่วไป — ให้เงินก้อนเมื่อวินิจฉัยโรคที่กำหนด ใช้จ่ายได้อิสระ' },
      { type: 'h2', text: 'ทำไมถึงควรพิจารณา?' },
      { type: 'p', text: 'เมื่อป่วยหนัก นอกจากค่ารักษา อาจมีรายได้ที่หายไปชั่วคราว ค่าใช้จ่ายในการดูแลตัวเอง หรือการปรับตัวของครอบครัว เงินก้อนจากประกันโรคร้ายแรงช่วยเติมช่องว่างนี้' },
      { type: 'ul', items: ['ครอบคลุมโรคร้ายแรงหลัก เช่น มะเร็ง หัวใจ ไตวาย', 'ไม่ต้องเก็บใบเสร็จเพื่อเคลม', 'ใช้ร่วมกับประกันสุขภาพได้'] },
    ],
  },
  'budget-planning': {
    date: '10 มิ.ย. 2026',
    takeaways: ['เริ่มจากงบที่จ่ายได้สบาย', 'จัดลำดับความคุ้มครองที่จำเป็นก่อน', 'เปรียบเทียบหลายแผนก่อนตัดสินใจ'],
    sections: [
      { type: 'p', text: 'การเลือกประกันไม่ใช่การหาแผนที่ "ดีที่สุด" แต่คือแผนที่ "พอดีที่สุด" กับชีวิตและงบประมาณของคุณ' },
      { type: 'h2', text: '3 ขั้นตอนง่าย ๆ' },
      { type: 'ul', items: ['กำหนดงบประมาณต่อเดือน/ปีที่จ่ายได้โดยไม่เดือดร้อน', 'จัดลำดับความต้องการ เช่น สุขภาพ → ชีวิต → ออม', 'เปรียบเทียบอย่างน้อย 2–3 แผนจากหลายบริษัท'] },
      { type: 'h2', text: 'BOYINSURE ช่วยอะไรได้?' },
      { type: 'p', text: 'ทีมช่วยสรุปแผนให้เข้าใจง่าย เปรียบเทียบเบี้ยและความคุ้มครอง โดยไม่บังคับซื้อ — ปรึกษาฟรีได้ทุกเมื่อ' },
    ],
  },
  'compare-plans': {
    date: '6 มิ.ย. 2026',
    takeaways: ['อย่าดูแค่เบี้ย ต้องดูข้อยกเว้นด้วย', 'ทุนประกันและผลประโยชน์ต้องเทียบคู่กัน', 'ถามผู้เชี่ยวชาญถ้าไม่แน่ใจ'],
    sections: [
      { type: 'p', text: 'แผนที่เบี้ยถูกกว่า ไม่ได้หมายความว่าคุ้มกว่าเสมอไป การเปรียบเทียบที่ดีต้องดูหลายมิติพร้อมกัน' },
      { type: 'h2', text: 'สิ่งที่ควรเทียบ' },
      { type: 'ul', items: ['ทุนประกันและผลประโยชน์', 'ข้อยกเว้นและเงื่อนไขสำคัญ', 'ระยะเวลารับประกันและการต่ออายุ', 'เบี้ยประกันและช่องทางชำระ'] },
      { type: 'p', text: 'BOYINSURE ช่วยจัดตารางเปรียบเทียบให้เห็นภาพรวม ก่อนคุณตัดสินใจด้วยตัวเองอย่างมั่นใจ' },
    ],
  },
  'save-today': {
    date: '15 พ.ค. 2026',
    takeaways: ['ออมเร็ว = เป้าหมายใกล้ขึ้น', 'ประกันสะสมทรัพย์ให้ทั้งออมและคุ้มครอง', 'เลือกระยะเวลาให้เข้ากับเป้าหมายชีวิต'],
    sections: [
      { type: 'p', text: 'การออมไม่ใช่เรื่องของคนรวยเท่านั้น การเริ่มออมตั้งแต่เนิ่น ๆ ช่วยให้เงินเติบโตและลดความกังวลในอนาคต' },
      { type: 'h2', text: 'ประกันสะสมทรัพย์เหมาะกับใคร?' },
      { type: 'p', text: 'เหมาะกับผู้ที่ต้องการวินัยในการออม พร้อมรับความคุ้มครองชีวิตไปด้วย ได้เงินคืนตามระยะเวลาที่กำหนดเมื่อครบสัญญา' },
      { type: 'ul', items: ['วางแผนเป้าหมายระยะ 10–20 ปี', 'สร้างเงินก้อนสำหรับอนาคต', 'มีความคุ้มครองชีวิตตลอดสัญญา'] },
    ],
  },
  'pension-planning': {
    date: '20 พ.ค. 2026',
    takeaways: ['เริ่มวางแผนเกษียณได้ตั้งแต่วันนี้', 'ประกันบำนาญให้รายได้รายเดือน', 'ไม่เป็นภาระลูกหลาน'],
    sections: [
      { type: 'p', text: 'อายุเกษียณอาจดูไกล แต่ยิ่งวางแผนเร็ว ยิ่งมีตัวเลือกมากขึ้น และเบี้ยมักต่ำกว่าเมื่อเริ่มต้นตอนยังหนุ่ม' },
      { type: 'h2', text: 'ประกันบำนาญทำงานอย่างไร?' },
      { type: 'p', text: 'ระหว่างทำงาน คุณชำระเบี้ยตามแผน เมื่อถึงอายุที่กำหนด จะได้รับเงินบำนาญรายเดือนต่อเนื่อง ช่วยสร้างรายได้หลังเกษียณอย่างมั่นคง' },
      { type: 'p', text: 'ทีม BOYINSURE ช่วยคำนวณและเปรียบเทียบแผนบำนาญจากหลายบริษัท ให้เหมาะกับเป้าหมายรายได้หลังเกษียณของคุณ' },
    ],
  },
  'agent-tips': {
    date: '2 มิ.ย. 2026',
    takeaways: ['การเข้าใจข้อมูลสำคัญกว่าการพูดเก่ง', 'มีระบบและทีมซัพพอร์ตช่วยได้', 'เริ่มจากลูกค้าใกล้ตัวและเรียนรู้ทีละขั้น'],
    sections: [
      { type: 'p', text: 'หลายคนกลัวว่าพูดไม่เก่งจะขายประกันไม่ได้ แต่ในความเป็นจริง ลูกค้าต้องการคนที่อธิบายให้เข้าใจและดูแลต่อเนื่อง มากกว่าคนพูดเก่งแต่ไม่รู้รายละเอียด' },
      { type: 'h2', text: 'ทักษะที่สำคัญกว่า' },
      { type: 'ul', items: ['ฟังความต้องการลูกค้าให้ชัด', 'อธิบายประโยชน์และข้อจำกัดอย่างตรงไปตรงมา', 'ติดตามและดูแลหลังทำสัญญา'] },
      { type: 'p', text: 'BOYINSURE มีระบบ การอบรม และทีมซัพพอร์ต ช่วยให้คุณเริ่มต้นได้อย่างมั่นใจ แม้ไม่เคยมีประสบการณ์มาก่อน' },
    ],
  },
  'claim-guide': {
    date: '18 พ.ค. 2026',
    takeaways: ['แจ้งเคลมเร็ว = ดำเนินการเร็ว', 'เตรียมเอกสารให้ครบตั้งแต่แรก', 'BOYINSURE ช่วยติดตามจนจบเคส'],
    sections: [
      { type: 'p', text: 'การเคลมประกันไม่ยากอย่างที่คิด ถ้ารู้ขั้นตอนและเตรียมเอกสารให้พร้อม บทความนี้สรุปสิ่งที่ควรทราบ' },
      { type: 'h2', text: 'ขั้นตอนเบื้องต้น' },
      { type: 'ul', items: ['แจ้งเหตุหรือการเคลมโดยเร็ว', 'เตรียมเอกสารตามที่กรมธรรม์กำหนด', 'ติดตามสถานะและตอบกลับเมื่อบริษัทขอข้อมูลเพิ่ม'] },
      { type: 'h2', text: 'เอกสารที่มักใช้' },
      { type: 'p', text: 'ขึ้นกับประเภทการเคลม เช่น ใบรับรองแพทย์ ใบเสร็จค่ารักษา สำเนาบัตรประชาชน หรือใบมรณบัตร (กรณีเสียชีวิต) ทีม BOYINSURE ช่วยตรวจสอบรายการก่อนส่ง' },
    ],
  },
  'promo-rewards': {
    date: '12 มิ.ย. 2026',
    takeaways: ['ลูกค้า BOYINSURE มีสิทธิ์ร่วมกิจกรรม', 'ของรางวัลจากพาร์ทเนอร์ชั้นนำ', 'ตรวจสอบเงื่อนไขก่อนร่วมสนุก'],
    sections: [
      { type: 'p', text: 'BOYINSURE จัดกิจกรรมและโปรโมชั่นให้ลูกค้าที่ทำประกันกับเรา ร่วมลุ้นของรางวัลและวอเชอร์จากพาร์ทเนอร์ที่หลากหลาย' },
      { type: 'h2', text: 'วงล้อของรางวัล' },
      { type: 'p', text: 'กิจกรรมวงล้อของรางวัลจัดขึ้นเป็นช่วง ๆ ลูกค้าที่มีสิทธิ์สามารถหมุนวงล้อเพื่อลุ้นรางวัลได้ รายละเอียดและเงื่อนไขดูได้ที่หน้าโปรโมชั่น' },
      { type: 'p', text: 'ติดตามข่าวสารและโปรโมชั่นล่าสุดได้ที่หน้าโปรโมชั่นและของรางวัลของ BOYINSURE' },
    ],
    extraLink: { href: 'promotions.html', label: 'ไปหน้าโปรโมชั่นและของรางวัล' },
  },
};

function renderArticleBody(sections) {
  return sections.map((block) => {
    if (block.type === 'h2') return `<h2>${block.text}</h2>`;
    if (block.type === 'ul') {
      return `<ul>${block.items.map((item) => `<li>${item}</li>`).join('')}</ul>`;
    }
    return `<p>${block.text}</p>`;
  }).join('\n');
}

function getAllArticles() {
  const items = [];
  for (const category of ACTIVE_ARTICLE_CATEGORIES) {
    for (const article of category.articles) {
      items.push({ category, article });
    }
  }
  return items;
}

function getRecommendedArticles(currentId, limit = 5) {
  const all = getAllArticles().filter((item) => item.article.id !== currentId);
  const featured = all.filter((item) => item.article.featured);
  const rest = all.filter((item) => !item.article.featured);
  return [...featured, ...rest].slice(0, limit);
}

function renderArticleSidebarList(items) {
  if (!items.length) return '';
  return `
    <ul class="article-read__list">
      ${items.map(({ category, article }) => `
        <li>
          <a href="article.html?article=${article.id}">
            <img src="${article.image}" alt="" width="80" height="56" loading="lazy" />
            <span>
              <strong>${article.title}</strong>
              <em>${category.title} · ${article.readTime}</em>
            </span>
          </a>
        </li>
      `).join('')}
    </ul>
  `;
}

function initArticleSidebarSearch(currentId) {
  const input = document.getElementById('articleSearch');
  const results = document.getElementById('articleSearchResults');
  if (!input || !results) return;

  const index = getAllArticles().map(({ category, article }) => ({
    category,
    article,
    haystack: `${article.title} ${article.excerpt} ${category.title}`.toLowerCase(),
  }));

  function renderResults(query) {
    const q = query.trim().toLowerCase();
    if (!q) {
      results.hidden = true;
      results.innerHTML = '';
      return;
    }

    const matched = index
      .filter((item) => item.article.id !== currentId && item.haystack.includes(q))
      .slice(0, 6);

    if (!matched.length) {
      results.hidden = false;
      results.innerHTML = '<p class="article-read__search-empty">ไม่พบบทความที่ตรงกับคำค้น</p>';
      return;
    }

    results.hidden = false;
    results.innerHTML = `
      <ul class="article-read__search-list">
        ${matched.map(({ category, article }) => `
          <li>
            <a href="article.html?article=${article.id}">
              <strong>${article.title}</strong>
              <span>${category.title}</span>
            </a>
          </li>
        `).join('')}
      </ul>
    `;
  }

  input.addEventListener('input', () => renderResults(input.value));
  input.addEventListener('focus', () => {
    if (input.value.trim()) renderResults(input.value);
  });
  document.addEventListener('click', (e) => {
    if (!e.target.closest('.article-read__search')) {
      results.hidden = true;
    }
  });
}

function initArticleDetail() {
  const root = document.getElementById('articleRead');
  if (!root) return;

  const articleId = new URLSearchParams(window.location.search).get('article');

  (async () => {
    let match = findArticle(articleId);
    let body = articleId ? ARTICLE_BODY[articleId] : null;

    try {
      const res = await fetch(`api/articles/detail.php?slug=${encodeURIComponent(articleId || '')}`);
      if (res.ok) {
        const data = await res.json();
        if (data.article) {
          const cat = data.article.category || { id: 'general', title: 'บทความ', icon: 'file-text' };
          match = {
            category: { id: cat.id, title: cat.title, icon: cat.icon || 'file-text', articles: [] },
            article: {
              id: data.article.id,
              title: data.article.title,
              excerpt: data.article.excerpt || '',
              image: data.article.image || 'assets/img/products/life.jpg',
              readTime: data.article.readTime || '3 นาที',
              featured: data.article.featured,
            },
          };
          if (data.body) body = data.body;
        }
      }
    } catch (_) {}

    if (!match) {
      root.innerHTML = `
      <div class="container">
        <div class="article-read__empty">
          <h1>ไม่พบบทความ</h1>
          <p>บทความที่คุณเลือกอาจถูกลบหรือลิงก์ไม่ถูกต้อง</p>
          <a href="articles.html" class="btn btn--primary">กลับหน้าบทความ</a>
        </div>
      </div>
    `;
      return;
    }

  const { category, article } = match;
  body = body || {
    date: '2026',
    takeaways: [],
    sections: [{ type: 'p', text: article.excerpt }],
  };
  const related = category.articles
    .filter((item) => item.id !== article.id)
    .map((item) => ({ category, article: item }));
  const recommended = getRecommendedArticles(article.id, 5);
  const recommendedIds = new Set(recommended.map((item) => item.article.id));
  const sameCategory = related.filter((item) => !recommendedIds.has(item.article.id));

  document.title = `BOYINSURE | ${article.title}`;

  let metaDesc = document.querySelector('meta[name="description"]');
  if (!metaDesc) {
    metaDesc = document.createElement('meta');
    metaDesc.name = 'description';
    document.head.appendChild(metaDesc);
  }
  metaDesc.content = article.excerpt.slice(0, 155);

  root.innerHTML = `
    <header class="article-read__header">
      <div class="container article-read__header-inner">
        <nav class="article-read__breadcrumb" aria-label="เส้นทาง">
          <a href="articles.html">บทความ</a>
          <span aria-hidden="true">/</span>
          <a href="articles.html#${category.id}">${category.title}</a>
          <span aria-hidden="true">/</span>
          <span aria-current="page">${article.title}</span>
        </nav>
        <a href="articles.html#${category.id}" class="article-read__category">
          <i data-lucide="${category.icon}" aria-hidden="true"></i>
          ${category.title}
        </a>
        <h1 class="article-read__title">${article.title}</h1>
        <p class="article-read__lead">${article.excerpt}</p>
        <div class="article-read__meta">
          <span><i data-lucide="user" aria-hidden="true"></i> ทีม BOYINSURE</span>
          <span><i data-lucide="calendar" aria-hidden="true"></i> ${body.date}</span>
          <span><i data-lucide="clock" aria-hidden="true"></i> ${article.readTime}</span>
        </div>
      </div>
    </header>

    <figure class="article-read__figure">
      <div class="container">
        <img src="${article.image}" alt="${article.title}" width="960" height="540" loading="eager" />
      </div>
    </figure>

    <div class="container">
      <div class="article-read__layout">
        <div class="article-read__content">
          ${body.takeaways?.length ? `
            <aside class="article-read__takeaways">
              <h2>สรุปสั้น ๆ</h2>
              <ul>
                ${body.takeaways.map((item) => `<li>${item}</li>`).join('')}
              </ul>
            </aside>
          ` : ''}
          <div class="article-read__prose">
            ${renderArticleBody(body.sections)}
          </div>
          ${body.extraLink ? `
            <p class="article-read__extra">
              <a href="${body.extraLink.href}" class="article-read__extra-link">
                ${body.extraLink.label}
                <i data-lucide="arrow-right" aria-hidden="true"></i>
              </a>
            </p>
          ` : ''}
          <footer class="article-read__end">
            <p>มีคำถามเรื่องประกัน? ปรึกษาทีม BOYINSURE ฟรี ไม่บังคับซื้อ</p>
            <a href="contact.html" class="btn btn--gold">สอบถามเรื่องนี้</a>
          </footer>
        </div>

        <aside class="article-read__aside" aria-label="ค้นหาและบทความแนะนำ">
          <div class="article-read__widget article-read__panel">
            <div class="article-read__search">
              <label class="article-read__search-label" for="articleSearch">ค้นหาบทความ</label>
              <div class="article-read__search-field">
                <i data-lucide="search" aria-hidden="true"></i>
                <input
                  type="search"
                  id="articleSearch"
                  placeholder="พิมพ์หัวข้อหรือคำค้น..."
                  autocomplete="off"
                  aria-controls="articleSearchResults"
                />
              </div>
              <div class="article-read__search-results" id="articleSearchResults" hidden></div>
            </div>

            <div class="article-read__recommended">
              <h2>บทความแนะนำ</h2>
              ${renderArticleSidebarList(recommended)}
            </div>
          </div>

          ${sameCategory.length ? `
            <div class="article-read__widget article-read__related">
              <h2>บทความในหมวดเดียวกัน</h2>
              ${renderArticleSidebarList(sameCategory)}
            </div>
          ` : ''}

          <div class="article-read__aside-cta">
            <i data-lucide="message-circle" aria-hidden="true"></i>
            <strong>อยากให้ช่วยเลือกแผน?</strong>
            <p>ทีม BOYINSURE วิเคราะห์และเปรียบเทียบให้ฟรี</p>
            <a href="contact.html" class="btn btn--primary">ปรึกษาฟรี</a>
          </div>
        </aside>
      </div>

      <div class="article-read__footer">
        <a href="articles.html#${category.id}" class="article-read__back">
          <i data-lucide="arrow-left" aria-hidden="true"></i>
          กลับหน้าบทความ
        </a>
      </div>
    </div>
  `;

  if (window.lucide?.createIcons) lucide.createIcons();
  initArticleSidebarSearch(article.id);
  })();
}

function renderArticleCatalogCard(category, article) {
  const meta = getArticleListMeta(article, category);
  const categoryLabel = article.categoryLabel || category.title;
  return `
    <a href="article.html?article=${article.id}" class="article-catalog-card${article.featured ? ' article-catalog-card--featured' : ''}">
      <div class="article-catalog-card__media">
        <img src="${article.image}" alt="${article.title}" width="640" height="400" loading="lazy" />
        ${article.featured ? '<span class="article-catalog-card__badge">แนะนำ</span>' : ''}
      </div>
      <div class="article-catalog-card__body">
        <span class="article-catalog-card__category">${categoryLabel}</span>
        <h3 class="article-catalog-card__title">${article.title}</h3>
        ${meta ? `<p class="article-catalog-card__meta">${meta}</p>` : ''}
        <p class="article-catalog-card__excerpt">${article.excerpt || ''}</p>
      </div>
    </a>
  `;
}

const HOME_INSURANCE_PRODUCT_IMAGES = new Set([
  'assets/img/products/health.jpg',
  'assets/img/products/life.jpg',
  'assets/img/products/savings.jpg',
  'assets/img/products/critical.jpg',
]);

const HOME_ARTICLE_IMAGE_OVERRIDES = {
  'why-life-insurance': 'assets/img/products/pension.jpg',
  'health-savings': 'assets/img/products/elderly.jpg',
  'ipd-opd': 'assets/img/products/tax.jpg',
  'critical-illness-guide': 'assets/img/products/group.jpg',
};

function getHomeArticleImage(article) {
  if (HOME_ARTICLE_IMAGE_OVERRIDES[article.id]) {
    return HOME_ARTICLE_IMAGE_OVERRIDES[article.id];
  }
  if (HOME_INSURANCE_PRODUCT_IMAGES.has(article.image)) {
    return 'assets/img/products/accident.jpg';
  }
  return article.image;
}

function getHomeLatestArticles(limit = 4) {
  const usedImages = new Set();
  const picked = [];

  for (const entry of getAllArticles()) {
    if (picked.length >= limit) break;
    const image = getHomeArticleImage(entry.article);
    if (usedImages.has(image)) continue;
    usedImages.add(image);
    picked.push({
      category: entry.category,
      article: { ...entry.article, image },
    });
  }

  if (picked.length < limit) {
    for (const entry of getAllArticles()) {
      if (picked.length >= limit) break;
      if (picked.some((item) => item.article.id === entry.article.id)) continue;
      picked.push({
        category: entry.category,
        article: { ...entry.article, image: getHomeArticleImage(entry.article) },
      });
    }
  }

  return picked.slice(0, limit);
}

function initHomeLatestArticles() {
  const grid = document.getElementById('homeArticleGrid');
  if (!grid) return;
  const items = getHomeLatestArticles(4);
  grid.innerHTML = items.map(({ category, article }) => renderArticleCatalogCard(category, article)).join('');
}

function getArticleListMeta(article, category) {
  const date = article.date || ARTICLE_BODY[article.id]?.date || '';
  let readTime = article.readTime || '';
  if (readTime && !readTime.includes('อ่าน')) {
    readTime = `อ่าน ${readTime}`;
  }
  return [date, readTime].filter(Boolean).join(' · ');
}

function initArticlesCatalog() {
  const nav = document.getElementById('articleCategoryNav');
  const head = document.getElementById('articleCategoryHead');
  const grid = document.getElementById('articleGrid');
  if (!nav || !head || !grid) return;

  let activeId = ACTIVE_ARTICLE_CATEGORIES[0]?.id;
  const hashCategory = window.location.hash.replace('#', '');
  if (ACTIVE_ARTICLE_CATEGORIES.some((cat) => cat.id === hashCategory)) {
    activeId = hashCategory;
  }

  nav.innerHTML = ACTIVE_ARTICLE_CATEGORIES.map((cat) => `
    <button
      type="button"
      class="insurance-sidebar__item${cat.id === activeId ? ' is-active' : ''}"
      data-category="${cat.id}"
      aria-current="${cat.id === activeId ? 'true' : 'false'}"
    >
      <i data-lucide="${cat.icon}" class="insurance-sidebar__icon" aria-hidden="true"></i>
      <span>${cat.title}</span>
    </button>
  `).join('');

  const sidebarDots = initSidebarNavDots(nav);

  function renderArticles(categoryId) {
    const cat = ACTIVE_ARTICLE_CATEGORIES.find((c) => c.id === categoryId);
    if (!cat) return;

    head.innerHTML = `
      <span class="insurance-catalog__badge">
        <i data-lucide="${cat.icon}" aria-hidden="true"></i>
        ${cat.articles.length} บทความในหมวดนี้
      </span>
      <h2>${cat.title}</h2>
      <p>${cat.tagline}</p>
    `;

    grid.innerHTML = cat.articles.map((article) => renderArticleCatalogCard(cat, article)).join('');

    if (window.lucide?.createIcons) lucide.createIcons();
  }

  function selectCategory(categoryId) {
    if (categoryId === activeId) return;
    activeId = categoryId;

    nav.querySelectorAll('.insurance-sidebar__item').forEach((btn) => {
      const isActive = btn.dataset.category === categoryId;
      btn.classList.toggle('is-active', isActive);
      btn.setAttribute('aria-current', isActive ? 'true' : 'false');
    });

    sidebarDots?.updateActiveDot();
    nav.querySelector('.insurance-sidebar__item.is-active')
      ?.scrollIntoView({ inline: 'center', block: 'nearest', behavior: 'smooth' });

    grid.classList.add('is-fading');
    setTimeout(() => {
      renderArticles(categoryId);
      grid.classList.remove('is-fading');
    }, 180);
  }

  nav.addEventListener('click', (e) => {
    const btn = e.target.closest('.insurance-sidebar__item');
    if (!btn) return;
    selectCategory(btn.dataset.category);
  });

  renderArticles(activeId);
  if (window.lucide?.createIcons) lucide.createIcons();
  requestAnimationFrame(() => sidebarDots?.refresh());
}

function initInsurancePlanDetail() {
  const root = document.getElementById('planDetail');
  if (!root) return;

  document.querySelector('.plan-detail-sticky')?.remove();
  document.body.classList.remove('has-plan-sticky');

  const planId = new URLSearchParams(window.location.search).get('plan');
  const settings = sitePublicSettings();
  const telHref = `tel:${settings.phone || '0627878968'}`;
  const telLabel = `โทร ${settings.phone_display || '062-787-8968'}`;

  (async () => {
    let match = findInsurancePlan(planId);
    let detail = null;

    try {
      const res = await fetch(`api/insurance/detail.php?slug=${encodeURIComponent(planId || '')}`);
      if (res.ok) {
        const data = await res.json();
        if (data.plan && data.category) {
          const catFromActive = ACTIVE_INSURANCE_CATEGORIES.find((c) => c.id === data.category.id);
          match = {
            category: {
              ...data.category,
              plans: catFromActive?.plans || [],
            },
            plan: {
              id: data.plan.id,
              name: data.plan.name,
              desc: data.plan.desc || data.plan.description,
              image: data.plan.image,
              featured: data.plan.featured,
              features: data.plan.features || [],
            },
          };
          if (data.detail) detail = data.detail;
        }
      }
    } catch (_) {}

    if (!match) {
      root.innerHTML = `
      <div class="plan-detail plan-detail--empty">
        <h1>ไม่พบแผนประกัน</h1>
        <p>แผนที่คุณเลือกอาจถูกลบหรือลิงก์ไม่ถูกต้อง</p>
        <a href="insurance.html" class="btn btn--primary">กลับหน้าแบบประกัน</a>
      </div>
    `;
      return;
    }

    const { category, plan } = match;
    detail = detail || getPlanDetailContent(plan, category);
  const summary = plan.summary || `${plan.desc} ทีม BOYINSURE ช่วยวิเคราะห์และเปรียบเทียบแผนจากบริษัทชั้นนำให้เหมาะกับงบประมาณและเป้าหมายของคุณ โดยไม่บังคับซื้อ`;
  const idealFor = plan.idealFor || [
    'ผู้ที่ต้องการวางแผนความคุ้มครองอย่างเป็นระบบ',
    'ครอบครัวที่ต้องการคำปรึกษาแบบไม่บังคับซื้อ',
    'ผู้ที่ต้องการเปรียบเทียบแผนจากหลายบริษัทก่อนตัดสินใจ',
  ];
  const contactUrl = `contact.html?plan=${encodeURIComponent(plan.id)}`;
  const relatedPlans = category.plans.filter((item) => item.id !== plan.id).slice(0, 2);

  document.title = `BOYINSURE | ${plan.name}`;

  let metaDesc = document.querySelector('meta[name="description"]');
  if (!metaDesc) {
    metaDesc = document.createElement('meta');
    metaDesc.name = 'description';
    document.head.appendChild(metaDesc);
  }
  metaDesc.content = summary.slice(0, 155);

  document.body.classList.add('has-plan-sticky');

  root.innerHTML = `
    <nav class="plan-detail__breadcrumb" aria-label="เส้นทาง">
      <a href="insurance.html">แบบประกันของเรา</a>
      <span aria-hidden="true">/</span>
      <a href="insurance.html#${category.id}">${category.title}</a>
      <span aria-hidden="true">/</span>
      <span aria-current="page">${plan.name}</span>
    </nav>

    <div class="plan-detail__hero">
      <div class="plan-detail__media">
        <img src="${plan.image}" alt="${plan.name}" width="960" height="600" loading="lazy" />
      </div>
      <div class="plan-detail__intro">
        <span class="plan-detail__category">
          <i data-lucide="${category.icon}" aria-hidden="true"></i>
          ${category.title}
        </span>
        <h1>${plan.name}</h1>
        <p class="plan-detail__summary">${summary}</p>
        <ul class="plan-detail__tags">
          ${plan.features.map((item) => `<li>${item}</li>`).join('')}
        </ul>
        <div class="plan-detail__actions">
          <a href="${contactUrl}" class="btn btn--gold">สอบถามแผนนี้</a>
          <a href="${telHref}" class="btn btn--outline btn--outline-dark">${telLabel}</a>
        </div>
      </div>
    </div>

    <div class="plan-detail__facts" aria-label="ข้อมูลสรุป">
      <div class="plan-detail__fact">
        <i data-lucide="user" aria-hidden="true"></i>
        <span class="plan-detail__fact-label">อายุรับประกัน</span>
        <strong>${detail.facts.age}</strong>
      </div>
      <div class="plan-detail__fact">
        <i data-lucide="calendar" aria-hidden="true"></i>
        <span class="plan-detail__fact-label">ระยะเวลา</span>
        <strong>${detail.facts.term}</strong>
      </div>
      <div class="plan-detail__fact">
        <i data-lucide="wallet" aria-hidden="true"></i>
        <span class="plan-detail__fact-label">เบี้ยโดยประมาณ</span>
        <strong>${detail.facts.premium}</strong>
      </div>
      <div class="plan-detail__fact">
        <i data-lucide="clock" aria-hidden="true"></i>
        <span class="plan-detail__fact-label">พิจารณาใบคำขอ</span>
        <strong>${detail.facts.processDays}</strong>
      </div>
    </div>
    <p class="plan-detail__facts-note">* ข้อมูลเป็นการแนะนำทั่วไป เงื่อนไขและเบี้ยจริงขึ้นกับบริษัทประกันและกรมธรรม์ที่เลือก</p>

    <div class="plan-detail__panels">
      <section class="plan-detail__panel">
        <h2><i data-lucide="shield-check" aria-hidden="true"></i> ความคุ้มครองหลัก</h2>
        <ul>
          ${detail.coverageDetails.map((item) => `<li>${item}</li>`).join('')}
        </ul>
      </section>
      <section class="plan-detail__panel">
        <h2><i data-lucide="users" aria-hidden="true"></i> เหมาะกับใคร</h2>
        <ul>
          ${idealFor.map((item) => `<li>${item}</li>`).join('')}
        </ul>
      </section>
    </div>

    <section class="plan-detail__usecase">
      <div class="plan-detail__usecase-icon" aria-hidden="true"><i data-lucide="lightbulb"></i></div>
      <div>
        <h2>ตัวอย่างสถานการณ์</h2>
        <p>${detail.useCase}</p>
      </div>
    </section>

    <section class="plan-detail__panel plan-detail__panel--wide">
      <h2><i data-lucide="circle-x" aria-hidden="true"></i> ข้อยกเว้นที่ควรทราบ</h2>
      <ul>
        ${detail.exclusions.map((item) => `<li>${item}</li>`).join('')}
      </ul>
    </section>

    <section class="plan-detail__steps">
      <h2 class="plan-detail__section-title">ขั้นตอนทำงานกับ BOYINSURE</h2>
      <p class="plan-detail__section-lead">ปรึกษาฟรี เปรียบเทียบหลายบริษัท ไม่บังคับซื้อ</p>
      <ol class="plan-detail__steps-flow">
        ${ACTIVE_PLAN_PROCESS_STEPS.map((step, i) => `
          <li class="plan-detail__step">
            <span class="plan-detail__step-num">${i + 1}</span>
            <span class="plan-detail__step-icon"><i data-lucide="${step.icon}" aria-hidden="true"></i></span>
            <strong>${step.title}</strong>
            <p>${step.desc}</p>
          </li>
        `).join('')}
      </ol>
    </section>

    <section class="plan-detail__faq">
      <h2 class="plan-detail__section-title">คำถามที่พบบ่อย</h2>
      <div class="plan-detail__faq-list">
        ${detail.faq.map((item) => `
          <details class="plan-detail__faq-item">
            <summary>${item.q}</summary>
            <p>${item.a}</p>
          </details>
        `).join('')}
      </div>
    </section>

    ${relatedPlans.length ? `
      <section class="plan-detail__related">
        <h2 class="plan-detail__section-title">แผนที่เกี่ยวข้อง</h2>
        <div class="plan-detail__related-grid">
          ${relatedPlans.map((item) => `
            <a href="insurance-plan.html?plan=${item.id}" class="plan-detail__related-card">
              <img src="${item.image}" alt="" width="320" height="200" loading="lazy" />
              <div>
                <h3>${item.name}</h3>
                <p>${item.desc}</p>
                <span>ดูรายละเอียด <i data-lucide="arrow-right" aria-hidden="true"></i></span>
              </div>
            </a>
          `).join('')}
        </div>
      </section>
    ` : ''}

    <section class="plan-detail__cta">
      <div class="plan-detail__cta-inner">
        <h2>สนใจ${plan.name}?</h2>
        <p>ปรึกษาทีม BOYINSURE ฟรี ไม่มีค่าใช้จ่าย ไม่บังคับซื้อ — เราช่วยเปรียบเทียบแผนที่เหมาะกับคุณ</p>
        <div class="plan-detail__actions">
          <a href="${contactUrl}" class="btn btn--gold">สอบถามแผนนี้</a>
          <a href="${telHref}" class="btn btn--outline btn--outline-dark">${telLabel}</a>
        </div>
      </div>
    </section>

    <p class="plan-detail__disclaimer">ข้อมูลบนหน้านี้เป็นการให้ความรู้และคำแนะนำทั่วไป ไม่ถือเป็นข้อเสนอซื้อขายประกันภัย เงื่อนไขความคุ้มครอง เบี้ยประกัน และผลประโยชน์ขึ้นอยู่กับกรมธรรม์ของแต่ละบริษัทประกันภัย</p>

    <div class="plan-detail__footer">
      <a href="insurance.html#${category.id}" class="plan-detail__back">
        <i data-lucide="arrow-left" aria-hidden="true"></i>
        กลับดูแผนอื่น
      </a>
    </div>
  `;

  const sticky = document.createElement('div');
  sticky.className = 'plan-detail-sticky';
  sticky.innerHTML = `
    <a href="${contactUrl}" class="btn btn--gold">สอบถามแผนนี้</a>
    <a href="${telHref}" class="btn btn--outline btn--outline-dark">โทร</a>
  `;
  document.body.appendChild(sticky);

  if (window.lucide?.createIcons) lucide.createIcons();
  })();
}

function initContactFormPrefill() {
  const form = document.querySelector('.contact-form');
  if (!form) return;

  const planId = new URLSearchParams(window.location.search).get('plan');
  if (!planId) return;

  const match = findInsurancePlan(planId);
  if (!match) return;

  const { plan, category } = match;
  const interest = form.querySelector('#interest');
  const message = form.querySelector('#message');

  const interestMap = {
    savings: 'ประกันออมเงิน / บำนาญ',
    health: 'ประกันชีวิต / สุขภาพ',
    risk: 'ประกันชีวิต / สุขภาพ',
    travel: 'สอบถามแบบประกัน / วางแผนความคุ้มครอง',
    property: 'สอบถามแบบประกัน / วางแผนความคุ้มครอง',
  };

  const targetInterest = interestMap[category.id] || 'สอบถามแบบประกัน / วางแผนความคุ้มครอง';
  if (interest) {
    for (const option of interest.options) {
      if (option.textContent.trim() === targetInterest) {
        interest.value = option.value || option.textContent.trim();
        break;
      }
    }
  }

  if (message && !message.value.trim()) {
    message.value = `สนใจแผน: ${plan.name}\n\n`;
    message.focus();
  }
}

function initHomePlanFormSubmit() {
  const form = document.getElementById('homePlanForm');
  if (!form || !window.BoyInsureAPI) return;

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = form.querySelector('[type="submit"]');
    const firstname = form.querySelector('#plan-firstname')?.value?.trim() || '';
    const lastname = form.querySelector('#plan-lastname')?.value?.trim() || '';
    const birthdate = form.querySelector('#plan-birthdate')?.value?.trim() || '';
    const phone = form.querySelector('#plan-phone')?.value?.trim() || '';
    const email = form.querySelector('#plan-email')?.value?.trim() || '';
    const province = form.querySelector('#plan-province')?.value?.trim() || '';
    const callbackDate = form.querySelector('#plan-callback-date')?.value?.trim() || '';
    const callbackTime = form.querySelector('#plan-callback-time')?.value?.trim() || '';
    const interest = form.querySelector('[name="interest"]')?.value?.trim() || 'ประกันรถยนต์ชั้น 1';

    const messageParts = [
      birthdate && `วันเกิด: ${birthdate}`,
      email && `อีเมล: ${email}`,
      province && `จังหวัด: ${province}`,
      callbackDate && `วันที่สะดวกให้ติดต่อกลับ: ${callbackDate}`,
      callbackTime && `ช่วงเวลาที่สะดวก: ${callbackTime}`,
    ].filter(Boolean);

    btn.disabled = true;
    try {
      await BoyInsureAPI.submitContact({
        name: `${firstname} ${lastname}`.trim(),
        phone,
        interest,
        plan: interest,
        message: messageParts.join('\n'),
      });
      alert('ขอบคุณครับ ทีมงานจะติดต่อกลับเรื่องประกันรถยนต์ชั้น 1 โดยเร็วที่สุด');
      form.reset();
    } catch (err) {
      alert(err.message || 'ส่งไม่สำเร็จ กรุณาลองใหม่');
    } finally {
      btn.disabled = false;
    }
  });
}

function initContactFormSubmit() {
  const form = document.getElementById('contactForm');
  if (!form || !window.BoyInsureAPI) return;

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = form.querySelector('[type="submit"]');
    const plan = new URLSearchParams(window.location.search).get('plan') || '';
    btn.disabled = true;
    try {
      await BoyInsureAPI.submitContact({
        name: form.querySelector('#name')?.value?.trim(),
        phone: form.querySelector('#phone')?.value?.trim(),
        interest: form.querySelector('#interest')?.value?.trim(),
        message: form.querySelector('#message')?.value?.trim(),
        plan,
      });
      alert('ขอบคุณครับ ทีมงานจะติดต่อกลับโดยเร็วที่สุด');
      form.reset();
      initContactFormPrefill();
    } catch (err) {
      alert(err.message || 'ส่งไม่สำเร็จ กรุณาลองใหม่');
    } finally {
      btn.disabled = false;
    }
  });
}

(function () {
  const page = document.body.dataset.page;
  if (page) {
    document.querySelectorAll(`[data-nav="${page}"]`).forEach(link => {
      link.classList.add('active');
    });
  }

  const navToggle = document.getElementById('navToggle');
  const menu = document.getElementById('navbarMenu') || document.querySelector('.navbar__menu');

  navToggle?.addEventListener('click', (e) => {
    e.stopPropagation();
    const isOpen = menu?.classList.toggle('open');
    navToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    document.body.classList.toggle('nav-open', !!isOpen);
  });

  menu?.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', () => {
      menu?.classList.remove('open');
      navToggle?.setAttribute('aria-expanded', 'false');
      document.body.classList.remove('nav-open');
    });
  });

  document.addEventListener('click', (e) => {
    if (!menu?.classList.contains('open')) return;
    if (e.target.closest('.navbar')) return;
    menu.classList.remove('open');
    navToggle?.setAttribute('aria-expanded', 'false');
    document.body.classList.remove('nav-open');
  });

  if (window.lucide?.createIcons) {
    lucide.createIcons();
  }

  initContactFab();

  (async () => {
    await loadSitePublicApi();
    loadFooter();
    initContactFab();
    initHomeHeroSlideshow();
    initPromoSpotlight();
    initHighlights();
    initReviewSlider();
    initHomeNewsModal();
    initHomeNewsCountdown();
    initHomeNewsPrizes();
    await loadInsuranceApi();
    initInsuranceCatalog();
    await loadArticleCategoriesApi();
    initHomeLatestArticles();
    initArticlesCatalog();
    initArticleDetail();
    initInsurancePlanDetail();
    initContactFormPrefill();
    initContactFormSubmit();
    initHomePlanFormSubmit();
  })();
})();

function initPromoSpotlight() {
  const section = document.getElementById('promoSpotlight');
  if (!section) return;

  const viewport = document.getElementById('promoSpotlightViewport');
  const track = document.getElementById('promoSpotlightTrack');
  const dotsWrap = document.getElementById('promoSpotlightDots');
  const cards = track ? [...track.querySelectorAll('.promo-spotlight__card')] : [];
  const prevBtn = section.querySelector('.promo-spotlight__arrow[data-dir="prev"]');
  const nextBtn = section.querySelector('.promo-spotlight__arrow[data-dir="next"]');
  if (!viewport || !track || cards.length === 0) return;

  const prefersReduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const AUTO_MS = 5000;
  let index = 0;
  let autoTimer = null;

  const dots = cards.map((_, i) => {
    const dot = document.createElement('button');
    dot.type = 'button';
    dot.className = 'promo-spotlight__dot';
    dot.setAttribute('role', 'tab');
    dot.setAttribute('aria-label', `การ์ดที่ ${i + 1}`);
    dot.addEventListener('click', () => { manualGo(i); });
    dotsWrap?.appendChild(dot);
    return dot;
  });

  function update() {
    dots.forEach((d, i) => d.classList.toggle('is-active', i === index));
    if (prevBtn) prevBtn.disabled = index === 0;
    if (nextBtn) nextBtn.disabled = index === cards.length - 1;
  }

  function goTo(i, smooth = true) {
    index = (i + cards.length) % cards.length;
    const target = cards[index];
    track.scrollTo({ left: target.offsetLeft - track.offsetLeft, behavior: smooth ? 'smooth' : 'auto' });
    update();
  }

  function startAuto() {
    if (prefersReduced || autoTimer) return;
    autoTimer = window.setInterval(() => {
      goTo(index + 1 >= cards.length ? 0 : index + 1);
    }, AUTO_MS);
  }
  function stopAuto() {
    if (autoTimer) { window.clearInterval(autoTimer); autoTimer = null; }
  }
  function manualGo(i) {
    stopAuto();
    goTo(i);
    startAuto();
  }

  prevBtn?.addEventListener('click', () => manualGo(index - 1));
  nextBtn?.addEventListener('click', () => manualGo(index + 1));

  let scrollRaf = null;
  track.addEventListener('scroll', () => {
    if (scrollRaf) return;
    scrollRaf = window.requestAnimationFrame(() => {
      scrollRaf = null;
      const center = track.scrollLeft + track.clientWidth / 2;
      let nearest = 0;
      let best = Infinity;
      cards.forEach((c, i) => {
        const cardCenter = c.offsetLeft - track.offsetLeft + c.offsetWidth / 2;
        const dist = Math.abs(cardCenter - center);
        if (dist < best) { best = dist; nearest = i; }
      });
      if (nearest !== index) { index = nearest; update(); }
    });
  }, { passive: true });

  const lightbox = document.createElement('div');
  lightbox.className = 'promo-spotlight__lightbox';
  lightbox.setAttribute('aria-hidden', 'true');
  const lightboxImg = document.createElement('img');
  lightboxImg.alt = '';
  lightbox.appendChild(lightboxImg);
  document.body.appendChild(lightbox);

  function openLightbox(src, alt) {
    lightboxImg.src = src;
    lightboxImg.alt = alt || '';
    lightbox.classList.add('is-open');
    lightbox.setAttribute('aria-hidden', 'false');
  }
  function closeLightbox() {
    lightbox.classList.remove('is-open');
    lightbox.setAttribute('aria-hidden', 'true');
  }

  cards.forEach((card) => {
    const media = card.querySelector('.promo-spotlight__media');
    const img = media?.querySelector('img');
    if (!media || !img) return;
    media.addEventListener('mouseenter', () => openLightbox(img.currentSrc || img.src, img.alt));
    media.addEventListener('mouseleave', closeLightbox);
    media.addEventListener('click', () => {
      if (lightbox.classList.contains('is-open')) closeLightbox();
      else openLightbox(img.currentSrc || img.src, img.alt);
    });
  });
  document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeLightbox(); });

  section.addEventListener('mouseenter', stopAuto);
  section.addEventListener('mouseleave', startAuto);
  section.addEventListener('focusin', stopAuto);
  section.addEventListener('focusout', startAuto);
  document.addEventListener('visibilitychange', () => {
    if (document.hidden) stopAuto(); else startAuto();
  });
  window.addEventListener('resize', () => goTo(index, false));

  update();
  startAuto();
}

function initReviewSlider() {
  const slider = document.getElementById('reviewSlider');
  if (!slider) return;

  const track = slider.querySelector('.review-slider__track');
  const viewport = slider.querySelector('.review-slider__viewport');
  const slides = [...slider.querySelectorAll('.review-card')];
  const prevBtn = slider.querySelector('.review-slider__nav--prev');
  const nextBtn = slider.querySelector('.review-slider__nav--next');
  const dotsContainer = slider.querySelector('.review-slider__dots');

  if (!track || !viewport || slides.length === 0) return;

  let current = 0;
  let perView = 3;
  let maxIndex = 0;
  let autoplayTimer = null;
  let touchStartX = 0;
  let dots = [];

  function getPerView() {
    return window.innerWidth <= 768 ? 1 : 3;
  }

  function getStepWidth() {
    const gap = parseFloat(getComputedStyle(track).columnGap || getComputedStyle(track).gap) || 24;
    const cardWidth = (viewport.offsetWidth - gap * (perView - 1)) / perView;
    return cardWidth + gap;
  }

  function updateTransform() {
    track.style.transform = `translateX(-${current * getStepWidth()}px)`;
  }

  function updateDots() {
    dots.forEach((dot, i) => {
      const active = i === current;
      dot.classList.toggle('is-active', active);
      dot.setAttribute('aria-selected', active ? 'true' : 'false');
    });
  }

  function buildDots() {
    dotsContainer.innerHTML = '';
    dots = [];
    const pageCount = maxIndex + 1;
    for (let i = 0; i < pageCount; i++) {
      const dot = document.createElement('button');
      dot.type = 'button';
      dot.className = 'review-slider__dot' + (i === current ? ' is-active' : '');
      dot.setAttribute('aria-label', `รีวิวชุดที่ ${i + 1}`);
      dot.setAttribute('role', 'tab');
      dot.setAttribute('aria-selected', i === current ? 'true' : 'false');
      dot.addEventListener('click', () => goTo(i, true));
      dotsContainer.appendChild(dot);
      dots.push(dot);
    }
  }

  function updateMetrics() {
    perView = getPerView();
    maxIndex = Math.max(0, slides.length - perView);
    if (current > maxIndex) current = maxIndex;
    slider.style.setProperty('--review-per-view', String(perView));
    buildDots();
    updateTransform();
    updateDots();
  }

  function goTo(index, manual) {
    if (index > maxIndex) current = 0;
    else if (index < 0) current = maxIndex;
    else current = index;
    updateTransform();
    updateDots();
    if (manual) restartAutoplay();
  }

  function startAutoplay() {
    stopAutoplay();
    if (maxIndex === 0) return;
    autoplayTimer = setInterval(() => {
      goTo(current >= maxIndex ? 0 : current + 1);
    }, 5500);
  }

  function stopAutoplay() {
    if (autoplayTimer) {
      clearInterval(autoplayTimer);
      autoplayTimer = null;
    }
  }

  function restartAutoplay() {
    stopAutoplay();
    startAutoplay();
  }

  prevBtn?.addEventListener('click', () => goTo(current - 1, true));
  nextBtn?.addEventListener('click', () => goTo(current + 1, true));

  slider.addEventListener('mouseenter', stopAutoplay);
  slider.addEventListener('mouseleave', startAutoplay);

  slider.addEventListener('touchstart', (e) => {
    touchStartX = e.changedTouches[0].screenX;
    stopAutoplay();
  }, { passive: true });

  slider.addEventListener('touchend', (e) => {
    const diff = e.changedTouches[0].screenX - touchStartX;
    if (Math.abs(diff) > 50) {
      goTo(current + (diff < 0 ? 1 : -1), true);
    } else {
      startAutoplay();
    }
  }, { passive: true });

  window.addEventListener('resize', () => {
    updateMetrics();
  });

  updateMetrics();
  startAutoplay();
}

const HOME_NEWS_PROMO_START = new Date('2026-06-20T00:00:00');
const HOME_NEWS_PROMO_END = new Date('2026-06-27T23:59:59');

function initHomeNewsCountdown() {
  const daysLabel = document.getElementById('homeNewsDurationDays');
  const hintEl = document.getElementById('homeNewsCountdownHint');
  const daysEl = document.getElementById('homeNewsCountdownDays');
  const hoursEl = document.getElementById('homeNewsCountdownHours');
  const minutesEl = document.getElementById('homeNewsCountdownMinutes');
  const secondsEl = document.getElementById('homeNewsCountdownSeconds');
  if (!daysEl) return;

  const pad = (n) => String(Math.max(0, n)).padStart(2, '0');
  const formatEndDate = (date) => date.toLocaleDateString('th-TH', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  });

  const campaignDays = Math.max(1, Math.ceil((HOME_NEWS_PROMO_END - HOME_NEWS_PROMO_START) / 86400000));
  if (daysLabel) daysLabel.textContent = String(campaignDays);

  function updateCountdown() {
    const diff = HOME_NEWS_PROMO_END - Date.now();
    if (diff <= 0) {
      daysEl.textContent = '00';
      hoursEl.textContent = '00';
      minutesEl.textContent = '00';
      secondsEl.textContent = '00';
      if (hintEl) hintEl.textContent = 'กิจกรรมสิ้นสุดแล้ว — ขอบคุณที่ร่วมสนุกกับ BOYINSURE';
      return;
    }

    if (hintEl) {
      hintEl.textContent = `เหลือเวลาร่วมกิจกรรมก่อนปิดรับสิทธิ์ — สิ้นสุด ${formatEndDate(HOME_NEWS_PROMO_END)}`;
    }

    const totalSec = Math.floor(diff / 1000);
    const days = Math.floor(totalSec / 86400);
    const hours = Math.floor((totalSec % 86400) / 3600);
    const minutes = Math.floor((totalSec % 3600) / 60);
    const seconds = totalSec % 60;

    daysEl.textContent = pad(days);
    hoursEl.textContent = pad(hours);
    minutesEl.textContent = pad(minutes);
    secondsEl.textContent = pad(seconds);
  }

  updateCountdown();
  setInterval(updateCountdown, 1000);
}

const HOME_NEWS_PRIZES = [
  { logo: 'assets/img/icon/Group 1.png', short: 'โลตัส', label: "บัตร Lotus's มูลค่า 500 บาท", qty: 100 },
  { logo: 'assets/img/icon/Group 2.png', short: 'ปตท.', label: 'บัตร PTT Station มูลค่า 500 บาท', qty: 100 },
  { logo: 'assets/img/icon/Group 11.png', short: 'Big C', label: 'Voucher Big C มูลค่า 500 บาท', qty: 40 },
  { logo: 'assets/img/icon/Group 3.png', short: 'รพ.กรุงเทพ', label: 'Voucher ตรวจสุขภาพ รพ.กรุงเทพ', qty: 30 },
  { logo: 'assets/img/icon/Group 4.png', short: 'Supersports', label: 'Voucher Supersports มูลค่า 500 บาท', qty: 50 },
  { logo: 'assets/img/icon/Group 5.png', short: 'ทันตกรรม', label: 'Voucher ทันตกรรม', qty: 25 },
  { logo: 'assets/img/icon/Group 6.png', short: 'โบท็อกซ์', label: 'Voucher โบท็อกซ์ฟิลเลอร์', qty: 20 },
  { logo: 'assets/img/icon/Group 7.png', short: 'Jett Fitness', label: 'Voucher Jett Fitness', qty: 30 },
  { logo: 'assets/img/prizes/ohkajhu.png', short: 'โอ้กะจู๋', label: 'Voucher โอ้กะจู๋ มูลค่า 1,000 บาท', qty: 40 },
  { logo: 'assets/img/icon/Group 13.png', short: 'ประกันรถ', label: 'ประกันรถยนต์ ชั้น 1', qty: 10 },
];

function renderHomeNewsPrizeItem(prize) {
  return `
    <article class="home-news-prize-card">
      <div class="home-news-prize-card__logo">
        <img src="${prize.logo}" alt="${prize.short}" width="56" height="56" loading="lazy" />
      </div>
      <strong class="home-news-prize-card__name">${prize.label}</strong>
      <span class="home-news-prize-card__qty">${prize.qty} รางวัล</span>
    </article>
  `;
}

function initHomeNewsPrizes() {
  const list = document.getElementById('homeNewsPrizeList');
  if (!list) return;
  list.innerHTML = HOME_NEWS_PRIZES.map(renderHomeNewsPrizeItem).join('');
  if (window.lucide?.createIcons) lucide.createIcons();
}

const HOME_NEWS_DETAILS = {
  'wheel-promo': {
    badge: 'กิจกรรม',
    category: 'โปรโมชั่น',
    title: 'หมุนวงล้อลุ้นของรางวัล',
    meta: '12 มิ.ย. 2026',
    image: 'assets/img/home-news-featured.png',
    imageAlt: 'กิจกรรมหมุนวงล้อรับของรางวัล',
    ctaHref: 'promotions.html',
    body: `
      <p>ลูกค้าที่ทำประกันกับ BOYINSURE รับสิทธิ์เข้าร่วมกิจกรรมหมุนวงล้อลุ้นของรางวัลและวอเชอร์จากพาร์ทเนอร์ชั้นนำ</p>
      <ul>
        <li>รางวัลจากพาร์ทเนอร์ เช่น โอ้กะจู๋ Jetts Supersports และอื่น ๆ</li>
        <li>จัดกิจกรรมเป็นช่วง ๆ ตามเงื่อนไขที่ประกาศ</li>
        <li>ตรวจสอบสิทธิ์และรายการรางวัลได้ที่หน้าโปรโมชั่น</li>
      </ul>
      <p>ลงทะเบียนเพื่อรับสิทธิ์และติดตามข่าวสารกิจกรรมล่าสุดจาก BOYINSURE</p>
    `,
  },
  'car-insurance': {
    badge: 'ข่าวสาร',
    category: 'ประกันรถยนต์',
    title: 'ประกันรถยนต์ชั้น 1 ปรึกษาฟรี',
    meta: '10 มิ.ย. 2026',
    image: 'assets/img/home-news-car-banner.png',
    imageAlt: 'ประกันรถยนต์ชั้น 1 ปรึกษาฟรี',
    ctaHref: '#homeCarPromo',
    body: `
      <p>ลงทะเบียนเพื่อปรึกษาและเปรียบเทียบเบี้ยประกันรถยนต์ชั้น 1 จากหลายบริษัท โดยทีม BOYINSURE อธิบายให้เข้าใจง่ายก่อนตัดสินใจ</p>
      <ul>
        <li>คุ้มครองรถยนต์ ผู้ขับขี่ และบุคคลภายนอก ตามเงื่อนไขกรมธรรม์</li>
        <li>เปรียบเทียบเบี้ยและความคุ้มครองให้ฟรี ไม่บังคับซื้อ</li>
        <li>ทีมงานติดต่อกลับเพื่อวิเคราะห์ความต้องการและเสนอแผนที่เหมาะสม</li>
      </ul>
      <p>กรอกแบบฟอร์มลงทะเบียนด้านล่างเพื่อรับคำปรึกษาโดยไม่มีค่าใช้จ่าย</p>
    `,
  },
};

function initHomeNewsModal() {
  const modal = document.getElementById('homeNewsModal');
  if (!modal) return;

  const image = document.getElementById('homeNewsModalImage');
  const badge = document.getElementById('homeNewsModalBadge');
  const category = document.getElementById('homeNewsModalCategory');
  const title = document.getElementById('homeNewsModalTitle');
  const meta = document.getElementById('homeNewsModalMeta');
  const body = document.getElementById('homeNewsModalBody');
  const cta = document.getElementById('homeNewsModalCta');
  const dialog = modal.querySelector('.site-modal__dialog');
  let lastTrigger = null;

  function openModal(detailId, trigger) {
    const data = HOME_NEWS_DETAILS[detailId];
    if (!data) return;

    lastTrigger = trigger || null;
    if (image) {
      image.src = data.image;
      image.alt = data.imageAlt || data.title;
    }
    if (badge) badge.textContent = data.badge;
    if (category) category.textContent = data.category;
    if (title) title.textContent = data.title;
    if (meta) meta.textContent = data.meta;
    if (body) body.innerHTML = data.body;
    if (cta) cta.href = data.ctaHref;

    modal.hidden = false;
    document.body.classList.add('site-modal-open');
    modal.querySelector('.site-modal__close')?.focus();
    if (window.lucide?.createIcons) lucide.createIcons();
  }

  function closeModal() {
    modal.hidden = true;
    document.body.classList.remove('site-modal-open');
    lastTrigger?.focus();
    lastTrigger = null;
  }

  document.querySelectorAll('[data-news-detail]').forEach((btn) => {
    btn.addEventListener('click', () => {
      openModal(btn.getAttribute('data-news-detail'), btn);
    });
  });

  modal.querySelectorAll('[data-news-modal-close]').forEach((el) => {
    el.addEventListener('click', closeModal);
  });

  dialog?.addEventListener('click', (e) => e.stopPropagation());

  document.addEventListener('keydown', (e) => {
    if (modal.hidden) return;
    if (e.key === 'Escape') closeModal();
  });
}
