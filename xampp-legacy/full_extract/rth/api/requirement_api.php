<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Requirement API
#
# $RCSfile: requirement_api.php,v $  
# $Revision: 1.21 $
# ---------------------------------------------------------------------


# ----------------------------------------------------------------------
# Print Requirement submenu
# INPUT:
#   Current Page (so that it will not be shown as a hyperlink)
# ----------------------------------------------------------------------
function requirement_menu_print($page) {

	$menu_items = array(
		lang_get('req_link') => 'requirement_page.php',
		lang_get('req_folder_view') => "requirement_associations_page.php",
		lang_get('req_add_rec_link') => 'requirement_add_page.php?type=R',
		lang_get('req_add_file_link') => 'requirement_add_page.php?type=F',
		lang_get('req_notification') => 'requirement_notification_page.php',
		lang_get('traceability_matrix_report') => 'requirement_traceability_matrix_page.php');
		//lang_get('req_add_folder') => 'requirement_add_folder.php',
		//lang_get('req_search') => 'requirement_search.php' );

    html_print_sub_menu( $page, $menu_items );
}

# ----------------------------------------------------------------------
# Print Test Menu that displays Test Steps, File Upload, and Requirement Associaton
# INPUT:
#   TestID
# ----------------------------------------------------------------------
function req_sub_menu_print( $req_id, $req_version_id, $page, $tab  ) {

	$url = $page ."?req_id=$req_id&amp;req_version_id=$req_version_id";

	$style_enabled = 'page-numbers';
	$style_disabled = 'page-numbers-disabled';

	print"<br>";
	print"<div class='center'>". NEWLINE;
	print"<form name='req_detail_tab' method=post action='$url'>". NEWLINE;
	print"<table class='width70'>". NEWLINE;
	print"<tr>". NEWLINE;

	print"<td class='menu' width='25%'>". NEWLINE;
	if( $tab == '1' ) {
		print lang_get('req_assoc');
	}
	else {
		$url = $page . "?tab=1";
		print"<a href='$url'>". lang_get('req_assoc') ."</a>";
	}
	print"</td>";

	print"<td class='menu' width='25%'>". NEWLINE;
	if( $tab == '2' ) {
		print lang_get( 'req_test_assoc' );
	}
	else {
		$url = $page . "?tab=2";
		print"<a href='$url'>". lang_get('req_test_assoc') ."</a>";
	}
	print"</td>". NEWLINE;

	print"<td class='menu' width='25%'>". NEWLINE;
	if( $tab == '4' ) {
		print lang_get('req_release_assoc');
	}
	else {
		$url = $page . "?tab=4";
		print"<a href='$url'>". lang_get('req_release_assoc') ."</a>";
	}
	print"</td>";

	print"<td class='menu' width='25%'>". NEWLINE;
	if( $tab == '3' ) {
		print lang_get('req_discussions');
	}
	else {
		$url = $page . "?tab=3";
		print"<a href='$url'>". lang_get('req_discussions') ."</a>";
	}
	print"</td>";

	print"</tr>". NEWLINE;
	print"</table>". NEWLINE;
	print"</form>". NEWLINE;
	print"</div>". NEWLINE;

}

# ----------------------------------------------------------------------
# Get requirement name
# INPUT:
#   requirement id
# OUTPUT:
#   Corresponding requirement name
# ----------------------------------------------------------------------
function requirement_get_name( $req_id ) {

    global $db;
    $req_tbl             = REQ_TBL;
    $f_req_id            = REQ_ID;
    $f_req_name          = REQ_FILENAME;

    $query = "SELECT $f_req_name
              FROM $req_tbl
              WHERE $f_req_id = '$req_id'";

     $req_name = db_get_one( $db, $query );

     return $req_name;
}

# ----------------------------------------------------------------------
# Get list of test priorities
# OUTPUT:
#   Array of test available priorities
# ----------------------------------------------------------------------
function requirement_get_priorities() {
    $low = lang_get('priority_low');
    $medium = lang_get('priority_medium');
    $high = lang_get('priority_high');
    $priorities_arr = array($low, $medium, $high, '');
    return $priorities_arr;
}

# ----------------------------------------------------------------------
# Get list of test priorities
# OUTPUT:
#   Array of test available priorities
# ----------------------------------------------------------------------
function requirement_get_requirements() {
    $low = lang_get('priority_low');
    $medium = lang_get('priority_medium');
    $high = lang_get('priority_high');
    $priorities_arr = array($low, $medium, $high, '');
    return $priorities_arr;
}


##################
#
# function: build_path
#
#####################

function requirement_build_path($folder, $path, $db){
	#print"<BR>llll $s";
	global $db;

	$q = "SELECT * FROM req_folders WHERE req_folders_id = '$folder'";
	#print"<BR>$q";
	$rs = db_query($db, $q);
	$num = db_num_rows( $db, $rs );
	#print"$n ...";
	if($num){
		$row = db_fetch_row( $db, $rs );

		$path = "<A HREF=requirement_page.php?req_folder_id=$row[req_folders_id]>" . $row['name'] . '</A>/' . $path;



		#print"$s -- $row[parent]";
		requirement_build_path($row['parent'], $path, $db);

	} else {
		$root = "<A HREF=requirement_page.php>root</A>/ ";

		print"$root $path";
		#return $s;
		#exit();

	}
#printf( $s);
#return $s;
}

function requirement_add(	$project_id,
							$name,
							$area_covered,
							$type,
							$record_or_file,
							$version,
							$status,
							$priority,
							$filename,
							$detail="",
							$reason_for_change,
							$assign_to,
							$assign_to_release,
							$upload_by,
							$functionality,
							$assoc_req ) {

	global $db;

	$tbl_req 					= REQ_TBL;
	$f_req_proj_id				= $tbl_req .".". REQ_PROJECT_ID;
	$f_req_id 					= $tbl_req .".". REQ_ID;
	$f_req_filename 			= $tbl_req .".". REQ_FILENAME;
	$f_req_area_covered 		= $tbl_req .".". REQ_AREA_COVERED;
	$f_req_type		 			= $tbl_req .".". REQ_TYPE;
	$f_req_parent	 			= $tbl_req .".". REQ_PARENT;
	$f_req_label	 			= $tbl_req .".". REQ_LABEL;
	$f_req_unique_id 			= $tbl_req .".". REQ_UNIQUE_ID;
	$f_req_functionality		= $tbl_req .".". REQ_FUNCTIONALITY;
	$f_req_locked_by			= $tbl_req .".". REQ_LOCKED_BY;
	$f_req_locked_date			= $tbl_req .".". REQ_LOCKED_DATE;
	$f_req_rec_file				= $tbl_req .".". REQ_REC_FILE;
	$f_req_priority				= $tbl_req .".". REQ_PRIORITY;

	$tbl_req_ver				= REQ_VERS_TBL;
	$f_req_ver_uid				= $tbl_req_ver .".". REQ_VERS_UNIQUE_ID;
	$f_req_ver_req_id			= $tbl_req_ver .".". REQ_VERS_REQ_ID;
	$f_req_ver_version			= $tbl_req_ver .".". REQ_VERS_VERSION;
	$f_req_ver_timestamp		= $tbl_req_ver .".". REQ_VERS_TIMESTAMP;
	$f_req_ver_uploaded_by		= $tbl_req_ver .".". REQ_VERS_UPLOADED_BY;
	$f_req_ver_filename			= $tbl_req_ver .".". REQ_VERS_FILENAME;
	$f_req_ver_comments			= $tbl_req_ver .".". REQ_VERS_COMMENTS;
	$f_req_ver_status			= $tbl_req_ver .".". REQ_VERS_STATUS;
	$f_req_ver_shed_release		= $tbl_req_ver .".". REQ_VERS_SCHEDULED_RELEASE_IMP;
	$f_req_ver_shed_build		= $tbl_req_ver .".". REQ_VERS_SCHEDULED_BUILD_IMP;
	$f_req_ver_actual_release	= $tbl_req_ver .".". REQ_VERS_ACTUAL_RELEASE_IMP;
	$f_req_ver_actual_build		= $tbl_req_ver .".". REQ_VERS_ACTUAL_BUILD_IMP;
	$f_req_ver_detail			= $tbl_req_ver .".". REQ_VERS_DETAIL;
	$f_req_ver_release			= $tbl_req_ver .".". REQ_VERS_ASSIGN_RELEASE;
	$f_req_ver_latest			= $tbl_req_ver .".". REQ_VERS_LATEST;
	$f_req_ver_reason_change	= $tbl_req_ver .".". REQ_VERS_REASON_CHANGE;
	$f_req_ver_assigned			= $tbl_req_ver .".". REQ_VERS_ASSIGNED_TO;
	$f_req_ver_last_updated		= $tbl_req_ver .".". REQ_VERS_LAST_UPDATED;
	$f_req_ver_last_updated_by	= $tbl_req_ver .".". REQ_VERS_LAST_UPDATED_BY;

	$last_updated		  = date_get_short_dt();
	$s_user_properties 	  = session_get_user_properties();
	$s_user_name		  = $s_user_properties['username'];
	$last_updated_by	  = $s_user_name;

	if( $area_covered == '' ) {
		$area_covered = 0;
	}
	if( $type == '' ) {
		$type = 0;
	}

	$q = "	INSERT INTO $tbl_req
				(	$f_req_proj_id,
					$f_req_filename,
					$f_req_area_covered,
					$f_req_type,
					$f_req_rec_file,
					$f_req_priority,
					$f_req_parent )
			VALUES
				(	'$project_id',
					'$name',
					'$area_covered',
					'$type',
					'$record_or_file',
					'$priority',
					'$assoc_req' )";

	db_query($db, $q);
	//print"$q<br>";

	$req_id = db_get_last_autoincrement_id($db);

	$q = "	INSERT INTO $tbl_req_ver
				(	$f_req_ver_req_id,
					$f_req_ver_version,
					$f_req_ver_timestamp,
					$f_req_ver_uploaded_by,
					$f_req_ver_filename,
					$f_req_ver_status,
					$f_req_ver_detail,
					$f_req_ver_reason_change,
					$f_req_ver_assigned,
					$f_req_ver_latest,
					$f_req_ver_last_updated,
					$f_req_ver_last_updated_by )
			VALUES
				(	$req_id,
					'$version',
					'".date("Y-m-d H:i:s")."',
					'$upload_by',
					'$filename',
					'$status',
					'$detail',
					'$reason_for_change',
					'$assign_to',
					'Y',
					'$last_updated',
					'$last_updated_by' )";

	db_query($db, $q);

	requirement_edit_functionality($req_id, $functionality);
}

function requirement_add_version(	$project_id,
									$req_id,
									$req_defect_id,
									$area_covered,
									$type,
									$record_or_file,
									$version,
									$status,
									$filename,
									$detail="",
									$reason_for_change="",
									$assign_to,
									//$assign_to_release,
									$upload_by,
									$functionality,
									$priority,
									$release_id ) {

	global $db;

	$tbl_req 					= REQ_TBL;
	$f_req_id 					= $tbl_req .".". REQ_ID;
	$f_req_filename 			= $tbl_req .".". REQ_FILENAME;
	$f_req_area_covered 		= $tbl_req .".". REQ_AREA_COVERED;
	$f_req_type		 			= $tbl_req .".". REQ_TYPE;
	$f_req_parent	 			= $tbl_req .".". REQ_PARENT;
	$f_req_label	 			= $tbl_req .".". REQ_LABEL;
	$f_req_unique_id 			= $tbl_req .".". REQ_UNIQUE_ID;
	$f_req_functionality		= $tbl_req .".". REQ_FUNCTIONALITY;
	$f_req_locked_by			= $tbl_req .".". REQ_LOCKED_BY;
	$f_req_locked_date			= $tbl_req .".". REQ_LOCKED_DATE;
	$f_req_priority				= $tbl_req .".". REQ_PRIORITY;
	$f_req_last_updated			= $tbl_req .".". REQ_LAST_UPDATED;

	$tbl_req_ver				= REQ_VERS_TBL;
	$f_req_ver_uid				= $tbl_req_ver .".". REQ_VERS_UNIQUE_ID;
	$f_req_ver_req_id			= $tbl_req_ver .".". REQ_VERS_REQ_ID;
	$f_req_ver_version			= $tbl_req_ver .".". REQ_VERS_VERSION;
	$f_req_ver_timestamp		= $tbl_req_ver .".". REQ_VERS_TIMESTAMP;
	$f_req_ver_uploaded_by		= $tbl_req_ver .".". REQ_VERS_UPLOADED_BY;
	$f_req_ver_filename			= $tbl_req_ver .".". REQ_VERS_FILENAME;
	$f_req_ver_comments			= $tbl_req_ver .".". REQ_VERS_COMMENTS;
	$f_req_ver_status			= $tbl_req_ver .".". REQ_VERS_STATUS;
	$f_req_ver_shed_release		= $tbl_req_ver .".". REQ_VERS_SCHEDULED_RELEASE_IMP;
	$f_req_ver_shed_build		= $tbl_req_ver .".". REQ_VERS_SCHEDULED_BUILD_IMP;
	$f_req_ver_actual_release	= $tbl_req_ver .".". REQ_VERS_ACTUAL_RELEASE_IMP;
	$f_req_ver_actual_build		= $tbl_req_ver .".". REQ_VERS_ACTUAL_BUILD_IMP;
	$f_req_ver_detail			= $tbl_req_ver .".". REQ_VERS_DETAIL;
	$f_req_ver_release			= $tbl_req_ver .".". REQ_VERS_ASSIGN_RELEASE;
	$f_req_ver_latest			= $tbl_req_ver .".". REQ_VERS_LATEST;
	$f_req_ver_reason_change	= $tbl_req_ver .".". REQ_VERS_REASON_CHANGE;
	$f_req_ver_assigned			= $tbl_req_ver .".". REQ_VERS_ASSIGNED_TO;
	$f_req_ver_defect_id		= $tbl_req_ver .".". REQ_VERS_DEFECT_ID;

	$last_updated				= date_get_short_dt(); 
	if( $area_covered == '' ) {
		$area_covered = 0;
	}
	if( $type == '' ) {
		$type = 0;
	}
	
	$q = "	UPDATE $tbl_req_ver
			SET
				$f_req_ver_latest = 'N'
			WHERE
				$f_req_ver_req_id = $req_id";

	db_query($db, $q);

	$q = "	UPDATE $tbl_req
			SET
				$f_req_area_covered = '$area_covered',
				$f_req_type = '$type',
				$f_req_priority = '$priority',
				$f_req_last_updated = '$last_updated'
			WHERE
				$f_req_id = $req_id";

	db_query($db, $q);

	$q = "	INSERT INTO $tbl_req_ver
				(	$f_req_ver_req_id,
					$f_req_ver_defect_id,
					$f_req_ver_version,
					$f_req_ver_timestamp,
					$f_req_ver_uploaded_by,
					$f_req_ver_filename,
					$f_req_ver_status,
					$f_req_ver_detail,
					$f_req_ver_reason_change,
					$f_req_ver_assigned,
					$f_req_ver_latest )
			VALUES
				(	$req_id,
				    '$req_defect_id',
					'$version',
					'$last_updated',
					'$upload_by',
					'$filename',
					'$status',
					'$detail',
					'$reason_for_change',
					'$assign_to',
					'Y' )";

	db_query($db, $q);

	requirement_edit_functionality($req_id, $functionality);

	# update related tests statuses to "Review Requirement"
	$req_test_relationships = requirement_get_test_relationships($req_id);

	foreach($req_test_relationships as $row_req_test_rels) {

		$test_id = $row_req_test_rels[TEST_ID];

		test_update_field( $project_id, $test_id, TEST_STATUS, "Review Requirement" );
	}

	# Get the req_version_id just inserted.
	$req_version_id = requirement_get_latest_version( $req_id );

	# update requirment_release_assoc
	requirement_edit_release( $req_version_id, $release_id );

}

