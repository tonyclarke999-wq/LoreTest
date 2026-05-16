<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Project API
#
# $RCSfile: project_api.php,v $ $Revision: 1.12 $
# ------------------------------------

# ----------------------------------------------------------------------
# Return details of all projects
# ----------------------------------------------------------------------
function project_get_all_projects_details($order_by=null, $order_dir=null) {

	global $db;
	$project_tbl 		= PROJECT_TBL;
	$f_proj_id			= $project_tbl .'.'. PROJ_ID;
	$f_proj_name		= $project_tbl .'.'. PROJ_NAME;
	$f_proj_status		= $project_tbl .'.'. PROJ_STATUS;
	$f_date_created		= $project_tbl .'.'. PROJ_DATE_CREATED;
	$f_proj_desc		= $project_tbl .'.'. PROJ_DESCRIPTION;
	$f_proj_deleted		= $project_tbl .".". PROJ_DELETED;

	$f_show_custom_1		= $project_tbl .'.'. PROJ_SHOW_CUSTOM_1;
	$f_show_custom_2		= $project_tbl .'.'. PROJ_SHOW_CUSTOM_2;
	$f_show_custom_3		= $project_tbl .'.'. PROJ_SHOW_CUSTOM_3;
	$f_show_custom_4		= $project_tbl .'.'. PROJ_SHOW_CUSTOM_4;
	$f_show_custom_5		= $project_tbl .'.'. PROJ_SHOW_CUSTOM_5;
	$f_show_custom_6		= $project_tbl .'.'. PROJ_SHOW_CUSTOM_6;
	$f_show_window			= $project_tbl .'.'. PROJ_SHOW_WINDOW;
	$f_show_object			= $project_tbl .'.'. PROJ_SHOW_OBJECT;
	$f_show_mem_stats		= $project_tbl .'.'. PROJ_SHOW_MEM_STATS;
	$f_show_priority		= $project_tbl .'.'. PROJ_SHOW_PRIORITY;
	$f_show_test_input		= $project_tbl .'.'. PROJ_SHOW_TEST_INPUT;

	$q = "	SELECT	$f_proj_id,
					$f_proj_name,
					$f_proj_status,
					$f_date_created,
					$f_proj_desc,
					$f_show_custom_1,
					$f_show_custom_2,
					$f_show_custom_3,
					$f_show_custom_4,
					$f_show_custom_5,
					$f_show_custom_6,
					$f_show_window,
					$f_show_object,
					$f_show_mem_stats,
					$f_show_priority,
					$f_show_test_input
		  	FROM	$project_tbl
		  	WHERE 	$f_proj_deleted != 'Y'";

	if( isset( $order_by ) && isset( $order_dir ) ) {
		$order_clause = " ORDER BY $order_by $order_dir";
	} else {
		$order_clause = " ORDER BY $f_proj_name ASC";
	}

	$q .= $order_clause;

 	$rs = db_query( $db, $q );

	return db_fetch_array($db, $rs);
}

# ----------------------------------------------------------------------
# Return the name of a project based on its id
# ----------------------------------------------------------------------
function project_get_name( $project_id ) {

	global $db;

	$project_tbl 				= PROJECT_TBL;
	$f_proj_id					= $project_tbl .'.'. PROJ_ID;
	$f_proj_name				= $project_tbl .'.'. PROJ_NAME;

	$q = "	SELECT	$f_proj_name
			FROM	$project_tbl
			WHERE	$f_proj_id = '$project_id'";

	$row = db_fetch_row($db, db_query($db, $q));
	return $row[PROJ_NAME];
}

# ----------------------------------------------------------------------
# Return the id of a project based on its name
# ----------------------------------------------------------------------
function project_get_id( $project_name ) {

	global $db;

	$project_tbl 		= PROJECT_TBL;
	$f_proj_id			= $project_tbl .'.'. PROJ_ID;
	$f_proj_name		= $project_tbl .'.'. PROJ_NAME;

	$q = "	SELECT	$f_proj_id
			FROM	$project_tbl
			WHERE	$f_proj_name = '$project_name'";

	$row = db_fetch_row($db, db_query($db, $q));
	return $row[PROJ_ID];
}

# ---------------------------------------------------------------
# Return the details for a specific project
# ---------------------------------------------------------------
function project_get_details( $project_id ) {

	global $db;
	$project_tbl 				= PROJECT_TBL;
	$f_proj_id					= $project_tbl .'.'. PROJ_ID;
	$f_proj_name				= $project_tbl .'.'. PROJ_NAME;
	$f_proj_status				= $project_tbl .'.'. PROJ_STATUS;
	$f_date_created				= $project_tbl .'.'. PROJ_DATE_CREATED;
	$f_proj_desc				= $project_tbl .'.'. PROJ_DESCRIPTION;
	$f_show_custom_1			= $project_tbl .'.'. PROJ_SHOW_CUSTOM_1;
	$f_show_custom_2			= $project_tbl .'.'. PROJ_SHOW_CUSTOM_2;
	$f_show_custom_3			= $project_tbl .'.'. PROJ_SHOW_CUSTOM_3;
	$f_show_custom_4			= $project_tbl .'.'. PROJ_SHOW_CUSTOM_4;
	$f_show_custom_5			= $project_tbl .'.'. PROJ_SHOW_CUSTOM_5;
	$f_show_custom_6			= $project_tbl .'.'. PROJ_SHOW_CUSTOM_6;
	$f_show_window				= $project_tbl .'.'. PROJ_SHOW_WINDOW;
	$f_show_object				= $project_tbl .'.'. PROJ_SHOW_OBJECT;
	$f_show_mem_stats			= $project_tbl .'.'. PROJ_SHOW_MEM_STATS;
	$f_show_priority			= $project_tbl .'.'. PROJ_SHOW_PRIORITY;
	$f_show_test_input			= $project_tbl .'.'. PROJ_SHOW_TEST_INPUT;
	$f_req_upload_path			= $project_tbl .'.'. PROJ_REQ_UPLOAD_PATH;
	$f_test_upload_path			= $project_tbl .'.'. PROJ_TEST_UPLOAD_PATH;
	$f_test_run_upload_path		= $project_tbl .'.'. PROJ_TEST_RUN_UPLOAD_PATH;
	$f_test_plan_upload_path	= $project_tbl .'.'. PROJ_TEST_PLAN_UPLOAD_PATH;
	$f_defect_upload_path		= $project_tbl .'.'. PROJ_DEFECT_UPLOAD_PATH;

	$q = "	SELECT	$f_proj_id,
					$f_proj_name,
					$f_proj_status,
					$f_date_created,
					$f_proj_desc,
					$f_show_custom_1,
					$f_show_custom_2,
					$f_show_custom_3,
					$f_show_custom_4,
					$f_show_custom_5,
					$f_show_custom_6,
					$f_show_window,
					$f_show_object,
					$f_show_mem_stats,
					$f_show_priority,
					$f_show_test_input,
					$f_req_upload_path,
					$f_test_upload_path,
					$f_test_run_upload_path,
					$f_test_plan_upload_path,
					$f_defect_upload_path
		  	FROM	$project_tbl
		  	WHERE	$f_proj_id = $project_id";

	return db_fetch_row( $db, db_query($db, $q) );
}

# ---------------------------------------------------------------------------------
# Return an array with the project statuses
# We should review the statuses
# The last value is blank so that a list box contains a blank as the first option
# ---------------------------------------------------------------------------------
function project_get_statuses() {

	$statuses = array('Development', 'Production Support', 'Stable', 'Obsolete', '');

	return $statuses;
}

# ---------------------------------------------------------------------------------
# Return an array with the project view statuses ( Enabled, Disabled )
# Disabled projects will only appear to admin users, Enabled will appear to all others
# ---------------------------------------------------------------------------------
function project_get_view_statuses() {

	$view_statuses = array('Enabled', 'Disabled');

	return $view_statuses;
}

# ---------------------------------------------------------------------------------
# Edit the details of a project
# ---------------------------------------------------------------------------------
function project_edit( $project_id, $name,
									$description,
									$status,
									$custom1,
									$custom2,
									$custom3,
									$custom4,
									$custom5,
									$custom6,
									$window,
									$object,
									$mem_stats,
									$priority,
									$test_input ) {

	$tbl_project			= PROJECT_TBL;
	$f_proj_id				= $tbl_project .".". PROJ_ID;
	$f_proj_name			= $tbl_project .".". PROJ_NAME;
	$f_proj_description		= $tbl_project .".". PROJ_DESCRIPTION;
	$f_proj_status			= $tbl_project .".". PROJ_STATUS;
	$f_show_custom1			= $tbl_project .".". PROJ_SHOW_CUSTOM_1;
	$f_show_custom2			= $tbl_project .".". PROJ_SHOW_CUSTOM_2;
	$f_show_custom3			= $tbl_project .".". PROJ_SHOW_CUSTOM_3;
	$f_show_custom4			= $tbl_project .".". PROJ_SHOW_CUSTOM_4;
	$f_show_custom5			= $tbl_project .".". PROJ_SHOW_CUSTOM_5;
	$f_show_custom6			= $tbl_project .".". PROJ_SHOW_CUSTOM_6;
	$f_show_window			= $tbl_project .".". PROJ_SHOW_WINDOW;
	$f_show_object			= $tbl_project .".". PROJ_SHOW_OBJECT;
	$f_show_mem_stats		= $tbl_project .".". PROJ_SHOW_MEM_STATS;
	$f_show_priority		= $tbl_project .".". PROJ_SHOW_PRIORITY;
	$f_show_test_input		= $tbl_project .".". PROJ_SHOW_TEST_INPUT;

	$q = "	UPDATE $tbl_project
			SET
				$f_proj_name 			= '$name',
				$f_proj_description 	= '$description',
				$f_proj_status		 	= '$status',
				$f_show_custom1			= '$custom1',
				$f_show_custom2			= '$custom2',
				$f_show_custom3			= '$custom3',
				$f_show_custom4			= '$custom4',
				$f_show_custom5			= '$custom5',
				$f_show_custom6			= '$custom6',
				$f_show_window			= '$window',
				$f_show_object			= '$object',
				$f_show_mem_stats		= '$mem_stats',
				$f_show_priority		= '$priority',
				$f_show_test_input		= '$test_input'

			WHERE $f_proj_id = $project_id";

    global $db;

    db_query($db, $q);
}

#----------------------------------------------------------------------------------
# Edit the file upload directory for a project
# this will only happen when a user renames a project
# We need to rename the file upload directories when a user renames the project
#----------------------------------------------------------------------------------
function project_edit_file_upload_path(	$project_id, 
										$req_docs_dir,
										$test_docs_dir,
										$test_run_docs_dir,
										$test_plan_docs_dir,
										$defect_docs_dir ) {

	global $db;
	$project_tbl			= PROJECT_TBL;
	$f_proj_id	 			= PROJ_ID;
	$f_req_upload 			= PROJ_REQ_UPLOAD_PATH;
	$f_test_upload	 		= PROJ_TEST_UPLOAD_PATH;
	$f_test_run_upload		= PROJ_TEST_RUN_UPLOAD_PATH;
	$f_test_plan_upload		= PROJ_TEST_PLAN_UPLOAD_PATH;
	$f_defect_upload		= PROJ_DEFECT_UPLOAD_PATH;
	
	$q = "UPDATE $project_tbl
		     SET
			$f_req_upload = '$req_docs_dir',
			$f_test_upload = '$test_docs_dir',
			$f_test_run_upload = '$test_run_docs_dir',
			$f_test_plan_upload = '$test_plan_docs_dir',
			$f_defect_upload = '$defect_docs_dir'
		  WHERE $f_proj_id = $project_id";


    db_query($db, $q);

}

