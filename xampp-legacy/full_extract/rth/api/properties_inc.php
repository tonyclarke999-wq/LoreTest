<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# --------------------------------------------------
# RTH Configuration Data
# $RCSfile: properties_inc.php,v $ $Revision: 1.45 $
# --------------------------------------------------

# -------------------------------------------
# This file contains all constants. 
# All variables in this file should be defined constants.
# The file is broken up into several sections
# 1) Environment Settings:
#   urls, path info, and database connection information
# 2) Cookie Settings
# 3) Configuration Settings:
#   debug, login, and optimization options, etc
# 4) Database Constants:
#   Table Names for the RTH database
#   Field Names for the RTH database
#   Table Names for the RTH Project database
#   Field Names for the RTH Project database
# 5) Error Codes
# 6) Email config
# -------------------------------------------

# -------------------------------------------
# Environment Information
# -------------------------------------------
if ( isset($_SERVER['PHP_SELF']) && isset($_SERVER['HTTP_HOST']) ) {
    
    $rth_path = split ('/', dirname($_SERVER['PHP_SELF']));
    
     
    if( strpos( $_SERVER['HTTP_HOST'], ":" ) ) {
    	
     	$hostname_array = split( ":", $_SERVER['HTTP_HOST']);
    	$hostname		= $hostname_array[0];
		$port_number 	= $hostname_array[1];
		
		$rth_url = 'http://' . $hostname .':'. $port_number .'/'. $rth_path[1] . '/';
	}
	else {
		$rth_url = 'http://' . $_SERVER['HTTP_HOST'] .'/'. $rth_path[1] . '/';
	}

} else {
    $rth_url = 'http://localhost/rth/' ;
}

define('RTH_URL', $rth_url);


# Set the database type, host, db_name, and provide login information
define('DB_TYPE', 'MySQL');
define('DB_HOST', 'localhost');
define('DB_NAME', 'rth');
define('DB_LOGIN', 'root');
define('DB_PWORD', '');


# FCK EDITOR
define('FCK_EDITOR_BASEPATH', RTH_URL ."fckeditor/");
define('IMG_SRC', RTH_URL ."images/");
define('ICON_SRC', RTH_URL ."images/icons"); # Might not need icons
define('DOC_ROOT', RTH_URL); # do not store documents in web server document root
define('FILE_UPLOAD_PATH', './rth_file_upload/'); # include forwardslash at end of path
define('WINDOW_TITLE', 'RTH - Requirements and Testing Hub');
define('PAGE_TITLE', 'RTH');
define('RTH_VERSION', 'Version 1.7.2');

# -------------------------------------------
# Cookies
# -------------------------------------------
define('USER_COOKIE_NAME','RTH_USER');
define('PWD_COOKIE_NAME','RTH_PWD');
define('COOKIE_PATH', '/'); # set this to something more restrictive if needed
define('COOKIE_DOMAIN', '');
define('COOKIE_EXPIRE_LENGTH', 30000000); # time for cookie to live in seconds (1 year)
define( 'ON', 1 );
define( 'OFF', 0 );

# -------------------------------------------
# Configuration Settings
# -------------------------------------------
define('LOGIN_METHOD', 'MD5'); # Type of encryption used (MD5, PLAIN, LDAP)
define('DEBUG', ON); # used to view debug info at the bottom of each page
define('LOGGING', ON); # not currently used. 
define('USE_JAVASCRIPT', true); # Allow javascript features.
define('USE_FCK_EDITOR', true);  # View textarea as fck editor.  Must have javascript enabled
define('IMPORT_EXPORT_TO_EXCEL', true);  # Set to false and all files will be imported and exported in csv format
define('REMOTE_TEST_EXE', true); # Enable remote test execution
define('NEWLINE', "\n"); # define the newline character for your OS
define('IGNORE_VERSION_FILENAME_VALIDATION', true); # if it's set true, the validation that a new supporting doc version have to be equal to an older version, will be ignored


#--------------------------------------------------------------------------
# Select the appropriate bug tracker
# rth is the default bugtracker but you can also select mantis
# support for bugzilla will follow 
#--------------------------------------------------------------------------

#--------------------------------------------------------------------------
# The urls for rth bug reporting
# Make sure to comment out these lines if you're using mantis
#--------------------------------------------------------------------------
define('BUGTRACKER', 'rth');
define('BUGTRACKER_URL', RTH_URL .'bug_page.php');
define('REPORT_BUG_URL', RTH_URL .'bug_add_page.php');
define('VIEW_BUG_URL', RTH_URL .'bug_detail_page.php');


#--------------------------------------------------------------------------
# The urls for mantis bug reporting
# Set the REPORT_BUG_URL to either the advanced page or the simple view
#	depending on the default you prefer.
#--------------------------------------------------------------------------
/*
define('BUGTRACKER', 'mantis');
define('BUGTRACKER_URL', 'http://localhost/mantis/view_all_bug_page.php');
define('REPORT_BUG_URL', 'http://localhost/mantis/bug_report_advanced_page.php'); // bug_report_page.php 
define('VIEW_BUG_URL', 'http://localhost/mantis/view.php');
*/

#-----------------------------------------------------------------------------------------
# FOR MORE ADVANCED MANTIS INTEGRATION FOLLOW THE DIRECTIONS BELOW
# These instructions are based on version 1.0.5 of mantis
# Take the code between the characters /* and */ and place it in the file listed
# This integration will populate the rth database with the mantis bug_id when you create
#    a bug by clicking the "defect" link in the test execution section of rth.
# This code will also redirect the user from mantis back to rth when the user created
#	a bug by clicking the "defect" link.
#-----------------------------------------------------------------------------------------

# Add the following lines to config_inc.php
# This code will allow for a database connection from mantis to rth
# We are assuming that the db type is the same for rth and mantis (MySQL by default)
/*
#-----------------------------------------------------------------------------------------
# CODE FOR RTH - MANTIS INTEGRATION - config_inc.php
#-----------------------------------------------------------------------------------------
$g_rth_hostname = 'localhost';
$g_rth_database_name = 'rth';
$g_rth_db_username = 'root';
$g_rth_db_password = '';
$g_rth_url = 'http://localhost/rth/results_view_verifications_page.php';
*/

# Copy rth_api.php from the /rth/docs/mantis directory to the /mantis/core directory
# This file will allow for a database connection to rth from within the mantis code.
# Once the file is in the /mantis/core directory, add the following line to core.php
# I added this code after line 105 of core.php
/*
#-----------------------------------------------------------------------------------------
# CODE FOR RTH - MANTIS INTEGRATION - core.php
#-----------------------------------------------------------------------------------------
require_once( $t_core_path.'rth_api.php' );
*/

# Add the following lines to bug_report_advanced_page.php and bug_report_page.php
# Place the lines in the <table> just below the <form> named "report_bug_form"
# This is line 119 of bug_report_advanced_page.php and line 102 of bug_report_page.php
# You will have to place the code in php tags.  
# I can't place the php tags in this file without breaking this file.
/*
#-----------------------------------------------------------------------------------------
# CODE FOR RTH - MANTIS INTEGRATION - bug_report_advanced_page.php and bug_report_page.php
#-----------------------------------------------------------------------------------------
$test_run_id  = $_GET['test_run_id'];
$verify_id	  = $_GET['verify_id'];
print"<input type='hidden' name='test_run_id' value='$test_run_id'/>";
print"<input type='hidden' name='verify_id' value='$verify_id'/>";
*/

# Now add the following lines to bug_report.php.
# You will place these lines somewhere after the bug_create function on line 89.
# The bug_id created by the bug_create function is necessary to populate the rth database with the bug_id.
# I placed the code after helper_call_custom_function( 'issue_create_notify', array( $t_bug_id ) );
# on line 138 of bug_report.php.
/*
#-----------------------------------------------------------------------------------------
# CODE FOR RTH INTEGRATION - bug_report.php
#-----------------------------------------------------------------------------------------
$test_run_id	= $_POST['test_run_id'];
$verify_id		= $_POST['verify_id'];

# Insert bug_id into rth database
if( !is_blank($verify_id) ) {
		
	$query = "UPDATE rth.verifyresults SET defect_id = '$t_bug_id' WHERE VerifyResultsID = '$verify_id'";
	rth_db_query( $query );
}
*/
# You must also alter the logic on line 156 of bug_report.php
# Change lines that read
#		if ( ! $f_report_stay  ) {
#			html_meta_redirect( 'view_all_bug_page.php' );
#		}
# to the logic below.  
# Be sure to enter the correct url for your rth installation.
/*
#-----------------------------------------------------------------------------------------
# CODE FOR RTH INTEGRATION - bug_report.php (approx. line 156)
#-----------------------------------------------------------------------------------------
# Redirect to mantis or rth.  Redirect to rth if the verify_id variable is set.
if ( ! $f_report_stay  ) {

		if( is_blank($verify_id) ) {
			html_meta_redirect( 'view_all_bug_page.php' );
		}
		else {
			html_meta_redirect( config_get('rth_url') );
		}
	}
*/

