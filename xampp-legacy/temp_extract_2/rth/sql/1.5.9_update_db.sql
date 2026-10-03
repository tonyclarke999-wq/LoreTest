### YOU MUST ALSO ADD A DIRECTORY TO YOUR FILE UPLOAD PATH.  THE DIRECTORY SHOULD BE THE FILE UPLOAD PATH AS DESCRIBED IN 
### properties_inc.php FOLLOWED BY THE DIRECTORY NAME 
##  /[PROJECTNAME]_defect_docs
### WITHOUT THIS THE FILE UPLOAD WILL NOT WORK.  YOU MUST ALSO ADD THE PATH TO THE PROJECT TABLE

ALTER TABLE project ADD defect_upload_path varchar(255) NOT NULL default '' AFTER test_plan_upload_path;

UPDATE project SET defect_upload_path = '[YOUR_FILE_UPLOAD_PATH]/PROJECTNAME_defect_docs/' WHERE project_name = '[YOUR PROJECT NAME]';

CREATE TABLE `bugfile` (
  `BugFileID` int(8) unsigned NOT NULL auto_increment,
  `BugID` int(8) unsigned default NULL,
  `UploadedDate` varchar(19) default NULL,
  `UploadedBy` varchar(30) default NULL,
  `DisplayName` varchar(255) default NULL,
  `BugFileName` varchar(255) default NULL,
  PRIMARY KEY  (`BugFileID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;




