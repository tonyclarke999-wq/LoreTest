# Remove the unique index that prevents a similar test name accross projects
DROP INDEX `TestSuiteName` ON testsuite;

ALTER TABLE testsuite ADD COLUMN email_ba_owner CHAR(1) NOT NULL AFTER AutoPass;
ALTER TABLE testsuite ADD COLUMN email_qa_owner CHAR(1) NOT NULL AFTER email_ba_owner;