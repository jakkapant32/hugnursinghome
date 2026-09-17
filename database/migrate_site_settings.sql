-- รันครั้งเดียวถ้ามี DB เก่าที่ยังไม่มีตาราง site_settings
CREATE TABLE IF NOT EXISTS site_settings (
  setting_key   VARCHAR(80) PRIMARY KEY,
  setting_value TEXT NOT NULL,
  updated_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO site_settings (setting_key, setting_value) VALUES
  ('site_name', 'ฮักเนอร์สซิ่งโฮม'),
  ('footer_description', 'ศูนย์ดูแลผู้สูงอายุที่เชื่อว่าการดูแลที่ดีเริ่มจากความเข้าใจ ให้เราดูแลท่านเหมือนคนในครอบครัว'),
  ('contact_address', '123 ถนนมิตรภาพ ต.ในเมือง อ.เมือง จ.ขอนแก่น 40000'),
  ('contact_address_detail', '123 ถนนมิตรภาพ ตำบลในเมือง อำเภอเมือง จังหวัดขอนแก่น 40000'),
  ('contact_phone', '043-000-000'),
  ('contact_email', 'info@hugnursinghome.com'),
  ('contact_hours', 'เปิดทุกวัน 09.00–17.00 น.'),
  ('line_url', 'https://line.me/ti/p/Zy3CrIzqwV')
ON CONFLICT (setting_key) DO NOTHING;
