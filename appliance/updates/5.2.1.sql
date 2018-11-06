USE notifier;
ALTER TABLE rules ADD COLUMN tracking TINYINT(1) NOT NULL DEFAULT 0 AFTER timeperiod_id;
UPDATE rules SET tracking=0;