# ---------------------------------------------------------------------------------
# Add a project to RTH
# ---------------------------------------------------------------------------------
function project_add( 	$name,
						$description,
						$status,
						$custom1,
						$custom2,
						$custom3,
						$custom4,
						$custom5,
						$custom6,
						$window,
						$object,
						$mem_stats,
						$priority,
						$test_input,
						$req_upload,
						$test_upload,
						$test_run_upload,
						$test_plan_upload,
						$defect_upload ) {

	global $db;
	$tbl_project			= PROJECT_TBL;
	$f_proj_id				= $tbl_project .".". PROJ_ID;
	$f_proj_name			= $tbl_project .".". PROJ_NAME;
	$f_proj_description		= $tbl_project .".". PROJ_DESCRIPTION;
	$f_proj_status			= $tbl_project .".". PROJ_STATUS;
	$f_show_custom1			= $tbl_project .".". PROJ_SHOW_CUSTOM_1;
	$f_show_custom2			= $tbl_project .".". PROJ_SHOW_CUSTOM_2;
	$f_show_custom3			= $tbl_project .".". PROJ_SHOW_CUSTOM_3;
	$f_show_custom4			= $tbl_project .".". PROJ_SHOW_CUSTOM_4;
	$f_show_custom5			= $tbl_project .".". PROJ_SHOW_CUSTOM_5;
	$f_show_custom6			= $tbl_project .".". PROJ_SHOW_CUSTOM_6;
	$f_show_window			= $tbl_project .".". PROJ_SHOW_WINDOW;
	$f_show_object			= $tbl_project .".". PROJ_SHOW_OBJECT;
	$f_show_mem_stats		= $tbl_project .".". PROJ_SHOW_MEM_STATS;
	$f_show_priority		= $tbl_project .".". PROJ_SHOW_PRIORITY;
	$f_show_test_input		= $tbl_project .".". PROJ_SHOW_TEST_INPUT;
	$f_date_created			= $tbl_project .".". PROJ_DATE_CREATED;
	$f_req_upload			= $tbl_project .".". PROJ_REQ_UPLOAD_PATH;
	$f_test_upload			= $tbl_project .".". PROJ_TEST_UPLOAD_PATH;
	$f_test_run_upload		= $tbl_project .".". PROJ_TEST_RUN_UPLOAD_PATH;
	$f_test_plan			= $tbl_project .".". PROJ_TEST_PLAN_UPLOAD_PATH;
	$f_defect_upload		= $tbl_project .".". PROJ_DEFECT_UPLOAD_PATH;
	$f_date_created			= $tbl_project .".". PROJ_DATE_CREATED;
	$current_date			= date("Y-m-d H:i:s");



	$q = "	INSERT INTO $tbl_project
				(	$f_proj_name,
					$f_proj_description,
					$f_proj_status,
					$f_show_custom1,
					$f_show_custom2,
					$f_show_custom3,
					$f_show_custom4,
					$f_show_custom5,
					$f_show_custom6,
					$f_show_window,
					$f_show_object,
					$f_show_mem_stats,
					$f_show_priority,
					$f_show_test_input,
					$f_req_upload,
					$f_test_upload,
					$f_test_run_upload,
					$f_test_plan,
					$f_defect_upload,
					$f_date_created)
			VALUES
				(	'$name',
					'$description',
					'$status',
					'$custom1',
					'$custom2',
					'$custom3',
					'$custom4',
					'$custom5',
					'$custom6',
					'$window',
					'$object',
					'$mem_stats',
					'$priority',
					'$test_input',
					'$req_upload',
					'$test_upload',
					'$test_run_upload',
					'$test_plan_upload',
					'$defect_upload',
					'$current_date' )";

    db_query($db, $q);
}

# ---------------------------------------------------------------------------------
# Delete a project from RTH
# We should also remove the files from the file_upload_path
# ---------------------------------------------------------------------------------
function project_delete( $project_id ) {

	global $db;
	$tbl_project			= PROJECT_TBL;
	$f_proj_id				= $tbl_project .".". PROJ_ID;
	$f_proj_deleted			= $tbl_project .".". PROJ_DELETED;

	$q = "	UPDATE 	$tbl_project
			SET		$f_proj_deleted = 'Y'
			WHERE	$f_proj_id = $project_id";

	# get project name and remove it from the session
	$project_name = project_get_name( $project_id );
	//print"project_name = $project_name<br>";
	
	$s_user_projects 	= session_get_user_projects();

	foreach( $s_user_projects as $key => $session_project ) {

		if( $project_name == $session_project ) {
			unset($_SESSION['s_user_projects'][$key]);
		}
	}
	
	// fix to remove files for project as well provided by SPKannan
//	$project_folder_name	= str_replace(" ", "", $project_name);
//	$req_docs		= FILE_UPLOAD_PATH .$project_folder_name."_req_docs/";
//	$test_docs		= FILE_UPLOAD_PATH .$project_folder_name."_test_docs/";
//	$test_run_docs		= FILE_UPLOAD_PATH .$project_folder_name."_test_run_docs/";
//	$test_plan_docs		= FILE_UPLOAD_PATH .$project_folder_name."_test_plan_docs/";
//	$defect_docs		= FILE_UPLOAD_PATH .$project_folder_name."_defect_docs/";
//
//	for ($i = 1; $i <= 5; $i++)
//	{
//		if ($i == 1)
//		{ 
//			$dir = $req_docs;
//		}
//	
//		if ($i ==2)
//		{
//			$dir = $test_docs;
//		}
//	
//		if ($i == 3)
//		{
//			$dir = $test_run_docs;
//		}
//	
//		if ($i == 4)
//		{
//			$dir = $test_plan_docs;
//		}
//	
//		if($i == 5)
//		{
//			$dir = $defect_docs;
//		}
//
//		if ($handle = opendir($dir))   
//		{   
//		$array = array();   
//		  
//		    while (false !== ($file = readdir($handle))) {   
//		        if ($file != "." && $file != "..") {   
//		  
//		            if(is_dir($dir.$file))   
//		            {   
//		                if(!@rmdir($dir.$file)) // Empty directory? Remove it   
//		                {   
//		                delete_directory($dir.$file.'/'); // Not empty? Delete the files inside it   
//		                }   
//		            }   
//		            else  
//		            {   
//		               @unlink($dir.$file);   
//		            }   
//		        }   
//		    }   
//		    closedir($handle);   
//		  
//		    @rmdir($dir);   
//		}  # if loop end 
//		
//	} #For loop end
//	// end fix be SPKannan

    db_query($db, $q);
}


# ---------------------------------------------------------------------------------
# Get user and associated project details
# ---------------------------------------------------------------------------------
function project_get_user_details( $project_id, $user_id=null ) {

	global $db;

	$tbl_user 				= USER_TBL;
	$f_user_id	 			= $tbl_user .".". USER_ID;
	$f_user_username 		= $tbl_user .".". USER_UNAME;
	$f_user_email 			= $tbl_user .".". USER_EMAIL;
	$f_user_first 			= $tbl_user .".". USER_FNAME;
	$f_user_last 			= $tbl_user .".". USER_LNAME;
	$f_user_phone	 		= $tbl_user .".". USER_PHONE;
	$f_user_password 		= $tbl_user .".". USER_PWORD;
	$f_user_default_project	= $tbl_user .".". USER_DEFAULT_PROJECT;

	$tbl_proj_user_assoc	= PROJECT_USER_ASSOC_TBL;
	$f_proj_user_proj_id	= $tbl_proj_user_assoc .".". PROJ_USER_PROJ_ID;
	$f_proj_user_user_id	= $tbl_proj_user_assoc .".". PROJ_USER_USER_ID;
	$f_user_rights			= $tbl_proj_user_assoc .".". PROJ_USER_PROJECT_RIGHTS;
	$f_delete_rights		= $tbl_proj_user_assoc .".". PROJ_USER_DELETE_RIGHTS;
	$f_email_testset		= $tbl_proj_user_assoc .".". PROJ_USER_EMAIL_TESTSET;
	$f_email_discussion		= $tbl_proj_user_assoc .".". PROJ_USER_EMAIL_REQ_DISCUSSION;
	$f_qa_owner				= $tbl_proj_user_assoc .".". PROJ_USER_QA_OWNER;
	$f_ba_owner				= $tbl_proj_user_assoc .".". PROJ_USER_BA_OWNER;

	$q =	"	SELECT	$f_user_id,
						$f_user_username,
						$f_user_email,
						$f_user_first,
						$f_user_last,
						$f_user_phone,
						$f_user_password,
						$f_user_default_project,
						$f_user_rights,
						$f_delete_rights,
						$f_email_testset,
						$f_email_discussion,
						$f_qa_owner,
						$f_ba_owner
				FROM	$tbl_user
				INNER JOIN $tbl_proj_user_assoc ON $f_proj_user_user_id = $f_user_id
				WHERE $f_proj_user_proj_id = $project_id";
				
	if( $user_id != null ) {
		$q .= " AND $f_user_id = $user_id";
	}

	$rs = db_query($db, $q);

	# return one row if user_id is specified, else return array of rows
	if( $user_id ) {
		return db_fetch_row($db, $rs);
	} else {
		return db_fetch_array($db, $rs);
	}
}

function project_get_user_email_prefs( $project_id, $pref ) {

	global $db;

	$user_ids				= array();
	$proj_user_assoc_tbl	= PROJECT_USER_ASSOC_TBL;
	$f_user_id				= PROJ_USER_USER_ID;
	$f_project_id			= PROJ_USER_PROJ_ID;

	$q = "SELECT $f_user_id
		  FROM $proj_user_assoc_tbl
		  WHERE $f_project_id = '$project_id'
		  AND $pref = 'Y'";

	$rs = db_query($db, $q);
	
	while($row = db_fetch_row( $db, $rs ) ) { ;
		array_push($user_ids, $row[PROJ_USER_USER_ID]);
    }

	return $user_ids;

}


# ---------------------------------------------------------------------------------
# Get the Test Areas Tested for a particular project
# ---------------------------------------------------------------------------------
function project_get_areas_tested($project_id, $order_by=AREA_TESTED_NAME, $order_dir="ASC", $page_number=null) {
	global $db;

	$tbl_area_tested 	= AREA_TESTED_TBL;
	$f_area_tested		= AREA_TESTED_TBL .".". AREA_TESTED_NAME;
	$f_area_tested_id	= AREA_TESTED_TBL .".". AREA_TESTED_ID;
	$f_proj_id			= AREA_TESTED_TBL .".". AREA_TESTED_PROJ_ID;

	$q	=	"	SELECT	$f_area_tested_id,
						$f_area_tested
				FROM	$tbl_area_tested
				WHERE	$f_proj_id = $project_id
				ORDER BY $order_by $order_dir";

	if( !is_null($page_number) ) {

		# Add the limit clause to the query so that we only show n number of records per page
		$offset = ( ( $page_number - 1 ) * RECORDS_PER_PAGE_PROJECT_MANAGE_AREAS );

		html_table_offset( 	db_num_rows( $db, db_query($db, $q) ),
							RECORDS_PER_PAGE_PROJECT_MANAGE_AREAS,
							$page_number );

		$q .= " LIMIT $offset, ".RECORDS_PER_PAGE_PROJECT_MANAGE_AREAS;

	}

	return db_fetch_array($db, db_query($db, $q));
}

# ---------------------------------------------------------------------------------
# Get the name of an area tested
# ---------------------------------------------------------------------------------
function project_get_area_tested( $project_id, $area_tested_id ) {

	$tbl_area	= AREA_TESTED_TBL;
	$f_name		= $tbl_area .".". AREA_TESTED_NAME;
	$f_proj_id	= $tbl_area .".". AREA_TESTED_PROJ_ID;
	$f_id		= $tbl_area .".". AREA_TESTED_ID;

	$q =	"	SELECT 	$f_name,
						$f_proj_id,
						$f_id
				FROM $tbl_area
				WHERE
					$project_id=$project_id
					AND $f_id=$area_tested_id";

	global $db;

	$row = db_fetch_row($db, db_query($db, $q));

	return $row;
}

# ---------------------------------------------------------------------------------
# Add users to a project
# ---------------------------------------------------------------------------------
function project_add_users( $project_id, 	$add_users,
											$user_rights,
											$delete_rights,
											$email_testset,
											$email_discussions,
											$qa_owner,
											$ba_owner ) {

	$tbl_proj_user_assoc	= PROJECT_USER_ASSOC_TBL;
	$f_proj_user_proj_id	= $tbl_proj_user_assoc .".". PROJ_USER_PROJ_ID;
	$f_proj_user_user_id	= $tbl_proj_user_assoc .".". PROJ_USER_USER_ID;
	$f_user_rights			= $tbl_proj_user_assoc .".". PROJ_USER_PROJECT_RIGHTS;
	$f_delete_rights		= $tbl_proj_user_assoc .".". PROJ_USER_DELETE_RIGHTS;
	$f_email_testset		= $tbl_proj_user_assoc .".". PROJ_USER_EMAIL_TESTSET;
	$f_email_discussion		= $tbl_proj_user_assoc .".". PROJ_USER_EMAIL_REQ_DISCUSSION;
	$f_qa_owner				= $tbl_proj_user_assoc .".". PROJ_USER_QA_OWNER;
	$f_ba_owner				= $tbl_proj_user_assoc .".". PROJ_USER_BA_OWNER;

	global $db;

	foreach($add_users as $user_id) {

		$q = "	SELECT	$f_proj_user_user_id
				FROM	$tbl_proj_user_assoc
				WHERE	$f_proj_user_user_id = $user_id
							AND $f_proj_user_proj_id = $project_id";

		if( !db_fetch_row( $db, db_query($db, $q) ) ) {

			$q = "	INSERT INTO	$tbl_proj_user_assoc
						(	$f_proj_user_proj_id,
							$f_proj_user_user_id,
							$f_user_rights,
							$f_delete_rights,
							$f_email_testset,
							$f_email_discussion,
							$f_qa_owner,
							$f_ba_owner )
					VALUES
						(	$project_id,
							$user_id,
							'$user_rights',
							'$delete_rights',
							'$email_testset',
							'$email_discussions',
							'$qa_owner',
							'$ba_owner' )";

			db_query($db, $q);
		}
	}

}