# END OF MANTIS INTEGRATION=

# -------------------------------------------
# LDAP Settings
# -------------------------------------------
/*
define('LDAP_SERVER', 'ldap://server_name/');
define('LDAP_PORT', '389');
define('LDAP_ID', 'uid=user_id,ou=people,o=in_your,o=org');
define('LDAP_PWD', 'password');
define('LDAP_DN', 'ou=people,o=in_your,o=org');
define('LDAP_PROTOCOL','3');
*/


# --- notice display ---
# Control whether errors of level NOTICE, the lowest level of error,
#  are displayed to the user.  Default is OFF, but turning it ON may 
# be useful while debugging. Leave this set to ON while we developing
define('SHOW_WARNINGS', ON);

# --- warning display ---
# Control whether errors of level WARNING, the middle level of error,
#  are displayed to the user.  Default is ON.  Turning it OFF may
#  hide useful information from the user.
define('SHOW_NOTICES', ON);

# -------------------------------------------
# DEFINE EMAIL PROPERTIES
#	NOTE: Right now the system only works with SMTP.  
#		  You will have to supply a valid SMTP server name for email to work
#		  We will eventually implement something that allows for different mail protocols.
# -------------------------------------------
define('ADMIN_EMAIL', 'rth.admin@yourdomain.com');
define('SEND_EMAIL_NOTIFICATION', true);
define('SMTP_HOST', 'localhost');
define('SMTP_USERNAME', '');
define('SMTP_PASSWORD', '');

# -------------------------------------------
# EMAIL CONFIG FOR BUGS
# The email in the bug section relies on the bugmonitor table and user preferences.  
# The array below determines whether a user is added to the bug_monitor table on certain actions.
# This allows the system administrator to determine whether they want email on specific actions in the bug section
# For instance, by setting 'reporter' = true, whoever reported the bug will appear in the bug_monitor table.
# This does not mean that the user will receive an email on an update to the bug, but that the system
# will look at the users preferences (email on new, email on update, etc) to determine if the user should 
# receive an email.  These are high level preferences which determine what actions will cause the bugmonitor
# table to be populated.
# The email routine works as follows.
# 1. user creates or updates a bug
# 2. the system looks at the particular action taken (did the user update the bug, add a bugnote, etc)
#	 and the array below to determine whether to add a user to the bugmonitor table.  
#		reporter = bug is reported and the logged in user is added to the bug_monitor table
#		assigned_to = bug is assigned to a user and that user is added to the bug_monitor table
#       bugnote = user who adds a bugnote is added to the bug_monitor table
#		status = user who changes the bug status is added to the bug_monitor table
#		update = user who makes a general update to the ticket is added to the bug_monitor table
# 3. The system scans the bug_monitor table for users 
# 4. The system scans the users preferences to see if they want email for that particular action.
#		NOTE: You may wonder why we add users to tbe bug_monitor table if their prefs say they don't want email
#			  for a particular action.  We do this in case the user later changes their email prefs.  If the user
#			  is in the bug_monitor table, they can change their email preferencs and immediately start getting notifications.
# 5. The system removes the logged in user from the list of email recipients if BUG_EMAIL_ON_OWN_ACTIONS = false
# 6. The system sends the email
# ----------------------------------------------
$default_notify_flags = array('reporter'	=> true,  # true = add the reporter to the bug_monitor table.
							  'assigned_to'	=> true,  # true = add user who was assigned to the ticket
							  'bugnote'		=> true,  # true = add user who added a bugnote
							  'status'		=> true,  # true = add user who changed status to bug_monitor table
							  'update'		=> true );  # true = add user when they update any other field on the ticket
								

# Whether user's should receive emails for their own actions in the bug section
define('BUG_EMAIL_ON_OWN_ACTIONS', true);



# used for alternate row colors on tables
define('ROW1_STYLE', 'row-1');
define('ROW2_STYLE', 'row-2');

# -------------------------------------------
# Permissions
# -------------------------------------------
define('ANYBODY',	0);
define('USER',		10);
define('DEVELOPER',	20);
define('MANAGER',	30);
define('ADMIN',		40);
define('NOBODY',	100);

# -------------------------------------------
# DEFINE RECORDS PER PAGE
# -------------------------------------------
define('RECORDS_PER_PAGE_TESTSET_ADD', 100);
define('RECORDS_PER_PAGE_TESTSET_COPY', 100);
define('RECORDS_PER_PAGE_TESTSET_EDIT', 100);
define('RECORDS_PER_PAGE_TEST_REQ_ASSOC', 100);
define('RECORDS_PER_PAGE_PROJECT_EDIT_USERS', 100);
define('RECORDS_PER_PAGE_PROJECT_MANAGE_AREAS', 25);
define('RECORDS_PER_PAGE_PROJECT_MANAGE_ENVIRONMENTS', 25);
define('RECORDS_PER_PAGE_PROJECT_MANAGE_MACHINES', 25);
define('RECORDS_PER_PAGE_PROJECT_MANAGE_REQ_AREA_COVERED', 25);
define('RECORDS_PER_PAGE_PROJECT_MANAGE_REQ_DOC_TYPE', 25);
define('RECORDS_PER_PAGE_PROJECT_MANAGE_REQ_FUNCT', 25);
define('RECORDS_PER_PAGE_PROJECT_MANAGE_TESTTYPE', 25);
define('RECORDS_PER_PAGE_PROJECT_MANAGE_TEST_DOC_TYPE', 25);
define('RECORDS_PER_PAGE_PROJECT_MANAGE_USERS', 25);
define('RECORDS_PER_PAGE_REPORT_FAILED_VERIFIS', 100);
define('RECORDS_PER_PAGE_REQUIREMENTS', 25);
define('RECORDS_PER_PAGE_REQUIREMENT_NOTIFICATIONS', 100);
define('RECORDS_PER_PAGE_TEST_STEPS', 50);
define('RECORDS_PER_PAGE_ARCHIVE_TESTS', 25);
define('RECORDS_PER_PAGE_PROJECT_MANAGE_BUG_CATEGORY', 25);
define('RECORDS_PER_PAGE_PROJECT_MANAGE_BUG_COMPONENT', 25);
define('RECORDS_PER_PAGE_100', 100);
define('RECORDS_PER_PAGE_25', 25);