function requirement_edit(	$project_id,
							$req_id,
							$req_version_id,
							$req_defect_id,
							$name,
							$area_covered,
							$type,
							$status,
							$detail,
							$reason_for_change,
							$functionality,
							$priority,
							$release_id ) {

	global $db;

	$tbl_req 					= REQ_TBL;
	$f_req_proj_id				= $tbl_req .".". REQ_PROJECT_ID;
	$f_req_id 					= $tbl_req .".". REQ_ID;
	$f_req_filename 			= $tbl_req .".". REQ_FILENAME;
	$f_req_area_covered 		= $tbl_req .".". REQ_AREA_COVERED;
	$f_req_type		 			= $tbl_req .".". REQ_TYPE;
	$f_req_parent	 			= $tbl_req .".". REQ_PARENT;
	$f_req_label	 			= $tbl_req .".". REQ_LABEL;
	$f_req_unique_id 			= $tbl_req .".". REQ_UNIQUE_ID;
	$f_req_functionality		= $tbl_req .".". REQ_FUNCTIONALITY;
	$f_req_locked_by			= $tbl_req .".". REQ_LOCKED_BY;
	$f_req_locked_date			= $tbl_req .".". REQ_LOCKED_DATE;
	$f_req_priority				= $tbl_req .".". REQ_PRIORITY;
	$f_req_last_updated			= $tbl_req .".". REQ_LAST_UPDATED;

	$tbl_req_ver				= REQ_VERS_TBL;
	$f_req_ver_uid				= $tbl_req_ver .".". REQ_VERS_UNIQUE_ID;
	$f_req_ver_req_id			= $tbl_req_ver .".". REQ_VERS_REQ_ID;
	$f_req_ver_version			= $tbl_req_ver .".". REQ_VERS_VERSION;
	$f_req_ver_timestamp		= $tbl_req_ver .".". REQ_VERS_TIMESTAMP;
	$f_req_ver_uploaded_by		= $tbl_req_ver .".". REQ_VERS_UPLOADED_BY;
	$f_req_ver_filename			= $tbl_req_ver .".". REQ_VERS_FILENAME;
	$f_req_ver_comments			= $tbl_req_ver .".". REQ_VERS_COMMENTS;
	$f_req_ver_status			= $tbl_req_ver .".". REQ_VERS_STATUS;
	$f_req_ver_shed_release		= $tbl_req_ver .".". REQ_VERS_SCHEDULED_RELEASE_IMP;
	$f_req_ver_shed_build		= $tbl_req_ver .".". REQ_VERS_SCHEDULED_BUILD_IMP;
	$f_req_ver_actual_release	= $tbl_req_ver .".". REQ_VERS_ACTUAL_RELEASE_IMP;
	$f_req_ver_actual_build		= $tbl_req_ver .".". REQ_VERS_ACTUAL_BUILD_IMP;
	$f_req_ver_detail			= $tbl_req_ver .".". REQ_VERS_DETAIL;
	$f_req_ver_release			= $tbl_req_ver .".". REQ_VERS_ASSIGN_RELEASE;
	$f_req_ver_reason_change	= $tbl_req_ver .".". REQ_VERS_REASON_CHANGE;
	$f_req_ver_latest			= $tbl_req_ver .".". REQ_VERS_LATEST;
	$f_req_ver_last_updated		= $tbl_req_ver .".". REQ_VERS_LAST_UPDATED;
	$f_req_ver_last_updated_by	= $tbl_req_ver .".". REQ_VERS_LAST_UPDATED_BY;
	$f_req_ver_defect_id		= $tbl_req_ver .".". REQ_VERS_DEFECT_ID;

	$last_updated		  = date_get_short_dt();
	$s_user_properties 	  = session_get_user_properties();
	$s_user_name		  = $s_user_properties['username'];
	$last_updated_by	  = $s_user_name;

	if( $area_covered == '' ) {
		$area_covered = 0;
	}
	if( $type == '' ) {
		$type = 0;
	}

	
	$q = "	UPDATE $tbl_req
			SET
				$f_req_filename = '$name',
				$f_req_area_covered = '$area_covered',
				$f_req_type = '$type',
				$f_req_priority = '$priority',
				$f_req_last_updated = '$last_updated'
			WHERE
				$f_req_id = '$req_id'
				AND $f_req_proj_id = $project_id";
	
	db_query($db, $q);
	//print"$q<br>";

	$q = "	UPDATE $tbl_req_ver
			SET
				$f_req_ver_status 			= '$status',
				$f_req_ver_detail 			= '$detail',
				$f_req_ver_reason_change	= '$reason_for_change',
				$f_req_ver_last_updated		= '$last_updated',
				$f_req_ver_last_updated_by	= '$last_updated_by',
				$f_req_ver_defect_id		= '$req_defect_id'
			WHERE
				$f_req_ver_uid = '$req_version_id'";

	db_query($db, $q);
	//print"$q<br>";

	requirement_edit_functionality($req_id, $functionality);

	requirement_edit_release( $req_version_id, $release_id );
}

function requirement_edit_functionality(	$req_id,
											$functionality ) {

	global $db;

	$tbl_assoc			= REQ_FUNCT_ASSOC_TBL;
	$f_assoc_id			= $tbl_assoc .".". REQ_FUNCT_ASSOC_ID;
	$f_assoc_req_id		= $tbl_assoc .".". REQ_FUNCT_ASSOC_REQ_ID;
	$f_assoc_funct_id	= $tbl_assoc .".". REQ_FUNCT_ASSOC_FUNCT_ID;

	$q = "DELETE FROM $tbl_assoc
		  WHERE $f_assoc_req_id = $req_id";

	db_query($db, $q);

	if( !empty($functionality) ) {
		foreach($functionality as $function_id) {

			$q	= "	INSERT INTO $tbl_assoc
						($f_assoc_req_id, $f_assoc_funct_id)
					VALUES
						($req_id, $function_id)";

			db_query($db, $q);
		}
	}
}

function requirement_update_req_version_field( $version_id, $field, $value ) {

	global $db;
	$req_vers_tbl			= REQ_VERS_TBL;
	$f_vers_id				= REQ_VERS_UNIQUE_ID;
	//$f_vers_req_id			= REQ_VERS_REQ_ID;

	$q = "UPDATE $req_vers_tbl
		  SET $field = '$value'
		  WHERE $f_vers_id = '$version_id'";
	//print"$q<br>";
	db_query( $db, $q );


}
# ----------------------------------------------------------------------
# Delete a requirement by test id
# ----------------------------------------------------------------------
function requirement_delete($project_id, $req_id) {

    global $db;

	$tbl_req 					= REQ_TBL;
	$f_req_id 					= $tbl_req .".". REQ_ID;
	$f_req_unique_id 			= $tbl_req .".". REQ_UNIQUE_ID;
	$f_req_parent	 			= $tbl_req .".". REQ_PARENT;

	$tbl_req_ver				= REQ_VERS_TBL;
	$f_req_ver_uid				= $tbl_req_ver .".". REQ_VERS_UNIQUE_ID;
	$f_req_ver_req_id			= $tbl_req_ver .".". REQ_VERS_REQ_ID;

    $tbl_test_req_assoc			= TEST_REQ_ASSOC_TBL;
    $f_test_req_assoc_id		= $tbl_test_req_assoc .".". TEST_REQ_ASSOC_ID;
    $f_test_req_assoc_req_id	= $tbl_test_req_assoc .".". TEST_REQ_ASSOC_REQ_ID;
    $f_test_req_assoc_test_id	= $tbl_test_req_assoc .".". TEST_REQ_ASSOC_TEMPEST_TEST_ID;
	$f_test_req_assoc_covered	= $tbl_test_req_assoc .".". TEST_REQ_ASSOC_PERCENT_COVERED;

	$tbl_req_ver_assoc_rel		= REQ_VERS_ASSOC_REL;
	$f_req_ver_assoc_rel_id		= $tbl_req_ver_assoc_rel.".".REQ_VERS_ASSOC_REL_ID;
	$f_req_ver_assoc_rel_req_id	= $tbl_req_ver_assoc_rel.".".REQ_VERS_ASSOC_REL_REQ_ID;
	$f_req_ver_assoc_rel_rel_id	= $tbl_req_ver_assoc_rel.".".REQ_VERS_ASSOC_REL_REL_ID;

	$tbl_discussion			= DISC_TBL;
	$f_discussion_id		= DISC_TBL .".". DISC_ID;
	$f_discussion_req_id	= DISC_TBL .".". DISC_REQ_ID;

	$project_properties     = session_get_project_properties();

	foreach( requirement_get_detail($project_id, $req_id) as $rows_req) {

		if($rows_req[REQ_VERS_FILENAME]!="") {
			unlink($project_properties[PROJ_REQ_UPLOAD_PATH].$rows_req[REQ_VERS_FILENAME]);
		}
	}

	# DELETE REQUIREMENT VERSIONS
	$q = "	DELETE FROM $tbl_req_ver
			WHERE $f_req_ver_req_id = $req_id";

    db_query( $db, $q );

	# DELETE REQUIREMENT
	$q = "	DELETE FROM $tbl_req
			WHERE $f_req_id = $req_id";

	db_query( $db, $q );

	# REMOVE REQUIREMENT ASSOCIATIONS
	$q = "	UPDATE $tbl_req
			SET $f_req_parent = 0
			WHERE $f_req_parent = $req_id";

	# REMOVE TEST ASSOCIATIONS
	$q = "	DELETE FROM $tbl_test_req_assoc
			WHERE $f_test_req_assoc_req_id = $req_id";

	db_query( $db, $q );

	# REMOVE RELEASE ASSOCIATIONS
	$q = "	DELETE FROM $tbl_req_ver_assoc_rel
			WHERE $f_req_ver_assoc_rel_req_id = $req_id";

	db_query( $db, $q );

	# DELETE DISCUSSIONS
	$q = "	SELECT $f_discussion_id
			FROM $tbl_discussion
			WHERE $f_discussion_req_id = $req_id";

	$rs = db_query( $db, $q );
	$rows = db_fetch_array($db, $rs);
	foreach($rows as $row) {

		$discussion_id = $row[DISC_ID];

		discussion_delete($discussion_id);
	}
}

/*# ----------------------------------------------------------------------
# Get the details for a test
# INPUT:
#   test id
# OUTPUT:
#   test details
# ----------------------------------------------------------------------
function requirement_get_detail( $testid) {

    global $db;
    $test_tbl             = TEST_TBL;
    $f_test_id            = TEST_ID;
    $f_test_name          = TEST_NAME;
    $f_test_purpose       = TEST_PURPOSE;
    $f_test_comments      = TEST_COMMENTS;
    $f_ba_owner           = TEST_BA_OWNER;
    $f_qa_owner           = TEST_QA_OWNER;
    $f_test_type          = TEST_TESTTYPE;
    $f_area_tested        = TEST_AREA_TESTED;
    $f_test_priority      = TEST_PRIORITY;
    $f_steps              = TEST_MANUAL;
    $f_script             = TEST_AUTOMATED;
    $f_autopass           = TEST_AUTO_PASS;
    $f_performance        = TEST_LR;
    $f_test_status        = TEST_STATUS;
    $f_assigned_to        = TEST_ASSIGNED_TO;
    $f_assigned_by        = TEST_ASSIGNED_BY;
    $f_dateassigned       = TEST_DATE_ASSIGNED;
    $f_dateexpcomplete    = TEST_DATE_EXPECTED;
    $f_dateactcomplete    = TEST_DATE_COMPLETE;
    $f_datebasignoff      = TEST_BA_SIGNOFF;

    $query = "SELECT $f_test_id, $f_test_comments, $f_test_status,
              $f_test_name, $f_test_type, $f_area_tested,
              $f_test_priority, $f_test_purpose, $f_qa_owner, $f_steps,
              $f_script, $f_performance, $f_ba_owner, $f_datebasignoff,
              $f_assigned_to , $f_assigned_by, $f_dateassigned,
              $f_dateexpcomplete, $f_dateactcomplete,
              $f_autopass
              FROM $test_tbl
              WHERE $f_test_id = '$testid'";

    $rs = & db_query( $db, $query );
    $row = db_fetch_row( $db, $rs ) ;
    return $row;

}
*/

