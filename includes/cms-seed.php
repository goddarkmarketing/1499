<?php

declare(strict_types=1);

/** @return list<string> */
function cms_seed_if_empty(PDO $pdo): array {
    $steps = [];
    $catCount = (int) $pdo->query('SELECT COUNT(*) FROM insurance_categories')->fetchColumn();
    if ($catCount === 0) {
        cms_seed_insurance($pdo);
        $steps[] = 'นำเข้าหมวดและแผนประกันจากข้อมูลเริ่มต้น';
    }
    $contentCount = (int) $pdo->query('SELECT COUNT(*) FROM site_content')->fetchColumn();
    if ($contentCount === 0) {
        cms_seed_site_content($pdo);
        $steps[] = 'นำเข้าเนื้อหาหน้าเว็บ (about, contact, footer, highlights)';
    }
    return $steps;
}

function cms_json_encode(mixed $data): string {
    return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

function cms_seed_insurance(PDO $pdo): void {
    $path = dirname(__DIR__) . '/database/cms-insurance.json';
    if (!is_file($path)) {
        throw new RuntimeException('ไม่พบ database/cms-insurance.json');
    }
    $data = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);

    $catStmt = $pdo->prepare(
        'INSERT INTO insurance_categories (slug, title, tagline, icon, detail_json, sort_order, status) VALUES (?,?,?,?,?,?,?)'
    );
    $planStmt = $pdo->prepare(
        'INSERT INTO insurance_plans (category_id, slug, name, description, image_path, features_json, detail_json, featured, sort_order, status) VALUES (?,?,?,?,?,?,?,?,?,?)'
    );

    foreach ($data['categories'] as $i => $cat) {
        $catStmt->execute([
            $cat['slug'],
            $cat['title'],
            $cat['tagline'] ?? null,
            $cat['icon'] ?? 'shield',
            isset($cat['detail']) ? cms_json_encode($cat['detail']) : null,
            (int) ($cat['sort_order'] ?? ($i + 1)),
            $cat['status'] ?? 'active',
        ]);
        $categoryId = (int) $pdo->lastInsertId();
        foreach ($cat['plans'] ?? [] as $j => $plan) {
            $planStmt->execute([
                $categoryId,
                $plan['slug'],
                $plan['name'],
                $plan['description'] ?? null,
                $plan['image'] ?? null,
                isset($plan['features']) ? cms_json_encode($plan['features']) : null,
                isset($plan['detail']) ? cms_json_encode($plan['detail']) : null,
                !empty($plan['featured']) ? 1 : 0,
                (int) ($plan['sort_order'] ?? ($j + 1)),
                $plan['status'] ?? 'active',
            ]);
        }
    }
}

function cms_seed_site_content(PDO $pdo): void {
    $path = dirname(__DIR__) . '/database/cms-site.json';
    if (!is_file($path)) {
        throw new RuntimeException('ไม่พบ database/cms-site.json');
    }
    $data = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
    $stmt = $pdo->prepare(
        'INSERT INTO site_content (content_key, title, body_json) VALUES (?,?,?)'
    );
    foreach ($data as $key => $block) {
        $stmt->execute([
            $key,
            $block['title'] ?? $key,
            cms_json_encode($block['body'] ?? $block),
        ]);
    }
}

function site_content_get(string $key, ?array $default = null): ?array {
    try {
        $stmt = db()->prepare('SELECT body_json FROM site_content WHERE content_key = ? LIMIT 1');
        $stmt->execute([$key]);
        $raw = $stmt->fetchColumn();
        if ($raw === false) {
            return $default;
        }
        $decoded = json_decode((string) $raw, true);
        return is_array($decoded) ? $decoded : $default;
    } catch (Throwable $e) {
        return $default;
    }
}

function site_content_set(string $key, array $body, ?string $title = null): void {
    db()->prepare(
        'INSERT INTO site_content (content_key, title, body_json) VALUES (?,?,?)
         ON DUPLICATE KEY UPDATE title = VALUES(title), body_json = VALUES(body_json)'
    )->execute([$key, $title ?? $key, cms_json_encode($body)]);
}

function insurance_plan_detail_merged(array $plan, array $category): array {
    $catDefaults = [];
    if (!empty($category['detail_json'])) {
        $catDefaults = json_decode((string) $category['detail_json'], true) ?: [];
    }
    $planData = [];
    if (!empty($plan['detail_json'])) {
        $planData = json_decode((string) $plan['detail_json'], true) ?: [];
    }
    $features = [];
    if (!empty($plan['features_json'])) {
        $features = json_decode((string) $plan['features_json'], true) ?: [];
    }
    $defaultFacts = [
        'age' => 'ตามเงื่อนไขกรมธรรม์',
        'term' => 'ตามแผนที่เลือก',
        'premium' => 'ติดต่อสอบถาม',
        'processDays' => '3–7 วันทำการ',
    ];
    $facts = array_merge($defaultFacts, $catDefaults['facts'] ?? [], $planData['facts'] ?? []);
    $coverage = $planData['coverageDetails'] ?? $features;
    $exclusions = $planData['exclusions'] ?? ($catDefaults['exclusions'] ?? []);
    $useCase = $planData['useCase'] ?? (
        'เหมาะสำหรับผู้ที่สนใจ' . ($plan['name'] ?? '') .
        ' และต้องการคำปรึกษาแบบไม่บังคับซื้อ ทีม BoyInsure ช่วยวิเคราะห์และเปรียบเทียบแผนจากหลายบริษัทให้เหมาะกับงบและเป้าหมายของคุณ'
    );
    $faq = array_merge($catDefaults['faq'] ?? [], $planData['faq'] ?? []);
    return compact('facts', 'coverage', 'exclusions', 'useCase', 'faq');
}

function public_site_settings(): array {
    return [
        'site_name' => setting_get('site_name', 'BoyInsure'),
        'site_tagline' => setting_get('site_tagline', 'คุ้มครองทุกช่วงชีวิต ด้วยใจ'),
        'contact_email' => setting_get('contact_email', 'contact@boyinsure.com'),
        'phone' => setting_get('phone', '0627878968'),
        'phone_display' => setting_get('phone_display', '062-787-8968'),
        'business_hours' => setting_get('business_hours', 'จันทร์–ศุกร์ 09:00–18:00 น.'),
        'address' => setting_get('address', 'ให้บริการทั่วประเทศ'),
        'footer_note' => setting_get('footer_note', 'ศูนย์ไทยประกันชีวิต'),
        'facebook_url' => setting_get('facebook_url', 'https://www.facebook.com/'),
        'tiktok_url' => setting_get('tiktok_url', 'https://www.tiktok.com/'),
        'line_url' => setting_get('line_url', 'https://line.me/R/ti/p/@boyinsure'),
    ];
}
