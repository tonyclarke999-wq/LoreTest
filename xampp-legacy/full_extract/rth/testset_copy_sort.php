<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Test Set Copy Sort Page
#
# $RCSfile: testset_copy_sort.php,v $  $Revision: 1.2 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$redirect_page = 'testset_copy_page.php';
session_set_properties("testset_copy", $_GET);

html_window_title();
html_print_body();

print"<form method=post action=$redirect_page>". NEWLINE;
foreach( $_POST as $key => $value ) {
	print"	<input type=hidden name=\"$key\" value=\"".stripslashes($value)."\">". NEWLINE;
}
print"</form>". NEWLINE;

?>

<script language="JavaScript" type="text/javascript">
	document.forms[0].submit();
</script>

<?php

html_print_footer();

# ---------------------------------------------------------------------
# $Log: testset_copy_sort.php,v $
# Revision 1.2  2006/08/05 22:09:13  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>