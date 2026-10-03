<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Test Detail Delete Page
#
# $RCSfile: test_detail_delete_page.php,v $  $Revision: 1.2 $
# ---------------------------------------------------------------------

include"./api/include_api.php";


$page                   = basename(__FILE__);
$action_page            = 'test_detail_delete_action.php';
$project_properties     = session_get_project_properties();
$project_name           = $project_properties['project_name'];

$s_test_details = session_get_test_properties();
$test_id = $s_test_details['test_id'];

html_window_title();

auth_authenticate_user();

html_page_title($project_name ." - DELETE TEST");

html_print_body();

$test_name = test_get_name( $test_id);

print "<br>";
print "<div align=center>";
print "<div align='center'>";
print "<table class=width75>";
print "<tr>";
print "<td>";
    print "<table class=inner>";
    print "<form name=testdelete method=post action='$action_page'>";
    print "<tr>";
    print "<td class=form-header-c>";
    print lang_get('delete_test').$test_name. '?';
    print "</td>";
    print "</tr>";

    print "<tr>";
    print "<td>";
    print "&nbsp;";
    print "</td>";
    print "</tr>";

    print "<tr>";
    print "<td class=center>";
    print "<input type=submit name='delete' value='Yes'>";
    print "&nbsp;&nbsp;";
    print "<input type=submit name='delete' value='No'>";
    print "</td>";
    print "<tr>";

    print "</form>";
    print "</table>";
print "</td>";
print "</tr>";
print "</table>";
print "<br>";
print "</div>";
print "</div>";

html_print_footer();

# ---------------------------------------------------------------------
# $Log: test_detail_delete_page.php,v $
# Revision 1.2  2006/02/24 11:36:04  gth2
# update to div - class=div-c not working in firefox - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>
