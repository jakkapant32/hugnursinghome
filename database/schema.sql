-- =========================================================
-- Hug Nursing Home — Database Schema
-- ระบบประชาสัมพันธ์และบริหารจัดการข้อมูลศูนย์ดูแลผู้สูงอายุ
-- Engine: MySQL 8 / MariaDB 10.4+  (utf8mb4 สำหรับภาษาไทย)
-- =========================================================

CREATE DATABASE IF NOT EXISTS hug_nursing_home
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE hug_nursing_home;

-- ---------------------------------------------------------
-- 1) ผู้ใช้งานระบบหลังบ้าน (เจ้าหน้าที่ / ผู้ดูแลระบบ)
-- ---------------------------------------------------------
CREATE TABLE users (
  user_id       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username      VARCHAR(50)  NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,             -- password_hash() ของ PHP
  full_name     VARCHAR(150) NOT NULL,
  email         VARCHAR(150) NULL,
  role          ENUM('admin','staff') NOT NULL DEFAULT 'staff',
  is_active     TINYINT(1)   NOT NULL DEFAULT 1,
  last_login_at DATETIME     NULL,
  created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
                 ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- 2) ข้อมูลผู้สูงอายุ / ผู้รับบริการ
-- ---------------------------------------------------------
CREATE TABLE residents (
  resident_id     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  prefix          VARCHAR(20)  NOT NULL,            -- นาย/นาง/นางสาว
  full_name       VARCHAR(150) NOT NULL,
  gender          ENUM('ชาย','หญิง') NOT NULL,
  birth_date      DATE NULL,
  national_id     VARCHAR(20)  NULL,
  phone           VARCHAR(20)  NULL,
  address         TEXT NULL,
  guardian_name   VARCHAR(150) NULL,                -- ผู้ติดต่อ/ญาติ
  guardian_phone  VARCHAR(20)  NULL,
  health_notes    TEXT NULL,                        -- โรคประจำตัว/ข้อควรระวัง
  admitted_date   DATE NOT NULL,                     -- วันที่เข้ารับบริการ
  status          ENUM('กำลังรับบริการ','พักการรับบริการ','สิ้นสุดการรับบริการ')
                  NOT NULL DEFAULT 'กำลังรับบริการ',
  photo_path      VARCHAR(255) NULL,
  created_by      INT UNSIGNED NULL,
  created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
                  ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_residents_user FOREIGN KEY (created_by) REFERENCES users(user_id)
    ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- 3) พนักงาน/เจ้าหน้าที่ดูแล (แยกจากบัญชีผู้ใช้ระบบ)
-- ---------------------------------------------------------
CREATE TABLE staff (
  staff_id     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  full_name    VARCHAR(150) NOT NULL,
  position     VARCHAR(100) NOT NULL,                -- พยาบาล/ผู้ช่วย/ครัว/ธุรการ
  phone        VARCHAR(20)  NULL,
  shift        ENUM('เช้า','บ่าย','ดึก','ยืดหยุ่น') NOT NULL DEFAULT 'ยืดหยุ่น',
  hired_date   DATE NULL,
  is_active    TINYINT(1)   NOT NULL DEFAULT 1,
  created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- 4) บริการของศูนย์ (แสดงหน้าเว็บประชาสัมพันธ์)
-- ---------------------------------------------------------
CREATE TABLE services (
  service_id   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title        VARCHAR(150) NOT NULL,
  description  TEXT NULL,
  icon         VARCHAR(50)  NULL,                    -- ชื่อไอคอน เช่น heart, wheelchair
  sort_order   INT UNSIGNED NOT NULL DEFAULT 0,
  is_published TINYINT(1)   NOT NULL DEFAULT 1,
  created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- 5) บันทึกการให้บริการรายวัน/รายกิจกรรมของผู้สูงอายุแต่ละคน