# ----------------------------------------------------------------------
# Get all requirements
# ----------------------------------------------------------------------
function requirement_get( $project_id, 
						  $page_number=0, 
						  $order_by=REQ_FILENAME, 
						  $order_dir="ASC",	
						  $doc_type="",
						  $status="", 
						  $area_covered="", 
						  $functionality="", 
						  $assign_release="", 
						  $show_versions="latest", 
						  $per_page=null, 
						  $search="", 
						  $priority, 
						  $csv_name=null, 
						  $root_node=false ) {

	global $db;
	$tbl_req 					= REQ_TBL;
	$f_req_proj_id				= $tbl_req .".". REQ_PROJECT_ID;
	$f_req_id 					= $tbl_req .".". REQ_ID;
	$f_req_filename 			= $tbl_req .".". REQ_FILENAME;
	$f_req_area_covered 		= $tbl_req .".". REQ_AREA_COVERED;
	$f_req_type		 			= $tbl_req .".". REQ_TYPE;
	$f_req_parent	 			= $tbl_req .".". REQ_PARENT;
	$f_req_label	 			= $tbl_req .".". REQ_LABEL;
	$f_req_unique_id 			= $tbl_req .".". REQ_UNIQUE_ID;
	$f_req_locked_by			= $tbl_req .".". REQ_LOCKED_BY;
	$f_req_locked_date			= $tbl_req .".". REQ_LOCKED_DATE;
	$f_req_parent	 			= $tbl_req .".". REQ_PARENT;
	$f_req_priority				= $tbl_req .".". REQ_PRIORITY;

	$tbl_req_ver				= REQ_VERS_TBL;
	$f_req_ver_uid				= $tbl_req_ver .".". REQ_VERS_UNIQUE_ID;
	$f_req_ver_req_id			= $tbl_req_ver .".". REQ_VERS_REQ_ID;
	$f_req_ver_version			= $tbl_req_ver .".". REQ_VERS_VERSION;
	$f_req_ver_timestamp		= $tbl_req_ver .".". REQ_VERS_TIMESTAMP;
	$f_req_ver_uploaded_by		= $tbl_req_ver .".". REQ_VERS_UPLOADED_BY;
	$f_req_ver_filename			= $tbl_req_ver .".". REQ_VERS_FILENAME;
	$f_req_ver_comments			= $tbl_req_ver .".". REQ_VERS_COMMENTS;
	$f_req_ver_status			= $tbl_req_ver .".". REQ_VERS_STATUS;
	$f_req_ver_shed_release		= $tbl_req_ver .".". REQ_VERS_SCHEDULED_RELEASE_IMP;
	$f_req_ver_shed_build		= $tbl_req_ver .".". REQ_VERS_SCHEDULED_BUILD_IMP;
	$f_req_ver_actual_release	= $tbl_req_ver .".". REQ_VERS_ACTUAL_RELEASE_IMP;
	$f_req_ver_actual_build		= $tbl_req_ver .".". REQ_VERS_ACTUAL_BUILD_IMP;
	$f_req_ver_detail			= $tbl_req_ver .".". REQ_VERS_DETAIL;
	$f_req_ver_latest			= $tbl_req_ver .".". REQ_VERS_LATEST;

	$tbl_functionality_assoc		= REQ_FUNCT_ASSOC_TBL;
	$f_functionality_assoc_id		= $tbl_functionality_assoc .".". REQ_FUNCT_ASSOC_ID;
	$f_functionality_assoc_req_id	= $tbl_functionality_assoc .".". REQ_FUNCT_ASSOC_REQ_ID;
	$f_functionality_assoc_funct_id	= $tbl_functionality_assoc .".". REQ_FUNCT_ASSOC_FUNCT_ID;

	$release_tbl 	= RELEASE_TBL;
	$f_release_id	= $release_tbl.".".RELEASE_ID;
	$f_release_name	= $release_tbl.".".RELEASE_NAME;

	$tbl_req_ver_assoc_rel		= REQ_VERS_ASSOC_REL;
	$f_req_ver_assoc_rel_id		= $tbl_req_ver_assoc_rel.".".REQ_VERS_ASSOC_REL_ID;
	$f_req_ver_assoc_rel_req_id	= $tbl_req_ver_assoc_rel.".".REQ_VERS_ASSOC_REL_REQ_ID;
	$f_req_ver_assoc_rel_rel_id	= $tbl_req_ver_assoc_rel.".".REQ_VERS_ASSOC_REL_REL_ID;

	$tbl_req_doc_type			= REQ_DOC_TYPE_TBL;
	$f_req_doc_type_name		= $tbl_req_doc_type .".". REQ_DOC_TYPE_NAME;
	$f_req_doc_type_id			= $tbl_req_doc_type .".". REQ_DOC_TYPE_ID;
	$f_req_doc_type_project_id	= $tbl_req_doc_type .".". REQ_DOC_TYPE_PROJ_ID;
	$f_req_doc_type_root_doc	= $tbl_req_doc_type .".". REQ_DOC_TYPE_ROOT_DOC;

	$tbl_req_area_covered		= REQ_AREA_COVERAGE_TBL;
	$f_req_area_covered_name	= $tbl_req_area_covered .".". REQ_AREA_COVERAGE;
	$f_req_area_covered_id		= $tbl_req_area_covered .".". REQ_AREA_COVERAGE_ID;
	$f_req_area_covered_proj_id	= $tbl_req_area_covered .".". REQ_AREA_PROJ_ID;

	//$where_clause = "";
	$where_clause = " WHERE $f_req_proj_id = $project_id";

	if( !empty($doc_type) ) {
		$where_clause .= " AND $f_req_type = '$doc_type'";
	} 
	if( !empty($status) ) {
		$where_clause .= " AND $f_req_ver_status = '$status'";
	}
	if( !empty($area_covered) ) {
		$where_clause .= " AND $f_req_area_covered = '$area_covered'";
	}
	if( !empty($functionality) ) {
	   $where_clause .= " AND $f_functionality_assoc_funct_id = '$functionality'";
	}
	if( !empty($assign_release) ) {
		$where_clause .= " AND $f_req_ver_assoc_rel_rel_id = '$assign_release'";
	}
	if( !empty($priority) ) {
		$where_clause .= " AND $f_req_priority = '$priority'";
	}
	if( $show_versions=="latest" ) {
		$where_clause .= " AND $f_req_ver_latest = 'Y'";
	}

	if( $root_node ) {
		$where_clause .= " AND $f_req_parent = 0 ";
	}

	# SEARCH
	if ( !empty($search) ) {
		$search = htmlspecialchars($search, ENT_QUOTES);
		$where_clause .= " AND (($f_req_filename LIKE '%$search%') OR ($f_req_ver_detail LIKE '%$search%'))";
    }

	
	$q = "	SELECT DISTINCT
				$f_req_id,
				$f_req_filename,
				$f_req_ver_filename,
				$f_req_ver_detail,
				$f_req_doc_type_name,
				$f_req_area_covered_name,
				$f_req_priority,
				$f_req_ver_status,
				$f_req_ver_version,
				$f_req_locked_by,
				$f_req_locked_date,
				$f_req_ver_uid	
			FROM $tbl_req
			INNER JOIN $tbl_req_ver ON $f_req_ver_req_id = $f_req_id
			LEFT JOIN $tbl_req_doc_type ON $f_req_doc_type_id = $f_req_type
			LEFT JOIN $tbl_req_area_covered ON $f_req_area_covered_id = $f_req_area_covered";


	if( !empty($assign_release) ) {
		$q .= "	INNER JOIN $tbl_req_ver_assoc_rel ON $f_req_ver_assoc_rel_req_id = $f_req_ver_uid
				INNER JOIN $release_tbl ON $f_release_id = $f_req_ver_assoc_rel_rel_id";
	}

	if( !empty($functionality) ) {
		$q .= "	INNER JOIN $tbl_functionality_assoc ON $f_functionality_assoc_req_id = $f_req_id";
	}

	// add the where condition
	$q .= "	$where_clause
			ORDER BY $order_by $order_dir";



	if( is_null($per_page) ) {

		$display_options	= session_get_filter_options("requirements");
		$per_page 			= $display_options['per_page'];
	}

	if( $per_page!=0 && $page_number!=0 ) {

		$row_count = db_num_rows( $db, db_query($db, $q) );

		$page_number = util_page_number($page_number, $row_count, $per_page);

		# Add the limit clause to the query so that we only show n number of records per page
		$offset = ( ( $page_number - 1 ) * $per_page );
		html_table_offset( 	$row_count,
							$per_page,
							$page_number,
							$order_by,
							$order_dir,
							$csv_name );

		$q .= " LIMIT $offset, ".$per_page;

	}


	$rows = db_fetch_array($db, db_query($db, $q));

	return $rows;
}



function requirement_get_for_export( $project_id, 
									 $page_number=0, 
									 $order_by=REQ_FILENAME, 
									 $order_dir="ASC",	
									 $doc_type="",
									 $status="", 
									 $area_covered="", 
									 $functionality="", 
									 $assign_release="", 
									 $show_versions="latest", 
									 $per_page=null, 
									 $search="", 
									 $priority, 
									 $csv_name=null, 
									 $root_node=false ) {

	global $db;
	$tbl_req 					= REQ_TBL;
	$f_req_proj_id				= $tbl_req .".". REQ_PROJECT_ID;
	$f_req_id 					= $tbl_req .".". REQ_ID;
	$f_req_filename 			= $tbl_req .".". REQ_FILENAME;
	$f_req_area_covered 		= $tbl_req .".". REQ_AREA_COVERED;
	$f_req_type		 			= $tbl_req .".". REQ_TYPE;
	$f_req_parent	 			= $tbl_req .".". REQ_PARENT;
	$f_req_label	 			= $tbl_req .".". REQ_LABEL;
	$f_req_unique_id 			= $tbl_req .".". REQ_UNIQUE_ID;
	$f_req_locked_by			= $tbl_req .".". REQ_LOCKED_BY;
	$f_req_locked_date			= $tbl_req .".". REQ_LOCKED_DATE;
	$f_req_parent	 			= $tbl_req .".". REQ_PARENT;
	$f_req_priority				= $tbl_req .".". REQ_PRIORITY;

	$tbl_req_ver				= REQ_VERS_TBL;
	$f_req_ver_uid				= $tbl_req_ver .".". REQ_VERS_UNIQUE_ID;
	$f_req_ver_req_id			= $tbl_req_ver .".". REQ_VERS_REQ_ID;
	$f_req_ver_version			= $tbl_req_ver .".". REQ_VERS_VERSION;
	$f_req_ver_timestamp		= $tbl_req_ver .".". REQ_VERS_TIMESTAMP;
	$f_req_ver_uploaded_by		= $tbl_req_ver .".". REQ_VERS_UPLOADED_BY;
	$f_req_ver_filename			= $tbl_req_ver .".". REQ_VERS_FILENAME;
	$f_req_ver_comments			= $tbl_req_ver .".". REQ_VERS_COMMENTS;
	$f_req_ver_status			= $tbl_req_ver .".". REQ_VERS_STATUS;
	$f_req_ver_shed_release		= $tbl_req_ver .".". REQ_VERS_SCHEDULED_RELEASE_IMP;
	$f_req_ver_shed_build		= $tbl_req_ver .".". REQ_VERS_SCHEDULED_BUILD_IMP;
	$f_req_ver_actual_release	= $tbl_req_ver .".". REQ_VERS_ACTUAL_RELEASE_IMP;
	$f_req_ver_actual_build		= $tbl_req_ver .".". REQ_VERS_ACTUAL_BUILD_IMP;
	$f_req_ver_detail			= $tbl_req_ver .".". REQ_VERS_DETAIL;
	$f_req_ver_latest			= $tbl_req_ver .".". REQ_VERS_LATEST;

	$tbl_functionality_assoc		= REQ_FUNCT_ASSOC_TBL;
	$f_functionality_assoc_id		= $tbl_functionality_assoc .".". REQ_FUNCT_ASSOC_ID;
	$f_functionality_assoc_req_id	= $tbl_functionality_assoc .".". REQ_FUNCT_ASSOC_REQ_ID;
	$f_functionality_assoc_funct_id	= $tbl_functionality_assoc .".". REQ_FUNCT_ASSOC_FUNCT_ID;

	$release_tbl 	= RELEASE_TBL;
	$f_release_id	= $release_tbl.".".RELEASE_ID;
	$f_release_name	= $release_tbl.".".RELEASE_NAME;

	$tbl_req_ver_assoc_rel		= REQ_VERS_ASSOC_REL;
	$f_req_ver_assoc_rel_id		= $tbl_req_ver_assoc_rel.".".REQ_VERS_ASSOC_REL_ID;
	$f_req_ver_assoc_rel_req_id	= $tbl_req_ver_assoc_rel.".".REQ_VERS_ASSOC_REL_REQ_ID;
	$f_req_ver_assoc_rel_rel_id	= $tbl_req_ver_assoc_rel.".".REQ_VERS_ASSOC_REL_REL_ID;

	$tbl_req_doc_type			= REQ_DOC_TYPE_TBL;
	$f_req_doc_type_name		= $tbl_req_doc_type .".". REQ_DOC_TYPE_NAME;
	$f_req_doc_type_id			= $tbl_req_doc_type .".". REQ_DOC_TYPE_ID;
	$f_req_doc_type_project_id	= $tbl_req_doc_type .".". REQ_DOC_TYPE_PROJ_ID;
	$f_req_doc_type_root_doc	= $tbl_req_doc_type .".". REQ_DOC_TYPE_ROOT_DOC;

	$tbl_req_area_covered		= REQ_AREA_COVERAGE_TBL;
	$f_req_area_covered_name	= $tbl_req_area_covered .".". REQ_AREA_COVERAGE;
	$f_req_area_covered_id		= $tbl_req_area_covered .".". REQ_AREA_COVERAGE_ID;
	$f_req_area_covered_proj_id	= $tbl_req_area_covered .".". REQ_AREA_PROJ_ID;

	$tbl_req_functionality		= REQ_FUNCT_TBL;
	$f_req_functonaility_id		= $tbl_req_functionality .".". REQ_FUNCT_ID;
	$f_req_functionality		= $tbl_req_functionality .".". REQ_FUNCT_NAME;

	//$where_clause = "";
	$where_clause = " WHERE $f_req_proj_id = $project_id";

	if( !empty($doc_type) ) {
		$where_clause .= " AND $f_req_type = '$doc_type'";
	} 
	if( !empty($status) ) {
		$where_clause .= " AND $f_req_ver_status = '$status'";
	}
	if( !empty($area_covered) ) {
		$where_clause .= " AND $f_req_area_covered = '$area_covered'";
	}
	if( !empty($functionality) ) {
	   $where_clause .= " AND $f_functionality_assoc_funct_id = '$functionality'";
	}
	if( !empty($assign_release) ) {
		$where_clause .= " AND $f_req_ver_assoc_rel_rel_id = '$assign_release'";
	}
	if( !empty($priority) ) {
		$where_clause .= " AND $f_req_priority = '$priority'";
	}
	if( $show_versions=="latest" ) {
		$where_clause .= " AND $f_req_ver_latest = 'Y'";
	}

	if( $root_node ) {
		$where_clause .= " AND $f_req_parent = 0";
	}

	# SEARCH
	if ( !empty($search) ) {
		$search = htmlspecialchars($search, ENT_QUOTES);
		$where_clause .= " AND (($f_req_filename LIKE '%$search%') OR ($f_req_ver_detail LIKE '%$search%'))";
    }

	/*
	$where_clause = substr( $where_clause, 3, strlen($where_clause) );
	if( !empty($where_clause) ) {
		$where_clause = "WHERE $where_clause";
	}
	*/

	$q = "	SELECT DISTINCT
				$f_req_id,
				$f_req_filename,
				$f_req_ver_filename,
				$f_req_ver_detail,
				$f_req_doc_type_name,
				$f_req_area_covered_name,
				$f_req_priority,
				$f_req_ver_status,
				$f_req_ver_version,
				$f_req_locked_by,
				$f_req_locked_date,
				$f_release_name	
			FROM $tbl_req
			INNER JOIN $tbl_req_ver ON $f_req_ver_req_id = $f_req_id
			LEFT JOIN $tbl_req_doc_type ON $f_req_doc_type_id = $f_req_type
			LEFT JOIN $tbl_req_area_covered ON $f_req_area_covered_id = $f_req_area_covered
			LEFT JOIN $tbl_req_ver_assoc_rel ON $f_req_ver_assoc_rel_req_id = $f_req_ver_uid
			LEFT JOIN $release_tbl ON $f_release_id = $f_req_ver_assoc_rel_rel_id";
			//$f_req_functionality
			//LEFT JOIN $tbl_functionality_assoc ON $f_functionality_assoc_req_id = $f_req_id
			//LEFT JOIN $tbl_req_functionality ON $f_functionality_assoc_funct_id = $f_req_functonaility_id";


	$q .= "	$where_clause
			ORDER BY $order_by $order_dir";

	if( is_null($per_page) ) {

		$display_options	= session_get_filter_options("requirements");
		$per_page 			= $display_options['per_page'];
	}

	if( $per_page!=0 && $page_number!=0 ) {

		$row_count = db_num_rows( $db, db_query($db, $q) );

		$page_number = util_page_number($page_number, $row_count, $per_page);

		# Add the limit clause to the query so that we only show n number of records per page
		$offset = ( ( $page_number - 1 ) * $per_page );
		html_table_offset( 	$row_count,
							$per_page,
							$page_number,
							$order_by,
							$order_dir,
							$csv_name );

		$q .= " LIMIT $offset, ".$per_page;

		

	}

	//print"$q<br>";
	$rows = db_fetch_array($db, db_query($db, $q));

	return $rows;
}



