<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Requirement Update Page
# I think we can remove this page
#
# $RCSfile: requirement_update.php,v $  $Revision: 1.2 $
# ---------------------------------------------------------------------

include"./api/include_api.php";


$page                   = basename(__FILE__);
#### Change to page that form is submitted to or hyperlinks link to ####
$action_page            = 'requirement_action.php';
$num                    = 0;
$project_properties     = session_get_project_properties();
$project_name           = $project_properties['project_name'];

auth_authenticate_user();

$tbl_req 					= REQ_TBL;
$f_req_id 					= REQ_ID;
$f_req_filename 			= REQ_FILENAME;
$f_req_area_covered 		= REQ_AREA_COVERED;
$f_req_type		 			= REQ_TYPE;

$tbl_req_ver				= REQ_VERS_TBL;
$f_req_ver_req_id			= REQ_VERS_REQ_ID;
$f_req_ver_version			= REQ_VERS_VERSION;
$f_req_ver_timestamp		= REQ_VERS_TIMESTAMP;
$f_req_ver_uploaded_by		= REQ_VERS_UPLOADED_BY;
$f_req_ver_status			= REQ_VERS_STATUS;
$f_req_ver_shed_release		= REQ_VERS_SCHEDULED_RELEASE_IMP;
$f_req_ver_detail			= REQ_VERS_DETAIL;

$query_updateReqs = "UPDATE $tbl_req SET $f_req_filename = '$reqfilename', $f_req_area_covered = '$areacoverage', $f_req_type = '$reqdoctype' WHERE $f_req_id = '$reqID'";
$db->Execute($query_updateReqs);


if($revision_type == "Major"){
	$version = floor($version) + 1;
}
else{	
	$x = explode(".", $version);
	$m = $x[1] + 1;
	
	$version = $x[0] . "." . $m;

}

$current_date = date("Y-m-d H:i:s");
$arr_user = user_get_current_user_name();
$username = $arr_user[0];

$query_version = "INSERT INTO $tbl_req_ver ($f_req_ver_req_id, $f_req_ver_version, $f_req_ver_uploaded_by, $f_req_ver_timestamp, $f_req_ver_detail, $f_req_ver_status, $f_req_ver_shed_release) VALUES ('$reqID', '$version', '$username', '$current_date', '$HTTP_POST_VARS[EditorDefault]', '$reqstatus', '$release')";	
$db->Execute($query_version);

header("Location: requirement_detail.php?reqID=$reqID");
#PRINT "<meta http-equiv=\"Refresh\" content=\"1;URL=requirement_detail.php?reqID=$reqID\">";
exit();

# ---------------------------------------------------------------------
# $Log: requirement_update.php,v $
# Revision 1.2  2006/02/24 11:33:32  gth2
# minor bug fixes and enhancements for 1.5.1 release - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------
?>