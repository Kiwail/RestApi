CREATE DATABASE IF NOT EXISTS resti_auth
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT   COLLATE utf8mb4_unicode_ci;

USE resti_auth;

-- Глобальный пользователь
CREATE TABLE auth_user (
  id             CHAR(36) PRIMARY KEY DEFAULT (UUID()),
  email          VARCHAR(320) NOT NULL UNIQUE,
  username       VARCHAR(64)  NULL UNIQUE,
  phone          VARCHAR(32)  NULL,
  email_verified BOOLEAN NOT NULL DEFAULT FALSE,
  role          ENUM('admin','user') NOT NULL DEFAULT 'user'
 status         ENUM('active','invited','blocked') NOT NULL DEFAULT 'active',
 created_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;


-- Пароль пользователя
CREATE TABLE auth_password (
  user_id   CHAR(36) PRIMARY KEY,
  algo      ENUM('argon2id','bcrypt') NOT NULL DEFAULT 'argon2id',
  hash      VARCHAR(255) NOT NULL,
  set_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_password_user FOREIGN KEY (user_id)
    REFERENCES auth_user(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Принадлежность к компаниям (M:N)
-- Нельзя сослаться напрямую на resti_core.company (другая БД),
-- поэтому company_id хранится "логически" — без FK, но с индексом.
CREATE TABLE auth_user_company (
  user_id     CHAR(36) NOT NULL,
  company_id  CHAR(36) NOT NULL,
  status      ENUM('active','invited','blocked','pending') NOT NULL DEFAULT 'pending',
  created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, company_id),
  KEY idx_company (company_id),
  CONSTRAINT fk_auc_user FOREIGN KEY (user_id)
    REFERENCES auth_user(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Сессии
CREATE TABLE auth_session (
  id          CHAR(36) PRIMARY KEY DEFAULT (UUID()),
  user_id     CHAR(36) NOT NULL,
  company_id  CHAR(36) NULL,
  expires_at  DATETIME NOT NULL,
  created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  revoked_at  DATETIME NULL,
  KEY idx_user (user_id),
  KEY idx_expires (expires_at),
  CONSTRAINT fk_session_user FOREIGN KEY (user_id)
    REFERENCES auth_user(id) ON DELETE CASCADE
) ENGINE=InnoDB;
