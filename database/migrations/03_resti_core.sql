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
