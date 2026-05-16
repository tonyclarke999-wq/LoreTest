<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Navigate to id action
#
# $RCSfile: navigate_to_id_action.php,v $ 
# $Revision: 1.2 $
# ------------------------------------

include"./api/include_api.php";
auth_authenticate_user();
$error_page		= 'home_page.php';

if(!empty($_POST['id_txt_field'])){	
	$test_id		= $_POST['id_txt_field'];
	$page			= 'test_detail_page.php';
	if(is_numeric($test_id)){
		if(test_get_projectid($test_id) > 0){
			$project_id		= test_get_projectid($test_id);
			$s_user_properties = session_get_user_properties() ;
			$user_id = $s_user_properties['user_id'];
			if(user_has_rights($project_id, $user_id, 10)){
				$redirect_page = $page ."?test_id=$test_id&project_id=$project_id";
				html_redirect($redirect_page);
			} else {
				error_report_show($error_page, NO_SUFFICIENT_RIGHTS);
			}
			
		} else {
			error_report_show($error_page, TEST_ID_NOT_FOUND);
		}
	} else {
		error_report_show($error_page, NUMERIC_ERROR);
	}
}
else
	error_report_show($error_page, TEST_ID_FIELD_EMPTY);

# ------------------------------------
# $Log: navigate_to_id_action.php,v $
# Revision 1.2  2009/01/12 12:02:45  cryobean
# now permissions are checked before switching to test id/project and an error message is shown on the home_page.php and the user doesn't get logged out if there are unsufficient permissions.
#
# Revision 1.1  2008/08/08 09:30:29  peter_thal
# added direct navigate to testid function above project switch select box
#
# ------------------------------------

?>
