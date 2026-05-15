<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Test Detail Delete Action Page
#
# $RCSfile: test_detail_delete_action.php,v $  $Revision: 1.1.1.1 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$s_test_details = session_get_properties("test");
$test_id = $s_test_details['test_id'];

$test_name = test_get_name( $test_id);

$redirect_page = "test_detail_page.php";

$delete = util_clean_post_vars('delete');

if ($delete == 'Yes') {

    test_delete_test($test_id);

    $page_name = "DELETE TEST";
    $deletion = 'Y';
    $creation = 'N';
    $upload = 'N';
    $action = "DELETED TEST $test_name ";

    log_activity_log( $page_name, $deletion, $creation, $upload, $action );

    $redirect_page = "test_page.php";
}

html_redirect($redirect_page);

# ---------------------------------------------------------------------
# $Log: test_detail_delete_action.php,v $
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>
