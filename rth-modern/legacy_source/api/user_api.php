<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# User API
#
# $RCSfile: user_api.php,v $ $Revision: 1.6 $
# ------------------------------------

# ----------------------------------------------------------------------
# Get name of current user
# OUTPUT:
#   array containing users first name, last name and email address
# ----------------------------------------------------------------------
function user_get_current_user_name() {

	global $db;
	$user_tbl    = USER_TBL;

	$f_email     = USER_EMAIL;
	$f_firstname = USER_FNAME;
	$f_lastname  = USER_LNAME;
	$f_id        = USER_ID;

	$s_user_properties = session_get_user_properties() ;
	$user_id = $s_user_properties['user_id'];

	$query = "SELECT $f_email, $f_firstname, $f_lastname
				FROM $user_tbl
				WHERE $f_id = '$user_id'";

    $rs = & db_query( $db, $query );
    $row = db_fetch_row( $db, $rs ) ;
    return $row;
}

function user_get_id( $username ) {

    global $db;

    $user_tbl    = USER_TBL;
    $f_id        = USER_ID;
    $f_username  = USER_UNAME;

    $q = "	SELECT $f_id
			FROM $user_tbl
			WHERE $f_username = '$username'";

    $row = db_fetch_row($db, db_query($db, $q)) ;

    return $row[USER_ID];
}

function user_get_username($user_id) {

    global $db;

    $user_tbl    = USER_TBL;
    $f_id        = USER_ID;
    $f_username  = USER_UNAME;

    $q = "	SELECT $f_username
			FROM $user_tbl
			WHERE $f_id = '$user_id'";

    $row = db_fetch_row($db, db_query($db, $q)) ;

    return $row[USER_UNAME];
}

# ----------------------------------------------------------------------
# Get name of user
# INPUT:
#   $username: username of user
# OUTPUT:
#   array containing users first name, last name and email address
# ----------------------------------------------------------------------
function user_get_name_by_username($username) {
    global $db;
    $user_tbl    = USER_TBL;

    $f_email     = USER_EMAIL;
    $f_firstname = USER_FNAME;
    $f_lastname  = USER_LNAME;
    $f_username  = USER_UNAME;

    $s_user_properties = session_get_user_properties() ;
    $user_id = $s_user_properties['user_id'];

    $query = "SELECT $f_email, $f_firstname, $f_lastname
              FROM $user_tbl
              WHERE $f_username = '$username'";

    $rs = & db_query( $db, $query );
    $row = db_fetch_row( $db, $rs ) ;
    return $row;
}

# ----------------------------------------------------------------------
# Get username of all users associated to a project
# INPUT:
#   $project_id:
#   $blank: set equal to true if you want a blank added to the end of return value
# OUTPUT:
#   array containing usernames
# ----------------------------------------------------------------------
function user_get_usernames_by_project($project_id, $blank=null) {

    global $db;
    $user_tbl    			= USER_TBL;
    $assoc_tbl				= PROJECT_USER_ASSOC_TBL;
    $f_assoc_project_id		= $assoc_tbl .".". PROJ_USER_PROJ_ID;
    $f_assoc_user_id		= $assoc_tbl .".". PROJ_USER_USER_ID;
    $f_user_id				= $user_tbl .".". USER_ID;
    $f_username				= $user_tbl .".". USER_UNAME;
    $usernames				= array();

    $query = "SELECT $f_user_id, $f_username
              FROM $user_tbl, $assoc_tbl
              WHERE $f_assoc_user_id = $f_user_id
              AND $f_assoc_project_id = '$project_id'
              AND $f_username != ''
              ORDER BY $f_username ASC";

    $rs = & db_query( $db, $query );
	while($row = db_fetch_row( $db, $rs ) ) { ;
		array_push($usernames, $row[USER_UNAME]);
	}

 	if( !is_null($blank) && $blank == true ) {
		$usernames[] = "";
    }

    return $usernames;
}

# ----------------------------------------------------------------------
# Get the username of all ba_owners associated to a project
# INPUT:
#   $project_id:
#   $blank: set equal to true if you want a blank added to the end of return value
# OUTPUT:
#   array containing usernames of ba_owners
# ----------------------------------------------------------------------
function user_get_baowners_by_project($project_id, $blank=null) {

    global $db;
    $user_tbl    			= USER_TBL;
    $assoc_tbl				= PROJECT_USER_ASSOC_TBL;
    $f_assoc_project_id		= $assoc_tbl .".". PROJ_USER_PROJ_ID;
    $f_assoc_user_id		= $assoc_tbl .".". PROJ_USER_USER_ID;
    $f_baowner				= $assoc_tbl .".". PROJ_USER_BA_OWNER;
    $f_user_id				= $user_tbl .".". USER_ID;
    $f_username				= $user_tbl .".". USER_UNAME;
	$usernames 				= array();

    $query = "SELECT DISTINCT($f_username)
              FROM $user_tbl, $assoc_tbl
              WHERE $f_assoc_user_id = $f_user_id
              AND $f_assoc_project_id = '$project_id'
              AND $f_baowner = 'Y'
              AND $f_username != ''
              ORDER BY $f_username ASC";

    $rs = & db_query( $db, $query );
	while($row = db_fetch_row( $db, $rs ) ) { ;
		array_push($usernames, $row[USER_UNAME]);
    }

    if( !is_null($blank) && $blank == true ) {
	    	$usernames[] = "";
    }

    return $usernames;

}

