<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Date and Time API
#
# $RCSfile: date_api.php,v $ $Revision: 1.2 $
# ------------------------------------

function date_rth_format( $time ) {
	STATIC $unix_time,
		   $date;

	$unix_time = mktime(	substr( $time, 8, 2 ),
							substr( $time, 10, 2 ),
							substr( $time, 12, 2 ),
							substr( $time, 4, 2 ),
							substr( $time, 6, 2 ),
							substr( $time, 0, 4 ) );

	return $date = ( date( "Y-m-d H:i:s", $unix_time ) );
}

# ----------------------------------------------------------------------
# Returns current date and time OR formats the timestamp
# ----------------------------------------------------------------------
function date_get_short_dt($timestamp=null) {

	if( $timestamp ) {
		$dt = date("Y-m-d H:i:s", $timestamp);
	} else {
		$dt = date("Y-m-d H:i:s");
	}
	return $dt;
}

# ----------------------------------------------------------------------
# Returns current date and time with the machine timezone in brackets
# ----------------------------------------------------------------------
function date_get_long_dt() {

	$dt = date("Y-m-d H:i:s (T)");
	return $dt;
}


# ----------------------------------------------------------------------
# Trim the time from a timestamp (2002-11-06 15:04:45) and return only the date
# OUTPUT:
#	Date formated without the time (2002-11-06)
# ----------------------------------------------------------------------
function date_trim_time( $timestamp ) {

	$date = substr($timestamp, 0, 10);

	return $date;

}

# --------------------------------------------------------
# $Log: date_api.php,v $
# Revision 1.2  2006/12/05 05:01:06  gth2
# rename date_format function - gth
#
# Revision 1.1.1.1  2005/11/30 23:01:11  gth2
# importing initial version - gth
#
# --------------------------------------------------------
?>