function requirement_get_edit_children( $project_id, 
										$req_id, 
										$page_number=0, 
										$order_by=REQ_FILENAME, 
										$order_dir="ASC",	
										$doc_type="",
										$status="", 
										$area_covered="", 
										$functionality="", 
										$assign_release="",
										$show_versions="latest",
										$search="",
										$priority="", 
										$per_page=null, 
										$csv_name=null ) {

	$tbl_req 					= REQ_TBL;
	$f_req_proj_id				= $tbl_req .".". REQ_PROJECT_ID;
	$f_req_id 					= $tbl_req .".". REQ_ID;
	$f_req_filename 			= $tbl_req .".". REQ_FILENAME;
	$f_req_area_covered 		= $tbl_req .".". REQ_AREA_COVERED;
	$f_req_type		 			= $tbl_req .".". REQ_TYPE;
	$f_req_parent	 			= $tbl_req .".". REQ_PARENT;
	$f_req_label	 			= $tbl_req .".". REQ_LABEL;
	$f_req_unique_id 			= $tbl_req .".". REQ_UNIQUE_ID;
	$f_req_locked_by			= $tbl_req .".". REQ_LOCKED_BY;
	$f_req_locked_date			= $tbl_req .".". REQ_LOCKED_DATE;
	$f_req_parent	 			= $tbl_req .".". REQ_PARENT;
	$f_req_priority				= $tbl_req .".". REQ_PRIORITY;
	//$f_req_root		 			= $tbl_req .".". REQ_ROOT;

	$tbl_req_ver				= REQ_VERS_TBL;
	$f_req_ver_uid				= $tbl_req_ver .".". REQ_VERS_UNIQUE_ID;
	$f_req_ver_req_id			= $tbl_req_ver .".". REQ_VERS_REQ_ID;
	$f_req_ver_version			= $tbl_req_ver .".". REQ_VERS_VERSION;
	$f_req_ver_timestamp		= $tbl_req_ver .".". REQ_VERS_TIMESTAMP;
	$f_req_ver_uploaded_by		= $tbl_req_ver .".". REQ_VERS_UPLOADED_BY;
	$f_req_ver_filename			= $tbl_req_ver .".". REQ_VERS_FILENAME;
	$f_req_ver_comments			= $tbl_req_ver .".". REQ_VERS_COMMENTS;
	$f_req_ver_status			= $tbl_req_ver .".". REQ_VERS_STATUS;
	$f_req_ver_shed_release		= $tbl_req_ver .".". REQ_VERS_SCHEDULED_RELEASE_IMP;
	$f_req_ver_shed_build		= $tbl_req_ver .".". REQ_VERS_SCHEDULED_BUILD_IMP;
	$f_req_ver_actual_release	= $tbl_req_ver .".". REQ_VERS_ACTUAL_RELEASE_IMP;
	$f_req_ver_actual_build		= $tbl_req_ver .".". REQ_VERS_ACTUAL_BUILD_IMP;
	$f_req_ver_detail			= $tbl_req_ver .".". REQ_VERS_DETAIL;
	$f_req_ver_latest			= $tbl_req_ver .".". REQ_VERS_LATEST;

	$tbl_functionality_assoc		= REQ_FUNCT_ASSOC_TBL;
	$f_functionality_assoc_id		= $tbl_functionality_assoc .".". REQ_FUNCT_ASSOC_ID;
	$f_functionality_assoc_req_id	= $tbl_functionality_assoc .".". REQ_FUNCT_ASSOC_REQ_ID;
	$f_functionality_assoc_funct_id	= $tbl_functionality_assoc .".". REQ_FUNCT_ASSOC_FUNCT_ID;

	$release_tbl 	= RELEASE_TBL;
	$f_release_id	= $release_tbl.".".RELEASE_ID;
	$f_release_name	= $release_tbl.".".RELEASE_NAME;

	$tbl_req_ver_assoc_rel		= REQ_VERS_ASSOC_REL;
	$f_req_ver_assoc_rel_id		= $tbl_req_ver_assoc_rel.".".REQ_VERS_ASSOC_REL_ID;
	$f_req_ver_assoc_rel_req_id	= $tbl_req_ver_assoc_rel.".".REQ_VERS_ASSOC_REL_REQ_ID;
	$f_req_ver_assoc_rel_rel_id	= $tbl_req_ver_assoc_rel.".".REQ_VERS_ASSOC_REL_REL_ID;

	$tbl_req_doc_type			= REQ_DOC_TYPE_TBL;
	$f_req_doc_type_name		= $tbl_req_doc_type .".". REQ_DOC_TYPE_NAME;
	$f_req_doc_type_id			= $tbl_req_doc_type .".". REQ_DOC_TYPE_ID;
	$f_req_doc_type_project_id	= $tbl_req_doc_type .".". REQ_DOC_TYPE_PROJ_ID;
	$f_req_doc_type_root_doc	= $tbl_req_doc_type .".". REQ_DOC_TYPE_ROOT_DOC;

	$tbl_req_area_covered		= REQ_AREA_COVERAGE_TBL;
	$f_req_area_covered_name	= $tbl_req_area_covered .".". REQ_AREA_COVERAGE;
	$f_req_area_covered_id		= $tbl_req_area_covered .".". REQ_AREA_COVERAGE_ID;
	$f_req_area_covered_proj_id	= $tbl_req_area_covered .".". REQ_AREA_PROJ_ID;

	$where_clause = "";

	if( !empty($doc_type) ) {
		$where_clause .= "AND $f_req_type = '$doc_type' ";
	}
	if( !empty($status) ) {
		$where_clause .= "AND $f_req_ver_status = '$status' ";
	}
	if( !empty($area_covered) ) {
		$where_clause .= "AND $f_req_area_covered = '$area_covered' ";
	}
	if( !empty($functionality) ) {
	   $where_clause .= "AND $f_functionality_assoc_funct_id = '$functionality' ";
	}
	if( !empty($assign_release) ) {
		$where_clause .= "AND $f_req_ver_assoc_rel_rel_id = '$assign_release'";
	}
	if( !empty($priority) ) {
		$where_clause .= "AND $f_req_priority = '$priority'";
	}
	if( $show_versions=="latest" ) {
		$where_clause .= "AND $f_req_ver_latest = 'Y'";
	}

		# SEARCH
		if ( !empty($search) ) {
			 $where_clause = $where_clause." AND ( ($f_req_filename LIKE '%$search%') OR ($f_req_ver_detail LIKE '%$search%') )";
    }
	
	$where_clause = substr( $where_clause, 3, strlen($where_clause) );
	if( !empty($where_clause) ) {
		$where_clause = "WHERE $where_clause";
	}

	$q = "	SELECT DISTINCT
				$f_req_id,
				$f_req_area_covered_name,
				$f_req_filename,
				$f_req_doc_type_name,
				$f_req_locked_by,
				$f_req_locked_date,
				$f_req_priority,
				$f_req_ver_uid,
				$f_req_ver_version,
				$f_req_ver_detail,
				$f_req_ver_status,
				$f_req_ver_filename
			FROM $tbl_req
			INNER JOIN $tbl_req_ver
				ON $f_req_ver_req_id = $f_req_id
			LEFT JOIN $tbl_req_doc_type
				ON $f_req_doc_type_id = $f_req_type
			LEFT JOIN $tbl_req_area_covered
				ON $f_req_area_covered_id = $f_req_area_covered";

	if( !empty($assign_release) ) {
		$q .= "	INNER JOIN $tbl_req_ver_assoc_rel
					ON $f_req_ver_assoc_rel_req_id = $f_req_ver_uid
				INNER JOIN $release_tbl
					ON $f_release_id = $f_req_ver_assoc_rel_rel_id";
	}

	if( !empty($functionality) ) {
		$q .= "	INNER JOIN $tbl_functionality_assoc
					ON $f_functionality_assoc_req_id = $f_req_id";
	}

	$q .= "	$where_clause
				AND $f_req_proj_id = $project_id
				AND ($f_req_parent = $req_id OR $f_req_parent = 0)
				AND $f_req_id != $req_id
			ORDER BY $order_by $order_dir";
	//print nl2br($q);
	global $db;
	//print$q;


	if( is_null($per_page) ) {
		$s_properties	= session_get_display_options("requirements");
		$per_page 		= $s_properties['filter']['per_page'];
	}

	if( $per_page!=0 && $page_number!=0 ) {

		$row_count = db_num_rows( $db, db_query($db, $q) );

		$page_number = util_page_number($page_number, $row_count, $per_page);

		# Add the limit clause to the query so that we only show n number of records per page
		$offset = ( ( $page_number - 1 ) * $per_page );
		html_table_offset( 	$row_count,
							$per_page,
							$page_number,
							$order_by,
							$order_dir,
							$csv_name );

		$q .= " LIMIT $offset, ".$per_page;

	}

//print$q;exit;

	$rows = db_fetch_array($db, db_query($db, $q));

	return $rows;
}

function requirement_edit_children($project_id, $req_id, $session_records_name) {

	global $db;

	$tbl_req 					= REQ_TBL;
	$f_req_proj_id				= $tbl_req .".". REQ_PROJECT_ID;
	$f_req_id 					= $tbl_req .".". REQ_ID;
	$f_req_filename 			= $tbl_req .".". REQ_FILENAME;
	$f_req_area_covered 		= $tbl_req .".". REQ_AREA_COVERED;
	$f_req_type		 			= $tbl_req .".". REQ_TYPE;
	$f_req_parent	 			= $tbl_req .".". REQ_PARENT;
	$f_req_label	 			= $tbl_req .".". REQ_LABEL;
	$f_req_unique_id 			= $tbl_req .".". REQ_UNIQUE_ID;
	$f_req_locked_by			= $tbl_req .".". REQ_LOCKED_BY;
	$f_req_locked_date			= $tbl_req .".". REQ_LOCKED_DATE;
	$f_req_parent	 			= $tbl_req .".". REQ_PARENT;

	$q = "	SELECT $f_req_id
			FROM $tbl_req
			WHERE $f_req_parent = $req_id";

	$rs = db_query($db, $q);

	foreach( requirement_get_edit_children($project_id, $req_id) as $child ) {

		$q = "	SELECT $f_req_id
				FROM $tbl_req
				WHERE $f_req_id = ".$child[REQ_ID]."
					AND	$f_req_parent = $req_id";

		$rs = db_query($db, $q);
		$record_exists = db_num_rows($db, $rs);
		//print"$record_exists--";
		//print"--".session_records_ischecked($session_records_name, $child[REQ_ID]);
		if( session_records_ischecked($session_records_name, $child[REQ_ID]) ) {

			if(!$record_exists) {
				$q = "	UPDATE $tbl_req
						SET $f_req_parent = $req_id
						WHERE $f_req_id = ".$child[REQ_ID];
			}
		} else {

			if($record_exists) {
				$q = "	UPDATE $tbl_req
						SET $f_req_parent = 0
						WHERE $f_req_id = ".$child[REQ_ID];
			}
		}

		db_query($db, $q);

	}

//print$q;

	db_query($db, $q);
}


function requirement_get_all_ids($project_id) {

	global $db;
	$tbl_req 					= REQ_TBL;
	$f_req_id 					= REQ_ID;
	$f_req_proj_id				= $tbl_req .".". REQ_PROJECT_ID;

	$q = "SELECT $f_req_id
		  FROM $tbl_req
		  WHERE $f_req_proj_id = $project_id";

	//$where_clase = " WHERE project_id = $project_id;

	$rs = db_query( $db, $q );
	$rows = db_fetch_array($db, $rs );

	return $rows;
}


function requirement_get_root_nodes($project_id) {

	global $db;

	$tbl_req 					= REQ_TBL;
	$f_req_proj_id				= $tbl_req .".". REQ_PROJECT_ID;
	$f_req_name					= $tbl_req .".". REQ_FILENAME;
	$f_req_id 					= $tbl_req .".". REQ_ID;
	$f_req_parent	 			= $tbl_req .".". REQ_PARENT;

	/*
	Changing query to use the new db format
	$q = "	SELECT
				$f_req_id,
				$f_req_parent,
				$f_req_root
			FROM $tbl_req
			WHERE $f_req_proj_id = $project_id
			AND $f_req_root = 'Y'";
	*/
	$q = "	SELECT
				$f_req_id,
				$f_req_parent
			FROM $tbl_req
			WHERE $f_req_proj_id = $project_id
			AND $f_req_parent = '0'
			ORDER BY $f_req_name ASC";
	//print"$q<br>";



	$rows = db_fetch_array( $db, db_query($db, $q) );

	return $rows;
}

function requirement_get_detail($project_id, $req_id, $version_id="") {

	global $db;

	$tbl_req 					= REQ_TBL;
	$f_req_proj_id				= $tbl_req .".". REQ_PROJECT_ID;
	$f_req_id 					= $tbl_req .".". REQ_ID;
	$f_req_filename 			= $tbl_req .".". REQ_FILENAME;
	$f_req_area_covered 		= $tbl_req .".". REQ_AREA_COVERED;
	$f_req_type		 			= $tbl_req .".". REQ_TYPE;
	$f_req_parent	 			= $tbl_req .".". REQ_PARENT;
	$f_req_label	 			= $tbl_req .".". REQ_LABEL;
	$f_req_unique_id 			= $tbl_req .".". REQ_UNIQUE_ID;
	$f_req_locked_by			= $tbl_req .".". REQ_LOCKED_BY;
	$f_req_locked_date			= $tbl_req .".". REQ_LOCKED_DATE;
	$f_req_rec_or_file			= $tbl_req .".". REQ_REC_FILE;
	$f_req_priority				= $tbl_req .".". REQ_PRIORITY;
	$f_req_last_updated			= $tbl_req .".". REQ_LAST_UPDATED;

	$tbl_req_ver				= REQ_VERS_TBL;
	$f_req_version_id			= $tbl_req_ver .".". REQ_VERS_UNIQUE_ID;
	$f_req_ver_req_id			= $tbl_req_ver .".". REQ_VERS_REQ_ID;
	$f_req_ver_defect_id		= $tbl_req_ver .".". REQ_VERS_DEFECT_ID;
	$f_req_ver_filename 		= $tbl_req_ver .".". REQ_VERS_FILENAME;
	$f_req_ver_version			= $tbl_req_ver .".". REQ_VERS_VERSION;
	$f_req_ver_timestamp		= $tbl_req_ver .".". REQ_VERS_TIMESTAMP;
	$f_req_ver_uploaded_by		= $tbl_req_ver .".". REQ_VERS_UPLOADED_BY;
	$f_req_ver_filename			= $tbl_req_ver .".". REQ_VERS_FILENAME;
	$f_req_ver_comments			= $tbl_req_ver .".". REQ_VERS_COMMENTS;
	$f_req_ver_status			= $tbl_req_ver .".". REQ_VERS_STATUS;
	$f_req_ver_shed_release		= $tbl_req_ver .".". REQ_VERS_SCHEDULED_RELEASE_IMP;
	$f_req_ver_shed_build		= $tbl_req_ver .".". REQ_VERS_SCHEDULED_BUILD_IMP;
	$f_req_ver_actual_release	= $tbl_req_ver .".". REQ_VERS_ACTUAL_RELEASE_IMP;
	$f_req_ver_actual_build		= $tbl_req_ver .".". REQ_VERS_ACTUAL_BUILD_IMP;
	$f_req_ver_detail			= $tbl_req_ver .".". REQ_VERS_DETAIL;
	$f_req_ver_assigned			= $tbl_req_ver .".". REQ_VERS_ASSIGNED_TO;
	$f_req_ver_reason_change	= $tbl_req_ver .".". REQ_VERS_REASON_CHANGE;

	$tbl_req_doc_type			= REQ_DOC_TYPE_TBL;
	$f_req_doc_type_name		= $tbl_req_doc_type .".". REQ_DOC_TYPE_NAME;
	$f_req_doc_type_id			= $tbl_req_doc_type .".". REQ_DOC_TYPE_ID;
	$f_req_doc_type_project_id	= $tbl_req_doc_type .".". REQ_DOC_TYPE_PROJ_ID;
	$f_req_doc_type_root_doc	= $tbl_req_doc_type .".". REQ_DOC_TYPE_ROOT_DOC;

	$tbl_req_area_covered		= REQ_AREA_COVERAGE_TBL;
	$f_req_area_covered_name	= $tbl_req_area_covered .".". REQ_AREA_COVERAGE;
	$f_req_area_covered_id		= $tbl_req_area_covered .".". REQ_AREA_COVERAGE_ID;
	$f_req_area_covered_proj_id	= $tbl_req_area_covered .".". REQ_AREA_PROJ_ID;

	$q = "SELECT
				$f_req_id,
				$f_req_filename,
				$f_req_doc_type_name,
				$f_req_locked_by,
				$f_req_locked_date,
				$f_req_parent,
				$f_req_version_id,
				$f_req_ver_defect_id,
				$f_req_ver_filename,
				$f_req_ver_version,
				$f_req_ver_detail,
				$f_req_ver_status,
				$f_req_ver_uploaded_by,
				$f_req_ver_timestamp,
				$f_req_ver_assigned,
				$f_req_ver_reason_change,
				$f_req_rec_or_file,
				$f_req_area_covered_name,
				$f_req_area_covered_id,
				$f_req_doc_type_id,
				$f_req_priority,
				$f_req_last_updated
			FROM $tbl_req
			INNER JOIN $tbl_req_ver
				ON $f_req_ver_req_id = $f_req_id
			LEFT JOIN $tbl_req_doc_type
				ON $f_req_doc_type_id = $f_req_type
			LEFT JOIN $tbl_req_area_covered
				ON $f_req_area_covered_id = $f_req_area_covered
			WHERE $f_req_id = $req_id
				AND $f_req_proj_id = $project_id";

	if( $version_id ) {
		$q .= " AND $f_req_version_id = '$version_id'";
	}
	else {
		$q .= " ORDER BY $f_req_version_id ASC";
	}
	//print"$q<br>";

	$row = db_fetch_array( $db, db_query($db, $q) );
	//$row = db_fetch_array( $db, $rs);

	return $row;
}

