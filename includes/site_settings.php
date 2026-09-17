<?php

function hug_site_setting_defaults(): array
{
    return [
        'site_name' => 'ฮักเนอร์สซิ่งโฮม',
        'footer_description' => 'ศูนย์ดูแลผู้สูงอายุที่เชื่อว่าการดูแลที่ดีเริ่มจากความเข้าใจ ให้เราดูแลท่านเหมือนคนในครอบครัว',
        'contact_address' => '123 ถนนมิตรภาพ ต.ในเมือง อ.เมือง จ.ขอนแก่น 40000',
        'contact_address_detail' => '123 ถนนมิตรภาพ ตำบลในเมือง อำเภอเมือง จังหวัดขอนแก่น 40000',
        'contact_phone' => '043-000-000',
        'contact_email' => 'info@hugnursinghome.com',
        'contact_hours' => 'เปิดทุกวัน 09.00–17.00 น.',
        'line_url' => 'https://line.me/ti/p/Zy3CrIzqwV',
    ];
}

function hug_clear_site_settings_cache(): void
{
    unset($GLOBALS['_hug_site_settings']);
}

/** @return array<string,string> */
function hug_load_site_settings(PDO $pdo): array
{
    if (isset($GLOBALS['_hug_site_settings']) && is_array($GLOBALS['_hug_site_settings'])) {
        return $GLOBALS['_hug_site_settings'];
    }

    $settings = hug_site_setting_defaults();
    try {
        $rows = $pdo->query('SELECT setting_key, setting_value FROM site_settings')->fetchAll(PDO::FETCH_KEY_PAIR);
        if ($rows) {
            foreach ($rows as $key => $value) {
                if (array_key_exists($key, $settings)) {
                    $settings[$key] = (string) $value;
                }
            }
        }
    } catch (PDOException) {
        // ตารางยังไม่มี — ใช้ค่า default
    }

    $GLOBALS['_hug_site_settings'] = $settings;

    return $settings;
}

function hug_site_setting(PDO $pdo, string $key): string
{
    $all = hug_load_site_settings($pdo);

    return $all[$key] ?? '';
}

function hug_phone_tel(string $phoneDisplay): string
{
    return preg_replace('/\D+/', '', $phoneDisplay) ?: $phoneDisplay;
}

/** @param array<string,string> $values */
function hug_save_site_settings(PDO $pdo, array $values): void
{
    $allowed = array_keys(hug_site_setting_defaults());
    $stmt = $pdo->prepare(
        'INSERT INTO site_settings (setting_key, setting_value, updated_at)
         VALUES (:k, :v, CURRENT_TIMESTAMP)
         ON CONFLICT (setting_key) DO UPDATE SET
           setting_value = EXCLUDED.setting_value,
           updated_at = CURRENT_TIMESTAMP'
    );

    foreach ($allowed as $key) {
        if (!array_key_exists($key, $values)) {
            continue;
        }
        $stmt->execute([':k' => $key, ':v' => trim($values[$key])]);
    }
    hug_clear_site_settings_cache();
}

function hug_line_url(PDO $pdo): string
{
    return hug_site_setting($pdo, 'line_url');
}
