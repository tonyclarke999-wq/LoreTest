<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#---------------------------------------------------------------------- 
# ------------------------------------
# Authenticate User
#
# $RCSfile: authenticate_user_inc.php,v $ $Revision: 1.1.1.1 $
# ------------------------------------

# Access global object g_timer for debug
global $g_timer;

# Check if user is logged in
auth_authenticate_user();

# connect to users project db
$db = ADONewConnection(DB_TYPE);
$db->debug = false;

$s_project_properties = session_get_project_properties();
//$s_db_name = $s_project_properties['dbname'];
$s_db_name = 'TMS';

if ( empty($s_db_name) ) {

	trigger_error(PROJECT_DB_NOT_SET, E_USER_ERROR);
}

$g_timer->mark_time( "Connect to " . $s_db_name . " database" );

$db->Connect(DB_HOST, DB_LOGIN, DB_PWORD, $s_db_name);

$g_timer->mark_time( "Finished connect to " . $s_db_name . " database" );

# --------------------------------------------------------
# $Log: authenticate_user_inc.php,v $
# Revision 1.1.1.1  2005/11/30 23:01:11  gth2
# importing initial version - gth
#
# --------------------------------------------------------
?>