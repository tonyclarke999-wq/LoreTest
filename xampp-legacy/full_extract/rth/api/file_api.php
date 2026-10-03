<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# File API
#
# $RCSfile: file_api.php,v $ $Revision: 1.10 $
# ------------------------------------


# ----------------------------------------------------------------------
# Add a new test plan
# ----------------------------------------------------------------------
function file_add_test_plan(	$upload_file_temp_name,
								$upload_file_name,
								$build_id,
								$comments,
								$redirect_page,
								$version="1.0" ) {

	global $db;

	$tbl_test_plan			= TEST_PLAN;
	$f_test_plan_id			= TEST_PLAN . "." . TEST_PLAN_ID;
	$f_test_plan_build_id	= TEST_PLAN . "." . TEST_PLAN_BUILDID;
	$f_test_plan_name		= TEST_PLAN . "." . TEST_PLAN_NAME;

	$tbl_test_plan_version				= TEST_PLAN_VERSION;
	$f_test_plan_version_id				= TEST_PLAN_VERSION . "." . TEST_PLAN_VERSION_ID;
	$f_test_plan_version_test_plan_id	= TEST_PLAN_VERSION . "." . TEST_PLAN_VERSION_TESTPLANID;
	$f_version							= TEST_PLAN_VERSION . "." . TEST_PLAN_VERSION_VERSION;
	$f_uploaded_date					= TEST_PLAN_VERSION . "." . TEST_PLAN_VERSION_UPLOADEDDATE;
	$f_uploaded_by						= TEST_PLAN_VERSION . "." . TEST_PLAN_VERSION_UPLOADEDBY;
	$f_file_name						= TEST_PLAN_VERSION . "." . TEST_PLAN_VERSION_FILENAME;
	$f_comments							= TEST_PLAN_VERSION . "." . TEST_PLAN_VERSION_COMMMENTS;

	$current_date 				= date("Y-m-d H:i:s");
	$project_properties			= session_get_project_properties();
	$file_upload_dir			= $project_properties['test_plan_upload_path'];

	# create a unique name for the file using the time function
	$file_db_name = "TestPlan" . time() . "-$upload_file_name";
	
	
	if($version=="1.0") {
	
		$q = "SELECT ". TEST_PLAN_NAME ." FROM ". TEST_PLAN ." WHERE ". TEST_PLAN_NAME ." = '$upload_file_name' AND ". TEST_PLAN_BUILDID ." = $build_id";

		$rs = db_query($db, $q);

		$num_rows = db_num_rows($db, $rs);

		if($num_rows != 0) {
			error_report_show( $redirect_page, DUPLICATE_FILE_NAME );
		}
	}
	
	
	
	if( !isset($upload_file_temp_name)  ||  !is_uploaded_file($upload_file_temp_name) ) {

		error_report_show($redirect_page, REQUIRED_FIELD_MISSING);
	}

	# The kid forgot to put sql in a function
	$test_plan_buid_id_f		= TEST_PLAN_BUILDID;
	$test_plan_name_f			= TEST_PLAN_NAME;

	if( move_uploaded_file($upload_file_temp_name, $file_upload_dir.$file_db_name) ) {

		$q_insert = "	INSERT INTO $tbl_test_plan
							( $test_plan_buid_id_f, $test_plan_name_f)
						VALUES
							('$build_id', '$upload_file_name')";

		db_query($db, $q_insert);

		# Gets the ID that you just created from the insert, could have used the php insertid function
		# but decided against it as I wasn't sure if the php ODBC functions covered it
		$q_test_plan_id = "	SELECT ". TEST_PLAN_ID ." FROM ". TEST_PLAN ." WHERE ". TEST_PLAN_BUILDID ." = $build_id AND ". TEST_PLAN_NAME ." = '$upload_file_name'";

		$test_plan_id = db_get_one($db, $q_test_plan_id);
		
		# looks like junior forgot to put sql in a function
		$test_plan_id_f				= TEST_PLAN_VERSION_TESTPLANID;
		$test_plan_version_vers_f 	= TEST_PLAN_VERSION_VERSION;
		$uploaded_date_f			= TEST_PLAN_VERSION_UPLOADEDDATE;
		$uploaded_by_f				= TEST_PLAN_VERSION_UPLOADEDBY;
		$file_name_f				= TEST_PLAN_VERSION_FILENAME;
		$comments_f					= TEST_PLAN_VERSION_COMMMENTS;


		$q_version = "INSERT INTO $tbl_test_plan_version
					( $test_plan_id_f, $test_plan_version_vers_f, $uploaded_date_f, $uploaded_by_f, $file_name_f, $comments_f )
					  VALUES
					  	('$test_plan_id', '$version', '$current_date', '". session_get_username() ."', '$file_db_name', '$comments')";

		db_query($db, $q_version);

		$build_name = admin_get_build_name( $build_id );

/*
		# Build up the message and format to be sent
		$subject = $build_name." has had a new document added to it in Tempest";
		$message = "A new document has been added to ".$build_name." in Tempest. Please check the new document and review any changes that may need to be made to the test.";

		email_send(	user_mail_by_pref( PROJ_USER_EMAIL_TESTSET ),
					$subject,
					$message );

		#######################################################################################################
		#Add entry into the log table for the project
		#######################################################################################################
		$page_name = "ADMIN TESTSET";
		$deletion = 'N';
		$creation = 'N';
		$upload = 'Y';
		$action = "ADDED TEST PLAN TEST DOCUMENT $upload_file_temp_name to Build ID - $build_id";

		log_activity( $page_name, $deletion, $creation, $upload, $action );

		#logfile entry end
		#######################################################################################################
*/
	} else {
		error_report_show( $redirect_page, FAILED_FILE_UPLOAD );
	}
}

