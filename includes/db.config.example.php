<?php
/**
 * คัดลอกเป็น db.config.php แล้วใส่ค่าจาก Render Dashboard
 * ใช้ External host: dpg-xxxxx.virginia-postgres.render.com (ไม่ใช่ชื่อสั้น dpg-xxxxx-a อย่างเดียว)
 */
return [
    'host'     => 'dpg-xxxxx.virginia-postgres.render.com',
    'port'     => 5432,
    'dbname'   => 'hugnursinghome_db',
    'user'     => 'hugnursinghome_user',
    'password' => 'YOUR_PASSWORD_HERE',
    'sslmode'  => 'require',
];
