CREATE DATABASE IF NOT EXISTS tenant_evorm
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT   COLLATE utf8mb4_unicode_ci;

USE tenant_evorm;

-- Клиент
CREATE TABLE client (
  id            CHAR(36) PRIMARY KEY DEFAULT (UUID()),
  auth_user_id  CHAR(36) NULL,    -- глобальный пользователь из resti_auth.auth_user
  name          VARCHAR(255) NOT NULL,
  email         VARCHAR(320) NULL,
  created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_auth_user (auth_user_id)
) ENGINE=InnoDB;

-- Контракт
CREATE TABLE contract (
  id            CHAR(36) PRIMARY KEY DEFAULT (UUID()),
  client_id     CHAR(36) NOT NULL,
  number        VARCHAR(100) NOT NULL,
  status        ENUM('draft','active','closed') NOT NULL DEFAULT 'active',
  signed_at     DATE NULL,
  created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_client_number (client_id, number),
  KEY idx_status (status),
  CONSTRAINT fk_contract_client FOREIGN KEY (client_id)
    REFERENCES client(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Счёт
CREATE TABLE invoice (
  id            CHAR(36) PRIMARY KEY DEFAULT (UUID()),
  client_id     CHAR(36) NOT NULL,
  contract_id   CHAR(36) NULL,
  number        VARCHAR(100) NOT NULL,
  issued_on     DATE NOT NULL,
  due_on        DATE NOT NULL,
  currency      CHAR(3) NOT NULL DEFAULT 'EUR',
  amount_cents  BIGINT UNSIGNED NOT NULL,
  status        ENUM('unpaid','paid','void') NOT NULL DEFAULT 'unpaid',
  created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_invoice_number (number),
  KEY idx_client (client_id),
  KEY idx_contract (contract_id),
  CONSTRAINT fk_invoice_client FOREIGN KEY (client_id)
    REFERENCES client(id) ON DELETE RESTRICT,
  CONSTRAINT fk_invoice_contract FOREIGN KEY (contract_id)
    REFERENCES contract(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Вложения (PDF)
CREATE TABLE invoice_attachment (
  id           CHAR(36) PRIMARY KEY DEFAULT (UUID()),
  invoice_id   CHAR(36) NOT NULL,
  filename     VARCHAR(255) NOT NULL,
  content_type VARCHAR(100) NOT NULL,
  content      LONGBLOB NOT NULL,
  created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_attachment_invoice FOREIGN KEY (invoice_id)
    REFERENCES invoice(id) ON DELETE CASCADE
) ENGINE=InnoDB;