function requirement_get_relationships($req_id) {

	$tbl_req_assoc				= REQ_ASSOC_TBL;
	$f_req_assoc_id				= $tbl_req_assoc .".". REQ_ASSOC_ID;
	$f_req_assoc_primary		= $tbl_req_assoc .".". REQ_ASSOC_PRIMARY_ID;
	$f_req_assoc_secondary		= $tbl_req_assoc .".". REQ_ASSOC_SECONDARY_ID;
	$f_req_assoc_relationship	= $tbl_req_assoc .".". REQ_ASSOC_RELATIONSHIP;

	$tbl_req 					= REQ_TBL;
	$f_req_proj_id				= $tbl_req .".". REQ_PROJECT_ID;
	$f_req_id 					= $tbl_req .".". REQ_ID;
	$f_req_filename 			= $tbl_req .".". REQ_FILENAME;

	global $db;

	$q = "	SELECT
				$f_req_assoc_id,
				$f_req_assoc_primary,
				$f_req_assoc_secondary,
				$f_req_assoc_relationship,
				$f_req_filename,
				$f_req_id
			FROM
				$tbl_req_assoc
			INNER JOIN $tbl_req
				ON $f_req_assoc_secondary = $f_req_id
			WHERE $f_req_assoc_primary = $req_id";

	$rows_primary = db_fetch_array( $db, db_query($db, $q) );

	$q = "	SELECT
				$f_req_assoc_id,
				$f_req_assoc_primary,
				$f_req_assoc_secondary,
				$f_req_assoc_relationship,
				$f_req_filename
			FROM
				$tbl_req_assoc
			INNER JOIN $tbl_req
				ON $f_req_assoc_primary = $f_req_id
			WHERE $f_req_assoc_secondary = $req_id";

	$rows_secondary = db_fetch_array( $db, db_query($db, $q) );

	return array("Primary"=>$rows_primary, "Secondary"=>$rows_secondary);
}

function requirement_get_test_relationships($req_id) {

	$tbl_req 					= REQ_TBL;
	$f_req_proj_id				= $tbl_req .".". REQ_PROJECT_ID;
	$f_req_id 					= $tbl_req .".". REQ_ID;
	$f_req_filename 			= $tbl_req .".". REQ_FILENAME;

    $tbl_test_req_assoc			= TEST_REQ_ASSOC_TBL;
    $f_test_req_assoc_id		= $tbl_test_req_assoc .".". TEST_REQ_ASSOC_ID;
    $f_test_req_assoc_req_id	= $tbl_test_req_assoc .".". TEST_REQ_ASSOC_REQ_ID;
    $f_test_req_assoc_test_id	= $tbl_test_req_assoc .".". TEST_REQ_ASSOC_TEMPEST_TEST_ID;
	$f_test_req_assoc_covered	= $tbl_test_req_assoc .".". TEST_REQ_ASSOC_PERCENT_COVERED;

	$tbl_test			= TEST_TBL;
	$f_test_name 		= $tbl_test .".". TEST_NAME;
	$f_test_id 			= $tbl_test .".". TEST_ID;

	$q = "	SELECT
				$f_test_req_assoc_id,
				$f_test_id,
				$f_test_name,
				$f_test_req_assoc_covered
			FROM $tbl_test_req_assoc
			INNER JOIN $tbl_test
				on $f_test_req_assoc_test_id = $f_test_id
			WHERE $f_test_req_assoc_req_id = $req_id";

	global $db;

	$rs = db_query($db, $q);
	$rows = db_fetch_array($db, $rs);

	return $rows;
}

function requirement_get_assoc_releases($req_id) {

	$tbl_req 					= REQ_TBL;
	$f_req_proj_id				= $tbl_req .".". REQ_PROJECT_ID;
	$f_req_id 					= $tbl_req .".". REQ_ID;
	$f_req_filename 			= $tbl_req .".". REQ_FILENAME;

	$tbl_req_ver_assoc_rel		= REQ_VERS_ASSOC_REL;
	$f_req_ver_assoc_rel_id		= $tbl_req_ver_assoc_rel.".".REQ_VERS_ASSOC_REL_ID;
	$f_req_ver_assoc_rel_req_id	= $tbl_req_ver_assoc_rel.".".REQ_VERS_ASSOC_REL_REQ_ID;
	$f_req_ver_assoc_rel_rel_id	= $tbl_req_ver_assoc_rel.".".REQ_VERS_ASSOC_REL_REL_ID;

	$tbl_release			= RELEASE_TBL;
	$f_release_id			= $tbl_release.".".RELEASE_ID;
	$f_release_project_id	= $tbl_release.".".PROJECT_ID;
	$f_release_name			= $tbl_release.".".RELEASE_NAME;
	$f_release_archive		= $tbl_release.".".RELEASE_ARCHIVE;

	$q = "	SELECT
				$f_req_ver_assoc_rel_id,
				$f_release_id,
				$f_release_name
			FROM $tbl_req_ver_assoc_rel
			INNER JOIN $tbl_release ON $f_release_id = $f_req_ver_assoc_rel_rel_id
			WHERE $f_req_ver_assoc_rel_req_id = $req_id";

	global $db;

	$rs = db_query($db, $q);
	return db_fetch_array($db, $rs);
}

function requirement_edit_assoc_tests($req_id, $session_records_name, $pc_covered_text_input_name) {

	global $db;

	$tbl_req 					= REQ_TBL;
	$f_req_proj_id				= $tbl_req .".". REQ_PROJECT_ID;
	$f_req_id 					= $tbl_req .".". REQ_ID;
	$f_req_filename 			= $tbl_req .".". REQ_FILENAME;

    $tbl_test_req_assoc			= TEST_REQ_ASSOC_TBL;
    $f_test_req_assoc_req_id	= $tbl_test_req_assoc .".". TEST_REQ_ASSOC_REQ_ID;
    $f_test_req_assoc_test_id	= $tbl_test_req_assoc .".". TEST_REQ_ASSOC_TEMPEST_TEST_ID;
	$f_test_req_assoc_covered	= $tbl_test_req_assoc .".". TEST_REQ_ASSOC_PERCENT_COVERED;

	$tbl_test			= TEST_TBL;
	$f_test_name 		= $tbl_test .".". TEST_NAME;
	$f_test_id 			= $tbl_test .".". TEST_ID;

	$s_project_properties   = session_get_project_properties();
	$project_id				= $s_project_properties['project_id'];

	$test_ids = test_get_all_ids($project_id);

	foreach($test_ids as $row) {

		$test_id = $row[TEST_ID];

		$q = "	SELECT $f_test_req_assoc_req_id
				FROM $tbl_test_req_assoc
				WHERE $f_test_req_assoc_req_id = $req_id
					AND	$f_test_req_assoc_test_id = $test_id";

		$rs = db_query($db, $q);
		$record_exists = db_num_rows($db, $rs);

		if( session_records_ischecked($session_records_name, $test_id) ) {

			$pc_covered = session_validate_form_get_field($pc_covered_text_input_name.$test_id);
			if( $pc_covered == '' ) {
				$pc_covered = 0;
			}

			if(!$record_exists) {

				# Add new record
				$q = "	INSERT INTO $tbl_test_req_assoc
							($f_test_req_assoc_req_id, $f_test_req_assoc_test_id, $f_test_req_assoc_covered)
						VALUES
							($req_id, $test_id, '$pc_covered')";
			} else {

				# Update current record
				$q = "	UPDATE $tbl_test_req_assoc
						SET
							$f_test_req_assoc_covered = '$pc_covered'
						WHERE
							$f_test_req_assoc_req_id = $req_id
							AND $f_test_req_assoc_test_id = $test_id";
			}
		} else {

			if($record_exists) {
				$q = "	DELETE FROM $tbl_test_req_assoc
						WHERE $f_test_req_assoc_req_id = $req_id
							AND	$f_test_req_assoc_test_id = $test_id";
			}
		}

		db_query($db, $q);
	}
}

# ----------------------------------------------------------------------
# Get the release details for a give requirment version
# ----------------------------------------------------------------------
function requirement_get_release( $req_version ) {

	global $db;
	
	$req_rel_tbl		= REQ_VERS_ASSOC_REL;
	$f_version_id		= $req_rel_tbl .".". REQ_VERS_ASSOC_REL_REQ_ID;
	$f_release_id		= $req_rel_tbl .".". REQ_VERS_ASSOC_REL_REL_ID;
	$release_name		= "";

	$q = "SELECT
			$f_release_id
		 FROM
			$req_rel_tbl
		 WHERE
			$f_version_id = '$req_version'";
	
	$release_id = db_get_one($db, $q);
		
	if( $release_id != '' ) {
		$release_name = admin_get_release_name( $release_id );
	}
	

	return $release_name;
}

#------------------------------------------------------------------------------------
#  PURPOSE:  
#		This function will update the requirement_release_assoc table
#		Given the requirment version, we will update the release id
#		This functionality is used when a user is updating a requirment without 
#		adding a new version
#
#------------------------------------------------------------------------------------
function requirement_edit_release( $req_version_id, $release_id ) {

	global $db;
	
	$req_rel_tbl		= REQ_VERS_ASSOC_REL;
	$f_version_id		= $req_rel_tbl .".". REQ_VERS_ASSOC_REL_REQ_ID;
	$f_release_id		= $req_rel_tbl .".". REQ_VERS_ASSOC_REL_REL_ID;

	# Check to see if the req_version_id appears in the assoc table
	$record_exists = requirement_release_assoc_exists( $req_version_id );
	
	# Delete the record if there is existing record and the user has selected a blank release name
	if( $record_exists  && $release_id == '' ) {
		
		$q = "DELETE FROM $req_rel_tbl WHERE $f_version_id = '$req_version_id'";
	}
	elseif( $record_exists && $release_id != '') { # Update the assoc table if there is an existing record and release exists
		
		$q = "UPDATE $req_rel_tbl SET $f_release_id = '$release_id' WHERE $f_version_id = '$req_version_id'";
	}
	elseif( !$record_exists && $release_id == '') {  # Do nothing if there is no existing record and release is blank
		
		return;
	}
	else { # Insert a new record if there is no record in the assoc table
	
		$q = "INSERT INTO $req_rel_tbl( $f_version_id, $f_release_id ) VALUES ( '$req_version_id', '$release_id' )";
	}
	
	db_query( $db, $q );

	
}

#------------------------------------------------------------------------------------
#  PURPOSE:  
#		Find out if there is an association between a given requirment version
#		and a release.  Return TRUE if there is an association (a record in the 
#		requirement_release_assoc table) and FALSE if there is not.
#		
#		Should probably add additional functionality to this function so that we 
#		don't have to update the record every time in requirement_edit_release
#
#------------------------------------------------------------------------------------
function requirement_release_assoc_exists( $req_version_id ) {

	global $db;
	
	$req_rel_tbl		= REQ_VERS_ASSOC_REL;
	$f_version_id		= $req_rel_tbl .".". REQ_VERS_ASSOC_REL_REQ_ID;
	$f_release_id		= $req_rel_tbl .".". REQ_VERS_ASSOC_REL_REL_ID;

	# check to see if there is an exact match.  If so, no need to update the assoc table
	$q = "SELECT $f_version_id FROM $req_rel_tbl WHERE $f_version_id = '$req_version_id'";
	$rc = db_get_one($db, $q);

	if( $rc == '' ) {
		$record = false;	
	}
	else { 
		$record = true;
	}

	return $record;

}

function requirement_edit_assoc_releases($req_id, $assoc_releases) {

	global $db;

	$tbl_req 					= REQ_TBL;
	$f_req_proj_id				= $tbl_req .".". REQ_PROJECT_ID;
	$f_req_id 					= $tbl_req .".". REQ_ID;
	$f_req_filename 			= $tbl_req .".". REQ_FILENAME;

	$tbl_req_ver_assoc_rel		= REQ_VERS_ASSOC_REL;
	$f_req_ver_assoc_rel_id		= $tbl_req_ver_assoc_rel.".".REQ_VERS_ASSOC_REL_ID;
	$f_req_ver_assoc_rel_req_id	= $tbl_req_ver_assoc_rel.".".REQ_VERS_ASSOC_REL_REQ_ID;
	$f_req_ver_assoc_rel_rel_id	= $tbl_req_ver_assoc_rel.".".REQ_VERS_ASSOC_REL_REL_ID;

	$tbl_release			= RELEASE_TBL;
	$f_release_id			= $tbl_release.".".RELEASE_ID;
	$f_release_project_id	= $tbl_release.".".PROJECT_ID;
	$f_release_name			= $tbl_release.".".RELEASE_NAME;
	$f_release_archive		= $tbl_release.".".RELEASE_ARCHIVE;

	$s_project_properties   = session_get_project_properties();
	$project_id				= $s_project_properties['project_id'];

	$release_ids = admin_get_releases($project_id);

	foreach($release_ids as $row) {

		$q = "	SELECT $f_req_ver_assoc_rel_id
				FROM $tbl_req_ver_assoc_rel
				WHERE $f_req_ver_assoc_rel_req_id = $req_id
					AND	$f_req_ver_assoc_rel_rel_id = ".$row[RELEASE_ID];

		$rs = db_query($db, $q);
		$record_exists = db_num_rows($db, $rs);

		if( util_array_key_search($row[RELEASE_ID], $assoc_releases) ) {

			if(!$record_exists) {
				$q = "	INSERT INTO $tbl_req_ver_assoc_rel
							($f_req_ver_assoc_rel_req_id, $f_req_ver_assoc_rel_rel_id)
						VALUES
							($req_id, ".$row[RELEASE_ID].")";
			}
		} else {

			if($record_exists) {
				$q = "	DELETE FROM $tbl_req_ver_assoc_rel
						WHERE $f_req_ver_assoc_rel_req_id = $req_id
							AND	$f_req_ver_assoc_rel_rel_id = ".$row[RELEASE_ID];
			}
		}

		db_query($db, $q);
	}
}