# ---------------------------------------------------------------------------------
# Add Test Area Tested to a project
# ---------------------------------------------------------------------------------
function project_add_area( $project_id, $area_name ) {

	$tbl_area	= AREA_TESTED_TBL;
	$f_name		= $tbl_area .".". AREA_TESTED_NAME;
	$f_proj_id	= $tbl_area .".". AREA_TESTED_PROJ_ID;

	$q =	"	INSERT INTO $tbl_area
					($f_proj_id, $f_name)
				VALUES
					($project_id, '$area_name')";

	global $db;

	db_query($db, $q);
}

# ----------------------------------------------------------------------
# Check if Project Test Area already exists
# INPUT:
#   Test Area Covered Name to Check and Project ID
# OUTPUT:
#   True if Test with  Test Area Name already exists, otherwise false.
# ----------------------------------------------------------------------
function project_test_area_exists( $project_id, $area_name ) {

    global $db;
    $tbl_area	= AREA_TESTED_TBL;
    $f_name		= $tbl_area .".". AREA_TESTED_NAME;
	$f_proj_id	= $tbl_area .".". AREA_TESTED_PROJ_ID;

    $query = "	SELECT COUNT($f_name)
				FROM $tbl_area
				WHERE $f_name='$area_name'
					AND $f_proj_id=$project_id";

   $result = db_get_one( $db, $query );

    if ( 0 == $result ) {
        return false;
    } else {
        return true;
    }
}


# ---------------------------------------------------------------------------------
# Add Requirement Document Type to a project
# ---------------------------------------------------------------------------------
function project_add_req_doc_type( $project_id, $name ) {

	$tbl_doc_type			= REQ_DOC_TYPE_TBL;
	$f_name					= $tbl_doc_type .".". REQ_DOC_TYPE_NAME;
	$f_id					= $tbl_doc_type .".". REQ_DOC_TYPE_ID;
	$f_root					= $tbl_doc_type .".". REQ_DOC_TYPE_ROOT_DOC;
	$f_proj_id				= $tbl_doc_type .".". REQ_DOC_TYPE_PROJ_ID;

	$q =	"	INSERT INTO $tbl_doc_type
					($f_proj_id, $f_name)
				VALUES
					($project_id, '$name')";

	global $db;

	db_query($db, $q);
}

# ----------------------------------------------------------------------
# Check if Requirement Document Type already exists
# INPUT:
#   Requirement Document Type to Check and Project ID
# OUTPUT:
#   True if Requirement Document Type already exists, otherwise false.
# ----------------------------------------------------------------------
function project_req_doc_type_exists( $project_id, $name ) {

    global $db;
    $tbl_doc_type			= REQ_DOC_TYPE_TBL;
    $f_name					= $tbl_doc_type .".". REQ_DOC_TYPE_NAME;
	$f_proj_id				= $tbl_doc_type .".". REQ_DOC_TYPE_PROJ_ID;

    $query = "	SELECT COUNT($f_name)
				FROM $tbl_doc_type
				WHERE $f_name='$name'
					AND $f_proj_id=$project_id";

   $result = db_get_one( $db, $query );

    if ( 0 == $result ) {
        return false;
    } else {
        return true;
    }
}


# ---------------------------------------------------------------------------------
# Add Requirement Area Covered to a project
# ---------------------------------------------------------------------------------
function project_add_req_area_covered( $project_id, $area_name ) {

	$tbl_req_area_covered	= REQ_AREA_COVERAGE_TBL;
	$f_name					= $tbl_req_area_covered .".". REQ_AREA_COVERAGE;
	$f_id					= $tbl_req_area_covered .".". REQ_AREA_COVERAGE_ID;
	$f_proj_id				= $tbl_req_area_covered .".". REQ_AREA_PROJ_ID;

	$q =	"	INSERT INTO $tbl_req_area_covered
					($f_proj_id, $f_name)
				VALUES
					($project_id, '$area_name')";

	global $db;

	db_query($db, $q);
}


# ----------------------------------------------------------------------
# Check if Requirement Area Name already exists
# INPUT:
#   Requirement Area Covered Name to Check and Project ID
# OUTPUT:
#   True if Test with  Requirement Area Covered Name already exists, otherwise false.
# ----------------------------------------------------------------------
function project_req_area_covered_exists( $project_id, $area_name ) {

    global $db;
    $tbl_req_area_covered	= REQ_AREA_COVERAGE_TBL;
    $f_name					= $tbl_req_area_covered .".". REQ_AREA_COVERAGE;
	$f_proj_id				= $tbl_req_area_covered .".". REQ_AREA_PROJ_ID;

    $query = "	SELECT COUNT($f_name)
				FROM $tbl_req_area_covered
				WHERE $f_name='$area_name'
					AND $f_proj_id=$project_id";

   $result = db_get_one( $db, $query );

    if ( 0 == $result ) {
        return false;
    } else {
        return true;
    }
}



# ---------------------------------------------------------------------------------
# Remove Requirement Area Covered from project
# ---------------------------------------------------------------------------------
function project_remove_req_area_covered( $project_id, $id ) {

	$tbl_req_area_covered	= REQ_AREA_COVERAGE_TBL;
	$f_name					= $tbl_req_area_covered .".". REQ_AREA_COVERAGE;
	$f_id					= $tbl_req_area_covered .".". REQ_AREA_COVERAGE_ID;
	$f_proj_id				= $tbl_req_area_covered .".". REQ_AREA_PROJ_ID;

	global $db;

	$q	= "	DELETE FROM $tbl_req_area_covered WHERE
				$f_id = $id
				AND $f_proj_id = $project_id";

	db_query($db, $q);
}

# ---------------------------------------------------------------------------------
# Add Requirement Document Type to project
# ---------------------------------------------------------------------------------
function project_add_rec_doc_type( $project_id, $doc_type ) {

	$tbl_req_doc_type	= REQ_DOC_TYPE_TBL;
	$f_name				= $tbl_req_doc_type .".". REQ_DOC_TYPE_NAME;
	$f_proj_id			= $tbl_req_doc_type .".". REQ_DOC_TYPE_PROJ_ID;

	$q =	"	INSERT INTO $tbl_req_doc_type
					($f_proj_id, $f_name)
				VALUES
					($project_id, '$doc_type')";

	global $db;

	db_query($db, $q);
}

# ---------------------------------------------------------------------------------
# Edit Requirement Document type
# ---------------------------------------------------------------------------------
function project_edit_req_doc_type( $project_id, $id, $name ) {

	$tbl_req_doc_type	= REQ_DOC_TYPE_TBL;
	$f_name				= $tbl_req_doc_type .".". REQ_DOC_TYPE_NAME;
	$f_id				= $tbl_req_doc_type .".". REQ_DOC_TYPE_ID;
	$f_proj_id			= $tbl_req_doc_type .".". REQ_DOC_TYPE_PROJ_ID;

	$q =	"	UPDATE $tbl_req_doc_type
				SET
					$f_name = '$name'
				WHERE
					$f_proj_id = $project_id
					AND $f_id = $id";

	global $db;

	db_query($db, $q);
}

# ---------------------------------------------------------------------------------
# Add Test Machine to project
# ---------------------------------------------------------------------------------
function project_add_machine( $project_id, $machine_name, $location, $ip ) {

	$tbl_machine	= MACH_TBL;
	$f_name			= $tbl_machine .".". MACH_NAME;
	$f_location		= $tbl_machine .".". MACH_LOCATION;
	$f_ip			= $tbl_machine .".". MACH_IP_ADDRESS;
	$f_proj_id		= $tbl_machine .".". MACH_PROJ_ID;

	$q =	"	INSERT INTO $tbl_machine
					($f_proj_id, $f_name, $f_location, $f_ip)
				VALUES
					('$project_id', '$machine_name', '$location', '$ip')";

	global $db;

	db_query($db, $q);
}

# ----------------------------------------------------------------------
# Check if Project Machine already exists
# INPUT:
#   Project Machine Name to Check  and Project ID
# OUTPUT:
#   True if Project Machine already exists, otherwise false.
# ----------------------------------------------------------------------
function project_machine_exists( $project_id, $machine_name ) {

    global $db;
    $tbl_machine	= MACH_TBL;
    $f_name			= $tbl_machine .".". MACH_NAME;
	$f_proj_id		= $tbl_machine .".". MACH_PROJ_ID;

    $query = "	SELECT COUNT($f_name)
				FROM $tbl_machine
				WHERE $f_name='$machine_name'
					AND $f_proj_id=$project_id";

   $result = db_get_one( $db, $query );

    if ( 0 == $result ) {
        return false;
    } else {
        return true;
    }
}

# ----------------------------------------------------------------------
# Check if Project Machine IP Address already exists
# INPUT:
#   Project Machine IP Address to Check and Project ID
# OUTPUT:
#   True Project Machine IP Address already exists, otherwise false.
# ----------------------------------------------------------------------
function project_ip_address_exists( $project_id, $ip_address ) {

    global $db;
    $tbl_machine	= MACH_TBL;
    $f_address		= $tbl_machine .".". MACH_IP_ADDRESS;
	$f_proj_id		= $tbl_machine .".". MACH_PROJ_ID;

    $query = "	SELECT COUNT($f_address)
				FROM $tbl_machine
				WHERE $f_address='$ip_address'
					AND $f_proj_id=$project_id";

   $result = db_get_one( $db, $query );

    if ( 0 == $result ) {
        return false;
    } else {
        return true;
    }
}

# ---------------------------------------------------------------------------------
# Get Machine information
# ---------------------------------------------------------------------------------
function project_get_machine( $project_id, $machine_id ) {

	$tbl_machine	= MACH_TBL;
	$f_id			= $tbl_machine .".". MACH_ID;
	$f_proj_id		= $tbl_machine .".". MACH_PROJ_ID;
	$f_name			= $tbl_machine .".". MACH_NAME;
	$f_location		= $tbl_machine .".". MACH_LOCATION;
	$f_ip			= $tbl_machine .".". MACH_IP_ADDRESS;

	$q =	"	SELECT	$f_name,
						$f_location,
						$f_ip
				FROM 	$tbl_machine
				WHERE	$f_id = $machine_id
						AND $f_proj_id = $project_id";

	global $db;

	return db_fetch_row($db, db_query($db, $q));
}

# ---------------------------------------------------------------------------------
# Get Requirement Area Covered information
# ---------------------------------------------------------------------------------
function project_get_req_area_covered( $project_id, $id ) {

	$tbl_req_area_covered	= REQ_AREA_COVERAGE_TBL;
	$f_name					= $tbl_req_area_covered .".". REQ_AREA_COVERAGE;
	$f_id					= $tbl_req_area_covered .".". REQ_AREA_COVERAGE_ID;
	$f_proj_id				= $tbl_req_area_covered .".". REQ_AREA_PROJ_ID;

	$q =	"	SELECT	$f_name
				FROM 	$tbl_req_area_covered
				WHERE	$f_id = $id";

	global $db;

	$row = db_fetch_row($db, db_query($db, $q));

	return $row;
}

# ---------------------------------------------------------------------------------
# Edit Requirement Area Covered
# ---------------------------------------------------------------------------------
function project_edit_req_area_covered($project_id, $id, $name) {

	$tbl_req_area_covered	= REQ_AREA_COVERAGE_TBL;
	$f_name					= $tbl_req_area_covered .".". REQ_AREA_COVERAGE;
	$f_id					= $tbl_req_area_covered .".". REQ_AREA_COVERAGE_ID;
	$f_proj_id				= $tbl_req_area_covered .".". REQ_AREA_PROJ_ID;

	$q	=	"	UPDATE	$tbl_req_area_covered
				SET
					$f_name = '$name'
				WHERE
					$f_id = $id
					AND $f_proj_id = $project_id";

	global $db;

	db_query($db, $q);
}