# -------------------------------------------
# DEFINE TABLES IN TEMPEST DATABASE
# -------------------------------------------
define('AREA_TESTED_TBL', 'testarea');
define('BUILD_TBL', 'build');
define('BUG_TBL', 'bug');
define('BUG_ASSOC_TBL', 'bugassoc');
define('BUG_CATEGORY_TBL', 'bugcategory');
define('BUG_COMPONENT_TBL', 'bugcomponent');
define('BUG_FILE_TBL', 'bugfile');
define('BUG_HISTORY_TBL', 'bughistory');
define('BUG_MONITOR_TBL', 'bugmonitor');
define('BUG_NOTE_TBL', 'bugnote');
#define('BUG_TEST_ASSOC_TBL', 'BugTestAssoc');
define('DISC_TBL', 'discussion');
define('DISC_POST_TBL', 'discussionpost');
define('ENVIRONMENT_TBL', 'testenvironment'); # needed???
define('FEEDBACK_TBL', 'feedback');
define('FIELD_TBL', 'field_tbl');
define('FUNCTION_TBL', 'function_tbl');
define('GUI_CHECK_TBL', 'guichecktable');
define('INDIV_RUN_DOCS_TBL', 'individualrundocs');
define('ISSUES_TBL', 'issues');
define('LOGS_TBL', 'logs');
define('MACH_TBL', 'testmachine'); # needed???
define('MAN_DOC_TYPE_TBL', 'manualdoctype');
define('MAN_TD_TBL', 'manualtestdocs');
define('MAN_TD_VER_TBL', 'manualtestdocs_version');
define('NEWS_TBL', 'news');
define('RELEASE_TBL', 'release_tbl');
define('REQ_TBL', 'requirement');
define('REQ_AREA_COVERAGE_TBL', 'requirementareacoverage');
define('REQ_DOC_TYPE_TBL', 'requirementdocumenttype');
define('REQ_FUNCT_TBL', 'requirementfunctionality');
define('REQ_FUNCT_ASSOC_TBL', 'requirementfunctionality_assoc');
define('REQ_NOTIFY_TBL', 'requirementnotifications');
define('REQ_VERS_TBL', 'requirementversion');
define('REQ_VERS_ASSOC_REL', 'requirementversion_release_assoc');
define('PROJECT_TBL', 'project');
define('PROJECT_USER_ASSOC_TBL', 'project_user_assoc');
define('RESET_PASS_TBL', 'reset_password');
define('SCREEN_TBL', 'screen');
define('STEP_TBL', 'step_tbl');
define('TEST_PLAN', 'testplan');
define('TEST_PLAN_VERSION', 'testplanversion');
define('TS_TBL', 'testset');
define('TEST_TS_ASSOC_TBL', 'testset_testsuite_assoc');
define('TEST_STATUS_TBL', 'teststatus');
define('TEST_STEP_TBL', 'teststep');
define('TEST_TBL', 'testsuite');
define('TEST_FUNC_ASSOC_TBL', 'test_func_assoc');
define('TEST_REQ_ASSOC_TBL', 'testsuite_requirement_assoc');
define('TEST_RESULTS_TBL', 'testsuiteresults');
define('TEST_TYPE_TBL', 'testtype');
define('TEST_VERS_TBL', 'testversion');
define('TEST_WORK_FLOW_TBL', 'testworkflow');
define('USER_TBL', 'user' );
define('VERIFY_RESULTS_TBL', 'verifyresults');
#define('TEST_CASE_RESULTS_TBL', 'testcaseresults');  # not needed


# -------------------------------------------
# DEFINE FIELDS IN TEMPEST DATABASE
# -------------------------------------------

define('PROJECT_ID', 'project_id'); # exists in every table

# -------------------------------------------
# AREA_TESTED TABLE - FIELDS
# -------------------------------------------
define('AREA_TESTED_ID', 'AreaTestedID');
define('AREA_TESTED_PROJ_ID', 'project_id');
define('AREA_TESTED_NAME', 'AreaTestedName');

# -------------------------------------------
# BUILD TABLE - FIELDS
# -------------------------------------------
define('BUILD_ID', "BuildID");
define('BUILD_REL_ID', "ReleaseID");
define('BUILD_NAME', "BuildName");
define('BUILD_DATE_REC', "DateReceived");
define('BUILD_ARCHIVE', "Archive");
define('BUILD_DESCRIPTION', 'Description');

# -------------------------------------------
# DISC TABLE - FIELDS
# -------------------------------------------
define('DISC_ID', 'DiscussionID');
define('DISC_REQ_ID', 'ReqID');
define('DISC_DISCUSSION', 'Discussion');
define('DISC_STATUS', 'Status');
define('DISC_SUBJECT', 'DiscSubject');
define('DISC_AUTHOR', 'Author');
define('DISC_ASSIGN_TO', 'AssignTo');
define('DISC_DATE', 'Date');


# -------------------------------------------
# POST TABLE - FIELDS
# -------------------------------------------
define('POST_ID', 'PostID');
define('POST_DISCUSSION_ID', 'DiscussionID');
define('POST_MESSAGE', 'Post');
define('POST_AUTHOR', 'Author');
define('POST_DATE', 'Date');

# -------------------------------------------
# ENVIRONMENT TABLE - FIELDS
# -------------------------------------------
define('ENVIRONMENT_ID', 'EnvironmentID');
define('ENVIRONMENT_NAME', 'EnvironmentName');
define('ENVIRONMENT_PROJ_ID', 'ProjectID');

# -------------------------------------------
# FEEDBACK TABLE - FIELDS
# -------------------------------------------
define('FEEDBACK_FEEDBACK_ID', 'FeedbackID');
define('FEEDBACK_AUTHOR', 'Author');
define('FEEDBACK_COMMENT', 'Comment');
define('FEEDBACK_PROJECT', 'Project');
define('FEEDBACK_TIMESTAMP', 'TimeStamp');

# -------------------------------------------
# FIELD TABLE - FIELDS
# -------------------------------------------
define('FIELD_ID', 'field_id');
define('FIELD_SCREEN_ID', 'screen_id');
define('FIELD_ORDER', 'field_order');
define('FIELD_NAME', 'field_name');
define('FIELD_DESC', 'field_desc');
define('FIELD_TEXT_ONLY', 'text_only');

# -------------------------------------------
# FUNCTION TABLE - FIELDS
# -------------------------------------------
define('FUNCTION_ID', 'function_id');
define('FUNCTION_NAME', 'function_name');
define('FUNCTION_DATE_CREATED', 'date_created');
define('FUNCTION_LAST_UPDATED', 'last_updated');
define('FUNCTION_LAST_UPDATED_BY', 'last_updated_by');
define('FUNCTION_DESC', 'description');

# -------------------------------------------
# GUI_CHECK TABLE - FIELDS
# -------------------------------------------
define('GUI_CHECK_ID', 'GuiCheckID');
define('GUI_CHECK_NUMBER', 'GuiCheckNumber');
define('GUI_CHECK_ICON_CODE', 'IconCode');
define('GUI_CHECK_TS_UNIQUE_RUN_ID', 'TS_UniqueRunID');
define('GUI_CHECK_OBJECT_NAME', 'ObjectName');
define('GUI_CHECK_EXPECTED_VALUE', 'ExpectedValue');
define('GUI_CHECK_ACTUAL_VALUE', 'ActualValue');
define('GUI_CHECK_STATUS', 'Status');

# -------------------------------------------
# INDIV_RUN_DOCS TABLE - FIELDS
# -------------------------------------------
define('INDIV_RUN_DOCS_UNIQUE_ID', 'UniqueDocID');
define('INDIV_RUN_DOCS_TS_UNIQUE_RUN_ID', 'TS_UniqueRunID');
define('INDIV_RUN_DOCS_TIMESTAMP', 'TimeStamp');
define('INDIV_RUN_DOCS_UPLOADED_BY', 'UploadedBy');
define('INDIV_RUN_DOCS_FILE_NAME', 'FileName');
define('INDIV_RUN_DOCS_DISPLAY_NAME', 'DisplayName');
define('INDIV_RUN_DOCS_COMMENTS', 'Comments');
define('INDIV_RUN_DOCS_LINK', 'Link');

# -------------------------------------------
# ISSUES TABLE - FIELDS
# -------------------------------------------
define('ISSUES_ID', 'id');
define('ISSUES_NAME', 'Name');
define('ISSUES_PRIORITY', 'Priority');
define('ISSUES_STATUS', 'Status');
define('ISSUES_DETAILS', 'Details');
define('ISSUES_OWNER', 'Owner');
define('ISSUES_TIMESTAMP', 'Timestamp');
define('ISSUES_TYPE', 'Type');

# -------------------------------------------
# LOGS TABLE - FIELDS
# -------------------------------------------
define('LOGS_LOG_ID', 'LogID');
define('LOGS_SESSION_ID', 'SessionID');
define('LOGS_DELETION', 'Deletion');
define('LOGS_CREATION', 'Creation');
define('LOGS_UPLOAD', 'Upload');
define('LOGS_TIMESTAMP', 'TimeStamp');
define('LOGS_USER', 'User');
define('LOGS_PAGE', 'Page');
define('LOGS_ACTION', 'Action');

# -------------------------------------------
# MACH_TABLE - FIELDS
# -------------------------------------------
define('MACH_ID', 'MachineID');
define('MACH_PROJ_ID', 'ProjectID');
define('MACH_NAME', 'MachineName');
define('MACH_LOCATION', 'MachineLocation');
define('MACH_IP_ADDRESS', 'MachineIPAddress');

# -------------------------------------------
# MAN_DOC_TYPE TABLE - FIELDS
# -------------------------------------------
define('MAN_DOC_TYPE_ID', 'ManualDocTypeID');
define('MAN_DOC_TYPE_NAME', 'ManualDocTypeName');
define('MAN_DOC_TYPE_PROJ_ID', 'ProjectID');

