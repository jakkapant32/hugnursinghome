<?php
/** รัน: php database/migrate_site_settings.php */
require_once __DIR__ . '/../includes/db.php';
$pdo->exec(file_get_contents(__DIR__ . '/migrate_site_settings.sql'));
echo "site_settings OK\n";