# ---------------------------------------------------------------------------------
# Add Test Document Type to project
# ---------------------------------------------------------------------------------
function project_add_test_doc_type( $project_id, $man_doc_type ) {

	$tbl_man_doc_type 	= MAN_DOC_TYPE_TBL;
	$f_name				= $tbl_man_doc_type .".". MAN_DOC_TYPE_NAME;
	$f_id				= $tbl_man_doc_type .".". MAN_DOC_TYPE_ID;
	$f_proj_id			= $tbl_man_doc_type .".". MAN_DOC_TYPE_PROJ_ID;

	$q =	"	INSERT INTO $tbl_man_doc_type
					($f_proj_id, $f_name)
				VALUES
					($project_id, '$man_doc_type')";

	global $db;

	db_query($db, $q);
}

# ----------------------------------------------------------------------
# Check if Test Doc Type name already exists
# INPUT:
#   Test Doc Type Name to Check and the Project ID
# OUTPUT:
#   True if Test Doc Type name already exists, otherwise false.
# ----------------------------------------------------------------------
function project_test_doc_type_exists( $project_id, $man_doc_type ) {

	global $db;
	$tbl_man_doc_type 	= MAN_DOC_TYPE_TBL;
	$f_name				= $tbl_man_doc_type .".". MAN_DOC_TYPE_NAME;
	$f_proj_id			= $tbl_man_doc_type .".". MAN_DOC_TYPE_PROJ_ID;


	$q = "SELECT COUNT($f_name	)
		  FROM $tbl_man_doc_type
		  WHERE $f_name	= '$man_doc_type '
		  AND $f_proj_id = '$project_id'";


	$result = db_get_one( $db, $q );


	if ( 0 == $result) {
	    return false;
	} else {
	    return true;
    }

}

# ---------------------------------------------------------------------------------
# Add Test Type to project
# ---------------------------------------------------------------------------------
function project_add_testtype( $project_id, $testtype ) {

	$tbl_testtype	= TEST_TYPE_TBL;
	$f_name			= $tbl_testtype .".". TEST_TYPE_TYPE;
	$f_proj_id		= $tbl_testtype .".". TEST_TYPE_PROJ_ID;

	$q =	"	INSERT INTO $tbl_testtype
					($f_proj_id, $f_name)
				VALUES
					('$project_id', '$testtype')";

	global $db;

	db_query($db, $q);
}

# ----------------------------------------------------------------------
# Check if Project Test Type already exists
# INPUT:
#   Project Test Type to Check and the Project ID
# OUTPUT:
#   True if Project Test Type already exists, otherwise false.
# ----------------------------------------------------------------------
function project_testtype_exists( $project_id, $testtype ) {

	global $db;
	$tbl_testtype	= TEST_TYPE_TBL;
	$f_name			= $tbl_testtype .".". TEST_TYPE_TYPE;
	$f_proj_id		= $tbl_testtype .".". TEST_TYPE_PROJ_ID;


	$q = "SELECT COUNT($f_name)
		  FROM $tbl_testtype
		  WHERE $f_name	= '$testtype'
		  AND $f_proj_id = '$project_id'";


	$result = db_get_one( $db, $q );


	if ( 0 == $result) {
	    return false;
	} else {
	    return true;
    }

}


# ---------------------------------------------------------------------------------
# Remove user from project
# ---------------------------------------------------------------------------------
function project_remove_user( $project_id, $user_id ) {

	$tbl_proj_user_assoc	= PROJECT_USER_ASSOC_TBL;
	$f_proj_user_proj_id	= $tbl_proj_user_assoc .".". PROJ_USER_PROJ_ID;
	$f_proj_user_user_id	= $tbl_proj_user_assoc .".". PROJ_USER_USER_ID;

	global $db;

	$q	= "	DELETE FROM $tbl_proj_user_assoc WHERE
				$f_proj_user_proj_id = $project_id AND
				$f_proj_user_user_id = $user_id";

	db_query($db, $q);
}

###################################################################
# Remove a record from the Test Area Tested table
# INPUT
#		project_id - not really needed
#       area_tested_id - the id of the record we want to delete
#
###################################################################
function project_remove_area_tested( $project_id, $area_id ) {

	$tbl_area		= AREA_TESTED_TBL;
	$f_area_id		= $tbl_area .".". AREA_TESTED_ID;
	$f_proj_id		= $tbl_area .".". AREA_TESTED_PROJ_ID;

	global $db;

	$q	= "	DELETE FROM $tbl_area WHERE
				$f_proj_id = $project_id AND
				$f_area_id = $area_id";

	db_query($db, $q);
}

###################################################################
# Remove a record from the Test Area Tested table
# INPUT
#		project_id - not really needed
#       area_tested_id - the id of the record we want to delete
#
###################################################################
function project_remove_req_functionality( $project_id, $func_id ) {

	global $db;

	$req_func_tbl		= REQ_FUNCT_TBL;
	$f_id				= $req_func_tbl .".". REQ_FUNCT_ID;
	$f_proj_id			= $req_func_tbl .".". REQ_FUNCT_PROJ_ID;

	$q = "DELETE FROM $req_func_tbl
		   WHERE $f_id = '$func_id'
		   AND $f_proj_id = '$project_id'";

	db_query($db, $q);

}

# ---------------------------------------------------------------------------------
# Remove Test Machine from project
# ---------------------------------------------------------------------------------
function project_remove_machine( $project_id, $machine_id ) {

	$tbl_machine	= MACH_TBL;
	$f_id			= $tbl_machine .".". MACH_ID;
	$f_proj_id		= $tbl_machine .".". MACH_PROJ_ID;

	global $db;

	$q	= "	DELETE FROM $tbl_machine
			WHERE
				$f_id = $machine_id
				AND $f_proj_id = $project_id";

	db_query($db, $q);
}

# ---------------------------------------------------------------------------------
# Remove Test Document Type from project
# ---------------------------------------------------------------------------------
function project_remove_man_doc_type( $project_id, $id ) {

	$tbl_man_doc_type	= MAN_DOC_TYPE_TBL;
	$f_id				= $tbl_man_doc_type .".". MAN_DOC_TYPE_ID;
	$f_proj_id			= $tbl_man_doc_type .".". MAN_DOC_TYPE_PROJ_ID;

	global $db;

	$q	= "	DELETE FROM $tbl_man_doc_type WHERE
				$f_id = $id
				AND $f_proj_id = $project_id";

	db_query($db, $q);
}

# ---------------------------------------------------------------------------------
# Remove Test Type from project
# ---------------------------------------------------------------------------------
function project_remove_testtype( $project_id, $testtype_id ) {

	$tbl_testtype 	= TEST_TYPE_TBL;
	$f_id			= $tbl_testtype .".". TEST_TYPE_ID;
	$f_proj_id		= $tbl_testtype .".". TEST_TYPE_PROJ_ID;

	global $db;

	$q	= "	DELETE FROM $tbl_testtype WHERE
				$f_id = $testtype_id
				AND $f_proj_id = $project_id";

	db_query($db, $q);
}

# ---------------------------------------------------------------------------------
# Edit Test Area Tested
# ---------------------------------------------------------------------------------
function project_edit_area_tested($project_id, $area_id, $name) {

	$tbl_area_tested 	= AREA_TESTED_TBL;
	$f_area_tested		= AREA_TESTED_TBL .".". AREA_TESTED_NAME;
	$f_area_tested_id	= AREA_TESTED_TBL .".". AREA_TESTED_ID;
	$f_proj_id			= AREA_TESTED_TBL .".". AREA_TESTED_PROJ_ID;

	$q	=	"	UPDATE $tbl_area_tested
				SET
					$f_area_tested = '$name'
				WHERE
					$f_area_tested_id = $area_id
					AND $f_proj_id = $project_id";

	global $db;

	db_query($db, $q);
}

# ---------------------------------------------------------------------------------
# Edit Test Area Tested
# ---------------------------------------------------------------------------------
function project_edit_machine( $project_id, $machine_id, $name, $ip, $location ) {

	$tbl_machine	= MACH_TBL;
	$f_id			= $tbl_machine .".". MACH_ID;
	$f_proj_id		= $tbl_machine .".". MACH_PROJ_ID;
	$f_name			= $tbl_machine .".". MACH_NAME;
	$f_location		= $tbl_machine .".". MACH_LOCATION;
	$f_ip			= $tbl_machine .".". MACH_IP_ADDRESS;

	$q =	"	UPDATE	$tbl_machine
				SET
					$f_name = '$name',
					$f_location = '$location',
					$f_ip = '$ip'
				WHERE
					$f_id = $machine_id
					AND $f_proj_id = $project_id";

	global $db;

	db_query($db, $q);
}

# ---------------------------------------------------------------------------------
# Edit Test Type
# ---------------------------------------------------------------------------------
function project_edit_test_type($project_id, $id, $name) {

	$tbl_testtype 	= TEST_TYPE_TBL;
	$f_id			= $tbl_testtype .".". TEST_TYPE_ID;
	$f_project_id	= $tbl_testtype .".". TEST_TYPE_PROJ_ID;
	$f_name			= $tbl_testtype .".". TEST_TYPE_TYPE;

	$q	= "	UPDATE	$tbl_testtype
			SET
				$f_name = '$name'
			WHERE
				$f_id = $id
				AND $f_project_id = $project_id";
	global $db;

	db_query($db, $q);
}

# ---------------------------------------------------------------------------------
# Edit project user properties
# ---------------------------------------------------------------------------------
function project_edit_user(	$project_id,
							$user_id,
							$user_rights,
							$delete_rights,
							$email_testset,
							$email_discussions,
							$qa_owner,
							$ba_owner ) {

	$tbl_proj_user_assoc	= PROJECT_USER_ASSOC_TBL;
	$f_proj_user_proj_id	= $tbl_proj_user_assoc .".". PROJ_USER_PROJ_ID;
	$f_proj_user_user_id	= $tbl_proj_user_assoc .".". PROJ_USER_USER_ID;
	$f_user_rights			= $tbl_proj_user_assoc .".". PROJ_USER_PROJECT_RIGHTS;
	$f_delete_rights		= $tbl_proj_user_assoc .".". PROJ_USER_DELETE_RIGHTS;
	$f_email_testset		= $tbl_proj_user_assoc .".". PROJ_USER_EMAIL_TESTSET;
	$f_email_discussion		= $tbl_proj_user_assoc .".". PROJ_USER_EMAIL_REQ_DISCUSSION;
	$f_qa_owner				= $tbl_proj_user_assoc .".". PROJ_USER_QA_OWNER;
	$f_ba_owner				= $tbl_proj_user_assoc .".". PROJ_USER_BA_OWNER;

	global $db;

	$q = "	UPDATE $tbl_proj_user_assoc
			SET	$f_user_rights = '$user_rights',
				$f_delete_rights = '$delete_rights',
				$f_email_testset = '$email_testset',
				$f_email_discussion = '$email_discussions',
				$f_qa_owner = '$qa_owner',
				$f_ba_owner = '$ba_owner'
			WHERE $f_proj_user_proj_id = $project_id
				AND $f_proj_user_user_id = $user_id";

	db_query($db, $q);
}

# ---------------------------------------------------------------------------------
# Get Test Machines for a project
# ---------------------------------------------------------------------------------
function project_get_machines($project_id, $order_by, $order_dir, $page_number) {
	global $db;

	$tbl_machines		= MACH_TBL;
	$f_machine_id		= $tbl_machines .".". MACH_ID;
	$f_proj_id			= $tbl_machines .".". MACH_PROJ_ID;
	$f_machine_name		= $tbl_machines .".". MACH_NAME;
	$f_machine_location	= $tbl_machines .".". MACH_LOCATION;
	$f_machine_ip		= $tbl_machines .".". MACH_IP_ADDRESS;

	$q =	"	SELECT 	$f_machine_id,
						$f_machine_name,
						$f_machine_location,
						$f_machine_ip
				FROM	$tbl_machines
				WHERE	$f_proj_id = $project_id
				ORDER BY $order_by $order_dir";

	if( RECORDS_PER_PAGE_PROJECT_MANAGE_MACHINES!=0 ) {

		# Add the limit clause to the query so that we only show n number of records per page
		$offset = ( ( $page_number - 1 ) * RECORDS_PER_PAGE_PROJECT_MANAGE_MACHINES );

		html_table_offset( 	db_num_rows( $db, db_query($db, $q) ),
							RECORDS_PER_PAGE_PROJECT_MANAGE_MACHINES,
							$page_number );

		$q .= " LIMIT $offset, ".RECORDS_PER_PAGE_PROJECT_MANAGE_MACHINES;

	}

	return db_fetch_array($db, db_query($db, $q));
}

