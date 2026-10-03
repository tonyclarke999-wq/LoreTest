<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Discussion API
#
# $RCSfile: discussion_api.php,v $ $Revision: 1.1.1.1 $
# ------------------------------------

function discussion_add($req_id, $subject, $text, $status, $author, $assign_to) {

	$tbl_discussion			= DISC_TBL;
	$f_discussion_id		= DISC_TBL .".". DISC_ID;
	$f_discussion_req_id	= DISC_TBL .".". DISC_REQ_ID;
	$f_discussion_text		= DISC_TBL .".". DISC_DISCUSSION;
	$f_discussion_status	= DISC_TBL .".". DISC_STATUS;
	$f_discussion_subject	= DISC_TBL .".". DISC_SUBJECT;
	$f_discussion_author	= DISC_TBL .".". DISC_AUTHOR;
	$f_discussion_assign_to	= DISC_TBL .".". DISC_ASSIGN_TO;
	$f_discussion_date		= DISC_TBL .".". DISC_DATE;

	$q = "	INSERT INTO $tbl_discussion
				(	$f_discussion_req_id,
					$f_discussion_text,
					$f_discussion_status,
					$f_discussion_subject,
					$f_discussion_author,
					$f_discussion_assign_to,
					$f_discussion_date )
			VALUES
				(	'$req_id',
					'$text',
					'$status',
					'$subject',
					'$author',
					'$assign_to',
					'".date("Y-m-d H:i:s")."' )";
//print$q;exit;
	global $db;

	db_query($db, $q);
}


function discussion_get($req_id, $order_by=DISC_DATE, $order_dir="ASC", $page_number=1) {

	$tbl_discussion			= DISC_TBL;
	$f_discussion_id		= DISC_TBL .".". DISC_ID;
	$f_discussion_req_id	= DISC_TBL .".". DISC_REQ_ID;
	$f_discussion_text		= DISC_TBL .".". DISC_DISCUSSION;
	$f_discussion_status	= DISC_TBL .".". DISC_STATUS;
	$f_discussion_subject	= DISC_TBL .".". DISC_SUBJECT;
	$f_discussion_author	= DISC_TBL .".". DISC_AUTHOR;
	$f_discussion_assign_to	= DISC_TBL .".". DISC_ASSIGN_TO;
	$f_discussion_date		= DISC_TBL .".". DISC_DATE;

	$q = "	SELECT
				$f_discussion_id,
				$f_discussion_req_id,
				$f_discussion_text,
				$f_discussion_status,
				$f_discussion_subject,
				$f_discussion_author,
				$f_discussion_assign_to,
				$f_discussion_date
			FROM
				$tbl_discussion
			WHERE
				$f_discussion_req_id = $req_id
			ORDER BY $order_by $order_dir";

	global $db;

	$rows = db_fetch_array( $db, db_query($db, $q) );

	return $rows;
}

function discussion_get_detail($discussion_id) {

	$tbl_discussion			= DISC_TBL;
	$f_discussion_id		= DISC_TBL .".". DISC_ID;
	$f_discussion_req_id	= DISC_TBL .".". DISC_REQ_ID;
	$f_discussion_text		= DISC_TBL .".". DISC_DISCUSSION;
	$f_discussion_status	= DISC_TBL .".". DISC_STATUS;
	$f_discussion_subject	= DISC_TBL .".". DISC_SUBJECT;
	$f_discussion_author	= DISC_TBL .".". DISC_AUTHOR;
	$f_discussion_assign_to	= DISC_TBL .".". DISC_ASSIGN_TO;
	$f_discussion_date		= DISC_TBL .".". DISC_DATE;

	$q = "	SELECT
				$f_discussion_id,
				$f_discussion_req_id,
				$f_discussion_text,
				$f_discussion_status,
				$f_discussion_subject,
				$f_discussion_author,
				$f_discussion_assign_to,
				$f_discussion_date
			FROM
				$tbl_discussion
			WHERE
				$f_discussion_id = $discussion_id";

	global $db;

	$rows = db_fetch_row( $db, db_query($db, $q) );

	return $rows;
}

function discussion_set_status($discussion_id, $status) {

	$tbl_discussion			= DISC_TBL;
	$f_discussion_id		= DISC_TBL .".". DISC_ID;
	$f_discussion_req_id	= DISC_TBL .".". DISC_REQ_ID;
	$f_discussion_text		= DISC_TBL .".". DISC_DISCUSSION;
	$f_discussion_status	= DISC_TBL .".". DISC_STATUS;
	$f_discussion_subject	= DISC_TBL .".". DISC_SUBJECT;
	$f_discussion_author	= DISC_TBL .".". DISC_AUTHOR;
	$f_discussion_assign_to	= DISC_TBL .".". DISC_ASSIGN_TO;
	$f_discussion_date		= DISC_TBL .".". DISC_DATE;

	$q = "	UPDATE $tbl_discussion
			SET
				$f_discussion_status = '$status'
			WHERE
				$f_discussion_id = $discussion_id";

	global $db;

	db_query($db, $q);
}


