<?php
require_once __DIR__ . '/../includes/db.php';
$pdo->exec(file_get_contents(__DIR__ . '/migrate_member_role.sql'));
echo "member role OK\n";