# ---------------------------------------------------------------------------------
# Get Test Types for a project
# ---------------------------------------------------------------------------------
function project_get_test_types($project_id, $order_by, $order_dir, $page_number) {

	global $db;

	$tbl_testtype 	= TEST_TYPE_TBL;
	$f_id			= $tbl_testtype .".". TEST_TYPE_ID;
	$f_testtype		= $tbl_testtype .".". TEST_TYPE_TYPE;
	$f_project_id	= $tbl_testtype .".". TEST_TYPE_PROJ_ID;

	$q =	"	SELECT	$f_id,
						$f_testtype,
						$f_project_id
				FROM	$tbl_testtype
				WHERE	$f_project_id = $project_id
				ORDER BY $order_by $order_dir";

	if( RECORDS_PER_PAGE_PROJECT_MANAGE_AREAS!=0 ) {

		# Add the limit clause to the query so that we only show n number of records per page
		$offset = ( ( $page_number - 1 ) * RECORDS_PER_PAGE_PROJECT_MANAGE_AREAS );

		html_table_offset( 	db_num_rows( $db, db_query($db, $q) ),
							RECORDS_PER_PAGE_PROJECT_MANAGE_AREAS,
							$page_number );

		$q .= " LIMIT $offset, ".RECORDS_PER_PAGE_PROJECT_MANAGE_AREAS;

	}

	return db_fetch_array($db, db_query($db, $q));
}

# ---------------------------------------------------------------------------------
# Get Test Type
# ---------------------------------------------------------------------------------
function project_get_test_type($project_id, $id) {

	$tbl_testtype 	= TEST_TYPE_TBL;
	$f_id			= $tbl_testtype .".". TEST_TYPE_ID;
	$f_project_id	= $tbl_testtype .".". TEST_TYPE_PROJ_ID;
	$f_name			= $tbl_testtype .".". TEST_TYPE_TYPE;

	$q	= "	SELECT	$f_id,
					$f_project_id,
					$f_name
			FROM	$tbl_testtype
			WHERE	$f_id = $id
					AND $f_project_id = $project_id";

	global $db;

	$rs = db_query($db, $q);

	return db_fetch_row($db, $rs);
}

# ---------------------------------------------------------------------------------
# Get Test Environments in project
# ---------------------------------------------------------------------------------
function project_get_environments($project_id, $order_by=ENVIRONMENT_NAME, $order_dir="ASC", $page_number=null) {

	global $db;

	$tbl_environment	= ENVIRONMENT_TBL;
	$f_name				= ENVIRONMENT_TBL .".". ENVIRONMENT_NAME;
	$f_id				= ENVIRONMENT_TBL .".". ENVIRONMENT_ID;
	$f_project			= ENVIRONMENT_TBL .".". ENVIRONMENT_PROJ_ID;

	$q	=	"	SELECT	$f_name,
						$f_id
				FROM	$tbl_environment
				WHERE	$f_project = $project_id
				ORDER BY $order_by $order_dir";

	if( !is_null($page_number) ) {
		if( RECORDS_PER_PAGE_PROJECT_MANAGE_ENVIRONMENTS!=0 ) {

			# Add the limit clause to the query so that we only show n number of records per page
			$offset = ( ( $page_number - 1 ) * RECORDS_PER_PAGE_PROJECT_MANAGE_ENVIRONMENTS );

			html_table_offset( 	db_num_rows( $db, db_query($db, $q) ),
								RECORDS_PER_PAGE_PROJECT_MANAGE_ENVIRONMENTS,
								$page_number );

			$q .= " LIMIT $offset, ".RECORDS_PER_PAGE_PROJECT_MANAGE_ENVIRONMENTS;

		}
	}

	return db_fetch_array($db, db_query($db, $q));
}

# ---------------------------------------------------------------------------------
# Get Test Environment
# ---------------------------------------------------------------------------------
function project_get_environment($project_id, $id=null) {
	global $db;

	$tbl_environment	= ENVIRONMENT_TBL;
	$f_name				= ENVIRONMENT_TBL .".". ENVIRONMENT_NAME;
	$f_id				= ENVIRONMENT_TBL .".". ENVIRONMENT_ID;
	$f_project			= ENVIRONMENT_TBL .".". ENVIRONMENT_PROJ_ID;

	$q	=	"SELECT	$f_name,
					$f_id
			FROM $tbl_environment
			WHERE $f_project = $project_id";

	if( isset( $id ) ) {
		$q .= " AND $f_id = $id";
	}

	return db_fetch_row($db, db_query($db, $q));
}

# ---------------------------------------------------------------------------------
# Get Requirement Functionality
# ---------------------------------------------------------------------------------
function project_get_functionality($project_id, $id=null) {
	global $db;

	$tbl_functionality	= REQ_FUNCT_TBL;
	$f_name				= $tbl_functionality .".". REQ_FUNCT_NAME;
	$f_id				= $tbl_functionality .".". REQ_FUNCT_ID;
	$f_project			= $tbl_functionality .".". REQ_FUNCT_PROJ_ID;

	$q	=	"SELECT	$f_name,
					$f_id
			FROM $tbl_functionality
			WHERE $f_project = $project_id";

	if( isset( $id ) ) {
		$q .= " AND $f_id = $id";
	}

	return db_fetch_row($db, db_query($db, $q));
}

# ---------------------------------------------------------------------------------
# Edit Test Environment
# ---------------------------------------------------------------------------------
function project_edit_environment($project_id, $id, $name) {
	global $db;

	$tbl_environment	= ENVIRONMENT_TBL;
	$f_name				= ENVIRONMENT_TBL .".". ENVIRONMENT_NAME;
	$f_id				= ENVIRONMENT_TBL .".". ENVIRONMENT_ID;
	$f_project			= ENVIRONMENT_TBL .".". ENVIRONMENT_PROJ_ID;

	$q	=	"	UPDATE	$tbl_environment
				SET
					$f_name = '$name'
				WHERE
					$f_id = $id
					AND $f_project = $project_id";

	db_query($db, $q);
}

# ---------------------------------------------------------------------------------
# Edit Requirement Functionality
# ---------------------------------------------------------------------------------
function project_edit_functionality($project_id, $id, $name) {
	global $db;

	$tbl_functionality	= REQ_FUNCT_TBL;
	$f_name				= $tbl_functionality .".". REQ_FUNCT_NAME;
	$f_id				= $tbl_functionality .".". REQ_FUNCT_ID;
	$f_project			= $tbl_functionality .".". REQ_FUNCT_PROJ_ID;

	$q	=	"	UPDATE	$tbl_functionality
				SET
					$f_name = '$name'
				WHERE
					$f_id = $id
					AND $f_project = $project_id";

	db_query($db, $q);
}

# ---------------------------------------------------------------------------------
# Add Test Environment to project
# ---------------------------------------------------------------------------------
function project_add_environment($project_id, $name) {

	global $db;

	$tbl_environment	= ENVIRONMENT_TBL;
	$f_name				= ENVIRONMENT_TBL .".". ENVIRONMENT_NAME;
	$f_id				= ENVIRONMENT_TBL .".". ENVIRONMENT_ID;
	$f_project			= ENVIRONMENT_TBL .".". ENVIRONMENT_PROJ_ID;

	$q	=	"	INSERT INTO $tbl_environment
					($f_project, $f_name)
				VALUES
					($project_id, '$name')";

	db_query($db, $q);
}

# ----------------------------------------------------------------------
# Check if Project Environment already exists
# INPUT:
#   Project Environment and the Project ID
# OUTPUT:
#   True if Project Environment already exists, otherwise false.
# ----------------------------------------------------------------------
function project_environment_exists( $project_id, $name ) {

	global $db;
	$tbl_environment	= ENVIRONMENT_TBL;
	$f_name				= ENVIRONMENT_TBL .".". ENVIRONMENT_NAME;
	$f_project			= ENVIRONMENT_TBL .".". ENVIRONMENT_PROJ_ID;


	$q = "SELECT COUNT($f_name	)
		  FROM $tbl_environment
		  WHERE $f_name	= '$name '
		  AND $f_project = '$project_id'";


	$result = db_get_one( $db, $q );


	if ( 0 == $result) {
	    return false;
	} else {
	    return true;
    }

}


# ---------------------------------------------------------------------------------
# Add Requirement Functionality to project
# ---------------------------------------------------------------------------------
function project_add_req_functionality($project_id, $name) {

	global $db;

	$tbl_req_funct		= REQ_FUNCT_TBL;
	$f_name				= $tbl_req_funct .".". REQ_FUNCT_NAME;
	$f_id				= $tbl_req_funct .".". REQ_FUNCT_ID;
	$f_project			= $tbl_req_funct .".". REQ_FUNCT_PROJ_ID;

	$q	=	"	INSERT INTO $tbl_req_funct
					($f_project, $f_name)
				VALUES
					($project_id, '$name')";

	db_query($db, $q);
}

# ----------------------------------------------------------------------
# Check if Project Requirement Functionality already exists
# INPUT:
#   Project Requirement Functionality to Check and Project ID
# OUTPUT:
#   True if Project Requirement Functionality already exists, otherwise false.
# ----------------------------------------------------------------------
function project_reqfunctionality_exists( $project_id, $name ) {

    global $db;
    $tbl_req_funct		= REQ_FUNCT_TBL;
    $f_name				= $tbl_req_funct .".". REQ_FUNCT_NAME;
	$f_project			= $tbl_req_funct .".". REQ_FUNCT_PROJ_ID;

    $query = "	SELECT COUNT($f_name)
				FROM $tbl_req_funct
				WHERE $f_name='$name'
					AND $f_project=$project_id";

   $result = db_get_one( $db, $query );

    if ( 0 == $result ) {
        return false;
    } else {
        return true;
    }
}


# ---------------------------------------------------------------------------------
# Remove Test Environment from project
# ---------------------------------------------------------------------------------
function project_remove_environment($project_id, $id) {

	global $db;

	$tbl_environment	= ENVIRONMENT_TBL;
	$f_name				= ENVIRONMENT_TBL .".". ENVIRONMENT_NAME;
	$f_id				= ENVIRONMENT_TBL .".". ENVIRONMENT_ID;
	$f_project			= ENVIRONMENT_TBL .".". ENVIRONMENT_PROJ_ID;

	$q	=	"	DELETE FROM $tbl_environment
				WHERE
					$f_id = $id
					AND $f_project = $project_id";

	db_query($db, $q);
}

# ---------------------------------------------------------------------------------
# Get Requirement Document Types of project
# ---------------------------------------------------------------------------------
function project_get_req_doc_types($project_id, $order_by=REQ_DOC_TYPE_NAME, $order_dir="ASC", $page_number=0) {

	global $db;

	$tbl_req_doc_type	= REQ_DOC_TYPE_TBL;
	$f_name				= $tbl_req_doc_type .".". REQ_DOC_TYPE_NAME;
	$f_id				= $tbl_req_doc_type .".". REQ_DOC_TYPE_ID;
	$f_project_id		= $tbl_req_doc_type .".". REQ_DOC_TYPE_PROJ_ID;
	$f_root_doc			= $tbl_req_doc_type .".". REQ_DOC_TYPE_ROOT_DOC;

	$q	=	"	SELECT	$f_name,
						$f_id,
						$f_root_doc
				FROM	$tbl_req_doc_type
				WHERE $f_project_id = $project_id
				ORDER BY $order_by $order_dir";

	if( $page_number != 0 ) {
		if( RECORDS_PER_PAGE_PROJECT_MANAGE_REQ_DOC_TYPE!=0 ) {

			# Add the limit clause to the query so that we only show n number of records per page
			$offset = ( ( $page_number - 1 ) * RECORDS_PER_PAGE_PROJECT_MANAGE_REQ_DOC_TYPE );

			html_table_offset( 	db_num_rows( $db, db_query($db, $q) ),
								RECORDS_PER_PAGE_PROJECT_MANAGE_REQ_DOC_TYPE,
								$page_number );

			$q .= " LIMIT $offset, ".RECORDS_PER_PAGE_PROJECT_MANAGE_REQ_DOC_TYPE;

		}
	}

	return db_fetch_array($db, db_query($db, $q));
}

