<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# $RCSfile: log_api.php,v $ $Revision: 1.1.1.1 $
# ------------------------------------

# ----------------------------------------------------------------------
# Log Activity to Log Table
# ----------------------------------------------------------------------
function log_activity_log($page_name, $deletion, $creation, $upload, $action) {

    global $db;

    $s_project_name = session_get_project_name();
    $s_user_properties = session_get_user_properties();
    $s_username = $s_user_properties['username'];
    $s_session = session_get_ID();

    $current_date = date("Y-m-d H:i:s");
    $page_name = $page_name." - ".$s_project_name;
    $logs_tbl = LOGS_TBL;
    $query = "	INSERT INTO $logs_tbl ( User,
    									Page,
    									TimeStamp,
    									SessionID,
    									Deletion,
    									Creation,
    									Upload,
    									Action )

    			VALUES (	'$s_username',
    						'$page_name',
    						'$current_date',
    						'$s_session',
    						'$deletion',
    						'$creation',
    						'$upload',
    						'$action' )";

    db_query( $db, $query );
}

# ------------------------------------
# $Log: log_api.php,v $
# Revision 1.1.1.1  2005/11/30 23:01:12  gth2
# importing initial version - gth
#
# ------------------------------------
?>