# -------------------------------------------
# MAN_TD_TBL TABLE - FIELDS
# -------------------------------------------
define('MAN_TD_MANUAL_TEST_ID', 'ManualTestID');
define('MAN_TD_TEST_ID', 'TestID');
define('MAN_TD_DISPLAY_NAME', 'DisplayName');

# -------------------------------------------
# MAN_TD_VER TABLE - FIELDS
# -------------------------------------------
define('MAN_TD_VER_ID', 'VersionID');
define('MAN_TD_VER_MANUAL_TEST_ID', 'ManualTestID');
define('MAN_TD_VER_VERSION', 'Version');
define('MAN_TD_VER_TIME_STAMP', 'TimeStamp');
define('MAN_TD_VER_UPLOADED_BY', 'UploadedBy');
define('MAN_TD_VER_FILENAME', 'FileName');
define('MAN_TEST_DOCS_VERS_COMMENTS', 'Comments');
define('MAN_TEST_DOCS_VERS_MANUAL_DOC_TYPE_NAME', 'ManualDocTypeName');

# -------------------------------------------
# POST TABLE - FIELDS
# -------------------------------------------
define('NEWS_ID', 'NewsID');
define('NEWS_PROJECT_ID', 'project_id');
define('NEWS_SUBJECT', 'Subject');
define('NEWS_BODY', 'Body');
define('NEWS_MODIFIED', 'LastModified');
define('NEWS_POSTER', 'Poster');
define('NEWS_DELETED', 'Deleted');

#-------------------------------------------
# RELEASE TABLE - FIELDS
# -------------------------------------------
define('RELEASE_ID', 'ReleaseID');
define('RELEASE_ARCHIVE', 'Archive');
define('RELEASE_PLATFORM', 'Platform');
define('RELEASE_NAME', 'ReleaseName');
define('RELEASE_DATE_RECEIVED', 'DateReceived');
define('RELEASE_QA_SIGNOFF', 'QASignoff');
define('RELEASE_BA_SIGNOFF', 'BASignoff');
define('RELEASE_QA_SIGNOFF_DATE', 'QASignoffDate');
define('RELEASE_BA_SIGNOFF_DATE', 'BASignoffDate');
define('RELEASE_QA_SIGNOFF_BY', 'QASignoffBy');
define('RELEASE_BA_SIGNOFF_BY', 'BASignoffBy');
define('RELEASE_QA_SIGNOFF_COMMENTS', 'QASignoffComments');
define('RELEASE_BA_SIGNOFF_COMMENTS', 'BASignoffComments');
define('RELEASE_DESCRIPTION', 'Description');
define('RELEASE_PROJECT_ID', 'project_id');

# -------------------------------------------
# REQ_AREA Coverage TABLE - FIELDS
# -------------------------------------------
define('REQ_AREA_COVERAGE_ID', 'ReqAreaCoverageID');
define('REQ_AREA_PROJ_ID', 'ProjectID');
define('REQ_AREA_COVERAGE', 'AreaCoverage');

# -------------------------------------------
# REQ_DOC_TYPE TABLE - FIELDS
# -------------------------------------------
define('REQ_DOC_TYPE_ID', 'ReqDocTypeID');
define('REQ_DOC_TYPE_NAME', 'ReqDocTypeName');
define('REQ_DOC_TYPE_ROOT_DOC', 'RootDocument');
define('REQ_DOC_TYPE_PROJ_ID', 'ProjectID');

# -------------------------------------------
# REQ_FUNCTIONALITY TABLE - FIELDS
# -------------------------------------------
define('REQ_FUNCT_ID', 'FunctionalityID');
define('REQ_FUNCT_NAME', 'FunctionalityName');
define('REQ_FUNCT_PROJ_ID', 'ProjectID');

# -------------------------------------------
# REQ_FUNCTIONALITY_ASSOC TABLE - FIELDS
# -------------------------------------------
define('REQ_FUNCT_ASSOC_ID', 'RequirementFunctionality_AssocID');
define('REQ_FUNCT_ASSOC_REQ_ID', 'RequirementID');
define('REQ_FUNCT_ASSOC_FUNCT_ID', 'RequirementFunctionalityID');

# -------------------------------------------
# REQ_NOTIFICATION TABLE - FIELDS
# -------------------------------------------
define('REQ_NOTIFY_ID', 'ReqNotifyID');
define('REQ_NOTIFY_REQ_ID', 'ReqID');
define('REQ_NOTIFY_USER_ID', 'UserID');

# -------------------------------------------
# REQ TABLE - FIELDS
# -------------------------------------------
define('REQ_PROJECT_ID', 'project_id');
define('REQ_ID', 'ReqID');
define('REQ_FILENAME', 'ReqName');
define('REQ_AREA_COVERED', 'AreaCovered');
define('REQ_TYPE', 'Type');
define('REQ_PARENT', 'Parent');
define('REQ_ROOT', 'RootNode');
define('REQ_LABEL', 'Label');
define('REQ_UNIQUE_ID', 'UniqueID');
define('REQ_FUNCTIONALITY', 'Functionality');
define('REQ_LOCKED', 'Locked');
define('REQ_LOCKED_BY', 'LockedBy');
define('REQ_LOCKED_DATE', 'LockedDate');
define('REQ_REC_FILE', 'RecordOrFile');
define('REQ_PRIORITY', 'Priority');
define('REQ_LAST_UPDATED', 'LastUpdated');

# -------------------------------------------
# REQ_VERS_TBL - FIELDS
# -------------------------------------------
define('REQ_VERS_UNIQUE_ID', 'ReqVersionID');
define('REQ_VERS_REQ_ID', 'ReqID');
define('REQ_VERS_DEFECT_ID', 'defect_id');
define('REQ_VERS_VERSION', 'Version');
define('REQ_VERS_TIMESTAMP', 'TimeStamp');
define('REQ_VERS_UPLOADED_BY', 'Author');
define('REQ_VERS_AUTHOR', 'Author');
define('REQ_VERS_FILENAME', 'FileName');
define('REQ_VERS_COMMENTS', 'Comments');
define('REQ_VERS_STATUS', 'Status');
define('REQ_VERS_SCHEDULED_RELEASE_IMP', 'Scheduled_Release_Implementation');
define('REQ_VERS_SCHEDULED_BUILD_IMP', 'Scheduled_Build_Implementation');
define('REQ_VERS_ACTUAL_RELEASE_IMP', 'Actual_Release_Implementation');
define('REQ_VERS_ACTUAL_BUILD_IMP', 'Actual_Build_Implementation');
define('REQ_VERS_ASSIGN_RELEASE', 'AssignToRelease');
define('REQ_VERS_ASSIGNED_TO', 'AssignedTo');
define('REQ_VERS_DETAIL', 'Detail');
define('REQ_VERS_LATEST', 'Latest');
define('REQ_VERS_REASON_CHANGE', 'ReasonForChange');
define('REQ_VERS_LAST_UPDATED', 'LastUpdated');
define('REQ_VERS_LAST_UPDATED_BY', 'LastUpdatedBy');

# -------------------------------------------
# REQ_VERS_ASSOC_REL TABLE - FIELDS
# -------------------------------------------
define('REQ_VERS_ASSOC_REL_ID', 'RequirementVersion_Release_AssocID');
define('REQ_VERS_ASSOC_REL_REQ_ID', 'RequirementVersionID');
define('REQ_VERS_ASSOC_REL_REL_ID', 'ReleaseID');

# -------------------------------------------
# TEST_CASE_RESULTS TABLE - FIELDS
# -------------------------------------------
/*
define('TEST_CASE_RESULTS_ID', 'TestCaseResultsID');
define('TEST_CASE_RESULTS_LOG_TIME_STAMP', 'LogTimeStamp');
define('TEST_CASE_RESULTS_TEST_STATUS', 'TestStatus');
define('TEST_CASE_RESULTS_STARTEd', 'Started');
define('TEST_CASE_RESULTS_FINISHED', 'Finished');
define('TEST_CASE_RESULTS_CVS_VERSION', 'CVSVersion');
define('TEST_CASE_RESULTS_TC_UNIQUE_RUN_ID', 'TC_UniqueRunID');
define('TEST_CASE_RESULTS_TS_UNIQUE_RUN_ID', 'TS_UniqueRunID');
define('TEST_CASE_RESULTS_TEST_CASE', 'TestCase');
define('TEST_CASE_RESULTS_TEST_PATH', 'TestPath');
define('TEST_CASE_RESULTS_NARRATIVE', 'Narrative');
define('TEST_CASE_RESULTS_RUN_ID', 'RunID');
define('TEST_CASE_RESULTS_TIME_STARTED', 'TimeStarted');
define('TEST_CASE_RESULTS_TIME_FINISHED', 'TimeFinished');
*/