# ---------------------------------------------------------------------------------
# Get Requirement Document Type
# ---------------------------------------------------------------------------------
function project_get_req_doc_type($project_id, $id) {

	global $db;

	$tbl_req_doc_type	= REQ_DOC_TYPE_TBL;
	$f_name				= $tbl_req_doc_type .".". REQ_DOC_TYPE_NAME;
	$f_id				= $tbl_req_doc_type .".". REQ_DOC_TYPE_ID;
	$f_proj_id			= $tbl_req_doc_type .".". REQ_DOC_TYPE_PROJ_ID;

	$q	=	"	SELECT	$f_name,
						$f_id
				FROM	$tbl_req_doc_type
				WHERE $f_id = $id
					AND $f_proj_id = $project_id";

	return db_fetch_row($db, db_query($db, $q));
}

# ---------------------------------------------------------------------------------
# Remove Requirement Document Type from project
# ---------------------------------------------------------------------------------
function project_remove_req_doc_type($project_id, $id) {

	global $db;

	$tbl_req_doc_type	= REQ_DOC_TYPE_TBL;
	$f_name				= $tbl_req_doc_type .".". REQ_DOC_TYPE_NAME;
	$f_id				= $tbl_req_doc_type .".". REQ_DOC_TYPE_ID;
	$f_proj_id			= $tbl_req_doc_type .".". REQ_DOC_TYPE_PROJ_ID;

	$q	=	"	DELETE FROM $tbl_req_doc_type
				WHERE $f_id = $id
					AND $f_proj_id = $project_id";

	db_query($db, $q);
}

# ---------------------------------------------------------------------------------
# Get Test Document Types
# ---------------------------------------------------------------------------------
function project_get_test_doc_type($project_id, $id) {

	$tbl_man_doc_type 	= MAN_DOC_TYPE_TBL;
	$f_name				= $tbl_man_doc_type .".". MAN_DOC_TYPE_NAME;
	$f_id				= $tbl_man_doc_type .".". MAN_DOC_TYPE_ID;
	$f_proj_id			= $tbl_man_doc_type .".". MAN_DOC_TYPE_PROJ_ID;

	$q	=	"	SELECT	$f_name
				FROM	$tbl_man_doc_type
				WHERE	$f_proj_id = $project_id
						AND $f_id = $id";

	global $db;

	$row = db_fetch_row($db, db_query($db, $q));

	return $row;
}

# ---------------------------------------------------------------------------------
# Get Requirement Areas Covered
# ---------------------------------------------------------------------------------
function project_get_req_areas_covered($project_id, $order_by=REQ_AREA_COVERAGE, $order_dir="ASC", $page_number=null) {

	$tbl_req_area_covered	= REQ_AREA_COVERAGE_TBL;
	$f_name					= $tbl_req_area_covered .".". REQ_AREA_COVERAGE;
	$f_id					= $tbl_req_area_covered .".". REQ_AREA_COVERAGE_ID;
	$f_proj_id				= $tbl_req_area_covered .".". REQ_AREA_PROJ_ID;

	$q	=	"	SELECT	$f_id,
						$f_proj_id,
						$f_name
				FROM	$tbl_req_area_covered
				WHERE	$f_proj_id = $project_id
				ORDER BY $order_by $order_dir";

	global $db;

	if( !is_null($page_number) ) {
		if( RECORDS_PER_PAGE_PROJECT_MANAGE_REQ_AREA_COVERED!=0 ) {

			# Add the limit clause to the query so that we only show n number of records per page
			$offset = ( ( $page_number - 1 ) *  RECORDS_PER_PAGE_PROJECT_MANAGE_REQ_AREA_COVERED );

			html_table_offset( 	db_num_rows( $db, db_query($db, $q) ),
								RECORDS_PER_PAGE_PROJECT_MANAGE_REQ_AREA_COVERED,
								$page_number );

			$q .= " LIMIT $offset, ". RECORDS_PER_PAGE_PROJECT_MANAGE_REQ_AREA_COVERED;

		}
	}
	$rows = db_fetch_array($db, db_query($db, $q));

	return $rows;
}

# ---------------------------------------------------------------------------------
# Get Requirement Functionality
# ---------------------------------------------------------------------------------
function project_get_req_functionality($project_id, $order_by=REQ_FUNCT_NAME, $order_dir="ASC", $page_number=null) {

	$tbl_req_funct		= REQ_FUNCT_TBL;
	$f_name				= $tbl_req_funct .".". REQ_FUNCT_NAME;
	$f_id				= $tbl_req_funct .".". REQ_FUNCT_ID;
	$f_project			= $tbl_req_funct .".". REQ_FUNCT_PROJ_ID;

	$q	=	"	SELECT	$f_id,
						$f_project,
						$f_name
				FROM	$tbl_req_funct
				WHERE	$f_project = $project_id
				ORDER BY $order_by $order_dir";

	global $db;

	if( !is_null($page_number) ) {
		if( RECORDS_PER_PAGE_PROJECT_MANAGE_REQ_FUNCT!=0 ) {

			# Add the limit clause to the query so that we only show n number of records per page
			$offset = ( ( $page_number - 1 ) *  RECORDS_PER_PAGE_PROJECT_MANAGE_REQ_FUNCT );

			html_table_offset( 	db_num_rows( $db, db_query($db, $q) ),
								RECORDS_PER_PAGE_PROJECT_MANAGE_REQ_FUNCT,
								$page_number );

			$q .= " LIMIT $offset, ". RECORDS_PER_PAGE_PROJECT_MANAGE_REQ_FUNCT;

		}
	}

	$row = db_fetch_array($db, db_query($db, $q));

	return $row;
}

# ---------------------------------------------------------------------------------
# Edit Test Document Type
# ---------------------------------------------------------------------------------
function project_edit_test_doc_type($project_id, $id, $name) {

	$tbl_man_doc_type 	= MAN_DOC_TYPE_TBL;
	$f_name				= $tbl_man_doc_type .".". MAN_DOC_TYPE_NAME;
	$f_id				= $tbl_man_doc_type .".". MAN_DOC_TYPE_ID;
	$f_proj_id			= $tbl_man_doc_type .".". MAN_DOC_TYPE_PROJ_ID;

	$q	=	"	UPDATE	$tbl_man_doc_type
				SET
					$f_name = '$name'
				WHERE
					$f_proj_id = $project_id
					AND $f_id = $id";

	global $db;

	db_query($db, $q);
}

# ---------------------------------------------------------------------------------
# Get Test Document Types
# ---------------------------------------------------------------------------------
function project_get_test_doc_types($project_id, $order_by, $order_dir, $page_number) {
	global $db;

	$tbl_man_doc_type 	= MAN_DOC_TYPE_TBL;
	$f_name				= $tbl_man_doc_type .".". MAN_DOC_TYPE_NAME;
	$f_id				= $tbl_man_doc_type .".". MAN_DOC_TYPE_ID;
	$f_proj_id			= $tbl_man_doc_type .".". MAN_DOC_TYPE_PROJ_ID;

	$q	=	"	SELECT	$f_id,
						$f_name
				FROM	$tbl_man_doc_type
				WHERE	$f_proj_id = $project_id
				ORDER BY $order_by $order_dir";

	if( RECORDS_PER_PAGE_PROJECT_MANAGE_TEST_DOC_TYPE!=0 ) {

		# Add the limit clause to the query so that we only show n number of records per page
		$offset = ( ( $page_number - 1 ) * RECORDS_PER_PAGE_PROJECT_MANAGE_TEST_DOC_TYPE );

		html_table_offset( 	db_num_rows( $db, db_query($db, $q) ),
							RECORDS_PER_PAGE_PROJECT_MANAGE_TEST_DOC_TYPE,
							$page_number );

		$q .= " LIMIT $offset, ".RECORDS_PER_PAGE_PROJECT_MANAGE_TEST_DOC_TYPE;

	}

	return db_fetch_array($db, db_query($db, $q));
}

# ---------------------------------------------------------------------------------
# Get users who are not associated to the project
# ---------------------------------------------------------------------------------
function project_get_non_users( $project_id ) {

	global $db;

	$tbl_user				= USER_TBL;
	$f_user_id				= $tbl_user .".". USER_ID;
	$f_user_name			= $tbl_user .".". USER_UNAME;
	$f_user_lname			= $tbl_user .".". USER_LNAME;
	$f_user_fname			= $tbl_user .".". USER_FNAME;
	$f_user_default_project	= $tbl_user .".". USER_DEFAULT_PROJECT;

	$tbl_proj_user_assoc	= PROJECT_USER_ASSOC_TBL;
	$f_proj_user_proj_id	= $tbl_proj_user_assoc .".". PROJ_USER_PROJ_ID;
	$f_proj_user_user_id	= $tbl_proj_user_assoc .".". PROJ_USER_USER_ID;

	$tbl_project			= PROJECT_TBL;
	$f_proj_id				= $tbl_project .".". PROJ_ID;

	$q = "	SELECT $f_user_id, $f_user_name, $f_user_fname, $f_user_lname
			FROM $tbl_user
			LEFT JOIN $tbl_proj_user_assoc ON $f_user_id = $f_proj_user_user_id
				AND $f_proj_user_proj_id = $project_id
			WHERE IsNull($f_proj_user_proj_id)
			ORDER BY $f_user_lname, $f_user_fname ASC";

	$rs = db_query($db, $q);
	$rows	= db_fetch_array($db, $rs);
	return $rows;

}

# ----------------------------------------------------------------------
# Return bug categories for display in an html table
# This function will call another function which displays the records per page,
# page number, etc.
# INPUT:
#	project_id
#	field to order by in the table
#	order direction - default = Ascending
#	page_number
# ----------------------------------------------------------------------
function project_get_all_bug_categories($project_id, $order_by=CATEGORY_NAME, $order_dir="ASC", $page_number=null) {

	global $db;

	$category_tbl	= BUG_CATEGORY_TBL;
	$f_id			= $category_tbl .".". CATEGORY_ID;
	$f_proj_id		= $category_tbl .".". CATEGORY_PROJECT_ID;
	$f_name			= $category_tbl .".". CATEGORY_NAME;

	$q	=	"SELECT	$f_id,
					$f_proj_id,
					$f_name
			FROM	$category_tbl
			WHERE	$f_proj_id = $project_id
			ORDER BY $order_by $order_dir";

	if( !is_null($page_number) ) {
		if( RECORDS_PER_PAGE_PROJECT_MANAGE_BUG_CATEGORY != 0 ) {

			# Add the limit clause to the query so that we only show n number of records per page
			$offset = ( ( $page_number - 1 ) *  RECORDS_PER_PAGE_PROJECT_MANAGE_BUG_CATEGORY );

			html_table_offset( 	db_num_rows( $db, db_query($db, $q) ),
								RECORDS_PER_PAGE_PROJECT_MANAGE_BUG_CATEGORY,
								$page_number );

			$q .= " LIMIT $offset, ". RECORDS_PER_PAGE_PROJECT_MANAGE_BUG_CATEGORY;

		}
	}
	$rows = db_fetch_array($db, db_query($db, $q));

	return $rows;
}

# ----------------------------------------------------------------------
# Add a category to the BugCategory table
# ----------------------------------------------------------------------
function project_add_bug_category( $project_id, $category_name ) {

	global $db;

	$category_tbl	= BUG_CATEGORY_TBL;
	$f_id			= $category_tbl .".". CATEGORY_ID;
	$f_proj_id		= $category_tbl .".". CATEGORY_PROJECT_ID;
	$f_name			= $category_tbl .".". CATEGORY_NAME;

	$q = "INSERT INTO $category_tbl
			($f_proj_id, $f_name)
		  VALUES
			($project_id, '$category_name')";

	db_query($db, $q);
}

# ----------------------------------------------------------------------
# Check if Project Bug Category already exists
# INPUT:
#   Project Bug Category to Check and Project ID
# OUTPUT:
#   True if Project Bug Category already exists, otherwise false.
# ----------------------------------------------------------------------
function project_bug_category_exists( $project_id, $category_name ) {

    global $db;
    $category_tbl	= BUG_CATEGORY_TBL;
    $f_name			= $category_tbl .".". CATEGORY_NAME;
	$f_proj_id		= $category_tbl .".". CATEGORY_PROJECT_ID;

    $query = "	SELECT COUNT($f_name)
				FROM $category_tbl
				WHERE $f_name='$category_name'
					AND $f_proj_id=$project_id";

   $result = db_get_one( $db, $query );

    if ( 0 == $result ) {
        return false;
    } else {
        return true;
    }
}


