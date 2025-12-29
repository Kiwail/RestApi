USE resti_core;

ALTER TABLE company_join_request
  ADD UNIQUE KEY uq_company_user (company_id, auth_user_id);

USE tenant_abss;
ALTER TABLE client ADD phone VARCHAR(32) NULL AFTER email;