# -------------------------------------------
# SCREEN TABLE - Fields
# -------------------------------------------
define('SCREEN_ID', 'screen_id');
define('SCREEN_PROJ_ID', 'project_id');
define('SCREEN_ORDER', 'screen_order');
define('SCREEN_NAME', 'screen_name');
define('SCREEN_DESC', 'screen_desc');

# -------------------------------------------
# STEP TABLE - Fields
# -------------------------------------------
define('STEP_ID', 'step_id');
define('STEP_FUNCTION_ID', 'function_id');
define('STEP_NO', 'step_number');
define('STEP_ACTION', 'action');
define('STEP_INPUTS', 'inputs');
define('STEP_EXPECTED', 'expected_result');
define('STEP_INFO_STEP', 'step_type');

# -------------------------------------------
# TEST FUNCTION ASSOC TABLE - Fields
# -------------------------------------------
define('TEST_FUNC_ASSOC_ID', 'test_func_assoc_id');
define('TEST_FUNC_TEST_ID', 'test_id');
define('TEST_FUNC_ASSOC_FUNC_ID', 'function_id');
define('TEST_FUNC_ASSOC_ON_ERROR', 'on_error');

# -------------------------------------------
# TEST_PLAN TABLE - Fields
# -------------------------------------------
define('TEST_PLAN_ID', 'TestPlanID');
define('TEST_PLAN_BUILDID', 'BuildID');
define('TEST_PLAN_NAME', 'TestPlanName');

# -------------------------------------------
# TEST_PLAN_VERSION TABLE - Fields
# -------------------------------------------
define('TEST_PLAN_VERSION_ID', 'TestPlanVersionID');
define('TEST_PLAN_VERSION_TESTPLANID', 'TestPlanID');
define('TEST_PLAN_VERSION_VERSION', 'Version');
define('TEST_PLAN_VERSION_UPLOADEDDATE', 'UploadedDate');
define('TEST_PLAN_VERSION_UPLOADEDBY', 'UploadedBy');
define('TEST_PLAN_VERSION_FILENAME', 'FileName');
define('TEST_PLAN_VERSION_COMMMENTS', 'Comments');
define('TEST_PLAN_VERSION_LATEST', 'Latest');

# -------------------------------------------
# TEST_SET TABLE - Fields
# -------------------------------------------
define('TS_ID', 'TestSetID');
define('TS_NAME', 'TestSetName');
define('TS_STATUS', 'TestSetStatus');
define('TS_DESCRIPTION', 'Description');
define('TS_BUILD_ID', 'BuildID');
define('TS_ORDERBY', 'TestSetOrderBy');
define('TS_ARCHIVE', 'Archive');
define('TS_DATE_CREATED', 'DateCreated');
define('TS_SIGNOFF_DATE', 'SignOffDate');
define('TS_SIGNOFF_BY', 'SignOffBy');
define('TS_SIGNOFF_COMMENTS', 'SignOffComments');
define('TS_UNIQUE_ID', 'UniqueID');
define('TS_LOCKCHANGE_DATE','LockChangeDate');
define('TS_LOCK','Locked');
define('TS_LOCK_BY','LockBy');
define('TS_LOCK_COMMENT','LockComment');

#--------------------------------------------
# TEST_TS_ASSOC TABLE - FIELDS
# -------------------------------------------
define('TEST_TS_ASSOC_ID', 'TestSet_TestSuite_AssocID');
define('TEST_TS_ASSOC_TS_ID', 'TestSetID');
define('TEST_TS_ASSOC_TEST_ID', 'TestID');
define('TEST_TS_ASSOC_FINISHED', 'Finished');
define('TEST_TS_ASSOC_TIMESTAMP', 'LogTimeStamp');
define('TEST_TS_ASSOC_ROOT_CAUSE', 'root_cause');
define('TEST_TS_ASSOC_STATUS', 'TestStatus');
define('TEST_TS_ASSOC_ASSIGNED_TO', 'AssignedTo');
define('TEST_TS_ASSOC_COMMENTS', 'Comments');

# -------------------------------------------
# TEST_STATUS TABLE - FIELDS
# -------------------------------------------
define('TEST_STATUS_ID', 'TestStatusID');
define('TEST_STATUS_PROJECT_ID', 'project_id');
define('TEST_STATUS_STATUS', 'TestStatus');

# -------------------------------------------
# TEST TABLE - FIELDS
# -------------------------------------------
define('TEST_ID', 'TestID');
define('TEST_PROJ_ID', 'project_id');
define('TEST_DELETED', 'Deleted');
define('TEST_ARCHIVED', 'Archive');
define('TEST_CODE_REVIEW', 'CodeReview');
define('TEST_BA_APPROVAL', 'BA_Approval');
define('TEST_MANUAL',   'Steps');
define('TEST_AUTOMATED', 'Script');
define('TEST_LR', 'LoadRunner');
define('TEST_EMAIL_BA_OWNER', 'email_ba_owner');
define('TEST_EMAIL_QA_OWNER', 'email_qa_owner');
define('TEST_AUTO_PASS', 'AutoPass');
define('TEST_DURATION', 'Duration');
define('TEST_PURPOSE', 'Purpose');
define('TEST_NAME', 'TestSuiteName');
define('TEST_TESTTYPE', 'TestType');
define('TEST_AREA_TESTED', 'AreaTested');
define('TEST_BA_OWNER', 'BAOwner');
define('TEST_QA_OWNER', 'Scripter');
define('TEST_APPROVED_FOR_AUTO', 'ApprovedForAuto');
define('TEST_PRIORITY', 'Priority');
define('TEST_STATUS', 'Status');
define('TEST_COMMENTS', 'Comments');
define('TEST_TESTER', 'Tester');
define('TEST_ASSIGNED_TO', 'AssignedTo');
define('TEST_ASSIGNED_BY', 'AssignedBy');
define('TEST_DATE_CREATED', 'DateCreated');
define('TEST_DATE_ASSIGNED', 'DateAssigned');
define('TEST_DATE_EXPECTED', 'ExpDateComplete');
define('TEST_DATE_COMPLETE', 'ActDateComplete');
define('TEST_BA_SIGNOFF', 'BASignOff');
define('TEST_SIGNOFF_BY', 'SignoffBy');
define('TEST_SIGNOFF_DATE', 'SignoffDate');
define('TEST_LAST_UPDATED', 'LastUpdated');
define('TEST_LAST_UPDATED_BY', 'LastUpdatedBy');
define('TEST_UNIQUE_ID', 'UniqueID');

# -------------------------------------------
# TEST_RESULTS TABLE - FIELDS
# -------------------------------------------
define('TEST_RESULTS_ID', 'TestSuiteResultsID');
define('TEST_RESULTS_TEST_SET_ID', 'TestSetID');
define('TEST_RESULTS_TEMPEST_TEST_ID', 'TestID');
define('TEST_RESULTS_LOG_TIME_STAMP', 'LogTimeStamp');
define('TEST_RESULTS_TEST_STATUS', 'TestStatus');
define('TEST_RESULTS_ASSIGNED_TO', 'assigned_to');
define('TEST_RESULTS_ROOT_CAUSE', 'root_cause');
define('TEST_RESULTS_COMMENTS', 'test_run_comment');
define('TEST_RESULTS_STARTED', 'Started');
define('TEST_RESULTS_FINISHED', 'Finished');
define('TEST_RESULTS_CVS_VERSION', 'CVSVersion');
define('TEST_RESULTS_CHECKED_FOR_AUTO_PASS', 'CheckedForAutoPass');
define('TEST_RESULTS_OS', 'OS');
define('TEST_RESULTS_SP', 'SP');
define('TEST_RESULTS_N_NUMBER_ID', 'nNumberID');
define('TEST_RESULTS_USER_ID', 'UserID');
define('TEST_RESULTS_MACHINE_NAME', 'MachineName');
define('TEST_RESULTS_TEST_SUITE', 'TestSuite');
define('TEST_RESULTS_TEST_PATH', 'TestPath');
define('TEST_RESULTS_ENVIRONMENT', 'Environment');
define('TEST_RESULTS_RUN_ID', 'RunID');
define('TEST_RESULTS_TS_UNIQUE_RUN_ID', 'TS_UniqueRunID');
define('TEST_RESULTS_TIME_STARTED', 'TimeStarted');
define('TEST_RESULTS_TIME_FINISHED', 'TimeFinished');