# ----------------------------------------------------------------------
# Edit a category to the BugCategory table
# ----------------------------------------------------------------------
function project_edit_bug_category($project_id, $id, $name) {

	global $db;

	$category_tbl	= BUG_CATEGORY_TBL;
	$f_id			= $category_tbl .".". CATEGORY_ID;
	$f_proj_id		= $category_tbl .".". CATEGORY_PROJECT_ID;
	$f_name			= $category_tbl .".". CATEGORY_NAME;

	$history_tbl		= BUG_HISTORY_TBL;
	$f_field			= BUG_HISTORY_FIELD;
	$f_old_value		= BUG_HISTORY_OLD_VALUE;
	$f_new_value		= BUG_HISTORY_NEW_VALUE;

	$bug_category		= BUG_CATEGORY;

	# Get current component name
	$old_category = bug_get_category( $id );

	$q = "UPDATE $category_tbl
		  SET
			$f_name = '$name'
		  WHERE
			$f_id = $id
		  AND $f_proj_id = $project_id";

	db_query( $db, $q );


	# Update the history table to the new value where it was the old value
	$q1 = "UPDATE $history_tbl
		   SET $f_old_value = '$name'
		   WHERE $f_field = '$bug_category'
		   AND $f_old_value = '$old_category'";
	db_query( $db, $q1 );

	$q2 = "UPDATE $history_tbl
		   SET $f_new_value = '$name'
		   WHERE $f_field = '$bug_category'
		   AND $f_new_value = '$old_category'";
	db_query( $db, $q2 );


}

# ----------------------------------------------------------------------
# Deleted a category to the BugCategory table
# ----------------------------------------------------------------------
function project_remove_bug_category( $project_id, $id ) {

	global $db;

	$category_tbl	= BUG_CATEGORY_TBL;
	$f_id			= $category_tbl .".". CATEGORY_ID;
	$f_proj_id		= $category_tbl .".". CATEGORY_PROJECT_ID;
	$f_name			= $category_tbl .".". CATEGORY_NAME;

	$q = "DELETE FROM $category_tbl WHERE
		 $f_id = $id
		 AND $f_proj_id = $project_id";

	db_query($db, $q);
}

# ----------------------------------------------------------------------
# Return the specified Category from the BugCategory table
# ----------------------------------------------------------------------
function project_get_bug_category( $project_id, $id ) {

	global $db;

	$category_tbl	= BUG_CATEGORY_TBL;
	$f_id			= $category_tbl .".". CATEGORY_ID;
	$f_proj_id		= $category_tbl .".". CATEGORY_PROJECT_ID;
	$f_name			= $category_tbl .".". CATEGORY_NAME;

	$q = "SELECT $f_name
		  FROM $category_tbl
		  WHERE	$f_id = '$id'";

	$row = db_fetch_row($db, db_query($db, $q));

	return $row;
}

# ----------------------------------------------------------------------
# Return bug components for display in an html table
# This function will call another function which displays the records per page,
# page number, etc.
# INPUT:
#	project_id
#	field to order by in the table
#	order direction - default = Ascending
#	page_number
# ----------------------------------------------------------------------
function project_get_all_bug_components($project_id, $order_by=COMPONENT_NAME, $order_dir="ASC", $page_number=null) {

	global $db;

	$component_tbl	= BUG_COMPONENT_TBL;
	$f_id			= $component_tbl .".". COMPONENT_ID;
	$f_proj_id		= $component_tbl .".". COMPONENT_PROJECT_ID;
	$f_name			= $component_tbl .".". COMPONENT_NAME;

	$q	=	"SELECT	$f_id,
					$f_proj_id,
					$f_name
			FROM	$component_tbl
			WHERE	$f_proj_id = $project_id
			ORDER BY $order_by $order_dir";

	if( !is_null($page_number) ) {
		if( RECORDS_PER_PAGE_PROJECT_MANAGE_BUG_CATEGORY != 0 ) {

			# Add the limit clause to the query so that we only show n number of records per page
			$offset = ( ( $page_number - 1 ) *  RECORDS_PER_PAGE_PROJECT_MANAGE_BUG_COMPONENT );

			html_table_offset( 	db_num_rows( $db, db_query($db, $q) ),
								RECORDS_PER_PAGE_PROJECT_MANAGE_BUG_COMPONENT,
								$page_number );

			$q .= " LIMIT $offset, ". RECORDS_PER_PAGE_PROJECT_MANAGE_BUG_COMPONENT;

		}
	}
	$rows = db_fetch_array($db, db_query($db, $q));

	return $rows;
}

# ----------------------------------------------------------------------
# Add a component to the BugComponent table
# ----------------------------------------------------------------------
function project_add_bug_component( $project_id, $component_name ) {

	global $db;

	$component_tbl	= BUG_COMPONENT_TBL;
	$f_id			= $component_tbl .".". COMPONENT_ID;
	$f_proj_id		= $component_tbl .".". COMPONENT_PROJECT_ID;
	$f_name			= $component_tbl .".". COMPONENT_NAME;

	$q = "INSERT INTO $component_tbl
			($f_proj_id, $f_name)
		  VALUES
			($project_id, '$component_name')";

	db_query($db, $q);
}

# ----------------------------------------------------------------------
# Check if Project Bug Component already exists
# INPUT:
#  Project Bug Component to Check and Project ID
# OUTPUT:
#   True if Project Bug Component already exists, otherwise false.
# ----------------------------------------------------------------------
function project_bug_component_exists( $project_id, $component_name ) {

    global $db;
    $component_tbl	= BUG_COMPONENT_TBL;
    $f_name			= $component_tbl .".". COMPONENT_NAME;
	$f_proj_id		= $component_tbl .".". COMPONENT_PROJECT_ID;

    $query = "	SELECT COUNT($f_name)
				FROM $component_tbl
				WHERE $f_name='$component_name'
					AND $f_proj_id=$project_id";

   $result = db_get_one( $db, $query );

    if ( 0 == $result ) {
        return false;
    } else {
        return true;
    }
}

# ----------------------------------------------------------------------
# Edit a component to the BugComponent table
# ----------------------------------------------------------------------
function project_edit_bug_component($project_id, $id, $name) {

	global $db;

	$component_tbl	= BUG_COMPONENT_TBL;
	$f_id			= $component_tbl .".". COMPONENT_ID;
	$f_proj_id		= $component_tbl .".". COMPONENT_PROJECT_ID;
	$f_name			= $component_tbl .".". COMPONENT_NAME;

	$history_tbl		= BUG_HISTORY_TBL;
	$f_field			= BUG_HISTORY_FIELD;
	$f_old_value		= BUG_HISTORY_OLD_VALUE;
	$f_new_value		= BUG_HISTORY_NEW_VALUE;

	$bug_component		= BUG_COMPONENT;

	# Get current component name
	$old_component = bug_get_component( $id );

	# Update the component
	$q = "UPDATE $component_tbl
		  SET
			$f_name = '$name'
		  WHERE
			$f_id = $id
		  AND $f_proj_id = $project_id";

	db_query($db, $q);

	# Update the history table to the new value where it was the old value
	$q1 = "UPDATE $history_tbl
		   SET $f_old_value = '$name'
		   WHERE $f_field = '$bug_component'
		   AND $f_old_value = '$old_component'";
	db_query( $db, $q1 );

	$q2 = "UPDATE $history_tbl
		   SET $f_new_value = '$name'
		   WHERE $f_field = '$bug_component'
		   AND $f_new_value = '$old_component'";
	db_query( $db, $q2 );


}

# ----------------------------------------------------------------------
# Deleted a component to the BugComponent table
# ----------------------------------------------------------------------
function project_remove_bug_component( $project_id, $id ) {

	global $db;

	$component_tbl	= BUG_COMPONENT_TBL;
	$f_id			= $component_tbl .".". COMPONENT_ID;
	$f_proj_id		= $component_tbl .".". COMPONENT_PROJECT_ID;
	$f_name			= $component_tbl .".". COMPONENT_NAME;

	$q = "DELETE FROM $component_tbl WHERE
		 $f_id = $id
		 AND $f_proj_id = $project_id";

	db_query($db, $q);
}

# ----------------------------------------------------------------------
# Return the specified component from the BugComponent table
# ----------------------------------------------------------------------
function project_get_bug_component( $project_id, $id ) {

	global $db;

	$component_tbl	= BUG_COMPONENT_TBL;
	$f_id			= $component_tbl .".". COMPONENT_ID;
	$f_proj_id		= $component_tbl .".". COMPONENT_PROJECT_ID;
	$f_name			= $component_tbl .".". COMPONENT_NAME;

	$q = "SELECT $f_name
		  FROM $component_tbl
		  WHERE	$f_id = '$id'";

	$row = db_fetch_row($db, db_query($db, $q));

	return $row;
}

