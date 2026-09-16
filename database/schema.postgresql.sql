-- Hug Nursing Home — PostgreSQL (Render)
-- รันผ่าน database/setup.php หรือ psql

CREATE OR REPLACE FUNCTION hug_set_updated_at()
RETURNS TRIGGER AS $$
BEGIN
  NEW.updated_at = CURRENT_TIMESTAMP;
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TABLE IF NOT EXISTS users (
  user_id       SERIAL PRIMARY KEY,
  username      VARCHAR(50)  NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  full_name     VARCHAR(150) NOT NULL,
  email         VARCHAR(150),
  role          VARCHAR(20)  NOT NULL DEFAULT 'staff' CHECK (role IN ('admin', 'staff', 'member')),
  is_active     SMALLINT     NOT NULL DEFAULT 1,
  last_login_at TIMESTAMP,
  created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
);

DROP TRIGGER IF EXISTS trg_users_updated ON users;
CREATE TRIGGER trg_users_updated
  BEFORE UPDATE ON users FOR EACH ROW EXECUTE PROCEDURE hug_set_updated_at();

CREATE TABLE IF NOT EXISTS residents (
  resident_id     SERIAL PRIMARY KEY,
  prefix          VARCHAR(20)  NOT NULL,
  full_name       VARCHAR(150) NOT NULL,
  gender          VARCHAR(10)  NOT NULL CHECK (gender IN ('ชาย', 'หญิง')),
  birth_date      DATE,
  national_id     VARCHAR(20),
  phone           VARCHAR(20),
  address         TEXT,
  guardian_name   VARCHAR(150),
  guardian_phone  VARCHAR(20),
  health_notes    TEXT,
  admitted_date   DATE NOT NULL,
  status          VARCHAR(40) NOT NULL DEFAULT 'กำลังรับบริการ'
    CHECK (status IN ('กำลังรับบริการ', 'พักการรับบริการ', 'สิ้นสุดการรับบริการ')),
  photo_path      VARCHAR(255),
  created_by      INT REFERENCES users(user_id) ON DELETE SET NULL,
  created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

DROP TRIGGER IF EXISTS trg_residents_updated ON residents;
CREATE TRIGGER trg_residents_updated
  BEFORE UPDATE ON residents FOR EACH ROW EXECUTE PROCEDURE hug_set_updated_at();

CREATE TABLE IF NOT EXISTS staff (
  staff_id     SERIAL PRIMARY KEY,
  full_name    VARCHAR(150) NOT NULL,
  position     VARCHAR(100) NOT NULL,
  phone        VARCHAR(20),
  shift        VARCHAR(20) NOT NULL DEFAULT 'ยืดหยุ่น'
    CHECK (shift IN ('เช้า', 'บ่าย', 'ดึก', 'ยืดหยุ่น')),
  hired_date   DATE,
  is_active    SMALLINT NOT NULL DEFAULT 1,
  created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS services (
  service_id   SERIAL PRIMARY KEY,
  title        VARCHAR(150) NOT NULL,
  description  TEXT,
  icon         VARCHAR(50),
  sort_order   INT NOT NULL DEFAULT 0,
  is_published SMALLINT NOT NULL DEFAULT 1,
  created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS service_records (
  record_id     SERIAL PRIMARY KEY,
  resident_id   INT NOT NULL REFERENCES residents(resident_id) ON DELETE CASCADE,
  service_id    INT REFERENCES services(service_id) ON DELETE SET NULL,
  record_date   DATE NOT NULL,
  detail        TEXT,
  recorded_by   INT REFERENCES users(user_id) ON DELETE SET NULL,
  created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS news (
  news_id       SERIAL PRIMARY KEY,
  category      VARCHAR(20) NOT NULL DEFAULT 'ข่าวสาร'
    CHECK (category IN ('ข่าวสาร', 'กิจกรรม', 'ประกาศ')),
  title         VARCHAR(200) NOT NULL,
  content       TEXT NOT NULL,
  cover_image   VARCHAR(255),
  event_date    DATE,
  is_published  SMALLINT NOT NULL DEFAULT 1,
  created_by    INT REFERENCES users(user_id) ON DELETE SET NULL,
  created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

DROP TRIGGER IF EXISTS trg_news_updated ON news;
CREATE TRIGGER trg_news_updated
  BEFORE UPDATE ON news FOR EACH ROW EXECUTE PROCEDURE hug_set_updated_at();

CREATE TABLE IF NOT EXISTS gallery (
  gallery_id   SERIAL PRIMARY KEY,
  album_name   VARCHAR(150) NOT NULL,
  image_path   VARCHAR(255) NOT NULL,
  caption      VARCHAR(255),
  news_id      INT REFERENCES news(news_id) ON DELETE SET NULL,
  uploaded_by  INT REFERENCES users(user_id) ON DELETE SET NULL,
  uploaded_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS contact_messages (
  message_id   SERIAL PRIMARY KEY,
  sender_name  VARCHAR(150) NOT NULL,
  phone        VARCHAR(20),
  email        VARCHAR(150),
  subject      VARCHAR(200),
  message      TEXT NOT NULL,
  is_read      SMALLINT NOT NULL DEFAULT 0,
  created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);