# -------------------------------------------
# TEST_STEPS TABLE - FIELDS
# -------------------------------------------
define('TEST_STEP_ID', 'TestStepID');
define('TEST_STEP_TEST_ID', 'TestID');
define('TEST_STEP_VERSION_ID', 'TestVersionID');
define('TEST_STEP_NO', 'TestStep_Number');
define('TEST_STEP_ACTION', 'Action');
define('TEST_STEP_EXPECTED', 'Expected_Result');
define('TEST_STEP_TEST_INPUTS', 'Inputs');
define('TEST_STEP_INFO_STEP', 'StepType');

# -------------------------------------------
# TEST_REQ_ASSOC TABLE - FIELDS
# -------------------------------------------
define('TEST_REQ_ASSOC_ID', 'TestSuite_Requirement_AssocID');
define('TEST_REQ_ASSOC_TEMPEST_TEST_ID', 'TestID');
define('TEST_REQ_ASSOC_REQ_ID', 'ReqID');
define('TEST_REQ_ASSOC_PERCENT_COVERED', 'PercentCovered');

# -------------------------------------------
# TEST_TYPE TABLE - FIELDS
# -------------------------------------------
define('TEST_TYPE_ID', 'TestTypeID');
define('TEST_TYPE_TYPE', 'TestType');
define('TEST_TYPE_PROJ_ID', 'project_id');

# -------------------------------------------
# TEST_VERS TABLE - FIELDS
# -------------------------------------------
define('TEST_VERS_ID', 'TestVersionID');
define('TEST_VERS_TEST_ID', 'TestID');
define('TEST_VERS_NUMBER', 'Version');
define('TEST_VERS_LATEST', 'Latest');
define('TEST_VERS_ACTIVE', 'ActiveVersion');
define('TEST_VERS_COMMENTS', 'Comments');
define('TEST_VERS_STATUS', 'Status');
define('TEST_VERS_ASSIGNED_TO', 'AssignedTo');
define('TEST_VERS_SIGNOFF_BY', 'SignOffBy');
define('TEST_VERS_SIGNOFF_DATE', 'BASignOff');
define('TEST_VERS_AUTHOR', 'Creator');
define('TEST_VERS_DATE_CREATED', 'DateCreated');

# -------------------------------------------
# TEST_WORK_FLOW TABLE - FIELDS
# -------------------------------------------
define('TEST_WORK_FLOW_UNIQUE_TEST_ID', 'UniqueTestID');
define('TEST_WORK_FLOW_COMPLETE', 'Complete');
define('TEST_WORK_FLOW_TEST_NAME', 'TestName');
define('TEST_WORK_FLOW_BA_TO_CREATE_TC', 'BAToCreateTC');
define('TEST_WORK_FLOW_BA_CREATE_DATE_COMP', 'BACreateDateComp');
define('TEST_WORK_FLOW_QA_TO_REVIEW_TV', 'QAToReviewTC');
define('TEST_WORK_FLOW_QA_REVIEW_DATE_COMP', 'QAReviewDateComp');
define('TEST_WORK_FLOW_QA_AUTOMATED', 'QAAutomated');
define('TEST_WORK_FLOW_QA_AUTO_DATE_START', 'QAAutoDateStart');
define('TEST_WORK_FLOW_QA_AUTO_DATE_EXP', 'QAAutoDateExp');
define('TEST_WORK_FLOW_QA_AUTO_DATE_COMP', 'QAAutoDateComp');
define('TEST_WORK_FLOW_BA_TO_APPROVE', 'BAToApprove');
define('TEST_WORK_FLOW_BA_APPROVAL_DATE', 'BAApprovalDate');
define('TEST_WORK_FLOW_COMMENTS', 'Comments');

# -------------------------------------------
# VERIFY_RESULTS TABLE - FIELDS
# -------------------------------------------
define('VERIFY_RESULTS_ID', 'VerifyResultsID');
define('VERIFY_RESULTS_LOG_TIME_STAMP', 'LogTimeStamp');
define('VERIFY_RESULTS_TEST_STATUS', 'TestStatus');
define('VERIFY_RESULTS_LINE_NUMBER', 'LineNumber');
define('VERIFY_RESULTS_TOTAL_PHY_MEM', 'TotalPhyMem');
define('VERIFY_RESULTS_FREE_PHY_MEM', 'FreePhyMem');
define('VERIFY_RESULTS_TOTAL_VIR_MEM', 'TotalVirMem');
define('VERIFY_RESULTS_FREE_VIR_MEM', 'FreeVirMem');
define('VERIFY_RESULTS_CUR_MEM_UTIL', 'CurMemUtil');
define('VERIFY_RESULTS_TOTAL_PAGE_FILE', 'TotalPageFile');
define('VERIFY_RESULTS_FREE_PAGE_FILE', 'FreePageFile');
define('VERIFY_RESULTS_SHOW_CUSTOM_1', 'Custom_1');
define('VERIFY_RESULTS_SHOW_CUSTOM_2', 'Custom_2');
define('VERIFY_RESULTS_SHOW_CUSTOM_3', 'Custom_3');
define('VERIFY_RESULTS_SHOW_CUSTOM_4', 'Custom_4');
define('VERIFY_RESULTS_SHOW_CUSTOM_5', 'Custom_5');
define('VERIFY_RESULTS_SHOW_CUSTOM_6', 'Custom_6');
define('VERIFY_RESULTS_COMMENT', 'Comment');
define('VERIFY_RESULTS_ACTION', 'Action');
define('VERIFY_RESULTS_EXPECTED_RESULT', 'ExpectedResult');
define('VERIFY_RESULTS_ACTUAL_RESULT', 'ActualResult');
define('VERIFY_RESULTS_WINDOW', 'Window');
define('VERIFY_RESULTS_OBJ', 'Object');
define('VERIFY_RESULTS_OBJ_TYPE', 'ObjType');
define('VERIFY_RESULTS_VAL_ID', 'StepNumber');
define('VERIFY_RESULTS_TS_UNIQUE_RUN_ID', 'TS_UniqueRunID');
define('VERIFY_RESULTS_TIMESTAMP', 'TimeStamp');
define('VERIFY_RESULTS_DEFECT_ID', 'defect_id');

# -------------------------------------------
# USER TABLE - FIELDS
# -------------------------------------------
define('USER_ID', 	 'user_id');
define('USER_PWORD', 'password');
define('USER_UNAME', 'username');
define('USER_FNAME', 'first_name');
define('USER_LNAME', 'last_name');
define('USER_PHONE', 'phone');
define('USER_EMAIL', 'email');
define('USER_ADMIN', 'user_admin');
define('USER_DELETED', 'deleted');
define('USER_DEFAULT_PROJECT', 'default_project');

# -------------------------------------------
# PROJECT TABLE - FIELDS #####
# -------------------------------------------
define('PROJ_ID', 'project_id');
define('PROJ_DBNAME', 'db_name');
define('PROJ_NAME', 'project_name');
define('PROJ_DATE_CREATED', 'date_created');
define('PROJ_DELETED', 'deleted');
define('PROJ_REQ_UPLOAD_PATH', 'req_upload_path');
define('PROJ_TEST_UPLOAD_PATH',	'test_upload_path');
define('PROJ_TEST_RUN_UPLOAD_PATH', 'test_run_upload_path');
define('PROJ_TEST_PLAN_UPLOAD_PATH', 'test_plan_upload_path');
define('PROJ_DEFECT_UPLOAD_PATH', 'defect_upload_path');
define('PROJ_USE_FILES', 'use_files');
define('PROJ_STATUS', 'status');
define('PROJ_DESCRIPTION', 'description');
define('PROJ_BUG_URL_UPLOAD_PATH', 'bug_url');
define('PROJ_SHOW_TESTCASE', 'show_testcase');
define('PROJ_SHOW_CUSTOM_1', 'show_custom_1');
define('PROJ_SHOW_CUSTOM_2', 'show_custom_2');
define('PROJ_SHOW_CUSTOM_3', 'show_custom_3');
define('PROJ_SHOW_CUSTOM_4', 'show_custom_4');
define('PROJ_SHOW_CUSTOM_5', 'show_custom_5');
define('PROJ_SHOW_CUSTOM_6', 'show_custom_6');
define('PROJ_SHOW_WINDOW', 'show_window');
define('PROJ_SHOW_OBJECT', 'show_object');
define('PROJ_SHOW_MEM_STATS', 'show_memory_stats');
define('PROJ_SHOW_PRIORITY', 'show_priority');
define('PROJ_SHOW_TEST_INPUT', 'show_test_input');
define('PROJ_TEST_VERSIONS', 'test_versions');