# ----------------------------------------------------------------------
# Get username of all qa_owners associated to a project
# INPUT:
#   $project_id:
#   $blank: set equal to true if you want a blank added to the end of return value
# OUTPUT:
#   array containing usernames of qa_owners
# ----------------------------------------------------------------------
function user_get_qaowners_by_project($project_id, $blank=null) {

    global $db;
    $user_tbl    		= USER_TBL;
    $assoc_tbl			= PROJECT_USER_ASSOC_TBL;
    $f_assoc_project_id	= $assoc_tbl .".". PROJ_USER_PROJ_ID;
    $f_assoc_user_id	= $assoc_tbl .".". PROJ_USER_USER_ID;
    $f_qaowner			= $assoc_tbl .".". PROJ_USER_QA_OWNER;
    $f_user_id			= $user_tbl .".". USER_ID;
    $f_username			= $user_tbl .".". USER_UNAME;
    $usernames 			= array();

    $query = "SELECT $f_username
              FROM $user_tbl, $assoc_tbl
              WHERE $f_assoc_user_id = $f_user_id
              AND $f_assoc_project_id = '$project_id'
              AND $f_qaowner = 'Y'
              AND $f_username != ''
              ORDER BY $f_username ASC";

    $rs = & db_query( $db, $query );
	while($row = db_fetch_row( $db, $rs ) ) { ;
		array_push($usernames, $row[USER_UNAME]);
    }

    if( !is_null($blank) && $blank == true ) {
    	$usernames[] = "";
    }

    return $usernames;

}

# ----------------------------------------------------------------------
# Returns the string representation of a user rights value
# ----------------------------------------------------------------------
function user_get_rights_string( $rights ) {
	switch($rights) {
	case ADMIN :
		return lang_get("admin");
	case MANAGER :
		return lang_get("manager");
	case DEVELOPER :
		return lang_get("developer");
	case USER :
		return lang_get("user");
	default :
		return "";
	}
}

function user_get_info( $user_id ) {

	$tbl_user 				= USER_TBL;
	$f_user_id	 			= USER_TBL .".". USER_ID;
	$f_username 			= USER_TBL .".". USER_UNAME;
	$f_email 				= USER_TBL .".". USER_EMAIL;
	$f_first_name 			= USER_TBL .".". USER_FNAME;
	$f_last_name			= USER_TBL .".". USER_LNAME;
	$f_phone	 			= USER_TBL .".". USER_PHONE;
	$f_password 			= USER_TBL .".". USER_PWORD;
	$f_tempest_admin 		= USER_TBL .".". USER_ADMIN;
	$f_user_default_project	= $tbl_user .".". USER_DEFAULT_PROJECT;

	$q =	"	SELECT	$f_user_id,
						$f_username,
						$f_email,
						$f_first_name,
						$f_last_name,
						$f_phone,
						$f_password,
						$f_tempest_admin,
						$f_user_default_project
				FROM	$tbl_user
				WHERE	$f_user_id = $user_id";

	global $db;

	$row = db_fetch_row($db, db_query($db, $q));

	return $row;
}

function user_get_pref( $user_id, $field_name ) {

	$tbl_user 				= USER_TBL;
	$f_user_id	 			= $tbl_user .".". USER_ID;
	$f_username 			= $tbl_user .".". USER_UNAME;
	$f_email 				= $tbl_user .".". USER_EMAIL;
	$f_first_name 			= $tbl_user .".". USER_FNAME;
	$f_last_name			= $tbl_user .".". USER_LNAME;
	$f_phone	 			= $tbl_user .".". USER_PHONE;
	$f_password 			= $tbl_user .".". USER_PWORD;
	$f_tempest_admin 		= $tbl_user .".". USER_ADMIN;
	$f_user_default_project	= $tbl_user .".". USER_DEFAULT_PROJECT;

	$tbl_proj_user_assoc	= PROJECT_USER_ASSOC_TBL;
	$f_proj_user_proj_id	= $tbl_proj_user_assoc .".". PROJ_USER_PROJ_ID;
	$f_proj_user_user_id	= $tbl_proj_user_assoc .".". PROJ_USER_USER_ID;
	$f_project_rights		= $tbl_proj_user_assoc .".". PROJ_USER_PROJECT_RIGHTS;
	$f_delete_rights		= $tbl_proj_user_assoc .".". PROJ_USER_DELETE_RIGHTS;
	$f_email_testset		= $tbl_proj_user_assoc .".". PROJ_USER_EMAIL_TESTSET;
	$f_email_discussion		= $tbl_proj_user_assoc .".". PROJ_USER_EMAIL_REQ_DISCUSSION;
	$f_email_new_bug		= $tbl_proj_user_assoc .".". PROJ_USER_EMAIL_NEW_BUG;
	$f_email_update_bug		= $tbl_proj_user_assoc .".". PROJ_USER_EMAIL_UPDATE_BUG;
	$f_qa_owner				= $tbl_proj_user_assoc .".". PROJ_USER_QA_OWNER;
	$f_ba_owner				= $tbl_proj_user_assoc .".". PROJ_USER_BA_OWNER;

	$q =	"	SELECT	$field_name
				FROM 	$tbl_user
				INNER JOIN $tbl_proj_user_assoc ON $f_user_id = $f_proj_user_user_id
				WHERE	$f_user_id = $user_id";

	global $db;

	$value = db_get_one($db, $q);

	return $value;
}

function user_get_info_by_email( $email_address ) {

	$tbl_user 				= USER_TBL;
	$f_user_id	 			= USER_TBL .".". USER_ID;
	$f_username 			= USER_TBL .".". USER_UNAME;
	$f_email 				= USER_TBL .".". USER_EMAIL;
	$f_first_name 			= USER_TBL .".". USER_FNAME;
	$f_last_name			= USER_TBL .".". USER_LNAME;
	$f_phone	 			= USER_TBL .".". USER_PHONE;
	$f_password 			= USER_TBL .".". USER_PWORD;
	$f_tempest_admin 		= USER_TBL .".". USER_ADMIN;
	$f_user_default_project	= $tbl_user .".". USER_DEFAULT_PROJECT;

	$q =	"	SELECT	$f_user_id,
						$f_username,
						$f_email,
						$f_first_name,
						$f_last_name,
						$f_phone,
						$f_password,
						$f_tempest_admin,
						$f_user_default_project
				FROM	$tbl_user
				WHERE	$f_email = '$email_address'";

	global $db;

	$row = db_fetch_row($db, db_query($db, $q));

	return $row;
}

