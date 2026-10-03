# Change the columns Action, ExpectedResult and ActualResult from table verifyresults
# from data type varchar(255) to text.

ALTER TABLE verifyresults MODIFY COLUMN Action TEXT;
ALTER TABLE verifyresults MODIFY COLUMN ExpectedResult TEXT;
ALTER TABLE verifyresults MODIFY COLUMN ActualResult TEXT;