# ----------------------------------------------------------------------
# Add a new version of a test plan
# ----------------------------------------------------------------------
function file_add_test_plan_version(	$upload_file_temp_name,
										$upload_file_name,
										$build_id,
										$comments,
										$old_test_plan_version_id,
										$version,
										$redirect_page ) {

	global $db;

	$tbl_test_plan_version				= TEST_PLAN_VERSION;
	$f_test_plan_version_id				= TEST_PLAN_VERSION . "." . TEST_PLAN_VERSION_ID;
	$f_latest							= TEST_PLAN_VERSION . "." . TEST_PLAN_VERSION_LATEST;

	file_add_test_plan( $upload_file_temp_name, $upload_file_name, $build_id, $comments, $redirect_page, $version );

	# reset latest flag on old version
	$q = "	UPDATE $tbl_test_plan_version
			SET $f_latest = 'N'
			WHERE $f_test_plan_version_id = $old_test_plan_version_id";

	db_query($db, $q);
}

# ----------------------------------------------------------------------
# Add a new test run document
# ----------------------------------------------------------------------
function file_add_test_run_doc(	$upload_file_temp_name, $upload_file_name,
								$test_run_id, $username, $comments, $redirect_page) {

	global $db;

	$test_run_doc_tbl	= INDIV_RUN_DOCS_TBL;
	$f_test_run_id		= INDIV_RUN_DOCS_TS_UNIQUE_RUN_ID;
	$f_timestamp		= INDIV_RUN_DOCS_TIMESTAMP;
	$f_uploaded_by		= INDIV_RUN_DOCS_UPLOADED_BY;
	$f_filename			= INDIV_RUN_DOCS_FILE_NAME;
	$f_display_name		= INDIV_RUN_DOCS_DISPLAY_NAME;
	$f_comments			= INDIV_RUN_DOCS_COMMENTS;
	$f_link				= INDIV_RUN_DOCS_LINK;

	#print"redirect_page = $redirect_page<br>";

	$current_date 			= date("Y-m-d H:i:s");
	$project_properties		= session_get_project_properties();
	$file_upload_path		= $project_properties['test_run_upload_path'];

	# create a unique name for the file using the time function
	$file_db_name = "TestRun" . time() . "-$upload_file_name";

	if( !isset($upload_file_temp_name)  ||  !is_uploaded_file($upload_file_temp_name) ) {

		#error_report_show($redirect_page, REQUIRED_FIELD_MISSING);
	}
	if( move_uploaded_file($upload_file_temp_name, $file_upload_path.$file_db_name) ) {

		$q_insert = "INSERT INTO $test_run_doc_tbl
					($f_test_run_id, $f_timestamp, $f_uploaded_by, $f_filename,
					 $f_display_name, $f_comments, $f_link )
					 VALUES
					('$test_run_id', '$current_date', '$username',
					 '$upload_file_name', '$file_db_name', '$comments', '')";
		db_query($db, $q_insert);


	} else {
		#error_report_show( $redirect_page, FAILED_FILE_UPLOAD );
	}
}

