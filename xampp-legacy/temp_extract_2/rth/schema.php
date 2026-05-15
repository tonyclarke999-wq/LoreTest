<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Schema Page
# rth used to use a seperate database for each project
# This page was used to find discrepancies between the various db schemas
# If you're going to create many projects, you might want to investigate
# the multiple database design.
# RTH was run with over 60 projects and 800 users with the mutiple db design
#
# $RCSfile: schema.php,v $  $Revision: 1.2 $
# ---------------------------------------------------------------------

ob_start();

$dbs = array();

$connect_link = mysql_connect("mysql_server:port", "uname", "pword") or die("Connection failed: Please try later.");


$db_list = mysql_list_dbs();

while ($row = mysql_fetch_object($db_list)) {
	$dbs[$row->Database] = array();
}

foreach($dbs as $db_name => $value) {

	$result = mysql_list_tables($db_name);
	$num_rows = mysql_num_rows($result);
	for ($i = 0; $i < $num_rows; $i++) {

		$result_2 = mysql_query("SHOW COLUMNS FROM ".mysql_tablename($result, $i));
		if (!$result_2) {
		   echo 'Could not run query: ' . mysql_error();
		   exit;
		}
		if (mysql_num_rows($result_2) > 0) {
		   while ($row = mysql_fetch_assoc($result_2)) {
			   $dbs[$db_name][mysql_tablename($result, $i)][] = $row;
		   }
		}


	}
}

echo "db_name,db_table_name,db_table_field[Field],db_table_field[Type],db_table_field[Null],db_table_field[Key],db_table_field[Default],db_table_field[Extra]". NEWLINE;

foreach($dbs as $db_name => $db_tables) {

	foreach($db_tables as $db_table_name => $db_table_fields) {

		foreach($db_table_fields as $db_table_field) {

				echo "$db_name,$db_table_name,$db_table_field[Field],$db_table_field[Type],$db_table_field[Null],$db_table_field[Key],$db_table_field[Default],$db_table_field[Extra]". NEWLINE;

		}
	}
}

# OUTPUT
header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=\"database.csv\"");

echo $csv;

ob_end_flush();

# ---------------------------------------------------------------------
# $Log: schema.php,v $
# Revision 1.2  2006/08/05 22:08:51  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>