# -------------------------------------------
# PROJECT_USER_ASSOC TABLE - FIELDS
# -------------------------------------------
define('PROJ_USER_ID', 'project_user_assoc_id');
define('PROJ_USER_PROJ_ID', 'project_id');
define('PROJ_USER_USER_ID', 'user_id');
define('PROJ_USER_BA_OWNER', 'ba_owner');
define('PROJ_USER_QA_OWNER', 'qa_tester');
//define('PROJ_USER_DEFAULT_PROJECT', 'default_project');
define('PROJ_USER_DELETE_RIGHTS', 'delete_rights');
define('PROJ_USER_EMAIL_TESTSET', 'email_testset');
define('PROJ_USER_EMAIL_REQ_DISCUSSION', 'email_discussion');
define('PROJ_USER_EMAIL_NEW_BUG', 'email_new_bug');
define('PROJ_USER_EMAIL_UPDATE_BUG', 'email_update_bug');
define('PROJ_USER_EMAIL_ASSIGNED_BUG', 'email_assigned_bug');
define('PROJ_USER_EMAIL_BUGNOTE_BUG', 'email_bugnote_bug');
define('PROJ_USER_EMAIL_STATUS_BUG', 'email_status_bug');
define('PROJ_USER_PROJECT_RIGHTS', 'user_rights');


# -------------------------------------------
# RESET_PASSWORD TABLE - FIELDS
# -------------------------------------------
define('RESET_PASS_ID', 'reset_id');
define('RESET_PASS_LINK', 'reset_link');
define('RESET_PASS_USER', 'user');
define('RESET_PASS_RESET_USED', 'reset_used');
define('RESET_PASS_EXPIRES', 'expires');

# -------------------------------------------
# BUG TABLE - FIELDS
# -------------------------------------------
define('BUG_ID', 'BugID');
define('BUG_PROJECT_ID', 'ProjectID');
define('BUG_CATEGORY', 'Category');
define('BUG_COMPONENT', 'Component');
define('BUG_PRIORITY', 'Priority');
define('BUG_SEVERITY', 'Severity');
define('BUG_CLOSED_REASON_CODE', 'ClosedReasonCode');
define('BUG_STATUS', 'Status');
define('BUG_REPORTER', 'Reporter');
define('BUG_REPORTED_DATE', 'ReportedDate');
define('BUG_ASSIGNED_TO', 'AssignedTo');
define('BUG_ASSIGNED_TO_DEVELOPER', 'AssignedToDeveloper');
define('BUG_CLOSED', 'Closed');
define('BUG_CLOSED_DATE', 'ClosedDate');
define('BUG_TEST_VERIFY_ID', 'TestID');
define('BUG_REQ_VERSION_ID', 'ReqID');
define('BUG_FOUND_IN_RELEASE', 'FoundInRelease');
define('BUG_ASSIGN_TO_RELEASE', 'AssignToRelease');
define('BUG_IMPLEMENTED_IN_RELEASE', 'ImplementedInRelease');
define('BUG_DISCOVERY_PERIOD', 'DiscoveryPeriod');
define('BUG_SUMMARY', 'Summary');
define('BUG_DESCRIPTION', 'Description');

# -------------------------------------------
# BUG MONITOR TABLE - FIELDS
# -------------------------------------------
define('BUG_MONITOR_ID', 'BugMonitorID');
define('BUG_MONITOR_USER_ID', 'UserID');
define('BUG_MONITOR_BUG_ID', 'BugID');
define('BUG_MONITOR_MONITOR', 'Monitor');

# -------------------------------------------
# BUG NOTE TABLE - FIELDS
# -------------------------------------------
define('BUG_NOTE_ID', 'BugNoteID');
define('BUG_NOTE_BUG_ID', 'BugID');
define('BUG_NOTE_AUTHOR', 'Author');
define('BUG_NOTE_DATE_CREATED', 'DateCreated');
define('BUG_NOTE_DETAIL', 'BugNoteDetail');

# -------------------------------------------
# BUG CATEGORY TABLE - FIELDS
# -------------------------------------------
define('CATEGORY_ID', 'CategoryID');
define('CATEGORY_PROJECT_ID', 'ProjectID');
define('CATEGORY_NAME', 'CategoryName');

# -------------------------------------------
# BUG COMPONENT TABLE - FIELDS
# -------------------------------------------
define('COMPONENT_ID', 'ComponentID');
define('COMPONENT_PROJECT_ID', 'ProjectID');
define('COMPONENT_NAME', 'ComponentName');

# -------------------------------------------
# BUG FILE TABLE - FIELDS
# -------------------------------------------
define('BUG_FILE_ID', 'BugFileID');
define('BUG_FILE_BUG_ID', 'BugID');
define('BUG_FILE_UPLOAD_DATE', 'UploadedDate');
define('BUG_FILE_UPLOAD_BY', 'UploadedBy');
define('BUG_FILE_DISPLAY_NAME', 'DisplayName');
define('BUG_FILE_NAME', 'BugFileName');

# -------------------------------------------
# BUG HISTORY TABLE - FIELDS
# -------------------------------------------
define('BUG_HISTORY_ID', 'BugHistoryID');
define('BUG_HISTORY_BUG_ID', 'BugID');
define('BUG_HISTORY_DATE', 'DateModified');
define('BUG_HISTORY_USER', 'UserName');
define('BUG_HISTORY_FIELD', 'Field');
define('BUG_HISTORY_OLD_VALUE', 'OldValue');
define('BUG_HISTORY_NEW_VALUE', 'NewValue');

# -------------------------------------------
# BUG ASSOC TABLE - FIELDS
# -------------------------------------------
define('BUG_ASSOC_ID', 'BugAssocID');
define('BUG_ASSOC_SRC_ID', 'PrimaryID');
define('BUG_ASSOC_DEST_ID', 'SecondaryID');
define('BUG_ASSOC_REL_TYPE', 'RelationshipType');

# -------------------------------------------
# BUG TEST ASSOC TABLE - FIELDS
# -------------------------------------------
/*
define('BUG_TEST_ASSOC_ID', 'BugAssocID');
define('BUG_ID', 'BugID');
define('VERIFICATION_ID', 'VerificationID');
*/

define('BUG_RELATED', 0);
define('BUG_CHILD', 1);
define('BUG_PARENT', 2);