function file_delete_test_run_doc( $id ) {

	
	global $db;
	
	$project_properties		= session_get_project_properties();
	$file_upload_path		= $project_properties['test_run_upload_path'];
	
	$test_run_doc_tbl				= INDIV_RUN_DOCS_TBL;
	$f_test_run_doc_id				= INDIV_RUN_DOCS_UNIQUE_ID;
	$f_test_run_doc_display_name	= INDIV_RUN_DOCS_DISPLAY_NAME;
	
	# Get test run doc filename
	$q = "SELECT $f_test_run_doc_display_name FROM $test_run_doc_tbl WHERE
		  $f_test_run_doc_id = '$id' ";

	$rs = db_query($db, $q);
	$row = db_fetch_row($db, $rs);
			
	# delete test run doc
	$file = $file_upload_path.$row[INDIV_RUN_DOCS_DISPLAY_NAME];
	unlink($file);

	# delete test run doc record
	$q	= "	DELETE FROM $test_run_doc_tbl WHERE
				$f_test_run_doc_id = '$id' ";

	db_query($db, $q);
	
}


function file_add_requirement( $redirect_page ) {


	$project_properties			= session_get_project_properties();
	$file_upload_dir			= $project_properties['req_upload_path'];

	$upload_file_temp_name = $_FILES['upload_file']['tmp_name'];

	# create a unique name for the file using the time function
	$file_db_name = "Req_" . time() . "_".$_FILES['upload_file']['name'];

	if( !isset($upload_file_temp_name)  ||  !is_uploaded_file($upload_file_temp_name) ) {

		error_report_show($redirect_page, REQUIRED_FIELD_MISSING);
	}

	if( move_uploaded_file($upload_file_temp_name, $file_upload_dir.$file_db_name) ) {

		return $file_db_name;
	} else {
		error_report_show( $redirect_page, FAILED_FILE_UPLOAD );
	}
}


#-----------------------------------------------------------------------------
# Upload a supporting file to the test section
#
#-----------------------------------------------------------------------------
function file_add_supporting_test_doc($upload_file_temp_name, $upload_file_name, $test_id, $comments, $file_type) {

	global $db;

	$project_properties		= session_get_project_properties();
	$file_upload_path		= $project_properties['test_upload_path'];

	# create a unique name for the file using the time function
	$file_db_name = "SupportingTestDoc" . time() . "-$upload_file_name";

	if( !isset($upload_file_temp_name)  ||  !is_uploaded_file($upload_file_temp_name) ) {

		#error_report_show($redirect_page, REQUIRED_FIELD_MISSING);
	}
	
	if( move_uploaded_file($upload_file_temp_name, $file_upload_path.$file_db_name) ) {

		$man_td_tbl			= MAN_TD_TBL;
		$f_test_id			= MAN_TD_TEST_ID;
		$f_display_name		= MAN_TD_DISPLAY_NAME;

		$q_insert = "INSERT INTO $man_td_tbl ($f_test_id, $f_display_name) VALUES ('$test_id', '$upload_file_name')";
		db_query($db, $q_insert);

		$manualTestID		= mysql_insert_id();
		$current_date		= date("Y-m-d H:i:s");
		$s_user				= session_get_user_properties();
		$username			= $s_user['username'];

		$td_vers_tbl		= MAN_TD_VER_TBL;
		$f_man_test_id		= MAN_TD_VER_MANUAL_TEST_ID;
		$f_file_name		= MAN_TD_VER_FILENAME;
		$f_timestamp		= MAN_TD_VER_TIME_STAMP;
		$f_version			= MAN_TD_VER_VERSION;
		$f_username			= MAN_TD_VER_UPLOADED_BY;
		$f_comments			= MAN_TEST_DOCS_VERS_COMMENTS;
		$f_file_type		= MAN_TEST_DOCS_VERS_MANUAL_DOC_TYPE_NAME;

		$query_5 = "INSERT INTO $td_vers_tbl 
		           ($f_man_test_id, $f_file_name, $f_timestamp, $f_version, $f_username, $f_comments, $f_file_type) 
				   VALUES ('$manualTestID', '$file_db_name', '$current_date', '1', '$username', '$comments', '$file_type')";
		//print"$query_5<br>";
		$db->Execute($query_5);



	} else {
		#error_report_show( $redirect_page, FAILED_FILE_UPLOAD );
	}
}


