<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# RTH Database Information
#
# $RCSfile: db_api.php,v $ $Revision: 1.3 $
# ------------------------------------
include'./adodb-4.65/adodb-errorhandler.inc.php';
include'./adodb-4.65/adodb.inc.php';


// was connecting to common db tempest_project


# Access g_timer object for debug
global $g_timer;

$db = ADONewConnection(DB_TYPE);
$db->SetFetchMode(ADODB_FETCH_ASSOC);
$db->debug = false;

# Time how long connection takes
$g_timer->mark_time( "Connect to " . DB_NAME . " database" );

$db->Connect(DB_HOST, DB_LOGIN, DB_PWORD, DB_NAME);

$g_timer->mark_time( "Finished connect to " . DB_NAME . " database" );

# ----------------------------------------------------------------------
# Database query abstraction function
# ----------------------------------------------------------------------
function db_query( $db, $query_string ) {

    $rs = $db->Execute( $query_string );
    return $rs;

}

# ----------------------------------------------------------------------
# Returns the number of rows from a recordset
# ----------------------------------------------------------------------
function db_num_rows( $db, $record_set ) {

    $num = $record_set->NumRows();

    return $num;
}

# ----------------------------------------------------------------------
# Returns a row from a recordset
# ----------------------------------------------------------------------
function db_fetch_row( $db, $record_set ) {

    $row = $record_set->FetchRow();

    return $row;
}

# ----------------------------------------------------------------------
# Returns specified number of records from a recordset, as an array
# ----------------------------------------------------------------------
function db_fetch_array( $db, $record_set, $record_count=null ) {

	$rows = array();

	if( $record_count!=null ) {

		for( $i=0; $i<$record_count; $i++ ) {
			array_push( $rows, db_fetch_row($db, $record_set) );
		}
	} else {

		while( $row = db_fetch_row($db, $record_set) ) {
			array_push($rows,  $row);
		}
	}

	return $rows;
}

# ----------------------------------------------------------------------
# Runs the query provided and returns the data from the first column of
# the first row then frees the recordset.
# ----------------------------------------------------------------------
function db_get_one( $db, $query_string ) {

    return $rs = $db->GetOne( $query_string );
}

# ----------------------------------------------------------------------
# Runs the query provided and returns the data from the first column of
# the first row then frees the recordset.
# ----------------------------------------------------------------------
function db_rs_eof( &$rs ) {

    return $rs->EOF;
}

function db_get_last_autoincrement_id( $db ) {

	$q = "SELECT LAST_INSERT_ID()";

    return db_get_one( $db, $q );
}

function db_field_exists( $db, $field_name, $table_name ) {

	return in_array ( $field_name , $db->MetaColumnNames( $table_name ) ) ;
}

# --------------------------------------------------------
# $Log: db_api.php,v $
# Revision 1.3  2007/02/03 10:25:30  gth2
# no message
#
# Revision 1.2  2006/06/30 00:55:42  gth2
# removing &$db from api files - gth
#
# Revision 1.1.1.1  2005/11/30 23:01:11  gth2
# importing initial version - gth
#
# --------------------------------------------------------
?>