/*
# ----------------------------------------------------------------------
# Get project options, project show options and project user options.
# INPUT:
#   Username
# OUTPUT:
#   array containing default project details for the user
# ----------------------------------------------------------------------
function project_get_application_details($username) {

	$tbl_project			= PROJECT_TBL;
	$f_proj_id				= $tbl_project .".". PROJ_ID;
	$f_proj_name			= $tbl_project .".". PROJ_NAME;
	$f_db_name				= $tbl_project .".". PROJ_DBNAME;
	$f_test_upload			= $tbl_project .".". PROJ_TEST_UPLOAD_PATH;
	$f_req_upload			= $tbl_project .".". PROJ_REQ_UPLOAD_PATH;
	$f_test_run_upload		= $tbl_project .".". PROJ_TEST_RUN_UPLOAD_PATH;
	$f_test_plan_upload		= $tbl_project .".". PROJ_TEST_PLAN_UPLOAD_PATH;
	$f_bug_url				= $tbl_project .".". PROJ_BUG_URL_UPLOAD_PATH;
	$f_show_policy			= $tbl_project .".". PROJ_SHOW_CUSTOM_1;
	$f_show_claim			= $tbl_project .".". PROJ_SHOW_CUSTOM_2;
	$f_show_nnumber			= $tbl_project .".". PROJ_SHOW_CUSTOM_3;
	$f_show_quote			= $tbl_project .".". PROJ_SHOW_CUSTOM_4;
	$f_show_window			= $tbl_project .".". PROJ_SHOW_WINDOW;
	$f_show_object			= $tbl_project .".". PROJ_SHOW_OBJECT;
	$f_show_mem_stats		= $tbl_project .".". PROJ_SHOW_MEM_STATS;
	$f_show_custom_5			= $tbl_project .".". PROJ_SHOW_CUSTOM_5;
	$f_show_custom_6			= $tbl_project .".". PROJ_SHOW_CUSTOM_6;
	$f_show_priority		= $tbl_project .".". PROJ_SHOW_PRIORITY;
	$f_test_versions		= $tbl_project .".". PROJ_TEST_VERSIONS;

	$tbl_user				= USER_TBL;
	$f_user_id				= $tbl_user .".". USER_ID;
	$f_user_name			= $tbl_user .".". USER_UNAME;
	$f_password				= $tbl_user .".". USER_PWORD;
	$f_email				= $tbl_user .".". USER_EMAIL;
	$f_tempest_admin		= $tbl_user .".". USER_ADMIN;

	$tbl_proj_user_assoc	= PROJECT_USER_ASSOC_TBL;
	$f_proj_user_proj_id	= $tbl_proj_user_assoc .".". PROJ_USER_PROJ_ID;
	$f_proj_user_user_id	= $tbl_proj_user_assoc .".". PROJ_USER_USER_ID;
	$f_default_proj			= $tbl_proj_user_assoc .".". PROJ_USER_DEFAULT_PROJECT;
	$f_project_rights		= $tbl_proj_user_assoc .".". PROJ_USER_PROJECT_RIGHTS;
	$f_delete_rights		= $tbl_proj_user_assoc .".". PROJ_USER_DELETE_RIGHTS;

	$query = "	SELECT	$f_proj_id,
						$f_proj_name,
						$f_db_name,
						$f_test_upload,
						$f_req_upload,
						$f_test_run_upload,
						$f_test_plan_upload,
						$f_bug_url,
						$f_show_policy,
						$f_show_claim,
						$f_show_nnumber,
						$f_show_quote,
						$f_show_window,
						$f_show_object,
						$f_show_mem_stats,
						$f_show_custom_5,
						$f_show_custom_6,
						$f_show_priority,
						$f_test_versions,

						$f_user_id,
						$f_user_name,
						$f_password,
						$f_email,
						$f_tempest_admin,

						$f_default_proj,
						$f_project_rights,
						$f_delete_rights

				FROM	$tbl_project
				INNER JOIN $tbl_proj_user_assoc ON
					$f_proj_id = $f_proj_user_proj_id
				INNER JOIN $tbl_user ON
					$f_proj_user_user_id = $f_user_id
				WHERE $f_user_name = '$username'
					AND $f_default_proj = 'Y'";

    global $db;

    $row = db_fetch_row($db, db_query($db, $query));

    return $row;
}
*/
# ----------------------------------------------------------------------
# Get project options, project show options and project user options for
# selected project
# INPUT:
#   Username, Project name
# OUTPUT:
#   array containing project details for the user and selected project
# ----------------------------------------------------------------------
//function project_get_application_details_by_project()
function project_get_application_details($project_name, $username) {

	$tbl_project			= PROJECT_TBL;
	$f_proj_id				= $tbl_project .".". PROJ_ID;
	$f_proj_name			= $tbl_project .".". PROJ_NAME;
	$f_test_upload			= $tbl_project .".". PROJ_TEST_UPLOAD_PATH;
	$f_test_plan_upload		= $tbl_project .".". PROJ_TEST_PLAN_UPLOAD_PATH;
	$f_req_upload			= $tbl_project .".". PROJ_REQ_UPLOAD_PATH;
	$f_test_run_upload		= $tbl_project .".". PROJ_TEST_RUN_UPLOAD_PATH;
	$f_bug_url				= $tbl_project .".". PROJ_BUG_URL_UPLOAD_PATH;
	$f_man_test_doc_upload	= $tbl_project .".". PROJ_TEST_RUN_UPLOAD_PATH;
	$f_defect_upload_path	= $tbl_project .".". PROJ_DEFECT_UPLOAD_PATH;
	$f_show_policy			= $tbl_project .".". PROJ_SHOW_CUSTOM_1;
	$f_show_claim			= $tbl_project .".". PROJ_SHOW_CUSTOM_2;
	$f_show_nnumber			= $tbl_project .".". PROJ_SHOW_CUSTOM_3;
	$f_show_quote			= $tbl_project .".". PROJ_SHOW_CUSTOM_4;
	$f_show_window			= $tbl_project .".". PROJ_SHOW_WINDOW;
	$f_show_object			= $tbl_project .".". PROJ_SHOW_OBJECT;
	$f_show_mem_stats		= $tbl_project .".". PROJ_SHOW_MEM_STATS;
	$f_show_custom_5		= $tbl_project .".". PROJ_SHOW_CUSTOM_5;
	$f_show_custom_6		= $tbl_project .".". PROJ_SHOW_CUSTOM_6;
	$f_show_priority		= $tbl_project .".". PROJ_SHOW_PRIORITY;
	$f_show_test_input		= $tbl_project .".". PROJ_SHOW_TEST_INPUT;

	$tbl_user				= USER_TBL;
	$f_user_id				= $tbl_user .".". USER_ID;
	$f_username				= $tbl_user .".". USER_UNAME;
	$f_password				= $tbl_user .".". USER_PWORD;
	$f_email				= $tbl_user .".". USER_EMAIL;
	$f_tempest_admin		= $tbl_user .".". USER_ADMIN;
	$f_user_default_project	= $tbl_user .".". USER_DEFAULT_PROJECT;

	$tbl_proj_user_assoc	= PROJECT_USER_ASSOC_TBL;
	$f_proj_user_proj_id	= $tbl_proj_user_assoc .".". PROJ_USER_PROJ_ID;
	$f_proj_user_user_id	= $tbl_proj_user_assoc .".". PROJ_USER_USER_ID;
	$f_project_rights		= $tbl_proj_user_assoc .".". PROJ_USER_PROJECT_RIGHTS;
	$f_delete_rights		= $tbl_proj_user_assoc .".". PROJ_USER_DELETE_RIGHTS;

	global $db;

	$q = "	SELECT	$f_proj_id,
					$f_proj_name,
					$f_test_upload,
					$f_req_upload,
					$f_test_run_upload,
					$f_test_plan_upload,
					$f_defect_upload_path,
					$f_bug_url,
					$f_show_policy,
					$f_show_claim,
					$f_show_nnumber,
					$f_show_quote,
					$f_show_window,
					$f_show_object,
					$f_show_mem_stats,
					$f_show_custom_5,
					$f_show_custom_6,
					$f_show_priority,
					$f_show_test_input

				FROM	$tbl_project
				WHERE 	$f_proj_name = '$project_name'";

	$rs = &db_query($db, $q);
    $project_row = db_fetch_row($db, $rs);


	$q = "	SELECT	$f_proj_name,
					$f_user_id,
					$f_username,
					$f_password,
					$f_email,
					$f_tempest_admin,
					$f_user_default_project,
					$f_project_rights,
					$f_delete_rights

				FROM $tbl_project
				LEFT JOIN $tbl_proj_user_assoc ON $f_proj_id = $f_proj_user_proj_id
				LEFT JOIN $tbl_user ON $f_proj_user_user_id = $f_user_id
				WHERE $f_username = '$username'";

	$rs = db_query( $db, $q." AND $f_proj_name = '$project_name'" );
	$num = db_num_rows( $db, $rs );

	$row = db_fetch_row( $db, $rs );

	# check if associated with a project
    if ($num==0) {

    	# check if administrator
    	$rs = db_query($db, $q);
    	$row = db_fetch_row($db, $rs);

    	# if tempest admin
    	if($row[USER_ADMIN] == "Y") {

			$q = "	SELECT	$f_user_id,
							$f_username,
							$f_password,
							$f_email,
							$f_tempest_admin
					FROM	$tbl_user
					WHERE 	$f_username = '$username'";

			$row = db_fetch_row($db, db_query($db, $q));

			$row = array_merge($row, Array(	"tempest_rights" => "Y",
											"default_project" => "N",
											"user_rights" => 20,
											"delete_rights" => "Y" ) );
    	} else {

			// TODO revert to default project?
			error_report_show("logout.php", PROJECT_SWITCH_FAILED );
    	}
    }

    return array_merge($row, $project_row);
}

# ---------------------------------------------------------------------------------
# Archive project Test Results
# ---------------------------------------------------------------------------------
function project_archive_results($project_id, $archive_results) {

	$release_tbl        = RELEASE_TBL;
	$f_release_id       = RELEASE_TBL .".". RELEASE_ID;
	$f_project_id	    = RELEASE_TBL .".". PROJECT_ID;
	$f_release_archive  = RELEASE_TBL .".". RELEASE_ARCHIVE;

	$build_tbl 			= BUILD_TBL;
	$f_build_id			= BUILD_TBL .".". BUILD_ID;
	$f_build_rel_id		= BUILD_TBL .".". BUILD_REL_ID;
	$f_build_archive	= BUILD_TBL .".". BUILD_ARCHIVE;

	$testset_tbl		= TS_TBL;
	$f_testset_id		= TS_TBL .".". TS_ID;
	$f_testset_build_id	= TS_TBL .".". TS_BUILD_ID;
	$f_testset_archive	= TS_TBL .".". TS_ARCHIVE;

	$release_array = admin_get_release_array($project_id);

	global $db;

	foreach($release_array as $release_row) {

		$release_id = $release_row[RELEASE_ID];
		$builds = $release_row["builds"];

		if( isset($archive_results['releases'][$release_id]) ) {
			$q = "	UPDATE $release_tbl
					SET
						$f_release_archive = 'Y'
					WHERE
						$f_release_id = $release_id";
		} else {
			$q = "	UPDATE $release_tbl
					SET
						$f_release_archive = 'N'
					WHERE
						$f_release_id = $release_id";
		}

		db_query($db, $q);

		foreach($builds as $build_row) {

			$build_id = $build_row[BUILD_ID];
			$testsets = $build_row["testsets"];

			if( isset($archive_results['builds'][$build_id]) ) {
				$q = "	UPDATE $build_tbl
						SET
							$f_build_archive = 'Y'
						WHERE
							$f_build_id = $build_id";
			} else {
				$q = "	UPDATE $build_tbl
						SET
							$f_build_archive = 'N'
						WHERE
							$f_build_id = $build_id";
			}

			db_query($db, $q);

			foreach($testsets as $testset_row) {

				$testset_id = $testset_row[TS_ID];

				if( isset($archive_results['testsets'][$testset_id]) ) {
					$q = "	UPDATE $testset_tbl
							SET
								$f_testset_archive = 'Y'
							WHERE
								$f_testset_id = $testset_id";
				} else {
					$q = "	UPDATE $testset_tbl
							SET
								$f_testset_archive = 'N'
							WHERE
								$f_testset_id = $testset_id";
				}

				db_query($db, $q);
			}
		}
	}
}

# ---------------------------------------------------------------------------------
# Archive project Tests
# ---------------------------------------------------------------------------------
function project_archive_tests($project_id) {

	global $db;

	$tbl_test		= TEST_TBL;
	$f_name 		= $tbl_test .".". TEST_NAME;
	$f_type 		= $tbl_test .".". TEST_TESTTYPE;
	$f_priority 	= $tbl_test .".". TEST_PRIORITY;
	$f_id 			= $tbl_test .".". TEST_ID;
	$f_steps 		= $tbl_test .".". TEST_MANUAL;
	$f_script 		= $tbl_test .".". TEST_AUTOMATED;
	$f_status 		= $tbl_test .".". TEST_STATUS;
	$f_area 		= $tbl_test .".". TEST_AREA_TESTED;
	$f_deleted 		= $tbl_test .".". TEST_DELETED;
	$f_archive 		= $tbl_test .".". TEST_ARCHIVED;
	$f_area_tested	= $tbl_test .".". TEST_AREA_TESTED;
	$f_project_id 	= $tbl_test .".". PROJECT_ID;

	foreach(admin_get_tests($project_id) as $row_test) {

		if( session_records_ischecked("archive_tests", $row_test[TEST_ID]) ) {
			$q = "	UPDATE $tbl_test
					SET
						$f_archive = 'Y'
					WHERE
						$f_id = ".$row_test[TEST_ID];

			db_query($db, $q);
		} else {
			$q = "	UPDATE $tbl_test
					SET
						$f_archive = 'N'
					WHERE
						$f_id = ".$row_test[TEST_ID];

			db_query($db, $q);
		}
	}
}

/*
# ----------------------------------------------------------------------
# This function is a placeholder.
# It should query the Environment table but I need environments for a test
# run so I'm adding the function without the reference to the db.
# INPUT:
#   Project ID
# OUTPUT:
#   array containing environments
# ----------------------------------------------------------------------
function project_get_environment( $project_id=null ) { # function shouldn't accept null for project_ID

	$arr = array('', 'WAS', 'Apache', 'PUC21', 'QACL1');

	return $arr;

}
*/

# ------------------------------------
# $Log: project_api.php,v $
# Revision 1.12  2009/04/02 09:31:54  sca_gs
# commented functionality to remove files until agreement how to handle this situation is made
#
# Revision 1.11  2009/03/16 12:52:04  sca_gs
# fix to remove files for project as well if project is deleted provided by SPKannan
#
# Revision 1.10  2007/02/02 04:29:07  gth2
# updating session information when an admin user deletes a project - gth
#
# Revision 1.9  2006/12/05 04:58:18  gth2
# Allow users to rename project - gth
#
# Revision 1.8  2006/10/11 02:41:11  gth2
# adding phpMailer - gth
#
# Revision 1.7  2006/10/05 02:42:18  gth2
# adding file upload to the bug page - gth
#
# Revision 1.6  2006/08/01 23:41:03  gth2
# fixing bug reported by user related to user management page. - gth
#
# Revision 1.5  2006/06/30 00:55:42  gth2
# removing &$db from api files - gth
#
# Revision 1.4  2006/06/10 01:55:03  gth2
# no message
#
# Revision 1.3  2006/02/27 17:26:16  gth2
# added email functionality to bug tracker - gth
#
# Revision 1.2  2006/02/06 13:08:21  gth2
# fixing minor bugs - gth
#
# Revision 1.1.1.1  2005/11/30 23:01:12  gth2
# importing initial version - gth
#
# ------------------------------------

?>