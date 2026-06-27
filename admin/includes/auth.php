<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/includes/bootstrap.php';

start_session(app_config('session.admin_key'));

function admin_user(): ?array {
    return $_SESSION['admin'] ?? null;
}

function require_admin(): array {
    $user = admin_user();
    if (!$user) {
        header('Location: login.php');
        exit;
    }
    return $user;
}

function admin_can(array $admin, array $roles): bool {
    return in_array($admin['role'], $roles, true) || $admin['role'] === 'super_admin';
}

function h(?string $s): string {
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

function status_badge(string $type, string $status): string {
    $labels = status_labels()[$type] ?? [];
    $text = $labels[$status] ?? $status;
    return '<span class="badge badge--' . h($status) . '">' . h($text) . '</span>';
}

function flash_set(string $type, string $msg): void {
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}

function flash_get(): ?array {
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}

function admin_edit_button(string $modalId, string $label = 'แก้ไข'): string {
    return '<button type="button" class="admin-btn admin-btn--ghost admin-btn--sm" data-admin-modal-open="' . h($modalId) . '">' . h($label) . '</button>';
}

function admin_modal_start(string $id, string $title, bool $wide = false): void {
    $wideClass = $wide ? ' admin-modal__dialog--wide' : '';
    echo '<div class="admin-modal" id="' . h($id) . '" hidden>';
    echo '<div class="admin-modal__backdrop" data-admin-modal-close></div>';
    echo '<div class="admin-modal__dialog' . $wideClass . '" role="dialog" aria-modal="true">';
    echo '<div class="admin-modal__head"><h3>' . h($title) . '</h3>';
    echo '<button type="button" class="admin-modal__close" data-admin-modal-close aria-label="ปิด"><i data-lucide="x" aria-hidden="true"></i></button></div>';
}

function admin_modal_end(): void {
    echo '<div class="admin-modal__foot">';
    echo '<button type="button" class="admin-btn admin-btn--ghost" data-admin-modal-close>ยกเลิก</button>';
    echo '<button type="submit" class="admin-btn admin-btn--primary">บันทึก</button>';
    echo '</div></form></div></div>';
}

function admin_require_roles(array $admin, array $roles): void {
    if (!admin_can($admin, $roles)) {
        flash_set('error', 'ไม่มีสิทธิ์เข้าถึงหน้านี้');
        header('Location: index.php');
        exit;
    }
}

/** @return list<array{0: string, 1: list<array>}> */
function admin_menu_groups(): array {
    return [
        ['ภาพรวม', [
            ['dashboard', 'index.php', 'Dashboard', 'layout-dashboard', null],
            ['reports', 'reports.php', 'รายงาน / Export', 'file-spreadsheet', ['super_admin', 'ops', 'sales_manager', 'support']],
        ]],
        ['ลูกค้า', [
            ['members', 'members.php', 'สมาชิก / ลูกค้า', 'users', ['super_admin', 'ops', 'sales_manager', 'support']],
            ['member-tiers', 'member-tiers.php', 'ระดับสมาชิก', 'layers', ['super_admin', 'ops']],
            ['rewards', 'rewards.php', 'สถานะรางวัล', 'package-check', ['super_admin', 'ops', 'support']],
            ['plays', 'plays.php', 'ประวัติการเล่น', 'history', ['super_admin', 'ops', 'support']],
        ]],
        ['ตัวแทน & Lead', [
            ['agents', 'agents.php', 'ตัวแทน', 'briefcase', ['super_admin', 'hr', 'sales_manager']],
            ['agent-tiers', 'agent-tiers.php', 'ระดับตัวแทน', 'award', ['super_admin', 'hr']],
            ['applications', 'applications.php', 'ใบสมัครตัวแทน', 'file-text', ['super_admin', 'hr']],
            ['leads', 'leads.php', 'Lead / สอบถาม', 'message-square', ['super_admin', 'support', 'sales_manager']],
        ]],
        ['โปรโมชั่น', [
            ['games', 'games.php', 'เกม / กิจกรรม', 'gamepad-2', ['super_admin', 'ops']],
            ['campaigns', 'campaigns.php', 'แคมเปญ', 'megaphone', ['super_admin', 'ops']],
            ['prizes', 'prizes.php', 'ของรางวัล', 'gift', ['super_admin', 'ops']],
        ]],
        ['ระบบ', [
            ['insurance-categories', 'insurance-categories.php', 'หมวดประกัน', 'layers', ['super_admin', 'ops']],
            ['insurance-plans', 'insurance-plans.php', 'แผนประกัน', 'shield', ['super_admin', 'ops']],
            ['articles', 'articles.php', 'บทความ', 'book-open', ['super_admin', 'ops']],
            ['site-pages', 'site-pages.php', 'เนื้อหาหน้าเว็บ', 'layout-template', ['super_admin', 'ops']],
            ['logs', 'logs.php', 'ประวัติระบบ', 'scroll-text', ['super_admin', 'ops']],
            ['backups', 'backups.php', 'สำรองข้อมูล', 'archive', ['super_admin']],
            ['settings', 'settings.php', 'ตั้งค่า', 'settings', ['super_admin']],
            ['admins', 'admins.php', 'ผู้ดูแลระบบ', 'shield', ['super_admin']],
            ['profile', 'profile.php', 'โปรไฟล์ / รหัสผ่าน', 'user', null],
        ]],
    ];
}

function admin_menu_filtered(array $admin): array {
    $out = [];
    foreach (admin_menu_groups() as [$label, $items]) {
        $filtered = [];
        foreach ($items as $item) {
            $roles = $item[4] ?? null;
            if ($roles === null || admin_can($admin, $roles)) {
                $filtered[] = $item;
            }
        }
        if ($filtered) {
            $out[] = [$label, $filtered];
        }
    }
    return $out;
}

function admin_nav_seen_mark(string $key): void {
    $_SESSION['admin_nav_seen'][$key] = date('Y-m-d H:i:s');
}

function admin_nav_seen_at(string $key): ?string {
    return $_SESSION['admin_nav_seen'][$key] ?? null;
}

function admin_nav_default_since(): string {
    return date('Y-m-d H:i:s', strtotime('-24 hours'));
}

function admin_nav_since(string $key): string {
    return admin_nav_seen_at($key) ?? admin_nav_default_since();
}

/** @return array<string, int> */
function admin_nav_badges(): array {
    $badges = [
        'members' => 0,
        'member-tiers' => 0,
        'rewards' => 0,
        'plays' => 0,
    ];

    try {
        $stmt = db()->prepare('SELECT COUNT(*) FROM members WHERE created_at > ?');
        $stmt->execute([admin_nav_since('members')]);
        $badges['members'] = (int) $stmt->fetchColumn();

        $sinceTiers = admin_nav_since('member-tiers');
        $stmt = db()->prepare('SELECT COUNT(*) FROM member_tiers WHERE created_at > ?');
        $stmt->execute([$sinceTiers]);
        $badges['member-tiers'] = (int) $stmt->fetchColumn();

        $stmt = db()->prepare('SELECT COUNT(*) FROM members WHERE tier_id IS NULL AND created_at > ?');
        $stmt->execute([$sinceTiers]);
        $badges['member-tiers'] += (int) $stmt->fetchColumn();

        $sinceRewards = admin_nav_seen_at('rewards');
        if ($sinceRewards) {
            $stmt = db()->prepare(
                "SELECT COUNT(*) FROM reward_claims
                 WHERE status IN ('won','pending_verify')
                 AND GREATEST(created_at, COALESCE(claimed_at, created_at), updated_at) > ?"
            );
            $stmt->execute([$sinceRewards]);
        } else {
            $stmt = db()->query(
                "SELECT COUNT(*) FROM reward_claims WHERE status IN ('won','pending_verify')"
            );
        }
        $badges['rewards'] = (int) $stmt->fetchColumn();

        $stmt = db()->prepare('SELECT COUNT(*) FROM spin_logs WHERE created_at > ?');
        $stmt->execute([admin_nav_since('plays')]);
        $badges['plays'] = (int) $stmt->fetchColumn();
    } catch (Throwable $e) {
        // ignore badge errors — sidebar still works
    }

    return $badges;
}

function admin_nav_badge_label(int $count): string {
    return $count > 99 ? '99+' : (string) $count;
}

function render_layout(string $title, string $content, string $active = ''): void {
    $admin = require_admin();
    $flash = flash_get();
    $menuGroups = admin_menu_filtered($admin);
    $navBadges = admin_nav_badges();
    require __DIR__ . '/layout.php';
}
