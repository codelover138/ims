-- Gamma schema additions for the existing `ims` database.
-- These tables use the existing `sma_` prefix convention.

CREATE TABLE IF NOT EXISTS `sma_gamma_user_notes` (
  `user_notes_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(11) unsigned NOT NULL,
  `entry_date` datetime DEFAULT NULL,
  `note_date` datetime DEFAULT NULL,
  `narrative` text,
  `last_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`user_notes_id`),
  KEY `idx_sma_gamma_user_notes_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `sma_gamma_forms` (
  `form_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(11) unsigned NOT NULL,
  `form_title` varchar(255) NOT NULL,
  `description` text,
  `button_label` varchar(100) DEFAULT NULL,
  `input_form_location` varchar(500) DEFAULT NULL,
  `precedent_clause_location` varchar(500) DEFAULT NULL,
  `document_creation_location` varchar(500) DEFAULT NULL,
  `output_filename_base` varchar(255) DEFAULT NULL,
  `output_file_location` varchar(500) DEFAULT NULL,
  `last_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`form_id`),
  KEY `idx_sma_gamma_forms_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `sma_gamma_form_inputs` (
  `form_input_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `form_id` int(11) unsigned NOT NULL,
  `input_name` varchar(100) NOT NULL,
  `input_label` varchar(255) NOT NULL,
  `input_type` enum('Textbox','Dropbox','Checkbox') NOT NULL,
  `data_type` varchar(100) DEFAULT NULL,
  `default_value` varchar(500) DEFAULT NULL,
  `allowed_input` varchar(500) DEFAULT NULL,
  `max_size` int(11) DEFAULT NULL,
  `input_form_instructions` text,
  `line_command` text,
  `table_number` int(11) DEFAULT NULL,
  `table_displayed` int(11) DEFAULT NULL,
  `table_row` int(11) DEFAULT NULL,
  `table_column` int(11) DEFAULT NULL,
  PRIMARY KEY (`form_input_id`),
  KEY `idx_sma_gamma_form_inputs_form_id` (`form_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `sma_gamma_output_file_logs` (
  `output_file_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `form_id` int(11) unsigned NOT NULL,
  `output_file` varchar(500) NOT NULL,
  PRIMARY KEY (`output_file_id`),
  KEY `idx_sma_gamma_output_file_logs_form_id` (`form_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `sma_gamma_input_records` (
  `form_input_record_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `output_file_id` int(11) unsigned NOT NULL,
  `input_name` varchar(100) NOT NULL,
  `input_value` text,
  PRIMARY KEY (`form_input_record_id`),
  KEY `idx_sma_gamma_input_records_output_file_id` (`output_file_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `sma_users`
  ADD COLUMN `middle_name` varchar(100) DEFAULT NULL,
  ADD COLUMN `birth_date` datetime DEFAULT NULL,
  ADD COLUMN `business_name` varchar(150) DEFAULT NULL,
  ADD COLUMN `unit_number` varchar(50) DEFAULT NULL,
  ADD COLUMN `street_number` varchar(50) DEFAULT NULL,
  ADD COLUMN `street_name` varchar(150) DEFAULT NULL,
  ADD COLUMN `street_type` varchar(50) DEFAULT NULL,
  ADD COLUMN `suburb` varchar(100) DEFAULT NULL,
  ADD COLUMN `state` varchar(100) DEFAULT NULL,
  ADD COLUMN `country` varchar(100) DEFAULT NULL,
  ADD COLUMN `postcode` varchar(20) DEFAULT NULL,
  ADD COLUMN `email2` varchar(150) DEFAULT NULL,
  ADD COLUMN `mobile_phone` varchar(50) DEFAULT NULL,
  ADD COLUMN `business_phone` varchar(50) DEFAULT NULL,
  ADD COLUMN `security_question` varchar(255) DEFAULT NULL,
  ADD COLUMN `security_answer` varchar(255) DEFAULT NULL,
  ADD COLUMN `last_updated` datetime DEFAULT NULL,
  ADD COLUMN `departure_date` datetime DEFAULT NULL,
  ADD COLUMN `departure_reason` varchar(255) DEFAULT NULL;

ALTER TABLE `sma_settings`
  ADD COLUMN `gamma_base_path` varchar(255) DEFAULT NULL;
