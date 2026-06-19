<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
$admin = require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'update' && ($id = (int) ($_POST['id'] ?? 0))) {
        db()->prepare('UPDATE games SET name=?, description=?, status=?, sort_order=? WHERE id=?')->execute([
            trim($_POST['name']),
            trim($_POST['description'] ?? '') ?: null,
            $_POST['status'],
            (int) $_POST['sort_order'],
            $id,
        ]);
        log_activity((int) $admin['id'], 'update_game', 'game', $id);
        flash_set('success', 'อัปเดตเกมแล้ว');
    }
    if ($action === 'create') {
        $code = preg_replace('/[^a-z0-9_]/', '', strtolower(trim($_POST['code'] ?? '')));
        if ($code === '') {
            flash_set('error', 'รหัสเกมต้องเป็นภาษาอังกฤษ a-z, 0-9, _ เท่านั้น');
        } else {
            try {
                db()->prepare(
                    'INSERT INTO games (code, name, type, description, status, sort_order) VALUES (?,?,?,?,?,?)'
                )->execute([
                    $code,
                    trim($_POST['name']),
                    $_POST['type'] ?? 'other',
                    trim($_POST['description'] ?? '') ?: null,
                    $_POST['status'] ?? 'inactive',
                    (int) ($_POST['sort_order'] ?? 99),
                ]);
                flash_set('success', 'เพิ่มเกมแล้ว — พร้อมตั้งของรางวัลและพัฒนา API ในอนาคต');
            } catch (PDOException $e) {
                flash_set('error', 'รหัสเกมซ้ำหรือข้อมูลไม่ถูกต้อง');
            }
        }
    }
    header('Location: games.php');
    exit;
}

$rows = db()->query(
    'SELECT g.*,
            (SELECT COUNT(*) FROM spin_logs s WHERE s.game_id = g.id) AS play_count,
            (SELECT COUNT(*) FROM prizes p WHERE p.game_id = g.id AND p.status = "active") AS prize_count
     FROM games g
     ORDER BY g.sort_order, g.id'
)->fetchAll();

ob_start();
?>
<div class="admin-card">
  <div class="admin-card__head"><h2>เพิ่มเกม / กิจกรรม</h2></div>
  <div class="admin-card__body">
    <form method="post" class="admin-form admin-form--wide">
      <input type="hidden" name="action" value="create" />
      <div class="admin-form__row">
        <div>
          <label>รหัสเกม (ภาษาอังกฤษ)</label>
          <input name="code" required placeholder="เช่น scratch_card" pattern="[a-z0-9_]+" />
        </div>
        <div>
          <label>ชื่อแสดง</label>
          <input name="name" required placeholder="เช่น ขูดบัตรลุ้นโชค" />
        </div>
      </div>
      <div class="admin-form__row">
        <div>
          <label>ประเภท</label>
          <select name="type">
            <option value="wheel">วงล้อ</option>
            <option value="scratch">ขูดบัตร</option>
            <option value="quiz">ทายคำถาม</option>
            <option value="other">อื่นๆ</option>
          </select>
        </div>
        <div>
          <label>สถานะ</label>
          <select name="status">
            <option value="inactive">ปิด (ร่าง)</option>
            <option value="active">เปิดใช้</option>
          </select>
        </div>
      </div>
      <label>คำอธิบาย</label>
      <textarea name="description" placeholder="รายละเอียดเกมสำหรับทีมงาน"></textarea>
      <button type="submit" class="admin-btn admin-btn--primary">เพิ่มเกม</button>
    </form>
  </div>
</div>

<div class="admin-card">
  <div class="admin-card__head"><h2>เกมทั้งหมด (<?= count($rows) ?>)</h2></div>
  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead>
        <tr>
          <th>ชื่อ</th>
          <th>รหัส</th>
          <th>ประเภท</th>
          <th>ของรางวัล</th>
          <th>การเล่น</th>
          <th>สถานะ</th>
          <th>จัดการ</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><strong><?= h($r['name']) ?></strong></td>
            <td><code><?= h($r['code']) ?></code></td>
            <td><?= h(game_type_label($r['type'])) ?></td>
            <td><?= (int) $r['prize_count'] ?></td>
            <td><?= (int) $r['play_count'] ?></td>
            <td>
              <?php if ($r['status'] === 'active'): ?>
                <span class="badge badge--active">เปิดใช้</span>
              <?php else: ?>
                <span class="badge badge--suspended">ปิด</span>
              <?php endif; ?>
            </td>
            <td>
              <div class="admin-actions">
                <button type="button" class="admin-btn admin-btn--ghost admin-btn--sm" data-admin-modal-open="game-edit-<?= (int) $r['id'] ?>">แก้ไข</button>
                <a href="plays.php?game_id=<?= (int) $r['id'] ?>" class="admin-btn admin-btn--ghost admin-btn--sm">ดูประวัติ</a>
                <a href="prizes.php?game_id=<?= (int) $r['id'] ?>" class="admin-btn admin-btn--ghost admin-btn--sm">ของรางวัล</a>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$rows): ?>
          <tr><td colspan="7" class="admin-empty">ยังไม่มีเกม — รัน <a href="../migrate.php">migrate.php</a></td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php foreach ($rows as $r): ?>
<div class="admin-modal" id="game-edit-<?= (int) $r['id'] ?>" hidden>
  <div class="admin-modal__backdrop" data-admin-modal-close></div>
  <div class="admin-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="game-edit-title-<?= (int) $r['id'] ?>">
    <div class="admin-modal__head">
      <h3 id="game-edit-title-<?= (int) $r['id'] ?>">แก้ไขเกม — <?= h($r['name']) ?></h3>
      <button type="button" class="admin-modal__close" data-admin-modal-close aria-label="ปิด">
        <i data-lucide="x" aria-hidden="true"></i>
      </button>
    </div>
    <form method="post" class="admin-form admin-form--modal">
      <input type="hidden" name="action" value="update" />
      <input type="hidden" name="id" value="<?= (int) $r['id'] ?>" />
      <div class="admin-form__row">
        <div>
          <label>ชื่อแสดง</label>
          <input name="name" value="<?= h($r['name']) ?>" required />
        </div>
        <div>
          <label>ลำดับแสดง</label>
          <input type="number" name="sort_order" value="<?= (int) $r['sort_order'] ?>" min="0" />
        </div>
      </div>
      <label>คำอธิบาย</label>
      <textarea name="description" rows="4"><?= h($r['description'] ?? '') ?></textarea>
      <label>สถานะ</label>
      <select name="status">
        <option value="active"<?= $r['status'] === 'active' ? ' selected' : '' ?>>เปิดใช้</option>
        <option value="inactive"<?= $r['status'] === 'inactive' ? ' selected' : '' ?>>ปิด</option>
      </select>
      <div class="admin-modal__foot">
        <button type="button" class="admin-btn admin-btn--ghost" data-admin-modal-close>ยกเลิก</button>
        <button type="submit" class="admin-btn admin-btn--primary">บันทึก</button>
      </div>
    </form>
  </div>
</div>
<?php endforeach; ?>
<?php
render_layout('เกม / กิจกรรม', ob_get_clean(), 'games');
