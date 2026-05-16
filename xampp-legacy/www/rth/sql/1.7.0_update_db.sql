# added four columns LockChangeDate, Lock, LockBy, LockComment to testset table
# for the new lock testset feature

ALTER TABLE testset ADD	LockChangeDate varchar(19) default '';
ALTER TABLE testset ADD	Locked varchar(1) NOT NULL default 'N';
ALTER TABLE testset ADD	LockBy varchar(25) default '';
ALTER TABLE testset ADD	LockComment varchar(300) default '';