function user_get_projects_info( $user_id, $order_by=PROJ_NAME, $order_dir="ASC" ) {

	$tbl_user 				= USER_TBL;
	$f_user_id	 			= $tbl_user .".". USER_ID;
	$f_username 			= $tbl_user .".". USER_UNAME;
	$f_email 				= $tbl_user .".". USER_EMAIL;
	$f_first_name 			= $tbl_user .".". USER_FNAME;
	$f_last_name			= $tbl_user .".". USER_LNAME;
	$f_phone	 			= $tbl_user .".". USER_PHONE;
	$f_password 			= $tbl_user .".". USER_PWORD;
	$f_tempest_admin 		= $tbl_user .".". USER_ADMIN;
	$f_user_default_project	= $tbl_user .".". USER_DEFAULT_PROJECT;

	$tbl_project			= PROJECT_TBL;
	$f_proj_id				= $tbl_project .".". PROJ_ID;
	$f_proj_name			= $tbl_project .".". PROJ_NAME;
	$f_test_upload			= $tbl_project .".". PROJ_TEST_UPLOAD_PATH;
	$f_test_plan_upload		= $tbl_project .".". PROJ_TEST_PLAN_UPLOAD_PATH;
	$f_req_upload			= $tbl_project .".". PROJ_REQ_UPLOAD_PATH;
	$f_test_run_upload		= $tbl_project .".". PROJ_TEST_RUN_UPLOAD_PATH;
	$f_bug_url				= $tbl_project .".". PROJ_BUG_URL_UPLOAD_PATH;
	$f_man_test_doc_upload	= $tbl_project .".". PROJ_TEST_RUN_UPLOAD_PATH;
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

	$tbl_proj_user_assoc	= PROJECT_USER_ASSOC_TBL;
	$f_proj_user_proj_id	= $tbl_proj_user_assoc .".". PROJ_USER_PROJ_ID;
	$f_proj_user_user_id	= $tbl_proj_user_assoc .".". PROJ_USER_USER_ID;
	$f_project_rights		= $tbl_proj_user_assoc .".". PROJ_USER_PROJECT_RIGHTS;
	$f_delete_rights		= $tbl_proj_user_assoc .".". PROJ_USER_DELETE_RIGHTS;
	$f_email_testset		= $tbl_proj_user_assoc .".". PROJ_USER_EMAIL_TESTSET;
	$f_email_discussion		= $tbl_proj_user_assoc .".". PROJ_USER_EMAIL_REQ_DISCUSSION;
	$f_email_new_bug		= $tbl_proj_user_assoc .".". PROJ_USER_EMAIL_NEW_BUG;
	$f_email_update_bug		= $tbl_proj_user_assoc .".". PROJ_USER_EMAIL_UPDATE_BUG;
	$f_email_assigned		= $tbl_proj_user_assoc .".". PROJ_USER_EMAIL_ASSIGNED_BUG;
	$f_email_bugnote		= $tbl_proj_user_assoc .".". PROJ_USER_EMAIL_BUGNOTE_BUG;
	$f_email_status			= $tbl_proj_user_assoc .".". PROJ_USER_EMAIL_STATUS_BUG;
	$f_qa_owner				= $tbl_proj_user_assoc .".". PROJ_USER_QA_OWNER;
	$f_ba_owner				= $tbl_proj_user_assoc .".". PROJ_USER_BA_OWNER;

	$q = "	SELECT	$f_proj_id,
					$f_proj_name,
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

					$f_user_id,
					$f_username,
					$f_email,
					$f_first_name,
					$f_last_name,
					$f_phone,
					$f_password,
					$f_tempest_admin,
					$f_user_default_project,

					$f_project_rights,
					$f_delete_rights,
					$f_email_testset,
					$f_email_discussion,
					$f_email_new_bug,
					$f_email_update_bug,
					$f_email_assigned,
					$f_email_bugnote,
					$f_email_status,
					$f_qa_owner,
					$f_ba_owner

				FROM $tbl_user
				INNER JOIN $tbl_proj_user_assoc ON $f_user_id = $f_proj_user_user_id
				INNER JOIN $tbl_project ON $f_proj_user_proj_id = $f_proj_id
				WHERE $f_user_id = '$user_id'
				ORDER BY $order_by $order_dir";

	global $db;

	$rows = db_fetch_array($db, db_query($db, $q));

	return $rows;
}

function user_get_access_level() {

}

function user_get_email() {


}