#-----------------------------------------------------------------------------
# Find out if a file name exists when given a test_id and file name
#
#-----------------------------------------------------------------------------
function file_name_exists( $test_id, $file_name ) {

	global $db;	
	$man_td_tbl				= MAN_TD_TBL;
	$f_manual_test_id		= MAN_TD_TEST_ID;
	$f_display_name			= MAN_TD_DISPLAY_NAME;

	$q = "SELECT $f_display_name
	      FROM $man_td_tbl
		  WHERE $f_manual_test_id = '$test_id'
		  AND $f_display_name = '$file_name'";

	$rs = db_query( $db, $q );
	$num = db_num_rows( $db, $rs );

	if( $num > 0 ) 
		return true;
	else
		return false;

}


#-----------------------------------------------------------------------------
# Find out if a file name exists when given a test_id and file name
#
#-----------------------------------------------------------------------------
function file_add_supporting_test_doc_version($file_temp_name, $file_name, $test_id, $manual_test_id, $comments, $file_type ) {

	global $db;
	$project_properties		= session_get_project_properties();
	$file_upload_path		= $project_properties['test_upload_path'];
	$project_id				= $project_properties['project_id'];

	$redirect_page			= "test_detail_page.php?test_id=$test_id&project_id=$project_id&tab=2";
	$redirect_on_error		= "test_add_doc_version_page.php?test_id=$test_id&manual_test_id=$manual_test_id";

	$file_db_name			= "";
	# Check to make sure the file name is the same as the previous version
	if( IGNORE_VERSION_FILENAME_VALIDATION || file_name_exists( $test_id, $file_name ) )  {

		# create a unique name for the file using the time function
		$file_db_name = "SupportingTestDoc" . time() . "-$file_name";

		# Get the current file version and increment it by one
		$current_file_version = test_get_max_file_version( $manual_test_id );
		$file_version = util_increment_version( $current_file_version );

		# Make sure the temp file was uploaded successfully
		if( move_uploaded_file($file_temp_name, $file_upload_path.$file_db_name) ) {

			$current_date		= date("Y-m-d H:i:s");
			$s_user				= session_get_user_properties();
			$username			= $s_user['username'];

			$td_vers_tbl		= MAN_TD_VER_TBL;
			$f_man_test_id		= MAN_TD_VER_MANUAL_TEST_ID;
			$f_file_name		= MAN_TD_VER_FILENAME;
			$f_timestamp		= MAN_TD_VER_TIME_STAMP;
			$f_version			= MAN_TD_VER_VERSION;
			$f_username			= MAN_TD_VER_UPLOADED_BY;
			$f_comments			= MAN_TEST_DOCS_VERS_COMMENTS;
			$f_file_type		= MAN_TEST_DOCS_VERS_MANUAL_DOC_TYPE_NAME;

			$q = "INSERT INTO $td_vers_tbl 
				($f_man_test_id, $f_file_name, $f_timestamp, $f_version, $f_username, $f_comments, $f_file_type) 
				VALUES ('$manual_test_id', '$file_db_name', '$current_date', '$file_version', '$username', '$comments', '$file_type')";
			//print"$q<br>";
			$db->Execute($q);

		}
	    else { # The file wasn't uploaded to the temp directory successfully
			error_report_show( $redirect_page, FAILED_FILE_UPLOAD );
		}

	}
	else {  # The file name of the new version doesn't match the old
		error_report_show( $redirect_on_error, NO_MATCHING_FILE_NAME );
	}
	
}
# ----------------------------------------------------------------------
# delete file from directory and unlink it from database
# ----------------------------------------------------------------------
function file_delete_unlink_file($filename,$upload_path){	
	global $db;
	$manual_test_version_tbl	= MAN_TD_VER_TBL;
	$tbl_filename				= MAN_TD_VER_FILENAME;
	$manual_test_tbl			= MAN_TD_TBL;
	$man_test_id_ver 			= MAN_TD_VER_MANUAL_TEST_ID;
	$man_test_id				= MAN_TD_MANUAL_TEST_ID;
	
	$hq = "select $man_test_id_ver from $manual_test_version_tbl 
			where $tbl_filename = '$filename'";
	$mtestid = db_get_one($db,$hq);
	
	$q1 = "select count($tbl_filename) from $manual_test_version_tbl 
			where $man_test_id = $mtestid";
	$count = db_get_one($db,$q1);
	
	if( $count <= 1){
		$q = "delete from $manual_test_tbl 
				where $man_test_id = $mtestid";
		$db->Execute($q);
	}
	$q2 = "delete from $manual_test_version_tbl
			where $tbl_filename = '$filename'";

	$db->Execute($q2);
	
	exec("rm -f ".$upload_path.$filename);
}
# ----------------------------------------------------------------------
# returns the filename rows that are matching
# ----------------------------------------------------------------------
function file_get_filenames_by_testid($man_test_id){
	global $db;
	$manual_test_version_tbl	= MAN_TD_VER_TBL;
	$tbl_filename				= MAN_TD_VER_FILENAME;
	$man_test_id_ver 			= MAN_TD_VER_MANUAL_TEST_ID;
	
	$q = "select $tbl_filename from $manual_test_version_tbl " .
			"where $man_test_id_ver = $man_test_id";
	
	
	$rs = db_query($db, $q);
	$arr = db_fetch_array($db, $rs);

	return $arr;
}