function requirement_group_assoc_release($req_ids, $release_id) {

	global $db;

	$tbl_req 					= REQ_TBL;
	$f_req_proj_id				= $tbl_req .".". REQ_PROJECT_ID;
	$f_req_id 					= $tbl_req .".". REQ_ID;
	$f_req_filename 			= $tbl_req .".". REQ_FILENAME;

	$tbl_req_ver_assoc_rel		= REQ_VERS_ASSOC_REL;
	$f_req_ver_assoc_rel_id		= $tbl_req_ver_assoc_rel.".".REQ_VERS_ASSOC_REL_ID;
	$f_req_ver_assoc_rel_req_id	= $tbl_req_ver_assoc_rel.".".REQ_VERS_ASSOC_REL_REQ_ID;
	$f_req_ver_assoc_rel_rel_id	= $tbl_req_ver_assoc_rel.".".REQ_VERS_ASSOC_REL_REL_ID;

	$tbl_release			= RELEASE_TBL;
	$f_release_id			= $tbl_release.".".RELEASE_ID;
	$f_release_project_id	= $tbl_release.".".PROJECT_ID;
	$f_release_name			= $tbl_release.".".RELEASE_NAME;
	$f_release_archive		= $tbl_release.".".RELEASE_ARCHIVE;

	$s_project_properties   = session_get_project_properties();
	$project_id				= $s_project_properties['project_id'];

	foreach($req_ids as $req_id) {

		$q = "	SELECT $f_req_ver_assoc_rel_id
				FROM $tbl_req_ver_assoc_rel
				WHERE $f_req_ver_assoc_rel_req_id = $req_id
					AND	$f_req_ver_assoc_rel_rel_id = ".$release_id;

		$rs = db_query($db, $q);
		$record_exists = db_num_rows($db, $rs);

		if(!$record_exists) {
			$q = "	INSERT INTO $tbl_req_ver_assoc_rel
						($f_req_ver_assoc_rel_req_id, $f_req_ver_assoc_rel_rel_id)
					VALUES
						($req_id, $release_id)";
		}

		db_query($db, $q);
	}
}


function requirements_test_assoc($req_id) {

	global $db;

	$tbl_req 					= REQ_TBL;
	$f_req_id 					= $tbl_req .".". REQ_ID;
	$f_req_filename 			= $tbl_req .".". REQ_FILENAME;
	$f_req_area_covered 		= $tbl_req .".". REQ_AREA_COVERED;
	$f_req_type		 			= $tbl_req .".". REQ_TYPE;
	$f_req_parent	 			= $tbl_req .".". REQ_PARENT;
	$f_req_label	 			= $tbl_req .".". REQ_LABEL;
	$f_req_unique_id 			= $tbl_req .".". REQ_UNIQUE_ID;
	$f_req_functionality		= $tbl_req .".". REQ_FUNCTIONALITY;
	$f_req_locked_by			= $tbl_req .".". REQ_LOCKED_BY;
	$f_req_locked_date			= $tbl_req .".". REQ_LOCKED_DATE;
	$f_req_rec_file				= $tbl_req .".". REQ_REC_FILE;

	$tbl_req_ver				= REQ_VERS_TBL;
	$f_req_ver_uid				= $tbl_req_ver .".". REQ_VERS_UNIQUE_ID;
	$f_req_ver_req_id			= $tbl_req_ver .".". REQ_VERS_REQ_ID;
	$f_req_ver_version			= $tbl_req_ver .".". REQ_VERS_VERSION;
	$f_req_ver_timestamp		= $tbl_req_ver .".". REQ_VERS_TIMESTAMP;
	$f_req_ver_uploaded_by		= $tbl_req_ver .".". REQ_VERS_UPLOADED_BY;
	$f_req_ver_filename			= $tbl_req_ver .".". REQ_VERS_FILENAME;
	$f_req_ver_comments			= $tbl_req_ver .".". REQ_VERS_COMMENTS;
	$f_req_ver_status			= $tbl_req_ver .".". REQ_VERS_STATUS;
	$f_req_ver_shed_release		= $tbl_req_ver .".". REQ_VERS_SCHEDULED_RELEASE_IMP;
	$f_req_ver_shed_build		= $tbl_req_ver .".". REQ_VERS_SCHEDULED_BUILD_IMP;
	$f_req_ver_actual_release	= $tbl_req_ver .".". REQ_VERS_ACTUAL_RELEASE_IMP;
	$f_req_ver_actual_build		= $tbl_req_ver .".". REQ_VERS_ACTUAL_BUILD_IMP;
	$f_req_ver_detail			= $tbl_req_ver .".". REQ_VERS_DETAIL;
	$f_req_ver_release			= $tbl_req_ver .".". REQ_VERS_ASSIGN_RELEASE;
	$f_req_ver_latest			= $tbl_req_ver .".". REQ_VERS_LATEST;
	$f_req_ver_reason_change	= $tbl_req_ver .".". REQ_VERS_REASON_CHANGE;
	$f_req_ver_assigned			= $tbl_req_ver .".". REQ_VERS_ASSIGNED_TO;

    $tbl_test_req_assoc			= TEST_REQ_ASSOC_TBL;
    $f_test_req_assoc_req_id	= $tbl_test_req_assoc .".". TEST_REQ_ASSOC_REQ_ID;
    $f_test_req_assoc_test_id	= $tbl_test_req_assoc .".". TEST_REQ_ASSOC_TEMPEST_TEST_ID;

	$tbl_test					= TEST_TBL;
	$f_test_id					= TEST_TBL .".". TEST_ID;
	$f_deleted					= TEST_TBL .".". TEST_DELETED;
	$f_archived					= TEST_TBL .".". TEST_ARCHIVED;


	$q = "	SELECT DISTINCT
				$f_test_req_assoc_test_id
			FROM
				$tbl_test_req_assoc
			INNER JOIN $tbl_test
				ON $f_test_id = $f_test_req_assoc_test_id
			WHERE
				$f_test_req_assoc_req_id = $req_id
				AND $f_deleted = 'N'
				AND $f_archived = 'N'";

	$rs = db_query( $db, $q );

	$rows = array();
	while( $fields = db_fetch_row($db, $rs) ) {

		$rows[$fields[TEST_ID]] = "";
	}

	return $rows;
}

# ----------------------------------------------------------------------
# Get the version of a release
# ----------------------------------------------------------------------
function requirement_get_release_version( $release_id ) {

	$tbl_req_ver				= REQ_VERS_TBL;
	$f_req_ver_uid				= $tbl_req_ver .".". REQ_VERS_UNIQUE_ID;
	$f_req_ver_req_id			= $tbl_req_ver .".". REQ_VERS_REQ_ID;
	$f_req_ver_version			= $tbl_req_ver .".". REQ_VERS_VERSION;
	$f_req_ver_timestamp		= $tbl_req_ver .".". REQ_VERS_TIMESTAMP;
	$f_req_ver_uploaded_by		= $tbl_req_ver .".". REQ_VERS_UPLOADED_BY;
	$f_req_ver_filename			= $tbl_req_ver .".". REQ_VERS_FILENAME;
	$f_req_ver_comments			= $tbl_req_ver .".". REQ_VERS_COMMENTS;
	$f_req_ver_status			= $tbl_req_ver .".". REQ_VERS_STATUS;
	$f_req_ver_shed_release		= $tbl_req_ver .".". REQ_VERS_SCHEDULED_RELEASE_IMP;
	$f_req_ver_shed_build		= $tbl_req_ver .".". REQ_VERS_SCHEDULED_BUILD_IMP;
	$f_req_ver_actual_release	= $tbl_req_ver .".". REQ_VERS_ACTUAL_RELEASE_IMP;
	$f_req_ver_actual_build		= $tbl_req_ver .".". REQ_VERS_ACTUAL_BUILD_IMP;
	$f_req_ver_detail			= $tbl_req_ver .".". REQ_VERS_DETAIL;
	$f_req_ver_assign_release	= $tbl_req_ver .".". REQ_VERS_ASSIGN_RELEASE;

	$q = "	SELECT
				$f_req_ver_version
			FROM $tbl_req_ver
			WHERE
				$f_req_ver_assign_release = $release_id";

	global $db;

	return db_get_one($db, $q);
}

# ----------------------------------------------------------------------
# Get the version of a release
# ----------------------------------------------------------------------
function requirement_get_latest_version( $req_id ) {

	global $db;
	$tbl_req_ver				= REQ_VERS_TBL;
	$f_req_ver_uid				= $tbl_req_ver .".". REQ_VERS_UNIQUE_ID;
	$f_req_ver_req_id			= $tbl_req_ver .".". REQ_VERS_REQ_ID;
	$f_req_ver_latest			= $tbl_req_ver .".". REQ_VERS_LATEST;

	$q = "SELECT
		    $f_req_ver_uid
		  FROM $tbl_req_ver
		  WHERE $f_req_ver_req_id = '$req_id'
		  AND $f_req_ver_latest = 'Y'";

	//print"$q<br>";

	return db_get_one($db, $q);
}

# ----------------------------------------------------------------------
# Get the version of a release
# ----------------------------------------------------------------------
function requirement_get_version_number( $req_id, $req_version_id ) {

	global $db;
	$tbl_req_ver				= REQ_VERS_TBL;
	$f_version_id				= $tbl_req_ver .".". REQ_VERS_UNIQUE_ID;
	$f_version_req_id			= $tbl_req_ver .".". REQ_VERS_REQ_ID;
	$f_version_number			= $tbl_req_ver .".". REQ_VERS_VERSION;

	$q = "SELECT
		    $f_version_number
		  FROM $tbl_req_ver
		  WHERE $f_version_req_id = '$req_id'
		  AND $f_version_id = '$req_version_id'";

	//print"$q<br>";

	return db_get_one($db, $q);
}

# ----------------------------------------------------------------------
# Get all tests associated with a requirement in the testset
# ----------------------------------------------------------------------
function requirement_get_test_details($req_id, $testset_id) {

	$tbl_test_req_assoc			= TEST_REQ_ASSOC_TBL;
	$f_test_req_assoc_id		= $tbl_test_req_assoc .".". TEST_REQ_ASSOC_ID;
	$f_test_req_assoc_test_id	= $tbl_test_req_assoc .".". TEST_REQ_ASSOC_TEMPEST_TEST_ID;
	$f_test_req_assoc_req_id	= $tbl_test_req_assoc .".". TEST_REQ_ASSOC_REQ_ID;
	$f_test_req_assoc_percent	= $tbl_test_req_assoc .".". TEST_REQ_ASSOC_PERCENT_COVERED;

	$ts_assoc_tbl           = TEST_TS_ASSOC_TBL;
	$f_ts_assoc_id          = TEST_TS_ASSOC_TBL. "." .TEST_TS_ASSOC_ID;
	$f_ts_assoc_ts_id       = TEST_TS_ASSOC_TBL. "." .TEST_TS_ASSOC_TS_ID;
	$f_ts_assoc_test_id     = TEST_TS_ASSOC_TBL. "." .TEST_TS_ASSOC_TEST_ID;
	$f_ts_assoc_test_status = TEST_TS_ASSOC_TBL. "." .TEST_TS_ASSOC_STATUS;
	$f_ts_assoc_assigned_to = TEST_TS_ASSOC_TBL. "." .TEST_TS_ASSOC_ASSIGNED_TO;
	$f_ts_assoc_comments    = TEST_TS_ASSOC_TBL. "." .TEST_TS_ASSOC_COMMENTS;
	$f_ts_assoc_timestamp   = TEST_TS_ASSOC_TBL. "." .TEST_TS_ASSOC_TIMESTAMP;

    $tbl_test          		= TEST_TBL;
	$f_test_id				= $tbl_test. "." .TEST_ID;

	$q = "	SELECT DISTINCT
				$f_test_req_assoc_test_id,
				$f_test_req_assoc_percent,
				$f_ts_assoc_test_status
			FROM $tbl_test_req_assoc
			INNER JOIN $tbl_test
				ON $f_test_id =  $f_test_req_assoc_test_id
			INNER JOIN $ts_assoc_tbl
				ON $f_test_req_assoc_test_id = $f_ts_assoc_test_id
			WHERE $f_test_req_assoc_req_id = $req_id
				AND $f_ts_assoc_ts_id = $testset_id";

	global $db;

	$rows = db_fetch_array($db, db_query($db, $q));

	return $rows;
}

# ----------------------------------------------------------------------
# Get field value from test table
# Useful for getting qa_owners, ba_owners, etc from tests
# INPUT:
#   $project_id:
#   $field = field in test table you want to query
#   $blank: set equal to true if you want a blank added to the end of return value
# OUTPUT:
#   array containing array containing field value
# ----------------------------------------------------------------------
function requirement_get_distinct_field($project_id, $field, $blank=false) {

    global $db;

	$tbl_req 					= REQ_TBL;
	$f_req_proj_id				= $tbl_req .".". REQ_PROJECT_ID;
	$f_req_id 					= $tbl_req .".". REQ_ID;
	$f_req_unique_id 			= $tbl_req .".". REQ_UNIQUE_ID;

	$tbl_req_ver				= REQ_VERS_TBL;
	$f_req_ver_uid				= $tbl_req_ver .".". REQ_VERS_UNIQUE_ID;
	$f_req_ver_req_id			= $tbl_req_ver .".". REQ_VERS_REQ_ID;

	$q = "	SELECT DISTINCT ($field)
			FROM $tbl_req
			INNER JOIN $tbl_req_ver
				ON $f_req_ver_req_id = $f_req_id
			WHERE $field != ''
				AND $f_req_proj_id = $project_id
			ORDER BY $field ASC";

    $rows = array();

	$rs = db_query($db, $q);

	while($row = db_fetch_row($db, $rs)) {
		$rows[] = $row[$field];
	}

    if( $blank == true ) {
    	$rows[] = "";
    }

    return $rows;
}

function requirement_table_set_field($req_id, $field, $value) {

    global $db;

	$tbl_req 					= REQ_TBL;
	$f_req_id 					= $tbl_req .".". REQ_ID;

	$q = "	UPDATE $tbl_req
			SET
				$field = '$value'
			WHERE
				$f_req_id = $req_id";

	db_query($db, $q);
}

function requirement_version_table_set_field($req_ver_id, $field, $value) {

    global $db;

	$tbl_req_ver				= REQ_VERS_TBL;
	$f_req_ver_uid				= $tbl_req_ver .".". REQ_VERS_UNIQUE_ID;
	$f_req_ver_req_id			= $tbl_req_ver .".". REQ_VERS_REQ_ID;

	$q = "	UPDATE $tbl_req_ver
			SET
				$field = '$value'
			WHERE
				$f_req_ver_uid = $req_ver_id";

    db_query($db, $q);
}


function requirement_get_functionality($project_id, $req_id=null) {

	$tbl_req_funct		= REQ_FUNCT_TBL;
	$f_name				= $tbl_req_funct .".". REQ_FUNCT_NAME;
	$f_id				= $tbl_req_funct .".". REQ_FUNCT_ID;
	$f_project			= $tbl_req_funct .".". REQ_FUNCT_PROJ_ID;

	$tbl_assoc			= REQ_FUNCT_ASSOC_TBL;
	$f_assoc_id			= $tbl_assoc .".". REQ_FUNCT_ASSOC_ID;
	$f_assoc_req_id		= $tbl_assoc .".". REQ_FUNCT_ASSOC_REQ_ID;
	$f_assoc_funct_id	= $tbl_assoc .".". REQ_FUNCT_ASSOC_FUNCT_ID;

	$where = "";

	if($req_id) {

		$where = "	WHERE	$f_assoc_req_id = $req_id
						AND $f_project = $project_id";
	} else {

		$where = "WHERE $f_project = $project_id";
	}

	$q	=	"	SELECT DISTINCT
						$f_id,
						$f_name
				FROM	$tbl_req_funct
				INNER JOIN $tbl_assoc
					ON $f_assoc_funct_id = $f_id
				$where
				ORDER BY $f_name ASC";

	global $db;
	$functions = array();

	$rows = db_fetch_array($db, db_query($db, $q));

	foreach($rows as $row) {

		$functions[$row[REQ_FUNCT_ID]] = $row[REQ_FUNCT_NAME];
	}

	return $functions;
}