function user_get_details_all( $project_id, $order_by=USER_UNAME, $order_dir="ASC", $page_number=0 ) {

	global $db;

	$tbl_user 		= USER_TBL;
	$f_user_id	 	= USER_TBL .".". USER_ID;
	$f_username 	= USER_TBL .".". USER_UNAME;
	$f_email 		= USER_TBL .".". USER_EMAIL;
	$f_first 		= USER_TBL .".". USER_FNAME;
	$f_last 		= USER_TBL .".". USER_LNAME;
	$f_phone	 	= USER_TBL .".". USER_PHONE;
	$f_password 	= USER_TBL .".". USER_PWORD;

	$tbl_proj_user_assoc	= PROJECT_USER_ASSOC_TBL;
	$f_proj_user_id			= $tbl_proj_user_assoc .".". PROJ_USER_ID;
	$f_proj_user_proj_id	= $tbl_proj_user_assoc .".". PROJ_USER_PROJ_ID;
	$f_proj_user_user_id	= $tbl_proj_user_assoc .".". PROJ_USER_USER_ID;
	$f_user_rights			= $tbl_proj_user_assoc .".". PROJ_USER_PROJECT_RIGHTS;
	$f_delete_rights		= $tbl_proj_user_assoc .".". PROJ_USER_DELETE_RIGHTS;
	$f_email_testset		= $tbl_proj_user_assoc .".". PROJ_USER_EMAIL_TESTSET;
	$f_email_discussion		= $tbl_proj_user_assoc .".". PROJ_USER_EMAIL_REQ_DISCUSSION;
	$f_qa_owner				= $tbl_proj_user_assoc .".". PROJ_USER_QA_OWNER;
	$f_ba_owner				= $tbl_proj_user_assoc .".". PROJ_USER_BA_OWNER;

	$q =	"	SELECT	$f_user_id,
						$f_username,
						$f_email,
						$f_first,
						$f_last,
						$f_phone,
						$f_password,
						$f_user_rights,
						$f_delete_rights,
						$f_email_testset,
						$f_email_discussion,
						$f_qa_owner,
						$f_ba_owner
				FROM $tbl_user
				INNER JOIN $tbl_proj_user_assoc ON $f_proj_user_user_id = $f_user_id
				WHERE $f_proj_user_proj_id = $project_id
				ORDER BY $order_by $order_dir";

	if($page_number!=0) {
		if( RECORDS_PER_PAGE_PROJECT_MANAGE_USERS!=0 ) {

			# Add the limit clause to the query so that we only show n number of records per page
			$offset = ( ( $page_number - 1 ) * RECORDS_PER_PAGE_PROJECT_MANAGE_USERS );

			html_table_offset( 	db_num_rows( $db, db_query($db, $q) ),
								RECORDS_PER_PAGE_PROJECT_MANAGE_USERS,
								$page_number );

			$q .= " LIMIT $offset, ".RECORDS_PER_PAGE_PROJECT_MANAGE_USERS;

		}
	}

	return db_fetch_array($db, db_query($db, $q));
}

function user_get_unassociated_projects($user_id, $order_by=null, $order_dir=null) {

	$match_found 		= false;
	$all_projects 		= project_get_all_projects_details($order_by, $order_dir);
	$user_projects 		= user_get_projects_info($user_id);
	$add_to_projects 	= array();

	foreach($all_projects as $project_row) {

		foreach($user_projects as $user_project_row) {

			if($project_row[PROJ_NAME]==$user_project_row[PROJ_NAME]) {

				$match_found = true;
			}
		}

		if(!$match_found) {
			$add_to_projects[] = $project_row[PROJ_NAME];
		}

		$match_found = false;
	}

	return $add_to_projects;
}

function user_get_all($order_by, $order_dir, $page_number, $deleted='Y') {

	global $db;

	$tbl_user				= USER_TBL;
	$f_user_id				= $tbl_user .".". USER_ID;
	$f_user_name			= $tbl_user .".". USER_UNAME;
	$f_user_lname			= $tbl_user .".". USER_LNAME;
	$f_user_fname			= $tbl_user .".". USER_FNAME;
	$f_email				= $tbl_user .".". USER_EMAIL;
	$f_user_deleted	 		= $tbl_user .".". USER_DELETED;
	$f_user_default_project	= $tbl_user .".". USER_DEFAULT_PROJECT;

	$tbl_proj_user_assoc	= PROJECT_USER_ASSOC_TBL;
	$f_proj_user_proj_id	= $tbl_proj_user_assoc .".". PROJ_USER_PROJ_ID;
	$f_proj_user_user_id	= $tbl_proj_user_assoc .".". PROJ_USER_USER_ID;

	$tbl_project			= PROJECT_TBL;
	$f_proj_id				= $tbl_project .".". PROJ_ID;

	$q =	"	SELECT	$f_user_id,
						$f_user_name,
						$f_user_fname,
						$f_user_lname,
						$f_email,
						$f_user_deleted
				FROM	$tbl_user
				ORDER BY $order_by $order_dir";
				//WHERE	$f_user_deleted != '". $deleted ."'

	if( RECORDS_PER_PAGE_PROJECT_EDIT_USERS!=0 ) {

		# Add the limit clause to the query so that we only show n number of records per page
		$offset = ( ( $page_number - 1 ) * RECORDS_PER_PAGE_PROJECT_EDIT_USERS );

		html_table_offset( 	db_num_rows( $db, db_query($db, $q) ),
							RECORDS_PER_PAGE_PROJECT_EDIT_USERS,
							$page_number );

		$q .= " LIMIT $offset, ".RECORDS_PER_PAGE_PROJECT_EDIT_USERS;

	}

	$rs = db_query($db, $q);

	return db_fetch_array($db, $rs);
}