# ----------------------------------------------------------------------
# Add a new file to the bugfile table
# ----------------------------------------------------------------------
function file_add_bug_file(	$upload_file_temp_name,
							$upload_file_name,
							$bug_id,
							$redirect_page ) {

	global $db;
	$bug_file_tbl			= BUG_FILE_TBL;
	$f_bug_id				= BUG_FILE_TBL .".". BUG_FILE_BUG_ID;
	$f_uploaded_by			= BUG_FILE_TBL .".". BUG_FILE_UPLOAD_DATE;
	$f_uploaded_date		= BUG_FILE_TBL .".". BUG_FILE_UPLOAD_BY;
	$f_display_name			= BUG_FILE_TBL .".". BUG_FILE_DISPLAY_NAME;
	$f_file_name			= BUG_FILE_TBL .".". BUG_FILE_NAME;

	$uploaded_by			= session_get_username();
	$current_date 			= date("Y-m-d H:i:s");
	$project_properties		= session_get_project_properties();
	$file_upload_dir		= $project_properties['defect_upload_path'];

	# create a unique name for the file using the time function
	$file_db_name = "BugFile" . time() . "-$upload_file_name";

	# Throw error if user didn't enter a file name
	if( !isset($upload_file_temp_name)  ||  !is_uploaded_file($upload_file_temp_name) ) {

		error_report_show($redirect_page, REQUIRED_FIELD_MISSING);
	}

	# Insert a record into the bugfile table if the file upload was successful
	if( move_uploaded_file($upload_file_temp_name, $file_upload_dir.$file_db_name) ) {
		$q = "INSERT INTO $bug_file_tbl
				( $f_bug_id, $f_uploaded_by, $f_uploaded_date, $f_display_name, $f_file_name)
			  VALUES
				('$bug_id', '$uploaded_by', '$current_date', '$upload_file_name', '$file_db_name')";
		
		//print"$q<br>";

		db_query($db, $q);

	}
	
}

# ------------------------------
# $Log: file_api.php,v $
# Revision 1.10  2008/08/07 10:57:52  peter_thal
# Now blanks are replaced with underscores by adding a new supporting doc
#
# Revision 1.9  2008/07/23 14:53:51  peter_thal
# delete supporting docs feature added (linux/unix)
#
# Revision 1.8  2008/07/17 13:54:12  peter_thal
# added new feature: test sets status (overview)
# +fixed some bugs with project_id parameter in testdetail_page references
#
# Revision 1.7  2008/07/09 07:13:20  peter_thal
# added direct linking of test detail by adding project_id link parameter
# added automated project switching if necessary
#
# Revision 1.6  2006/10/05 02:42:18  gth2
# adding file upload to the bug page - gth
#
# Revision 1.5  2006/04/11 12:11:01  gth2
# create a test version when uploading test steps - gth
#
# Revision 1.4  2006/02/15 03:31:34  gth2
# correcting case in sql - gth
#
# Revision 1.3  2006/02/09 12:34:26  gth2
# changing db field names for consistency - gth
#
# Revision 1.2  2006/01/09 02:02:24  gth2
# fixing some defects found while writing help file
#
# Revision 1.1.1.1  2005/11/30 23:01:11  gth2
# importing initial version - gth
#
# ------------------------------
?>