function requirement_get_all_assoc_releases($project_id, $blank=false) {

	$tbl_req 					= REQ_TBL;
	$f_req_proj_id				= $tbl_req .".". REQ_PROJECT_ID;
	$f_req_id 					= $tbl_req .".". REQ_ID;
	$f_req_filename 			= $tbl_req .".". REQ_FILENAME;

	$tbl_req_ver_assoc_rel		= REQ_VERS_ASSOC_REL;
	$f_req_ver_assoc_rel_id		= $tbl_req_ver_assoc_rel.".".REQ_VERS_ASSOC_REL_ID;
	$f_req_ver_assoc_rel_req_id	= $tbl_req_ver_assoc_rel.".".REQ_VERS_ASSOC_REL_REQ_ID;
	$f_req_ver_assoc_rel_rel_id	= $tbl_req_ver_assoc_rel.".".REQ_VERS_ASSOC_REL_REL_ID;

	$tbl_release			= RELEASE_TBL;
	$f_release_id			= $tbl_release.".".RELEASE_ID;
	$f_release_project_id	= $tbl_release.".".PROJECT_ID;
	$f_release_name			= $tbl_release.".".RELEASE_NAME;
	$f_release_archive		= $tbl_release.".".RELEASE_ARCHIVE;

	$q = "	SELECT DISTINCT
				$f_release_id,
				$f_release_name
			FROM $tbl_req_ver_assoc_rel
			INNER JOIN $tbl_release ON $f_release_id = $f_req_ver_assoc_rel_rel_id
			WHERE $f_release_project_id = $project_id";

	global $db;
	$releases = array();

	$rows = db_fetch_array($db, db_query($db, $q));

	foreach($rows as $row) {

		$releases[$row[RELEASE_ID]] = $row[RELEASE_NAME];
	}

	if( $blank ) {

		$releases[""] = "";
	}

	return $releases;
}

function requirement_get_statuses() {

	return array( 	"New",
					"Reviewed",
					"Approved",
					"Rejected",
					"Implemented",
					"" );

}

############################################################
# Return an array with the valid priorities of a requirement
############################################################
function requirement_get_priority() {

	return array( "High",
				  "Medium",
				  "Low",
				  "" );

}

function requirement_get_types($project_id, $blank=false) {

	global $db;

	$types = array();

	/*
	$tbl_req 					= REQ_TBL;
	$f_req_proj_id				= $tbl_req .".". REQ_PROJECT_ID;
	$f_req_id 					= $tbl_req .".". REQ_ID;
	$f_req_type		 			= $tbl_req .".". REQ_TYPE;
	*/

	$tbl_req_doc_type			= REQ_DOC_TYPE_TBL;
	$f_req_doc_type_name		= $tbl_req_doc_type .".". REQ_DOC_TYPE_NAME;
	$f_req_doc_type_id			= $tbl_req_doc_type .".". REQ_DOC_TYPE_ID;
	$f_req_doc_type_project_id	= $tbl_req_doc_type .".". REQ_DOC_TYPE_PROJ_ID;
	$f_req_doc_type_root_doc	= $tbl_req_doc_type .".". REQ_DOC_TYPE_ROOT_DOC;


	$q	=	"	SELECT DISTINCT
						$f_req_doc_type_name,
						$f_req_doc_type_id,
						$f_req_doc_type_root_doc
				FROM $tbl_req_doc_type
				WHERE $f_req_doc_type_project_id = $project_id
				ORDER BY $f_req_doc_type_name ASC";
	
	/*
	$q	=	"	SELECT DISTINCT
						$f_req_doc_type_name,
						$f_req_doc_type_id,
						$f_req_doc_type_root_doc
				FROM $tbl_req_doc_type
				INNER JOIN $tbl_req ON $f_req_type = $f_req_doc_type_id
				WHERE $f_req_doc_type_project_id = $project_id
				ORDER BY $f_req_doc_type_name ASC";
	*/

	print"$q<br>";

	$rs = db_query($db, $q);

	while( $row = db_fetch_row($db, $rs) ) {

		$types[$row[REQ_DOC_TYPE_ID]] = $row[REQ_DOC_TYPE_NAME];
	}

	if($blank) {

		$types[""] = "";
	}

	return $types;

}

function requirement_get_areas($project_id, $blank=false) {

	global $db;

	$areas = array();

	$tbl_req 					= REQ_TBL;
	$f_req_proj_id				= $tbl_req .".". REQ_PROJECT_ID;
	$f_req_id 					= $tbl_req .".". REQ_ID;
	$f_req_area		 			= $tbl_req .".". REQ_AREA_COVERED;

	$tbl_req_area_covered		= REQ_AREA_COVERAGE_TBL;
	$f_req_area_covered_name	= $tbl_req_area_covered .".". REQ_AREA_COVERAGE;
	$f_req_area_covered_id		= $tbl_req_area_covered .".". REQ_AREA_COVERAGE_ID;
	$f_req_area_covered_proj_id	= $tbl_req_area_covered .".". REQ_AREA_PROJ_ID;

	$q	=	"	SELECT DISTINCT
						$f_req_area_covered_name,
						$f_req_area_covered_id
				FROM $tbl_req_area_covered
				INNER JOIN $tbl_req ON $f_req_area = $f_req_area_covered_id
				WHERE $f_req_area_covered_proj_id = $project_id
				ORDER BY $f_req_area_covered_name ASC";

	$rs = db_query($db, $q);

	while( $row = db_fetch_row($db, $rs) ) {

		$areas[$row[REQ_AREA_COVERAGE_ID]] = $row[REQ_AREA_COVERAGE];
	}

	if($blank) {

		$areas[""] = "";
	}

	return $areas;

}

/*
function req_assoc_exists( $req_id, $secondary_req_id ) {

	global $db;
	$match = false;



	$q = "SELECT * FROM Requirement_Assoc WHERE PrimaryID = '$req_id' AND SecondaryID = '$secondary_req_id' OR PrimaryID = '$secondary_req_id' AND SecondaryID = '$req_id'";
	$rs = $db->Execute( $q );
	$num = $rs->NumRows( $rs );
	if( $num > 0 ) {
		$match = true;
	}


	return $match;

}
*/

function requirement_get_last_updated($project_id ) {

	$tbl_req 					= REQ_TBL;
	$f_req_proj_id				= $tbl_req .".". REQ_PROJECT_ID;
	$f_req_id 					= $tbl_req .".". REQ_ID;
	$f_req_filename 			= $tbl_req .".". REQ_FILENAME;
	$f_req_area_covered			= $tbl_req .".". REQ_AREA_COVERED;
	$f_req_type		 			= $tbl_req .".". REQ_TYPE;
	$f_req_parent	 			= $tbl_req .".". REQ_PARENT;
	$f_req_label	 			= $tbl_req .".". REQ_LABEL;
	$f_req_unique_id 			= $tbl_req .".". REQ_UNIQUE_ID;
	$f_req_locked_by			= $tbl_req .".". REQ_LOCKED_BY;
	$f_req_locked_date			= $tbl_req .".". REQ_LOCKED_DATE;

	$tbl_req_ver				= REQ_VERS_TBL;
	$f_req_ver_uid				= $tbl_req_ver .".". REQ_VERS_UNIQUE_ID;
	$f_req_ver_req_id			= $tbl_req_ver .".". REQ_VERS_REQ_ID;
	$f_req_ver_version			= $tbl_req_ver .".". REQ_VERS_VERSION;
	$f_req_ver_timestamp		= $tbl_req_ver .".". REQ_VERS_TIMESTAMP;
	$f_req_ver_uploaded_by		= $tbl_req_ver .".". REQ_VERS_UPLOADED_BY;
	$f_req_ver_filename			= $tbl_req_ver .".". REQ_VERS_FILENAME;
	$f_req_ver_comments			= $tbl_req_ver .".". REQ_VERS_COMMENTS;
	$f_req_ver_status			= $tbl_req_ver .".". REQ_VERS_STATUS;
	$f_req_ver_shed_release		= $tbl_req_ver .".". REQ_VERS_SCHEDULED_RELEASE_IMP;
	$f_req_ver_shed_build		= $tbl_req_ver .".". REQ_VERS_SCHEDULED_BUILD_IMP;
	$f_req_ver_actual_release	= $tbl_req_ver .".". REQ_VERS_ACTUAL_RELEASE_IMP;
	$f_req_ver_actual_build		= $tbl_req_ver .".". REQ_VERS_ACTUAL_BUILD_IMP;
	$f_req_ver_detail			= $tbl_req_ver .".". REQ_VERS_DETAIL;
	$f_req_ver_latest			= $tbl_req_ver .".". REQ_VERS_LATEST;
	$f_req_ver_last_updated		= $tbl_req_ver .".". REQ_VERS_LAST_UPDATED;
	$f_req_ver_last_updated_by	= $tbl_req_ver .".". REQ_VERS_LAST_UPDATED_BY;

	$tbl_assoc			= REQ_FUNCT_ASSOC_TBL;
	$f_assoc_id			= $tbl_assoc .".". REQ_FUNCT_ASSOC_ID;
	$f_assoc_req_id		= $tbl_assoc .".". REQ_FUNCT_ASSOC_REQ_ID;
	$f_assoc_funct_id	= $tbl_assoc .".". REQ_FUNCT_ASSOC_FUNCT_ID;

	$release_tbl 	= RELEASE_TBL;
	$f_release_id	= RELEASE_ID;
	$f_release_name	= RELEASE_NAME;

	$q = "	SELECT DISTINCT
				$f_req_id,
				$f_req_area_covered,
				$f_req_filename,
				$f_req_type,
				$f_req_locked_by,
				$f_req_locked_date,
				$f_req_ver_uid,
				$f_req_ver_version,
				$f_req_ver_detail,
				$f_req_ver_status,
				$f_req_ver_filename,
				$f_req_ver_last_updated,
				$f_req_ver_last_updated_by
			FROM $tbl_req
			INNER JOIN $tbl_req_ver
				ON $f_req_ver_req_id = $f_req_id
			WHERE $f_req_proj_id = $project_id
			ORDER BY $f_req_ver_last_updated DESC
			LIMIT 5";

	global $db;

	$rows = db_fetch_array($db, db_query($db, $q));

	return $rows;
}

# $parent is the parent of the children we want to see
# Returns the children as a tree in array form
#       array( index => array( child_id, child_name, child_children) )
function requirement_get_children($parent) {

	global $db;
	$tbl_req 					= REQ_TBL;
	$f_req_id 					= REQ_ID;
	$f_req_filename 			= REQ_FILENAME;
	$f_req_parent	 			= REQ_PARENT;

	$children = array();

	# retrieve all children of $parent
	$q = "	SELECT $f_req_id, $f_req_filename
			FROM $tbl_req
			WHERE $f_req_parent = $parent";

	$result = db_query($db, $q);

	# display each child
	while( $row = db_fetch_row($db, $result) ) {

		# call this function again to display this
		# child's children
		# we don't need the child's children in this function

		# for display on the req detail page, we only need immediate children
		# not sub-children.  We'll need sub-children for the tree view
		$children[] = array(	"uid"	=> $row[REQ_ID],
								"name"	=> $row[REQ_FILENAME],
								"children"	=> requirement_get_children($row[REQ_ID]) );
	}

	return $children;
}

# $node is the name of the node we want the path of
function requirement_get_path($node) {

	global $db;
	$tbl_req 				= REQ_TBL;
	$f_req_id 				= REQ_ID;
	$f_req_filename 		= REQ_FILENAME;
	$f_req_parent	 		= REQ_PARENT;
	$f_root_node			= REQ_ROOT;

	# look up the parent of this node
	$q = "	SELECT $f_req_parent, $f_root_node, $f_req_filename
   			FROM $tbl_req
   			WHERE $f_req_id = $node";

   $result = db_query($db, $q);
   $row = db_fetch_row($db, $result);

   # save the path in this array
   $path = array();

   # only continue if this $node isn't the root node
   # (that's the node with no parent)

	if ($row[REQ_ROOT]!='Y') {
		# the last part of the path to $node, is the name
		# of the parent of $node
		$path[] = $row[REQ_PARENT];

		# we should add the path to the parent of this node
		# to the path
		if($row[REQ_PARENT]) {

			$path = array_merge(requirement_get_path($row[REQ_PARENT]), $path);
		} else {

			return;
		}
	}

   # return the path
   return $path;
}

# $parent is the parent of the children we want to see
# Returns the children as a tree in array form
#       array( index => array( child_id, child_name, child_children) )
function requirement_get_related( $req_id ) {

	global $db;
	$tbl_req 				= REQ_TBL;
	$f_req_id 				= REQ_ID;
	$f_req_filename 		= REQ_FILENAME;
	$f_req_parent	 		= REQ_PARENT;
	$f_root_node			= REQ_ROOT;


	$children = array();

	# retrieve all children of $parent
	$q = "	SELECT $f_req_id, $f_req_filename
			FROM $tbl_req
			WHERE $f_req_parent = $req_id";

	$result = db_query($db, $q);

	# display each child
	while( $row = db_fetch_row( $db, $result) ) {

		# we don't need the child's children in this function
		# for display on the req detail page, we only need immediate children
		# not sub-children.
		$children[] = array(	"req_id"	=> $row[REQ_ID],
								"req_name"	=> $row[REQ_FILENAME] );
	}

	return $children;
}

function requirement_test_get_pc_covered($req_id, $test_id) {

	global $db;

    $tbl_test_req_assoc			= TEST_REQ_ASSOC_TBL;
    $f_test_req_assoc_req_id	= $tbl_test_req_assoc .".". TEST_REQ_ASSOC_REQ_ID;
    $f_test_req_assoc_test_id	= $tbl_test_req_assoc .".". TEST_REQ_ASSOC_TEMPEST_TEST_ID;
	$f_test_req_assoc_covered	= $tbl_test_req_assoc .".". TEST_REQ_ASSOC_PERCENT_COVERED;

	$q = "	SELECT
				$f_test_req_assoc_covered
			FROM $tbl_test_req_assoc
			WHERE
				$f_test_req_assoc_req_id = $req_id
				AND $f_test_req_assoc_test_id = $test_id";

	$pc_covered = db_get_one($db, $q);

	return $pc_covered;
}

function requirement_delete_req_assoc($parent_id, $child_id) {

	global $db;

	$tbl_req 					= REQ_TBL;
	$f_req_proj_id				= $tbl_req .".". REQ_PROJECT_ID;
	$f_req_id 					= $tbl_req .".". REQ_ID;
	$f_req_parent	 			= $tbl_req .".". REQ_PARENT;

	$q = "UPDATE $tbl_req SET $f_req_parent=0 WHERE $f_req_id = '$child_id' AND $f_req_parent = '$parent_id'";
	db_query($db, $q);
}

