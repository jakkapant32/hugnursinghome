-- เพิ่มบทบาท member (สมาชิก/ญาติ) สำหรับหน้าเว็บ
ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check;
ALTER TABLE users ADD CONSTRAINT users_role_check
  CHECK (role IN ('admin', 'staff', 'member'));