function user_add(	$username,
					$password,
					$first_name,
					$last_name,
					$email,
					$phone,
					$tempest_rights,
					$delete_rights,
					$email_testset,
					$email_discussion,
					$qa_owner,
					$ba_owner,
					$add_to_projects,
					$project_rights,
					$default_project) {

	$tbl_user 				= USER_TBL;
	$f_user_id	 			= $tbl_user .".". USER_ID;
	$f_username 			= $tbl_user .".". USER_UNAME;
	$f_email 				= $tbl_user .".". USER_EMAIL;
	$f_first_name 			= $tbl_user .".". USER_FNAME;
	$f_last_name			= $tbl_user .".". USER_LNAME;
	$f_phone	 			= $tbl_user .".". USER_PHONE;
	$f_password 			= $tbl_user .".". USER_PWORD;
	$f_tempest_admin 		= $tbl_user .".". USER_ADMIN;
	$f_user_default_project	= $tbl_user .".". USER_DEFAULT_PROJECT;

    # when using encryption, encrypt password cookie
    if (LOGIN_METHOD == 'MD5') {
        $password = auth_process_plain_password($password);
    }

	global $db;

	$q = "	INSERT INTO $tbl_user
				(	$f_username,
					$f_password,
					$f_first_name,
					$f_last_name,
					$f_email,
					$f_phone,
					$f_tempest_admin,
					$f_user_default_project )
			VALUES
				(	'$username',
					'$password',
					'$first_name',
					'$last_name',
					'$email',
					'$phone',
					'$tempest_rights',
					'$default_project' )";

	db_query($db, $q);

	# get new user id
	$q = "	SELECT $f_user_id
			FROM $tbl_user
			WHERE $f_username = '$username'";

	$row 			= db_fetch_row($db, db_query($db, $q));
	$new_user_id 	= $row[USER_ID];

	$tbl_proj_user_assoc	= PROJECT_USER_ASSOC_TBL;
	$f_proj_user_id			= $tbl_proj_user_assoc .".". PROJ_USER_ID;
	$f_proj_user_proj_id	= $tbl_proj_user_assoc .".". PROJ_USER_PROJ_ID;
	$f_proj_user_user_id	= $tbl_proj_user_assoc .".". PROJ_USER_USER_ID;
	$f_user_rights			= $tbl_proj_user_assoc .".". PROJ_USER_PROJECT_RIGHTS;
	$f_delete_rights		= $tbl_proj_user_assoc .".". PROJ_USER_DELETE_RIGHTS;
	$f_email_testset		= $tbl_proj_user_assoc .".". PROJ_USER_EMAIL_TESTSET;
	$f_email_discussion		= $tbl_proj_user_assoc .".". PROJ_USER_EMAIL_REQ_DISCUSSION;
	$f_qa_owner				= $tbl_proj_user_assoc .".". PROJ_USER_QA_OWNER;
	$f_ba_owner				= $tbl_proj_user_assoc .".". PROJ_USER_BA_OWNER;

	$tbl_project			= PROJECT_TBL;
	$f_proj_id				= $tbl_project .".". PROJ_ID;
	$f_proj_name			= $tbl_project .".". PROJ_NAME;


	foreach($add_to_projects as $project_id) {

		$q = "	INSERT INTO $tbl_proj_user_assoc
					(	$f_proj_user_user_id,
						$f_proj_user_proj_id,
						$f_user_rights,
						$f_delete_rights,
						$f_email_testset,
						$f_email_discussion,
						$f_qa_owner,
						$f_ba_owner )
				VALUES
					(	$new_user_id,
						$project_id,
						'$project_rights',
						'$delete_rights',
						'$email_testset',
						'$email_discussion',
						'$qa_owner',
						'$ba_owner' )";

		db_query($db, $q);
	}
}

function user_delete( $user_id ) {

	$tbl_user 			= USER_TBL;
	$f_user_id	 		= USER_TBL .".". USER_ID;
	$f_user_deleted	 	= USER_TBL .".". USER_DELETED;

	global $db;

	$q = "	UPDATE $tbl_user
			SET		$f_user_deleted = 'Y'
			WHERE	$f_user_id = $user_id";

	db_query($db, $q);
}

function user_edit(	$username,
					$password,
					$first_name,
					$last_name,
					$email,
					$phone,
					$tempest_rights,
					$default_project,
					$project_prefs,
					$add_to_projects ) {

	$tbl_user 				= USER_TBL;
	$f_user_id	 			= $tbl_user .".". USER_ID;
	$f_username 			= $tbl_user .".". USER_UNAME;
	$f_email 				= $tbl_user .".". USER_EMAIL;
	$f_first_name 			= $tbl_user .".". USER_FNAME;
	$f_last_name			= $tbl_user .".". USER_LNAME;
	$f_phone	 			= $tbl_user .".". USER_PHONE;
	$f_password 			= $tbl_user .".". USER_PWORD;
	$f_tempest_admin 		= $tbl_user .".". USER_ADMIN;
	$f_user_default_project	= $tbl_user .".". USER_DEFAULT_PROJECT;

	global $db;

	$user_id = user_get_id($username);

	# Update user table
	$q = "	UPDATE $tbl_user
			SET	$f_username = '$username',";
				if( !empty($password) ) {

					if (LOGIN_METHOD == 'MD5') {
						$password = auth_process_plain_password($password);
					}

					$q .= "$f_password = '$password',";
				}
	$q .= "		$f_first_name = '$first_name',
				$f_last_name = '$last_name',
				$f_email = '$email',
				$f_phone = '$phone',
				$f_tempest_admin = '$tempest_rights',
				$f_user_default_project = '$default_project'
			WHERE
				$f_user_id = $user_id";

	db_query($db, $q);

	$tbl_proj_user_assoc	= PROJECT_USER_ASSOC_TBL;
	$f_proj_user_id			= $tbl_proj_user_assoc .".". PROJ_USER_ID;
	$f_proj_user_proj_id	= $tbl_proj_user_assoc .".". PROJ_USER_PROJ_ID;
	$f_proj_user_user_id	= $tbl_proj_user_assoc .".". PROJ_USER_USER_ID;
	$f_user_rights			= $tbl_proj_user_assoc .".". PROJ_USER_PROJECT_RIGHTS;
	$f_delete_rights		= $tbl_proj_user_assoc .".". PROJ_USER_DELETE_RIGHTS;
	$f_email_testset		= $tbl_proj_user_assoc .".". PROJ_USER_EMAIL_TESTSET;
	$f_email_discussion		= $tbl_proj_user_assoc .".". PROJ_USER_EMAIL_REQ_DISCUSSION;
	$f_email_new_bug		= $tbl_proj_user_assoc .".". PROJ_USER_EMAIL_NEW_BUG;
	$f_email_update_bug		= $tbl_proj_user_assoc .".". PROJ_USER_EMAIL_UPDATE_BUG;
	$f_email_assigned		= $tbl_proj_user_assoc .".". PROJ_USER_EMAIL_ASSIGNED_BUG;
	$f_email_bugnote		= $tbl_proj_user_assoc .".". PROJ_USER_EMAIL_BUGNOTE_BUG;
	$f_email_status			= $tbl_proj_user_assoc .".". PROJ_USER_EMAIL_STATUS_BUG;
	$f_qa_owner				= $tbl_proj_user_assoc .".". PROJ_USER_QA_OWNER;
	$f_ba_owner				= $tbl_proj_user_assoc .".". PROJ_USER_BA_OWNER;

	$tbl_project			= PROJECT_TBL;
	$f_proj_id				= $tbl_project .".". PROJ_ID;
	$f_proj_name			= $tbl_project .".". PROJ_NAME;

	# update associations

	# change prefs or delete associations
	foreach($project_prefs as $project_id => $value) {

		$project_id = project_get_id( $value['project_name'] );

		if( $value['remove']=='N') {
			$q = "	UPDATE $tbl_proj_user_assoc
					SET
						$f_user_rights = '".$value['project_rights']."',
						$f_delete_rights = '".$value['delete_rights']."',
						$f_email_testset = '".$value['email_testset']."',
						$f_email_discussion = '".$value['email_discussion']."',
						$f_email_new_bug = '".$value['email_new_bug']."',
						$f_email_update_bug = '".$value['email_update_bug']."',
						$f_email_assigned = '".$value['email_assigned_bug']."',
						$f_email_bugnote = '".$value['email_bugnote_bug']."',
						$f_email_status = '".$value['email_status_bug']."',
						$f_qa_owner = '".$value['qa_owner']."',
						$f_ba_owner = '".$value['ba_owner']."'
					WHERE
						$f_proj_user_proj_id = $project_id
						AND $f_proj_user_user_id = $user_id";
		} else {
			$q = "	DELETE FROM $tbl_proj_user_assoc
					WHERE
						$f_proj_user_proj_id = $project_id
						AND $f_proj_user_user_id = $user_id";
		}

		db_query($db, $q);
	}


	# add associations
	foreach($add_to_projects as $project_name) {

		$project_id = project_get_id( $project_name );

		$q = "	INSERT INTO $tbl_proj_user_assoc
					(	$f_proj_user_user_id,
						$f_proj_user_proj_id,
						$f_user_rights,
						$f_delete_rights,
						$f_email_testset,
						$f_email_discussion,
						$f_qa_owner,
						$f_ba_owner )
				VALUES
					(	$user_id,
						$project_id,
						'10',
						'N',
						'N',
						'N',
						'N',
						'N' )";

		db_query($db, $q);
	}
}

