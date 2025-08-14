-- Update script to add user identity fields for claim verification
USE lost_and_found;

ALTER TABLE users
  ADD COLUMN identifier_type ENUM('nin','email','matric') NULL AFTER password;

ALTER TABLE users
  ADD COLUMN identifier_value VARCHAR(100) NULL AFTER identifier_type;

-- Enforce uniqueness of identifier_value across users
ALTER TABLE users
  ADD UNIQUE KEY users_identifier_value_unique (identifier_value);