function requirement_delete_test_assoc($assoc_id) {

	global $db;

    $tbl_test_req_assoc			= TEST_REQ_ASSOC_TBL;
    $f_test_req_assoc_id		= $tbl_test_req_assoc .".". TEST_REQ_ASSOC_ID;
    $f_test_req_assoc_req_id	= $tbl_test_req_assoc .".". TEST_REQ_ASSOC_REQ_ID;
    $f_test_req_assoc_test_id	= $tbl_test_req_assoc .".". TEST_REQ_ASSOC_TEMPEST_TEST_ID;
	$f_test_req_assoc_covered	= $tbl_test_req_assoc .".". TEST_REQ_ASSOC_PERCENT_COVERED;

	$q = "DELETE FROM $tbl_test_req_assoc WHERE $f_test_req_assoc_id = '$assoc_id'";
	db_query($db, $q);
}

function requirement_delete_release_assoc($assoc_id) {

	global $db;

	$tbl_req_ver_assoc_rel		= REQ_VERS_ASSOC_REL;
	$f_req_ver_assoc_rel_id		= $tbl_req_ver_assoc_rel.".".REQ_VERS_ASSOC_REL_ID;
	$f_req_ver_assoc_rel_req_id	= $tbl_req_ver_assoc_rel.".".REQ_VERS_ASSOC_REL_REQ_ID;
	$f_req_ver_assoc_rel_rel_id	= $tbl_req_ver_assoc_rel.".".REQ_VERS_ASSOC_REL_REL_ID;

	$q = "DELETE FROM $tbl_req_ver_assoc_rel WHERE $f_req_ver_assoc_rel_id = '$assoc_id'";
	db_query($db, $q);
}

function requirement_get_notify_users( $project_id, $req_id ) {

	$tbl_notify 			= REQ_NOTIFY_TBL;
	$f_notify_id			= REQ_NOTIFY_TBL .".". REQ_NOTIFY_ID;
	$f_notify_req_id		= REQ_NOTIFY_TBL .".". REQ_NOTIFY_REQ_ID;
	$f_notify_user_id		= REQ_NOTIFY_TBL .".". REQ_NOTIFY_USER_ID;

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

	$q = "	SELECT $f_email
			FROM $tbl_notify
			INNER JOIN $tbl_user ON $f_user_id = $f_notify_user_id
			WHERE $f_notify_req_id = $req_id";

	$rs = db_query($db, $q);
	$rows = db_fetch_array($db, $rs);

	return $rows;
}

# Returns the users who want to be notified about discussions
function requirement_get_discussion_users( $project_id ) {

	$tbl_notify 			= REQ_NOTIFY_TBL;
	$f_notify_id			= REQ_NOTIFY_TBL .".". REQ_NOTIFY_ID;
	$f_notify_req_id		= REQ_NOTIFY_TBL .".". REQ_NOTIFY_REQ_ID;
	$f_notify_user_id		= REQ_NOTIFY_TBL .".". REQ_NOTIFY_USER_ID;

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
	$f_qa_owner				= $tbl_proj_user_assoc .".". PROJ_USER_QA_OWNER;
	$f_ba_owner				= $tbl_proj_user_assoc .".". PROJ_USER_BA_OWNER;

	global $db;

	$q = "	SELECT $f_email
			FROM $tbl_user
			INNER JOIN $tbl_proj_user_assoc ON $f_user_id = $f_proj_user_user_id
			WHERE $f_proj_user_proj_id = $project_id
				AND $f_email_discussion = 'Y'";

	$rs = db_query($db, $q);
	$rows = db_fetch_array($db, $rs);

	return $rows;
}

# ----------------------------------------------------------------------
# Check if Test name already exists
# INPUT:
#   Test Name to Check
# OUTPUT:
#   True if Test with Test Name already exists, otherwise false.
# ----------------------------------------------------------------------
function requirement_name_exists( $project_id, $req_name ) {

	global $db;
	$tbl_req 					= REQ_TBL;
	$f_req_proj_id				= $tbl_req .".". REQ_PROJECT_ID;
	$f_req_id 					= $tbl_req .".". REQ_ID;
	$f_req_filename 			= $tbl_req .".". REQ_FILENAME;

	$query = "	SELECT COUNT($f_req_filename)
				FROM $tbl_req
				WHERE $f_req_filename='$req_name'
					AND $f_req_proj_id=$project_id";

	$result = db_get_one( $db, $query );

	if ( $result == 0 ) {
		return false;
	} else {
		return true;
	}
}

function requirement_email($project_id, $req_id, $recipients, $action, $discussion_id=null) {

	$display_generic_info	= true;
	$display_generic_url	= true;
	# Link to the req detail page
	$generic_url = RTH_URL."login.php?project_id=$project_id&page=requirement_detail_page.php&req_id=$req_id";

	$username				= session_get_username();
	$project_name			= session_get_project_name();

	$req_id					= util_pad_id($req_id);

	$user_details			= user_get_name_by_username($username);
	$first_name				= $user_details[USER_FNAME];
	$last_name				= $user_details[USER_LNAME];

	$rows_requirement		= requirement_get_detail( $project_id, $req_id );
	$row_requirement 		= $rows_requirement[0];

	$req_version_id			= $row_requirement[REQ_VERS_UNIQUE_ID];
	$req_rec_or_file		= $row_requirement[REQ_REC_FILE];
	$req_name				= $row_requirement[REQ_FILENAME];
	$req_detail				= $row_requirement[REQ_VERS_DETAIL];
	$req_reason_for_change	= $row_requirement[REQ_VERS_REASON_CHANGE];
	$req_version_status		= $row_requirement[REQ_VERS_STATUS];
	$req_area_covered		= $row_requirement[REQ_AREA_COVERAGE];
	$req_doc_type			= $row_requirement[REQ_DOC_TYPE_NAME];
	$req_version			= $row_requirement[REQ_VERS_VERSION];

	# REQ FUNCTIONALITY
	$rows_functions 		= requirement_get_functionality($project_id, $req_id);
	$req_functionality		= "";
	foreach($rows_functions as $function) {
		$req_functionality .= $function.", ";
	}
	$req_functionality		= trim($req_functionality, ", ");

	# CREATE EMAIL SUBJECT AND MESSAGE
	switch($action) {
	case"updated":

		$subject = "RTH: Requirement Updated in $project_name";
		$message = "Requirement $req_name has been updated by $first_name $last_name\n". NEWLINE;
		break;

	case"new_version":

		$subject = "RTH: Requirement Updated in $project_name";
		$message = "A new version of Requirement $req_name has been created by $first_name $last_name\n". NEWLINE;
		break;

	case"delete":
		$display_generic_info	= false;
		$display_generic_url	= false;

		$url = RTH_URL."login.php?project_id=$project_id&page=requirement_page.php";

		$subject = "RTH: Requirement Deleted in $project_name";
		$message = "Requirement $req_name has been deleted by $first_name $last_name\n". NEWLINE;
		$message .= "Click the following link to view Requirements in $project_name:". NEWLINE;
		$message .= "$url\n". NEWLINE;
		break;

	case"lock":

		$subject = "RTH: Requirement Locked in $project_name";
		$message = "Requirement $req_name has been locked by $first_name $last_name\n". NEWLINE;
		break;

	case"unlock":

		$subject = "RTH: Requirement Unlocked in $project_name";
		$message = "Requirement $req_name has been unlocked by $first_name $last_name\n". NEWLINE;
		break;

	case"edit_children":

		# Get requirement children
		//$rows_children = requirement_get_children($req_id);

		$generic_url .= "&tab=1";

		$subject = "RTH: Requirement Updated in $project_name";
		$message = "The children of Requirement $req_name have been edited by $first_name $last_name\n". NEWLINE;
		break;

	case"edit_test_assoc":

		# Get related tests
		//$assoc_tests = requirement_get_test_relationships($req_id);

		$generic_url .= "&tab=2";

		$subject = "RTH: Requirement Updated in $project_name";
		$message = "The Tests related to Requirement $req_name have been edited by $first_name $last_name\n". NEWLINE;
		break;

	case"edit_release_assoc":

		# Get related releases
		//requirement_get_assoc_releases($s_req_version_id)

		$generic_url .= "&tab=4";

		$subject = "RTH: Requirement Updated in $project_name";
		$message = "The Releases related to Requirement $req_name have been edited by $first_name $last_name\n". NEWLINE;
		break;

	case"new_discussion":

		# GET LAST DISCUSSION
		$rows_discussion = discussion_get($req_id);
		foreach($rows_discussion as $row_discussion) {
			$discussion_subject	= $row_discussion[DISC_SUBJECT];
		}

		$generic_url .= "&tab=3";

		$subject = "RTH: New Requirement Discussion in $project_name";
		$message = "A new discussion has been added to Requirement $req_name by $first_name $last_name\n". NEWLINE;
		$message .= "".lang_get("subject").": $discussion_subject\n". NEWLINE;
		break;

	case"new_post":

		# GET DISCUSSION
		$row_discussion 	= discussion_get_detail($discussion_id);
		$discussion_subject	= $row_discussion[DISC_SUBJECT];

		$url = RTH_URL."login.php?project_id=$project_id&page=requirement_discussion_page.php&discussion_id=$discussion_id";

		$subject = "RTH: Discussion $discussion_subject in $project_name";
		$message = "A new post has been added to Discussion $discussion_subject by $first_name $last_name\n". NEWLINE;
		$message .= "Click the following link to view the discussion:\n". NEWLINE;
		$message .= "$url\n". NEWLINE;
		break;

	case"close_discussion":

		$display_generic_info	= false;
		$display_generic_url	= false;

		# GET DISCUSSION
		$row_discussion 	= discussion_get_detail($discussion_id);
		$discussion_subject	= $row_discussion[DISC_SUBJECT];

		$url = RTH_URL."login.php?project_id=$project_id&page=requirement_discussion_page.php&discussion_id=$discussion_id";

		$subject = "RTH: Discussion $discussion_subject in $project_name";
		$message = "Discussion $discussion_subject has been closed by $first_name $last_name\n". NEWLINE;
		$message .= "Click the following link to view the discussion:". NEWLINE;
		$message .= "$url\n". NEWLINE;
		break;
	}

	# Generic link to requirement detail page if the $url variable has been set
	if( $display_generic_url ) {
		$message .= "Click the following link to view the Requirement:". NEWLINE;
		$message .= "$generic_url\n". NEWLINE;
	}

	if( $display_generic_info ) {
		$message .= "".lang_get("project_name").": $project_name". NEWLINE;
		$message .= "".lang_get("req_id").": $req_id". NEWLINE;
		$message .= "".lang_get("req_version").": $req_version". NEWLINE;
		$message .= "".lang_get("req_name").": $req_name". NEWLINE;
		$message .= "".lang_get("req_detail").": $req_detail". NEWLINE;
		$message .= "".lang_get("req_status").": $req_version_status". NEWLINE;
		$message .= "".lang_get("req_area_covered").": $req_area_covered". NEWLINE;
		$message .= "".lang_get("req_functionality").": $req_functionality". NEWLINE;
		$message .= "".lang_get("req_doc_type").": $req_doc_type\n". NEWLINE;
	}

	# Convert any html entities stored in the DB back to characters.
	$message = util_unhtmlentities($message);

	email_send($recipients, $subject, $message);
}

function requirement_unlock($req_id) {

	global $db;

	$tbl_req 					= REQ_TBL;
	$f_req_proj_id				= $tbl_req .".". REQ_PROJECT_ID;
	$f_req_id 					= $tbl_req .".". REQ_ID;
	$f_req_filename 			= $tbl_req .".". REQ_FILENAME;
	$f_req_area_covered			= $tbl_req .".". REQ_AREA_COVERED;
	$f_req_type		 			= $tbl_req .".". REQ_TYPE;
	$f_req_parent	 			= $tbl_req .".". REQ_PARENT;
	$f_req_label	 			= $tbl_req .".". REQ_LABEL;
	$f_req_unique_id 			= $tbl_req .".". REQ_UNIQUE_ID;
	$f_req_locked_by			= $tbl_req .".". REQ_LOCKED_BY;
	$f_req_locked_date			= $tbl_req .".". REQ_LOCKED_DATE;

	$q = "	UPDATE $tbl_req
			SET
				$f_req_locked_by = '',
				$f_req_locked_date = ''
			WHERE $f_req_id = '$req_id'";

	db_query($db, $q);
}

function requirement_lock($req_id, $user) {

	global $db;

	$tbl_req 					= REQ_TBL;
	$f_req_proj_id				= $tbl_req .".". REQ_PROJECT_ID;
	$f_req_id 					= $tbl_req .".". REQ_ID;
	$f_req_filename 			= $tbl_req .".". REQ_FILENAME;
	$f_req_area_covered			= $tbl_req .".". REQ_AREA_COVERED;
	$f_req_type		 			= $tbl_req .".". REQ_TYPE;
	$f_req_parent	 			= $tbl_req .".". REQ_PARENT;
	$f_req_label	 			= $tbl_req .".". REQ_LABEL;
	$f_req_unique_id 			= $tbl_req .".". REQ_UNIQUE_ID;
	$f_req_locked_by			= $tbl_req .".". REQ_LOCKED_BY;
	$f_req_locked_date			= $tbl_req .".". REQ_LOCKED_DATE;

	$q = "	UPDATE $tbl_req
			SET
				$f_req_locked_by = '$user',
				$f_req_locked_date = '".date("Y-m-d H:i:s")."'
			WHERE $f_req_id = '$req_id'";

	db_query($db, $q);
}

# ---------------------------------------------------------------------
# $Log: requirement_api.php,v $
# Revision 1.21  2009/01/12 09:15:25  cryobean
# incorporate requirements traceability matrix feature developed by Bruce Butler
#
# Revision 1.20  2007/03/14 17:45:52  gth2
# removing code that passes varables by reference - gth
#
# Revision 1.19  2007/02/03 10:25:53  gth2
# no message
#
# Revision 1.18  2006/09/27 23:47:00  gth2
# Adding functionality to link a change request (defect_id) to a requirement - gth
#
# Revision 1.17  2006/08/05 22:31:46  gth2
# adding NEWLINE constant to support mulitple OS - gth
#
# Revision 1.16  2006/06/30 00:55:42  gth2
# removing &$db from api files - gth
#
# Revision 1.15  2006/05/03 22:06:11  gth2
# no message
#
# Revision 1.13  2006/02/24 11:33:31  gth2
# minor bug fixes and enhancements for 1.5.1 release - gth
#
# Revision 1.12  2006/02/15 03:11:20  gth2
# correcting case - gth
#
# Revision 1.11  2006/02/09 12:34:26  gth2
# changing db field names for consistency - gth
#
# Revision 1.10  2006/02/06 13:07:53  gth2
# fixing bug when deleting a requirement - gth
#
# Revision 1.9  2006/01/16 13:27:48  gth2
# adding excel integration - gth
#
# Revision 1.8  2006/01/09 02:02:24  gth2
# fixing some defects found while writing help file
#
# Revision 1.7  2006/01/06 00:35:33  gth2
# fixed bug with associations - gth
#
# Revision 1.6  2006/01/04 22:58:06  gth2
# fixing bug with filter on req-to-req assoc page - gth
#
# Revision 1.5  2005/12/28 23:23:32  gth2
# Fixing minor bugs caused by MySQL upgrade - gth
#
# Revision 1.4  2005/12/13 13:59:53  gth2
# Completed the addition of requirement priority - gth
#
# Revision 1.3  2005/12/08 22:13:57  gth2
# adding Assign To Release to requirment edit page - gth
#
# Revision 1.2  2005/12/06 13:56:50  gth2
# Adding requirement priority and last updated - gth
#
# Revision 1.1.1.1  2005/11/30 23:01:12  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------
?>