function user_get_default_project_name( $username ) {

	$tbl_project			= PROJECT_TBL;
	$f_proj_id				= $tbl_project .".". PROJ_ID;
	$f_proj_name			= $tbl_project .".". PROJ_NAME;

	$tbl_user				= USER_TBL;
	$f_user_id				= $tbl_user .".". USER_ID;
	$f_username				= $tbl_user .".". USER_UNAME;
	$f_user_default_project	= $tbl_user .".". USER_DEFAULT_PROJECT;

	$tbl_proj_user_assoc	= PROJECT_USER_ASSOC_TBL;
	$f_proj_user_proj_id	= $tbl_proj_user_assoc .".". PROJ_USER_PROJ_ID;
	$f_proj_user_user_id	= $tbl_proj_user_assoc .".". PROJ_USER_USER_ID;

	global $db;

	$q = "	SELECT	$f_proj_name
			FROM $tbl_user
			INNER JOIN $tbl_project ON $f_proj_id = $f_user_default_project
			WHERE $f_username = '$username'";

	$default_project = db_get_one($db, $q);

	return $default_project;
}

function user_get_default_project_id( $username ) {

	$tbl_project			= PROJECT_TBL;
	$f_proj_id				= $tbl_project .".". PROJ_ID;
	$f_proj_name			= $tbl_project .".". PROJ_NAME;

	$tbl_user				= USER_TBL;
	$f_user_id				= $tbl_user .".". USER_ID;
	$f_username				= $tbl_user .".". USER_UNAME;
	$f_user_default_project	= $tbl_user .".". USER_DEFAULT_PROJECT;

	$tbl_proj_user_assoc	= PROJECT_USER_ASSOC_TBL;
	$f_proj_user_proj_id	= $tbl_proj_user_assoc .".". PROJ_USER_PROJ_ID;
	$f_proj_user_user_id	= $tbl_proj_user_assoc .".". PROJ_USER_USER_ID;

	global $db;

	$q = "	SELECT	$f_user_default_project
			FROM $tbl_user
			WHERE $f_username = '$username'";

	$default_project = db_get_one($db, $q);

	return $default_project;
}

# ----------------------------------------------------------------------
# Compares the $rights value with the users own access rights for the
# given project id.
#
# INPUT:
#	project id
#	user id
#	access rights required
# OUTPUT:
#	True or False
# ----------------------------------------------------------------------
function user_has_rights( $project_id, $user_id, $project_rights ) {

	global $db;
	$tbl_project			= PROJECT_TBL;
	$f_proj_id				= $tbl_project .".". PROJ_ID;
	$f_proj_name			= $tbl_project .".". PROJ_NAME;

	$tbl_user				= USER_TBL;
	$f_user_id				= $tbl_user .".". USER_ID;
	$f_username				= $tbl_user .".". USER_UNAME;
	$f_tempest_admin		= $tbl_user .".". USER_ADMIN;

	$tbl_proj_user_assoc	= PROJECT_USER_ASSOC_TBL;
	$f_proj_user_proj_id	= $tbl_proj_user_assoc .".". PROJ_USER_PROJ_ID;
	$f_proj_user_user_id	= $tbl_proj_user_assoc .".". PROJ_USER_USER_ID;
	$f_project_rights		= $tbl_proj_user_assoc .".". PROJ_USER_PROJECT_RIGHTS;

	# get user project rights
	$q = "SELECT $f_project_rights
		  FROM $tbl_proj_user_assoc
		  WHERE $f_proj_user_proj_id = '$project_id'
		  AND $f_proj_user_user_id = '$user_id'";
	$user_project_rights = db_get_one($db, $q);

	# get user tempest rights
	$q = "	SELECT	$f_tempest_admin
			FROM	$tbl_user
			WHERE	$f_user_id = '$user_id'";
	$user_tempest_admin = db_get_one($db, $q);

	if( $user_project_rights>=$project_rights || $user_tempest_admin=="Y" ) {
		return true;
	} else {
		return false;
	}

}

