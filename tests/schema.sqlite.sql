-- Схема тестовой базы для режима SQLite (без MySQL).
-- Структура повторяет рабочую salon_db.

CREATE TABLE categories (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL
);

CREATE TABLE users (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  username TEXT NOT NULL,
  email TEXT NOT NULL UNIQUE,
  phone TEXT,
  login TEXT NOT NULL UNIQUE,
  password TEXT NOT NULL,
  pass TEXT,
  status TEXT NOT NULL DEFAULT 'user' CHECK (status IN ('user', 'admin')),
  registration_date TEXT NOT NULL
);

CREATE TABLE services (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  category_id INTEGER NOT NULL REFERENCES categories (id),
  title TEXT NOT NULL,
  master_name TEXT,
  description TEXT,
  price NUMERIC NOT NULL,
  duration INTEGER,
  schedule_days TEXT,
  image TEXT,
  is_active INTEGER NOT NULL DEFAULT 1
);

CREATE TABLE enrollments (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  user_id INTEGER NOT NULL REFERENCES users (id),
  course_id INTEGER NOT NULL REFERENCES services (id),
  status TEXT NOT NULL,
  enrollment_date TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE ratings (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  user_id INTEGER NOT NULL REFERENCES users (id) ON DELETE CASCADE,
  service_id INTEGER NOT NULL REFERENCES services (id) ON DELETE CASCADE,
  rating INTEGER NOT NULL,
  review TEXT,
  created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE (user_id, service_id)
);