-- ---------------------------------------------------------
CREATE TABLE service_records (
  record_id     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  resident_id   INT UNSIGNED NOT NULL,
  service_id    INT UNSIGNED NULL,
  record_date   DATE NOT NULL,
  detail        TEXT NULL,
  recorded_by   INT UNSIGNED NULL,
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_record_resident FOREIGN KEY (resident_id) REFERENCES residents(resident_id) ON DELETE CASCADE,
  CONSTRAINT fk_record_service  FOREIGN KEY (service_id)  REFERENCES services(service_id)  ON DELETE SET NULL,
  CONSTRAINT fk_record_user     FOREIGN KEY (recorded_by) REFERENCES users(user_id)         ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- 6) ข่าวสารและกิจกรรม
-- ---------------------------------------------------------
CREATE TABLE news (
  news_id       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  category      ENUM('ข่าวสาร','กิจกรรม','ประกาศ') NOT NULL DEFAULT 'ข่าวสาร',
  title         VARCHAR(200) NOT NULL,
  content       TEXT NOT NULL,
  cover_image   VARCHAR(255) NULL,
  event_date    DATE NULL,                            -- วันที่จัดกิจกรรม (ถ้ามี)
  is_published  TINYINT(1) NOT NULL DEFAULT 1,
  created_by    INT UNSIGNED NULL,
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
                ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_news_user FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- 7) แกลเลอรีรูปภาพ/สื่อประชาสัมพันธ์
-- ---------------------------------------------------------
CREATE TABLE gallery (
  gallery_id   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  album_name   VARCHAR(150) NOT NULL,
  image_path   VARCHAR(255) NOT NULL,
  caption      VARCHAR(255) NULL,
  news_id      INT UNSIGNED NULL,                     -- ผูกกับกิจกรรม/ข่าวถ้ามี
  uploaded_by  INT UNSIGNED NULL,
  uploaded_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_gallery_news FOREIGN KEY (news_id) REFERENCES news(news_id) ON DELETE SET NULL,
  CONSTRAINT fk_gallery_user FOREIGN KEY (uploaded_by) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- 8) ข้อความติดต่อจากหน้าเว็บประชาสัมพันธ์
-- ---------------------------------------------------------
CREATE TABLE contact_messages (
  message_id   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  sender_name  VARCHAR(150) NOT NULL,
  phone        VARCHAR(20)  NULL,
  email        VARCHAR(150) NULL,
  subject      VARCHAR(200) NULL,
  message      TEXT NOT NULL,
  is_read      TINYINT(1) NOT NULL DEFAULT 0,
  created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- ข้อมูลตั้งต้นสำหรับทดสอบระบบ
-- ---------------------------------------------------------
-- รหัสผ่านตัวอย่าง: Admin@1234  (ให้ hash จริงด้วย password_hash() ในโค้ด PHP ก่อนใช้งานจริง)
INSERT INTO users (username, password_hash, full_name, email, role) VALUES
('admin', '$2y$10$examplehashreplaceinproduction000000000000000000000', 'ผู้ดูแลระบบ', 'admin@hugnursinghome.local', 'admin');

INSERT INTO services (title, description, icon, sort_order) VALUES
('ดูแลสุขภาพ', 'ตรวจสุขภาพประจำวัน และดูแลโดยทีมพยาบาลวิชาชีพ', 'heart', 1),
('กายภาพบำบัด', 'ฟื้นฟูสมรรถภาพร่างกายโดยนักกายภาพบำบัด', 'walk', 2),
('อาหารและโภชนาการ', 'อาหารครบ 5 หมู่ ปรุงสดใหม่ตามหลักโภชนาการ', 'food', 3),
('กิจกรรมสันทนาการ', 'กิจกรรมเสริมสร้างคุณภาพชีวิตและความสัมพันธ์ที่ดี', 'group', 4),
('ดูแล 24 ชั่วโมง', 'เจ้าหน้าที่ดูแลอย่างใกล้ชิดตลอด 24 ชั่วโมง', 'clock', 5);