function user_mail_by_pref( $email_pref ) {

	global $db;

	$project_properties     = session_get_project_properties();
	$project_id				= $project_properties['project_id'];

	$tbl_user 		= USER_TBL;
	$f_user_id		= USER_TBL .".". USER_ID;
	$f_user_email	= USER_TBL .".". USER_EMAIL;

	$tbl_proj_user_assoc	= PROJECT_USER_ASSOC_TBL;
	$f_proj_user_id			= PROJECT_USER_ASSOC_TBL .".". PROJ_USER_USER_ID;
	$f_proj_id				= PROJECT_USER_ASSOC_TBL .".". PROJ_USER_PROJ_ID;
	$f_proj_email_testset	= PROJECT_USER_ASSOC_TBL .".". $email_pref;

	$q = "	SELECT $f_user_email
			FROM $tbl_user
			INNER JOIN $tbl_proj_user_assoc ON $f_user_id = $f_proj_user_id
			WHERE $f_proj_email_testset = 'Y'
				AND $f_proj_id = '$project_id'";

	$rs = db_query($db, $q);

	return db_fetch_array($db, $rs);
}


# ----------------------------------------------------------------------
# Returns comma seperated email addresses of a single user or multiple users
# INPUT:
#	username - this can be an array of usernames or a single username
# OUTPUT:
#	comma seperated e-mail addresses
# ----------------------------------------------------------------------
function user_get_email_by_username( $username ) {

	if( empty($username) ) {
		return;
	}

	global $db;

	$user_tbl    = USER_TBL;
    $f_email     = USER_EMAIL;
	$f_username  = USER_UNAME;
	$users		 = '';
	$email_to	 = '';

	# Build the usernames for use in the IN clause below
	# We have to treat arrays differntly from a single username
	if( is_array( $username ) ) {

		foreach( $username as $user ) {

			$users = $users ."'". $user ."',";
		}
		$users = substr( $users, 0, -1 );  # remove trailing comma
	}
	else { # there is only one user selected
		$users = "'". $username ."'";
	}

	# Get email addresses based on username
	$q = "SELECT $f_email
		  FROM $user_tbl
		  WHERE $f_username IN ( $users )";
	$rs = & db_query( $db, $q );

	# build the list of email addresses
	while( 	$row = db_fetch_row( $db, $rs ) ) {
		$email_to = $email_to . $row['email'] .",";
	}
	$email_to = substr( $email_to, 0, -1 );  # remove trailing comma

    return $email_to;

}

# ----------------------------------------------------------------------
# Returns comma seperated email addresses of a single user or multiple users
# INPUT:
#	username - this can be an array of usernames or a single username
# OUTPUT:
#	comma seperated e-mail addresses
# ----------------------------------------------------------------------
function user_get_email_by_user_id( $user_ids ) {

	if( empty($user_ids) || !is_array($user_ids) ) {
		return;
	}

	global $db;

	$user_tbl    = USER_TBL;
    $f_email     = USER_EMAIL;
	$f_user_id	 = USER_ID;
	$ids		 = '';
	$email_to	 = '';

	# Build the usernames for use in the IN clause below
	foreach( $user_ids as $user_id ) {

		$ids = $ids ."'". $user_id ."',";
	}

	# remove trailing comma
	$ids = substr( $ids, 0, -1 ); 

	# Get email addresses based on username
	$q = "SELECT $f_email
		  FROM $user_tbl
		  WHERE $f_user_id IN ( $ids )";
	$rs = & db_query( $db, $q );

	# build the list of email addresses
	while( 	$row = db_fetch_row( $db, $rs ) ) {
		$email_to = $email_to . $row['email'] .",";
	}
	$email_to = substr( $email_to, 0, -1 );  # remove trailing comma

    return $email_to;

}


function user_requirement_notifications( $project_id, $user_id ) {

	$tbl_notify 	= REQ_NOTIFY_TBL;
	$f_id			= REQ_NOTIFY_TBL .".". REQ_NOTIFY_ID;
	$f_req_id		= REQ_NOTIFY_TBL .".". REQ_NOTIFY_REQ_ID;
	$f_user_id		= REQ_NOTIFY_TBL .".". REQ_NOTIFY_USER_ID;

	$rows = array();

	global $db;

	$q = "	SELECT
				$f_req_id
			FROM
				$tbl_notify
			WHERE
				$f_user_id = $user_id";

	$rs = db_query( $db, $q );
	while( $fields = db_fetch_row($db, $rs) ) {

		$rows[$fields[REQ_NOTIFY_REQ_ID]] = "";
	}

	return $rows;
}

function user_edit_requirement_notifications( $project_id, $user_id, $s_variable ) {

	$tbl_notify 	= REQ_NOTIFY_TBL;
	$f_id			= REQ_NOTIFY_TBL .".". REQ_NOTIFY_ID;
	$f_req_id		= REQ_NOTIFY_TBL .".". REQ_NOTIFY_REQ_ID;
	$f_user_id		= REQ_NOTIFY_TBL .".". REQ_NOTIFY_USER_ID;

	global $db;

	$req_ids = requirement_get_all_ids($project_id);
	foreach( $req_ids as $row) {

		if( session_records_ischecked($s_variable, $row[REQ_ID]) ) {

			# Check for associations between TestSet and the Test
			$query_check = "
				SELECT $f_id
				FROM $tbl_notify
				WHERE
					$f_user_id = $user_id
					AND $f_req_id = ".$row[REQ_ID];

			$num_check	= db_num_rows( $db, db_query($db, $query_check) );

			if($num_check == 0) {
				$query_Assoc = "
					INSERT INTO	$tbl_notify
						($f_user_id, $f_req_id )
					VALUES
						($user_id, ". $row[REQ_ID] .")";
				db_query($db, $query_Assoc);
			}

		} else {

			$query_Assoc = "
				DELETE FROM $tbl_notify
				WHERE
					$f_req_id = ".$row[REQ_ID]."
					AND  $f_user_id = $user_id";

			db_query($db, $query_Assoc);
		}
	}
}

