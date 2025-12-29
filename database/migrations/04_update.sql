USE resti_core;

ALTER TABLE company_join_request
  ADD UNIQUE KEY uq_company_user (company_id, auth_user_id);