function discussion_get_posts($discussion_id) {

	$tbl_discussion			= DISC_TBL;
	$f_discussion_id		= DISC_TBL .".". DISC_ID;
	$f_discussion_req_id	= DISC_TBL .".". DISC_REQ_ID;
	$f_discussion_text		= DISC_TBL .".". DISC_DISCUSSION;
	$f_discussion_status	= DISC_TBL .".". DISC_STATUS;
	$f_discussion_subject	= DISC_TBL .".". DISC_SUBJECT;
	$f_discussion_author	= DISC_TBL .".". DISC_AUTHOR;
	$f_discussion_assign_to	= DISC_TBL .".". DISC_ASSIGN_TO;
	$f_discussion_date		= DISC_TBL .".". DISC_DATE;

	$tbl_post				= DISC_POST_TBL;
	$f_post_id 				= $tbl_post .".". POST_ID;
	$f_post_discussion_id	= $tbl_post .".". POST_DISCUSSION_ID;
	$f_post_message			= $tbl_post .".". POST_MESSAGE;
	$f_post_author			= $tbl_post .".". POST_AUTHOR;
	$f_post_date			= $tbl_post .".". POST_DATE;

	$q = "	SELECT
				$f_post_id,
				$f_post_discussion_id,
				$f_post_message,
				$f_post_author,
				$f_post_date
			FROM
				$tbl_post
			WHERE
				$f_post_discussion_id = $discussion_id
			ORDER BY $f_post_date ASC";

	global $db;

	$rows = db_fetch_array( $db, db_query($db, $q) );

	return $rows;
}

function discussion_get_num_posts($discussion_id) {

	$tbl_discussion			= DISC_TBL;
	$f_discussion_id		= DISC_TBL .".". DISC_ID;
	$f_discussion_req_id	= DISC_TBL .".". DISC_REQ_ID;
	$f_discussion_text		= DISC_TBL .".". DISC_DISCUSSION;
	$f_discussion_status	= DISC_TBL .".". DISC_STATUS;
	$f_discussion_subject	= DISC_TBL .".". DISC_SUBJECT;
	$f_discussion_author	= DISC_TBL .".". DISC_AUTHOR;
	$f_discussion_assign_to	= DISC_TBL .".". DISC_ASSIGN_TO;
	$f_discussion_date		= DISC_TBL .".". DISC_DATE;

	$tbl_post				= DISC_POST_TBL;
	$f_post_id 				= $tbl_post .".". POST_ID;
	$f_post_discussion_id	= $tbl_post .".". POST_DISCUSSION_ID;
	$f_post_message			= $tbl_post .".". POST_MESSAGE;
	$f_post_author			= $tbl_post .".". POST_AUTHOR;
	$f_post_date			= $tbl_post .".". POST_DATE;


	$q = "	SELECT
				$f_post_id
			FROM
				$tbl_post
			WHERE
				$f_post_discussion_id = $discussion_id";

	global $db;

	$num_rows = db_num_rows( $db, db_query($db, $q) );

	return $num_rows;
}

function discussion_add_post($discussion_id, $message, $author) {

	$tbl_discussion			= DISC_TBL;
	$f_discussion_id		= DISC_TBL .".". DISC_ID;
	$f_discussion_req_id	= DISC_TBL .".". DISC_REQ_ID;
	$f_discussion_text		= DISC_TBL .".". DISC_DISCUSSION;
	$f_discussion_status	= DISC_TBL .".". DISC_STATUS;
	$f_discussion_subject	= DISC_TBL .".". DISC_SUBJECT;
	$f_discussion_author	= DISC_TBL .".". DISC_AUTHOR;
	$f_discussion_assign_to	= DISC_TBL .".". DISC_ASSIGN_TO;
	$f_discussion_date		= DISC_TBL .".". DISC_DATE;

	$tbl_post				= DISC_POST_TBL;
	$f_post_id 				= $tbl_post .".". POST_ID;
	$f_post_discussion_id	= $tbl_post .".". POST_DISCUSSION_ID;
	$f_post_message			= $tbl_post .".". POST_MESSAGE;
	$f_post_author			= $tbl_post .".". POST_AUTHOR;
	$f_post_date			= $tbl_post .".". POST_DATE;

	$q = "	INSERT INTO $tbl_post
				(	$f_post_discussion_id,
					$f_post_message,
					$f_post_author,
					$f_post_date )
			VALUES
				(	'$discussion_id',
					'$message',
					'$author',
					'".date("Y-m-d H:i:s")."' )";

	global $db;

	db_query($db, $q);
}


function discussion_delete($discussion_id) {

	$tbl_discussion			= DISC_TBL;
	$f_discussion_id		= DISC_TBL .".". DISC_ID;
	$f_discussion_req_id	= DISC_TBL .".". DISC_REQ_ID;
	$f_discussion_text		= DISC_TBL .".". DISC_DISCUSSION;
	$f_discussion_status	= DISC_TBL .".". DISC_STATUS;
	$f_discussion_subject	= DISC_TBL .".". DISC_SUBJECT;
	$f_discussion_author	= DISC_TBL .".". DISC_AUTHOR;
	$f_discussion_assign_to	= DISC_TBL .".". DISC_ASSIGN_TO;
	$f_discussion_date		= DISC_TBL .".". DISC_DATE;

	$tbl_post				= DISC_POST_TBL;
	$f_post_id 				= $tbl_post .".". POST_ID;
	$f_post_discussion_id	= $tbl_post .".". POST_DISCUSSION_ID;
	$f_post_message			= $tbl_post .".". POST_MESSAGE;
	$f_post_author			= $tbl_post .".". POST_AUTHOR;
	$f_post_date			= $tbl_post .".". POST_DATE;

	global $db;

	$q = "	DELETE FROM $tbl_post
			WHERE $f_post_discussion_id=$discussion_id";

	db_query($db, $q);

	$q = "	DELETE FROM $tbl_discussion
			WHERE $f_discussion_id=$discussion_id";

	db_query($db, $q);
}

# --------------------------------------------------------
# $Log: discussion_api.php,v $
# Revision 1.1.1.1  2005/11/30 23:01:11  gth2
# importing initial version - gth
#
# --------------------------------------------------------
?>