function user_get_display_name($project_id, $user) {

	$tbl_user 				= USER_TBL;
	$f_user_id	 			= USER_TBL .".". USER_ID;
	$f_username 			= USER_TBL .".". USER_UNAME;
	$f_email 				= USER_TBL .".". USER_EMAIL;
	$f_first_name 			= USER_TBL .".". USER_FNAME;
	$f_last_name			= USER_TBL .".". USER_LNAME;
	$f_password 			= USER_TBL .".". USER_PWORD;

	global $db;

	if( is_numeric($user) ) {

		$where = "$f_user_id = $user";
	} else {

		$where = "$f_username = '$user'";
	}

	$q = "	SELECT
				CONCAT($f_last_name,', ',$f_first_name) AS full_name,
				$f_username AS username
			FROM
				$tbl_user
			WHERE $where";

	$rs = db_query($db, $q);
	$row = db_fetch_row($db, $rs);

	return $row["full_name"];
}

# This function stores a reset password link which the user has to click on
# in a confirmation email.
# INPUTS
#	$user - email address
#	$reset_link
# OUTPUTS
#
function user_new_reset_password($user, $reset_link) {

	$tbl_reset_pass			= RESET_PASS_TBL;
	$f_reset_pass_id		= $tbl_reset_pass .".". RESET_PASS_ID;
	$f_reset_pass_link		= $tbl_reset_pass .".". RESET_PASS_LINK;
	$f_reset_pass_user		= $tbl_reset_pass .".". RESET_PASS_USER;
	$f_reset_pass_used		= $tbl_reset_pass .".". RESET_PASS_RESET_USED;
	$f_reset_pass_expires	= $tbl_reset_pass .".". RESET_PASS_EXPIRES;

	global $db;

	$expires	= date_get_short_dt( strtotime("+1 day") );

	$q = "	INSERT INTO $tbl_reset_pass
				($f_reset_pass_link, $f_reset_pass_user, $f_reset_pass_used, $f_reset_pass_expires)
			VALUES
				('$reset_link', '$user', 'N', '$expires')";

	db_query($db, $q);
}

# This function resets a users password if they request it.
# INPUTS
#	$reset_link
#	$new_password
# OUTPUTS
# 	users email address
function user_reset_password($reset_link, $new_password) {

	$tbl_reset_pass			= RESET_PASS_TBL;
	$f_reset_pass_id		= $tbl_reset_pass .".". RESET_PASS_ID;
	$f_reset_pass_link		= $tbl_reset_pass .".". RESET_PASS_LINK;
	$f_reset_pass_user		= $tbl_reset_pass .".". RESET_PASS_USER;
	$f_reset_pass_used		= $tbl_reset_pass .".". RESET_PASS_RESET_USED;
	$f_reset_pass_expires	= $tbl_reset_pass .".". RESET_PASS_EXPIRES;

	$tbl_user 				= USER_TBL;
	$f_user_id	 			= $tbl_user .".". USER_ID;
	$f_username 			= $tbl_user .".". USER_UNAME;
	$f_email 				= $tbl_user .".". USER_EMAIL;
	$f_first_name 			= $tbl_user .".". USER_FNAME;
	$f_last_name			= $tbl_user .".". USER_LNAME;
	$f_phone	 			= $tbl_user .".". USER_PHONE;
	$f_password 			= $tbl_user .".". USER_PWORD;
	$f_tempest_admin 		= $tbl_user .".". USER_ADMIN;
	$f_user_default_project	= $tbl_user .".". USER_DEFAULT_PROJECT;

	global $db;

	# fetch the users email address where the record has not expired,
	# and reset link has not already been accessed
	$current_date	= date_get_short_dt();

	$q = "	SELECT $f_reset_pass_user
			FROM $tbl_reset_pass
			WHERE $f_reset_pass_link = '$reset_link'
				AND $f_reset_pass_expires > '$current_date'
				AND $f_reset_pass_used = 'N'";

	$email	= db_get_one($db, $q);

	# if a users email address was returned
	if( $email ) {

		# mark the reset link as used
		$q = "	UPDATE $tbl_reset_pass
				SET $f_reset_pass_used = 'Y'
				WHERE $f_reset_pass_link = '$reset_link'";
		db_query($db, $q);

		# encrypt password
		if (LOGIN_METHOD == 'MD5') {
			$new_password = auth_process_plain_password($new_password);
		}

		# change the users password
		$q = "	UPDATE $tbl_user
				SET $f_password = '$new_password'
				WHERE $f_email = '$email'";

		db_query($db, $q);
	}

	return $email;
}


# ------------------------------------
# $Log: user_api.php,v $
# Revision 1.6  2007/02/03 10:26:19  gth2
# no message
#
# Revision 1.5  2006/12/05 05:01:42  gth2
# display deleted users on user page - gth
#
# Revision 1.4  2006/06/30 00:55:43  gth2
# removing &$db from api files - gth
#
# Revision 1.3  2006/02/27 17:25:53  gth2
# added email functionality to bug tracker - gth
#
# Revision 1.2  2006/02/06 13:08:21  gth2
# fixing minor bugs - gth
#
# Revision 1.1.1.1  2005/11/30 23:01:13  gth2
# importing initial version - gth
#
# ------------------------------------
?>