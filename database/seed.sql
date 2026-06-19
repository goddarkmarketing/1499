USE boyinsure;

INSERT INTO admin_users (email, password_hash, name, role, status) VALUES
('admin@boyinsure.com', '$2y$10$hfz8TrVCHGApSnNURFev4.hTdm.uV.wuNLrf8gAvCOxq36vxsZd/O', 'Super Admin', 'super_admin', 'active');

INSERT INTO member_tiers (code, name, spin_quota, min_premium, description, sort_order) VALUES
('general', 'ระดับทั่วไป', 1, NULL, 'ได้รับสิทธิ์หมุนวงล้อ 1 ครั้ง', 1),
('S', 'ระดับ S', 1, 50000, 'เบี้ยประกัน 50,000 บาท', 2),
('M', 'ระดับ M', 1, 100000, 'เบี้ยประกัน 100,000 บาท', 3),
('L', 'ระดับ L (Executive)', 3, 250000, 'แผน Executive Max Plan เบี้ย 250,000 บาทขึ้นไป — หมุน 3 ครั้ง', 4);

INSERT INTO agent_tiers (code, name, description, min_policies, sort_order) VALUES
('trainee', 'Trainee', 'สมัครใหม่ / กำลังอบรม', 0, 1),
('agent', 'Agent', 'มีใบอนุญาต เริ่มขายได้', 1, 2),
('senior', 'Senior Agent', 'ยอดขายและประสบการณ์สูง', 10, 3),
('leader', 'Leader', 'มีทีมและรับสมัคร downline', 25, 4);

INSERT INTO campaigns (name, description, start_date, end_date, status) VALUES
('วงล้อของรางวัล BoyInsure 2026', 'กิจกรรมหมุนวงล้อสำหรับลูกค้า BoyInsure', '2026-01-01', '2026-12-31', 'active');

INSERT INTO games (code, name, type, description, status, sort_order) VALUES
('lucky_wheel', 'วงล้อโชคดี', 'wheel', 'เกมหมุนวงล้อลุ้นรางวัลสำหรับลูกค้า BoyInsure', 'active', 1);

INSERT INTO prizes (game_id, campaign_id, name, short_name, detail, logo_path, color, prize_type, weight, wheel_enabled, status, sort_order) VALUES
(1, 1, 'บัตรโลตัส', 'โลตัส', 'บัตรของขวัญมูลค่า 500 บาท ใช้ได้ทุกสาขา Lotus''s', 'assets/img/prizes/lotus.svg', '#fff9e6', 'voucher', 12, 1, 'active', 1),
(1, 1, 'บัตรน้ำมันปตท.', 'ปตท.', 'บัตรเติมน้ำมันมูลค่า 500 บาท', 'assets/img/prizes/ptt.svg', '#e8f4fd', 'voucher', 12, 1, 'active', 2),
(1, 1, 'Voucher ตรวจสุขภาพ เครือโรงพยาบาลกรุงเทพ', 'รพ.กรุงเทพ', 'ตรวจสุขภาพพื้นฐาน', 'assets/img/prizes/hospital.svg', '#fde8f0', 'voucher', 10, 1, 'active', 3),
(1, 1, 'Voucher Super sport', 'Supersports', 'Voucher มูลค่า 1,000 บาท', 'assets/img/prizes/supersports.svg', '#fff0e8', 'voucher', 10, 1, 'active', 4),
(1, 1, 'Voucher ทันตกรรม', 'ทันตกรรม', 'ฟอกสีฟัน หรือขูดหินปูน 1 ครั้ง', 'assets/img/prizes/dental.svg', '#e8f8ff', 'voucher', 10, 1, 'active', 5),
(1, 1, 'Voucher โบท็อกซ์ฟิลเลอร์', 'Botox', 'Voucher โบท็อกซ์/ฟิลเลอร์', 'assets/img/prizes/beauty.svg', '#fde8f8', 'voucher', 8, 1, 'active', 6),
(1, 1, 'Voucher Jett Fitness', 'Jett', 'Voucher Jett Fitness', 'assets/img/prizes/jetts.svg', '#e8fff0', 'voucher', 8, 1, 'active', 7),
(1, 1, 'Voucher Big C', 'Big C', 'Voucher Big C', 'assets/img/prizes/bigc.svg', '#fff5e6', 'voucher', 10, 1, 'active', 8),
(1, 1, 'Voucher โอ้กะจู๋', 'โอ้กะจู๋', 'Voucher โอ้กะจู๋', 'assets/img/prizes/ohkajhu.png', '#f0ffe8', 'voucher', 8, 1, 'active', 9),
(1, 1, 'ประกันรถยนต์ ชั้น 1', 'ประกันรถ', 'ประกันรถยนต์ชั้น 1', 'assets/img/prizes/car.svg', '#e8f0ff', 'insurance', 5, 1, 'active', 10),
(1, 1, 'ประกันอุบัติเหตุ 400,000', 'อุบัติเหตุ', 'ทุนประกันอุบัติเหตุ 400,000 บาท', 'assets/img/prizes/accident.svg', '#fff8e8', 'insurance', 5, 1, 'active', 11),
(1, 1, 'ประกันอัคคีภัย ที่อยู่อาศัย', 'อัคคีภัย', 'ประกันอัคคีภัยที่อยู่อาศัย', 'assets/img/prizes/fire.svg', '#ffe8e8', 'insurance', 5, 1, 'active', 12),
(1, 1, 'Voucher ติดตั้ง โซล่าร์เซลล์', 'Solar', 'Voucher ติดตั้งโซล่าร์เซลล์', 'assets/img/prizes/solar.svg', '#fffde8', 'voucher', 5, 1, 'active', 13);

INSERT INTO article_categories (slug, title, tagline, icon, sort_order) VALUES
('life', 'ประกันชีวิต', 'สร้างความมั่นคงให้ครอบครัว', 'heart', 1),
('health', 'สุขภาพและการรักษา', 'เจ็บป่วยไม่สะเทือนเงินเก็บ', 'heart-pulse', 2),
('planning', 'วางแผนประกัน', 'เลือกแผนที่พอดีกับงบ', 'clipboard-list', 3),
('savings', 'ออมเงินและอนาคต', 'ออมวันนี้ สบายวันหน้า', 'piggy-bank', 4),
('tips', 'เคล็ดลับและ FAQ', 'คำตอบจากทีม BoyInsure', 'lightbulb', 5),
('promo', 'โปรโมชั่น', 'กิจกรรมและของรางวัล', 'gift', 6);

INSERT INTO settings (setting_key, setting_value) VALUES
('site_name', 'BoyInsure'),
('site_tagline', 'คุ้มครองทุกช่วงชีวิต ด้วยใจ'),
('contact_email', 'admin@boyinsure.com'),
('phone', '0627878968'),
('phone_display', '062-787-8968'),
('business_hours', 'จันทร์–ศุกร์ 09:00–18:00 น.'),
('address', 'ให้บริการทั่วประเทศ'),
('footer_note', 'ศูนย์ไทยประกันชีวิต'),
('notify_email', ''),
('low_stock_threshold', '5');
