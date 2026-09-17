<?php
/** เมนูหลังบ้าน — admin_only = เฉพาะ role admin */
return [
    ['dashboard', 'dashboard.php', 'home', 'หน้าหลัก', false],
    ['residents', 'manage_residents.php', 'elder', 'ผู้รับบริการ', false],
    ['records', 'manage_service_records.php', 'medical', 'บันทึกการให้บริการ', false],
    ['staff', 'manage_staff.php', 'briefcase', 'พนักงาน (HR)', false],
    ['services', 'manage_services.php', 'service', 'บริการเว็บไซต์', false],
    ['activities', 'manage_activities.php', 'calendar', 'กิจกรรม', false],
    ['news', 'manage_news.php', 'news', 'ข่าวสาร', false],
    ['gallery', 'manage_gallery.php', 'image', 'แกลเลอรี', false],
    ['inbox', 'manage_inbox.php', 'mail', 'ข้อความติดต่อ', false],
    ['search', 'search.php', 'search', 'ค้นหาข้อมูล', false],
    ['reports', 'reports.php', 'chart', 'รายงาน', false],
    ['users', 'manage_users.php', 'user-badge', 'บัญชีผู้ใช้ระบบ', true],
    ['site', 'manage_site.php', 'layout', 'Footer / ติดต่อ', true],
    ['settings', 'settings.php', 'account', 'ตั้งค่าบัญชี', false],
];