# -------------------------------------------
# Error Codes
# -------------------------------------------
define('INVALID_LOGIN', 10);
define('NO_DEFAULT_PROJ', 20);
define('NO_USER_RIGHTS', 30);
define('NOT_LOGGED_IN', 40);
define('COOKIES_NOT_ENABLED', 50);
define('PROJECT_SWITCH_FAILED', 60);
define('PROJECT_DB_NOT_SET', 70);
define('REQUIRED_FIELD_MISSING', 80);
define('INVALID_DATE', 90);
define('DUPLICATE_TESTNAME', 100);
define('LDAP_CONNECTION_FAILED',110);
define('DUPLICATE_RELEASE_NAME', 120);
define('FAILED_FILE_UPLOAD', 130);
define('PASSWORDS_NOT_MATCH', 140);
define('USERNAME_NOT_UNIQUE', 150);
define('NO_RIGHTS_TO_VIEW_PAGE', 155);
define('NO_REQ_SELECTED', 160);
define('NO_TESTS_SELECTED', 170);
define('DUPLICATE_REQ_NAME', 180);
define('UNABLE_TO_CREATE_PROJECT_FOLDERS', 190);
define('PROJECT_NOT_UNIQUE', 200);
define('ERROR_ACCOUNT_NOT_FOUND', 210);
define('ERROR_CANNOT_RESET_PASSWORD', 220);
define('ERROR_WRONG_HOST', 230);
define('EMAIL_NOT_UNIQUE', 240);
define('NO_RIGHTS_TO_PROJECT', 250);
define('COULD_NOT_CREATE_RELATIONSHIP', 260);
define('NO_BUGS_SELECTED', 270);
define('INVALID_BUG_ID', 280);
define('DUPLICATE_FILE_NAME', 290);
define('DUPLICATE_AREANAME', 300);
define('DUPLICATE_BUILD_NAME', 310);
define('DUPLICATE_TESTSET_NAME', 320);
define('DUPLICATE_TESTAREA', 330);
define('DUPLICATE_TEST_DOC_TYPE', 340);
define('DUPLICATE_ENVIRONMENT_NAME', 350);
define('DUPLICATE_TESTTYPE_NAME', 360);
define('DUPLICATE_MACHINE_NAME', 370);
define('DUPLICATE_IP_ADDRESS', 380);
define('DUPLICATE_REQUIREMENT_FUNCTIONALITY', 390);
define('DUPLICATE_REQUIREMENT_DOCTYPE', 400);
define('DUPLICATE_BUG_CATEGORY', 410);
define('DUPLICATE_BUG_COMPONENT', 420);
define('NO_MATCHING_FILE_NAME', 430);
define('NO_FILE_SPECIFIED', 440);
define('DUPLICATE_SCREEN_NAME', 450);
define('DUPLICATE_FIELD_NAME', 460);
define('USERNAME_CONTAINS_BLANK',470);
define('PROJECT_NOT_EXISTS',480);
define('NO_RIGHTS_TO_VIEW_PROJECT',490);
define('TEST_NOT_EXISTS',500);
define('USERNAME_CONTAINS_INVALID_CHARS',510);
define('PASSWORD_INVALID',520);
define('FAILED_DELETE_DOC',530);
define('TEST_ID_NOT_FOUND',540);
define('TEST_ID_FIELD_EMPTY',550);
define('NUMERIC_ERROR',560);
define('NO_SUFFICIENT_RIGHTS',570);


# -------------------------------------------
# Delete Messages
# -------------------------------------------
define('DEL_TEST_RUN', 10);
define('DEL_RELEASE', 20);
define('DEL_BUILD', 30);
define('DEL_TESTSET', 40);
define('DEL_USER_FROM_PROJECT', 50);
define('DEL_AREA_FROM_PROJECT', 60);
define('DEL_MACHINE_FROM_PROJECT', 70);
define('DEL_TESTTYPE_FROM_PROJECT', 80);
define('DEL_TEST_DOC_TYPE_FROM_PROJECT', 90);
define('DEL_TEST_STEP', 100);
define('DEL_ENVIRONMENT_FROM_PROJECT', 110);
define('DEL_REQ_DOC_TYPE_FROM_PROJECT', 120);
define('DEL_PROJECT', 130);
define('DEL_USER', 140);
define('DEL_REQ_AREA_FROM_PROJECT', 150);
define('DEL_REQUIREMENT', 190);
define('DEL_REQ_FUNCT_FROM_PROJECT', 200);
define('DEL_TEST', 210);
define('DEL_NEWS', 220);
define('DEL_BUG_CATEGORY_FROM_PROJECT', 230);
define('DEL_BUG_COMPONENT_FROM_PROJECT', 240);
define('DEL_BUG', 250);
define('DEL_BUGNOTE', 260);
define('DEL_BUG_ASSOC', 270);
define('DEL_TEST_PLAN', 280);
define('DEL_TEST_RUN_DOC', 290);
define('DEL_SCREEN', 300);
define('DEL_FIELD', 310);


# -------------------------------
# $Log: properties_inc.php,v $
# Revision 1.45  2009/04/02 11:23:40  sca_gs
# version update to 1.7.2
#
# Revision 1.44  2009/01/28 14:38:44  cryobean
# updating version string to 1.7.1
#
# Revision 1.43  2009/01/28 08:36:44  cryobean
# changed window header text
#
# Revision 1.42  2009/01/12 12:02:45  cryobean
# now permissions are checked before switching to test id/project and an error message is shown on the home_page.php and the user doesn't get logged out if there are unsufficient permissions.
#
# Revision 1.41  2008/08/08 09:30:25  peter_thal
# added direct navigate to testid function above project switch select box
#
# Revision 1.40  2008/08/07 11:18:35  cryobean
# changed version number to 1.7.0
#
# Revision 1.39  2008/08/07 10:57:52  peter_thal
# Now blanks are replaced with underscores by adding a new supporting doc
#
# Revision 1.38  2008/07/25 09:50:07  peter_thal
# added lock testset feature
# disabled detail column in test result, because functionality is not implemented yet
#
# Revision 1.37  2008/07/23 14:53:51  peter_thal
# delete supporting docs feature added (linux/unix)
#
# Revision 1.36  2008/07/10 07:28:30  peter_thal
# security update:
# disabled writing spaces or apostrophe and others into login textfields
#
# Revision 1.35  2008/07/09 07:13:20  peter_thal
# added direct linking of test detail by adding project_id link parameter
# added automated project switching if necessary
#
# Revision 1.34  2008/07/01 13:46:24  peter_thal
# now usernames can't contain whitespaces
#
# Revision 1.33  2008/01/31 07:59:53  cryobean
# change version number for release
#
# Revision 1.32  2007/12/11 12:12:35  cryobean
# change version number to 1.6.2
#
# Revision 1.31  2007/03/14 17:45:52  gth2
# removing code that passes varables by reference - gth
#
# Revision 1.30  2007/02/25 23:17:41  gth2
# fixing bugs for release 1.6.1 - gth
#
# Revision 1.29  2007/02/12 07:16:35  gth2
# adding email functionality on test update - gth
#
# Revision 1.28  2007/02/02 04:26:12  gth2
# adding version information to the footer of each page - gth
#
# Revision 1.27  2006/12/05 05:29:19  gth2
# updates for 1.6.1 release
#
# Revision 1.26  2006/10/11 02:41:11  gth2
# adding phpMailer - gth
#
# Revision 1.25  2006/10/05 02:42:18  gth2
# adding file upload to the bug page - gth
#
# Revision 1.24  2006/09/27 23:46:50  gth2
# Adding functionality to link a change request (defect_id) to a requirement - gth
#
# Revision 1.23  2006/09/27 06:09:29  gth2
# correcting case sensativity with FCKeditor - gth
#
# Revision 1.22  2006/09/27 05:37:59  gth2
# adding Mantis integration - gth
#
# Revision 1.21  2006/09/27 05:35:01  gth2
# adding Mantis integration - gth
#
# Revision 1.20  2006/09/25 12:46:37  gth2
# Working on linking rth and other bugtrackers - gth
#
# Revision 1.19  2006/08/05 22:31:46  gth2
# adding NEWLINE constant to support mulitple OS - gth
#
# Revision 1.18  2006/08/05 22:08:36  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.17  2006/08/05 20:31:43  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.16  2006/08/01 23:44:46  gth2
# fixing case sensativity errors reported by users - gth
#
# Revision 1.15  2006/08/01 23:42:56  gth2
# fixing case sensativity errors reported by users - gth
#
# Revision 1.14  2006/06/24 14:34:15  gth2
# updating changes lost with cvs problem.
#
# Revision 1.13  2006/05/03 20:35:35  gth2
# no message
#
# Revision 1.12  2006/04/11 12:11:01  gth2
# create a test version when uploading test steps - gth
#
# Revision 1.11  2006/04/09 16:37:28  gth2
# no message
#
# Revision 1.10  2006/02/27 17:26:16  gth2
# added email functionality to bug tracker - gth
#
# Revision 1.9  2006/02/24 11:33:31  gth2
# minor bug fixes and enhancements for 1.5.1 release - gth
#
# Revision 1.8  2006/02/15 03:11:20  gth2
# correcting case - gth
#
# Revision 1.7  2006/02/09 12:41:46  gth2
# clean up syntax causing NOTICES in php - gth
#
# Revision 1.6  2006/02/06 13:04:23  gth2
# fixing problem with mkdir when creating a new project - gth
#
# Revision 1.5  2006/01/16 13:27:48  gth2
# adding excel integration - gth
#
# Revision 1.4  2006/01/09 02:02:24  gth2
# fixing some defects found while writing help file
#
# Revision 1.3  2005/12/28 23:05:33  gth2
# Updating release table definition - gth
#
# Revision 1.2  2005/12/06 13:56:50  gth2
# Adding requirement priority and last updated - gth
#
# Revision 1.1.1.1  2005/11/30 23:01:12  gth2
# importing initial version - gth
#
# -------------------------------