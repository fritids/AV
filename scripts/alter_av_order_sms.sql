ALTER TABLE  `av_orders` ADD  `alert_sms` INT( 1 ) NOT NULL DEFAULT  '0';

ALTER TABLE  `av_orders` ADD  `alert_sms_phone` VARCHAR( 10 ) NOT NULL AFTER  `alert_sms`
