<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# $RCSfile: news_api.php,v $ $Revision: 1.2 $
# ------------------------------------
function news_get($project_id, $news_id=null) {

	$tbl_news			= NEWS_TBL;
	$f_news_id			= NEWS_TBL .".". NEWS_ID;
	$f_news_project_id	= NEWS_TBL .".". NEWS_PROJECT_ID;
	$f_news_subject		= NEWS_TBL .".". NEWS_SUBJECT;
	$f_news_body		= NEWS_TBL .".". NEWS_BODY;
	$f_news_modified	= NEWS_TBL .".". NEWS_MODIFIED;
	$f_news_poster		= NEWS_TBL .".". NEWS_POSTER;
	$f_news_deleted		= NEWS_TBL .".". NEWS_DELETED;

	global $db;

	$q = "	SELECT *
			FROM $tbl_news
			WHERE $f_news_project_id = $project_id
				AND $f_news_deleted!='Y'";

	if($news_id) {

		$q .= " AND $f_news_id = $news_id";
	}

	$q .= " ORDER BY $f_news_modified DESC";

	$rs = db_query($db, $q);
	$rows = db_fetch_array($db, $rs);

	return $rows;
}

function news_add($project_id, $subject, $body, $poster) {

	$tbl_news			= NEWS_TBL;
	$f_news_id			= NEWS_TBL .".". NEWS_ID;
	$f_news_project_id	= NEWS_TBL .".". NEWS_PROJECT_ID;
	$f_news_subject		= NEWS_TBL .".". NEWS_SUBJECT;
	$f_news_body		= NEWS_TBL .".". NEWS_BODY;
	$f_news_modified	= NEWS_TBL .".". NEWS_MODIFIED;
	$f_news_poster		= NEWS_TBL .".". NEWS_POSTER;
	$f_news_deleted		= NEWS_TBL .".". NEWS_DELETED;

	$date = date_get_short_dt();

	global $db;

	$q = "	INSERT INTO $tbl_news
				($f_news_project_id, $f_news_subject, $f_news_body, $f_news_modified, $f_news_poster)
			VALUES
				($project_id, '$subject', '$body', '$date', '$poster')";

	db_query($db, $q);
}

function news_edit($project_id, $news_id, $poster, $subject, $body) {

	$tbl_news			= NEWS_TBL;
	$f_news_id			= NEWS_TBL .".". NEWS_ID;
	$f_news_project_id	= NEWS_TBL .".". NEWS_PROJECT_ID;
	$f_news_subject		= NEWS_TBL .".". NEWS_SUBJECT;
	$f_news_body		= NEWS_TBL .".". NEWS_BODY;
	$f_news_modified	= NEWS_TBL .".". NEWS_MODIFIED;
	$f_news_poster		= NEWS_TBL .".". NEWS_POSTER;
	$f_news_deleted		= NEWS_TBL .".". NEWS_DELETED;

	$date = date_get_short_dt();

	global $db;

	$q = "	UPDATE $tbl_news
			SET
				$f_news_subject		= '$subject',
				$f_news_body		= '$body',
				$f_news_modified	= '$date',
				$f_news_poster		= '$poster'
			WHERE
				$f_news_project_id = $project_id
				AND $f_news_id = $news_id";

	db_query($db, $q);
}

function news_delete($project_id, $news_id) {

	$tbl_news			= NEWS_TBL;
	$f_news_id			= NEWS_TBL .".". NEWS_ID;
	$f_news_project_id	= NEWS_TBL .".". NEWS_PROJECT_ID;
	$f_news_subject		= NEWS_TBL .".". NEWS_SUBJECT;
	$f_news_body		= NEWS_TBL .".". NEWS_BODY;
	$f_news_modified	= NEWS_TBL .".". NEWS_MODIFIED;
	$f_news_poster		= NEWS_TBL .".". NEWS_POSTER;
	$f_news_deleted		= NEWS_TBL .".". NEWS_DELETED;

	global $db;

	$q = "	UPDATE $tbl_news
			SET
				$f_news_deleted = 'Y'
			WHERE
				$f_news_project_id = $project_id
				AND $f_news_id = $news_id";

	db_query($db, $q);
}

# ------------------------------------
# $Log: news_api.php,v $
# Revision 1.2  2007/02/03 10:25:05  gth2
# no message
#
# Revision 1.1.1.1  2005/11/30 23:01:12  gth2
# importing initial version - gth
#
# ------------------------------------
?>