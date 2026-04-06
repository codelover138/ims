-- Run this once to add Gamma permission columns to sma_permissions.
ALTER TABLE `sma_permissions`
  -- Gamma Workspace (user-facing)
  ADD COLUMN IF NOT EXISTS `gamma-workspace`     tinyint(1) NOT NULL DEFAULT 1,
  ADD COLUMN IF NOT EXISTS `gamma-submit`        tinyint(1) NOT NULL DEFAULT 1,
  ADD COLUMN IF NOT EXISTS `gamma-delete`        tinyint(1) NOT NULL DEFAULT 1,
  ADD COLUMN IF NOT EXISTS `gamma-download`      tinyint(1) NOT NULL DEFAULT 1,
  -- Gamma Forms (admin area)
  ADD COLUMN IF NOT EXISTS `gamma-forms`         tinyint(1) NOT NULL DEFAULT 0,
  ADD COLUMN IF NOT EXISTS `gamma-forms-import-form`   tinyint(1) NOT NULL DEFAULT 0,
  ADD COLUMN IF NOT EXISTS `gamma-forms-import-inputs` tinyint(1) NOT NULL DEFAULT 0,
  ADD COLUMN IF NOT EXISTS `gamma-forms-delete`        tinyint(1) NOT NULL DEFAULT 0,
  ADD COLUMN IF NOT EXISTS `gamma-forms-generate`      tinyint(1) NOT NULL DEFAULT 0;
