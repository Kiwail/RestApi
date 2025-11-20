CREATE DATABASE IF NOT EXISTS resti_core
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT   COLLATE utf8mb4_unicode_ci;

USE resti_core;

-- Таблица компаний
CREATE TABLE company (
  id         CHAR(36) PRIMARY KEY DEFAULT (UUID()),
  name       VARCHAR(255) NOT NULL UNIQUE,
  slug       VARCHAR(64)  NOT NULL UNIQUE,
  status     ENUM('active','suspended') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE company_join_request (
  id           CHAR(36) PRIMARY KEY DEFAULT (UUID()),
  company_id   CHAR(36) NOT NULL,        -- resti_core.company.id
  auth_user_id CHAR(36) NOT NULL,        -- resti_auth.auth_user.id (логическая ссылка)
  status       ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  message      VARCHAR(500) NULL,        -- опциональный комментарий от пользователя
  created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  processed_at TIMESTAMP NULL,
  processed_by CHAR(36) NULL,            -- кто одобрил/отклонил (auth_user.id, логически)
  KEY idx_company_status (company_id, status),
  KEY idx_user (auth_user_id)
) ENGINE=InnoDB;

-- Реестр tenant-баз
CREATE TABLE tenant_registry (
  id         CHAR(36) PRIMARY KEY DEFAULT (UUID()),
  company_id CHAR(36) NOT NULL,
  db_name    VARCHAR(64) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_db_name (db_name),
  CONSTRAINT fk_tenant_company FOREIGN KEY (company_id)
    REFERENCES company(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- API-ключи (один ко многим к компании)
CREATE TABLE api_key (
  id            CHAR(36) PRIMARY KEY DEFAULT (UUID()),
  company_id    CHAR(36) NOT NULL,
  name          VARCHAR(100) NOT NULL,
  prefix        VARCHAR(16)  NOT NULL,
  hashed_secret VARCHAR(255) NOT NULL,
  created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_company_name (company_id, name),
  KEY idx_company_prefix (company_id, prefix),
  CONSTRAINT fk_apikey_company FOREIGN KEY (company_id)
    REFERENCES company(id) ON DELETE CASCADE
) ENGINE=InnoDB;

ALTER TABLE company_join_request
  ADD CONSTRAINT fk_cjr_company
    FOREIGN KEY (company_id) REFERENCES company(id)
    ON DELETE CASCADE;
