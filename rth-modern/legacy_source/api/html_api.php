<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# HTML API
#
# $RCSfile: html_api.php,v $ 
# $Revision: 1.23 $
# ------------------------------------


    //include( config_get( 'meta_include_file' ) );
    # maybe include meta include when you find out what it does
    /*
    print"<meta http-equiv=Pragma content=no-cache />";
    print"<meta http-equiv=Cache-Control content=no-cache />";
    print"<meta http-equiv=Pragma-directive content=no-cache />";
    print"<meta http-equiv=Cache-Directive content=no-cache />";
    print"<meta http-equiv=Expires content=0 />";
    */

#--------------------------------------------------------------------------------------------------
# This function and the two following functions have the wrong names
# Maybe this should be html_window_title, the next html_page_title, and the last html_print_header.
# Just a thought
#--------------------------------------------------------------------------------------------------
function html_window_title() {

	print"<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">". NEWLINE;
	print"<html>". NEWLINE;
	print"<head>". NEWLINE;
	print"<meta http-equiv='Content-type' content='text/html;charset=utf-8'>". NEWLINE;
	print"<title>". WINDOW_TITLE ."</title>". NEWLINE;
	print"<link rel=stylesheet type='text/css' href='./css/default.php'>". NEWLINE;
	 
	# Include javascript file if USE_JAVASCRIPT constant is ON
	if( USE_JAVASCRIPT ) {
		html_head_javascript();
	}
	print"</head>". NEWLINE;
}

# ---------------------------------------------------------------------
# Include javascript file
# ---------------------------------------------------------------------
function html_head_javascript() {

		echo"<script type='text/JavaScript' src='api/javascript_api.js'></script>". NEWLINE;
		echo"<script src='sortable.js'></script>".NEWLINE;
}

#-----------------------------------------
# Prints main title on page
#-----------------------------------------
function html_page_title( $page_title ) {

    if( trim($page_title) == '' ) {
        $page_title = PAGE_TITLE;
    }
    print"<h2>$page_title</h2>". NEWLINE;
}

# ---------------------------------------------------------------------
# This function will display the user name, date and time, and project listbox accross the top of the page
# The project list box will only appear if the user has access to more than one project
# ---------------------------------------------------------------------
function html_page_header( $db, $current_proj ) {

	$s_user_projects 	= session_get_user_projects();
	$s_user_properties 	= session_get_user_properties();
    $date               = date_get_long_dt();
    $message			= lang_get('error_empty_id_field');

    $username			= $s_user_properties['username'];
	
	print"<form method=post name=navigate_to_id id=form_validate action='navigate_to_id_action.php' >". NEWLINE;
	print"<table class=hide100>". NEWLINE;
	print"<tr><td class=header-r>". NEWLINE;
	print lang_get('goto_to_id') . NEWLINE;
	print"<input type=text size=6 id='validate_txt_field' name=id_txt_field>". NEWLINE;
	#print'<a href="javascript:return ValidateForm(\''.$message.'\')">'. lang_get('go') .'</a></td>'. NEWLINE;
	print"<input type=submit value='". lang_get('go') ."'>";#</td>";
	print"</td></tr>". NEWLINE;
	print"</table>". NEWLINE;
	print"</form>". NEWLINE;
	
    print"<form method=post name=form_set_project action='login_switch_proj.php'>". NEWLINE;
    print"<table class=hide100>". NEWLINE;
    print"<tr>". NEWLINE;
    print"<td class=header-l width='33%'>logged in as $username</td>". NEWLINE;
    print"<td class=header-c width='33%'>$date</td>". NEWLINE;

    if( $s_user_projects > 1 ) { # display the project list box
        print"<td class=header-r width='33%'>". NEWLINE;
     
		
		# Check for Javascript
		print"<noscript>". NEWLINE;
		print"<input type=hidden name=javascript_disabled value=true>". NEWLINE;
		print"</noscript>". NEWLINE;

		# login variables set, so there is no need to do if( isset($_GET[login][page]) )
		# in login_switch_proj.php
		print"<input type=hidden name='login[page]' value=''>". NEWLINE;
		print"<input type=hidden name='login[get]' value=''>". NEWLINE;
		print"<input type=hidden name='uname' value='$username'>". NEWLINE;

		# Check for Javascript
		print"<noscript>". NEWLINE;
		print"<input type=hidden name=non_javascript_browser value=true>". NEWLINE;
		print"</noscript>". NEWLINE;

        if( session_use_javascript() ) {
        	print lang_get('switch_project'). NEWLINE;
        } else {
        	print"<input type=submit value='". lang_get('switch_project') ."'>". NEWLINE;
        }

        print"<select name='login[switch_project]' onchange='document.forms.form_set_project.submit();'>". NEWLINE;
        html_print_list_box_from_array(	$s_user_projects, $current_proj );
        print"</select>". NEWLINE;
        print"</td>". NEWLINE;
    } else { # don't display the project list box
        print"<td class=header-r width=33%>&nbsp</td>". NEWLINE;
    }


    print"</tr>". NEWLINE;
    print"</table>". NEWLINE;
    print"</form>". NEWLINE;
}

# -----------------------------------------------------------------
# This function will generate the main menu at the top of every page
# The RTH_URL is defined in the properties_inc.php file
# -----------------------------------------------------------------
function html_print_menu() {

    $home_url       = RTH_URL . "home_page.php";
    $req_url        = RTH_URL . "requirement_page.php";
    $test_url       = RTH_URL . "test_page.php";
	$release_url    = RTH_URL . "release_page.php";
    $results_url    = RTH_URL . "results_page.php";
	$bug_url		= BUGTRACKER_URL;
	$reports_url    = RTH_URL . "report_page.php";
	$admin_url      = RTH_URL . "admin_page.php";
    $user_url       = RTH_URL . "user_edit_my_account_page.php";
    $help_url		= RTH_URL . "help_index.php";
    $logout_url     = RTH_URL . "logout.php";

	# set user url if user has admin rights
    $s_user_properties 	= session_get_user_properties();
	$user_id 			= $s_user_properties['user_id'];

	$s_project_properties   = session_get_project_properties();
	$project_name           = $s_project_properties['project_name'];
	$project_id 			= $s_project_properties['project_id'];

	if( user_has_rights($project_id, $user_id, ADMIN) ) {
		$user_url       = RTH_URL . "user_manage_page.php";
	}

    # Get the session variables from the results page and append them on the query string if they are set
    $s_results = session_get_display_options("results");

    if( isset( $s_results['release_id'] ) ) {
        $results_url = $results_url . "?release_id=" .$s_results['release_id'];
    }
    if( isset( $s_results['build_id'] ) ) {
        $results_url = $results_url . "&build_id=" .$s_results['build_id'];
    }
    if( isset( $s_results['testset_id'] ) ) {
        $results_url = $results_url . "&testset_id=" .$s_results['testset_id'];
    }
    if( isset( $s_results['test_id'] ) ) {
		$results_url = "results_test_run_page.php?test_id=" .$s_results['test_id'] ."&testset_id=". $s_results['testset_id'];
    }

    print"<table class=width100 cellspacing=0>". NEWLINE;
    print"<tr>". NEWLINE;
	print"<td class=menu>". NEWLINE;
	print"<a href='$home_url'>"		. lang_get( 'home_link' ) ."</a> | ". NEWLINE;
	print"<a href='$req_url'>" 		. lang_get( 'req_link' ) ."</a> | ". NEWLINE;
	print"<a href='$test_url'>"		. lang_get( 'test_link' ) . "</a> | ". NEWLINE;
	print"<a href='$release_url'>"	. lang_get( 'release_link' ) . "</a> | ". NEWLINE;
	print"<a href='$results_url'>"	. lang_get( 'results_link' ) . "</a> | ". NEWLINE;
	print"<a href='$bug_url'";
	if( BUGTRACKER != 'rth' ) {
		print" target='new'";
	}
	print">"		. lang_get( 'bug_link' ) . "</a> | ". NEWLINE;
	print"<a href='$reports_url'>"	. lang_get( 'reports_link' ) . "</a> | ". NEWLINE;
	print"<a href='$admin_url'>"	. lang_get( 'admin_link' ) . "</a> | ". NEWLINE;
	print"<a href='$user_url'>" 	. lang_get( 'user_link' ) . "</a> | ". NEWLINE;
	print"<a href='$help_url' target=_blank>"		. lang_get( 'help_link' ) . "</a> | ". NEWLINE;
	print"<a href='$logout_url'>"	. lang_get( 'logout_link' ) . "</a>". NEWLINE;
	print"</td>". NEWLINE;
    print"</tr>". NEWLINE;
    print"</table>". NEWLINE;
    print"<br>". NEWLINE;
}

# ----------------------------------------------------------------
# Prints a menu of values seperated by "  |  "
#
# INPUT:
#	current page
#	array of menu items and their urls
# OUTPUT:
#	html menu
# ----------------------------------------------------------------
function html_print_sub_menu($page, $menu_items) {

	$test_add_url       = RTH_URL . "main.php";
	$test_workflow_url  = RTH_URL . "main.php";
	$query_str			= "";

	if( isset($_SERVER['QUERY_STRING']) ) {
		$query_str = "?".$_SERVER['QUERY_STRING'];
	}

	print"<div align='center'>". NEWLINE;
	print"<table class='sub-menu'>". NEWLINE;
	print"<tr>". NEWLINE;
	print"<td class='menu'>&nbsp;&nbsp;";

	$i = 1;
	$array_size = count($menu_items);

	foreach($menu_items as $menu_page => $menu_url) {

		$parsed_url = parse_url($menu_url);
		$url_page = basename($parsed_url['path']);


		if( $page == $menu_url || $page.$query_str == $menu_url ) {
			print $menu_page;
		} else {
			print "<a href='$menu_url'>" . $menu_page ."</a>";
		}

		# if not the last element in the menu
		if ($i != $array_size) {
			print "&nbsp;&nbsp;|&nbsp;&nbsp;";
		}

		$i++;
	}

	print"&nbsp;&nbsp;</td>". NEWLINE;
	print"</tr>". NEWLINE;
	print"</table>". NEWLINE;
	print"</div>". NEWLINE;
}

# -----------------------------------------------------------------------
# Display the menu when drilling down to view a testset
# The page, $table_display_properties and $filter parameters should allow
# us to reuse this function on different pages
# INPUT:
#   db, page and project_id
#   table_display_properties contains release, build and testset data
#   filter
# OUTPUT:
#   Corresponding testset information
# -----------------------------------------------------------------------
function html_test_results_menu( $db, $page, $project_id, $table_display_properties=null, $filter=null ) {


    $release_tbl        = RELEASE_TBL;
    $f_release_id       = RELEASE_TBL .".". RELEASE_ID;
    $f_project_id		= RELEASE_TBL .".". PROJECT_ID;
    $f_release_name     = RELEASE_TBL .".". RELEASE_NAME;
    $f_release_archive  = RELEASE_TBL .".". RELEASE_ARCHIVE;

    $build_tbl          = BUILD_TBL;
    $f_build_id         = BUILD_TBL .".". BUILD_ID;
    $f_build_rel_id     = BUILD_TBL .".". BUILD_REL_ID;
    $f_build_name       = BUILD_TBL .".". BUILD_NAME;
    $f_build_archive    = BUILD_TBL .".". BUILD_ARCHIVE;

    $testset_tbl        = TS_TBL;
    $f_testset_id       = TS_TBL .".". TS_ID;
    $f_testset_name     = TS_TBL .".". TS_NAME;

    $test_tbl			= TEST_TBL;
    $f_test_id			= TEST_ID;
    $f_test_name		= TEST_NAME;

    $test_run_page = "results_test_run_page.php";

	$release_id		= $table_display_properties['release_id'];
	$build_id		= $table_display_properties['build_id'];
	$testset_id		= $table_display_properties['testset_id'];
	$test_id		= $table_display_properties['test_id'];

    if( empty( $filter['release_id'] ) ) {
        $show_all = true;
    } else {
        $show_all = false;
    }

    //<!--Table for holding all other tables-->
    print"<table class='hide100'>". NEWLINE;
    print"<tr>". NEWLINE;
    print"<td>". NEWLINE;

	# Release Name
    print"<table align='left'>". NEWLINE;
    print"<tr>". NEWLINE;
    print"<td class='sub_menu' nowrap><b><a href='$page?release_id=all'>". lang_get( 'release_name' ) ."</a></b></td>". NEWLINE;
    print"</tr>". NEWLINE;

    # if the user has not selected a release show all releases
    if ( ( empty($release_id) || $release_id == 'all')  && $show_all == true ) {

		$q 		= "SELECT DISTINCT $f_release_name, $f_release_id FROM $release_tbl WHERE $f_project_id = '$project_id' AND  $f_release_archive = 'N' ORDER BY $f_release_id";
		$rs 	= db_query( $db, $q);
		$rows	= db_fetch_array( $db, $rs );

        if($rows) {

        	foreach($rows as $row_release) {
				$rel_id 	= $row_release[RELEASE_ID];
				$rel_name 	= $row_release[RELEASE_NAME];
				print"<tr>". NEWLINE;
				print"<td class='sub_menu'><a href='$page?release_id=$rel_id'>$rel_name</a></td>". NEWLINE;
				print"</tr>". NEWLINE;
			}
        } else  {

			print"<tr>". NEWLINE;
			print"<td class='error'>".lang_get('no_releases_in_project')."</td>". NEWLINE;
			print"</tr>". NEWLINE;
        }

        print"</table>". NEWLINE;
    } else { # Show the selected release and the build information

        $q_rel_name = "SELECT $f_release_name FROM $release_tbl WHERE $f_release_id = '$table_display_properties[release_id]'";
        $release_name = db_get_one( $db, $q_rel_name );

        print"<tr>". NEWLINE;
        print"<td class='sub_menu' nowrap>$release_name</td>". NEWLINE;
        print"</tr>". NEWLINE;
        print"</table>". NEWLINE;

		print"<table align='left'>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td class='sub_menu'>&nbsp;</td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"</table>". NEWLINE;

        $q_build = "SELECT DISTINCT $f_build_name, $f_build_id FROM $build_tbl WHERE $f_build_archive = 'N' AND $f_build_rel_id = '$table_display_properties[release_id]' ORDER BY $f_build_name";
        $rs_build = db_query( $db, $q_build );
        $num_build = db_num_rows( $db, $rs_build );

        print"<table align='left'>". NEWLINE;
        print"<tr>". NEWLINE;
        print"<td class='sub_menu' nowrap><b><a href='$page?release_id=$table_display_properties[release_id]&amp;build_id=all'>". lang_get('build_name') ."</a></b></td>". NEWLINE;
        print"</tr>". NEWLINE;

        # if the user has not selected a build, show all builds
        if ( ( empty( $build_id ) || $build_id == 'all' ) && $show_all == true ) {
            if($num_build == 0) { # if there are no builds display a message
                print"<tr>". NEWLINE;
                print"<td class='sub_menu'>". lang_get('builds_none') ."	</td>". NEWLINE;
                print"</tr>". NEWLINE;
                print"</table>". NEWLINE;
            } else { # Show all builds associated to the selected release
                while($row_build = db_fetch_row( $db, $rs_build ) ) {

                    $b_name = $row_build[BUILD_NAME];
                    $b_id	= $row_build[BUILD_ID];
                    print"<tr>". NEWLINE;
                    print"<td class='sub_menu'><a href='$page?release_id=$table_display_properties[release_id]&amp;build_id=$b_id'>$b_name</a></td>". NEWLINE;
                    print"</tr>". NEWLINE;
                }
                print"</table>". NEWLINE;
            }
        } else { # show the selected build and testset information
            $q_build_name = "SELECT $f_build_name FROM $build_tbl WHERE $f_build_id = '$table_display_properties[build_id]'";
            $build_name = db_get_one( $db, $q_build_name );

            print"<tr>". NEWLINE;
            print"<td class='sub_menu'>$build_name</td>". NEWLINE;
            print"</tr>". NEWLINE;
            print"</table>";

			print"<table align='left'>". NEWLINE;
			print"<tr>". NEWLINE;
			print"<td class='sub_menu'>&nbsp;</td>". NEWLINE;
			print"</tr>". NEWLINE;
			print"</table>". NEWLINE;

            # MAY NEED TO CHANGE THE LOGIC TO EXCLUDE ALL
            # WHEN A USER CLICKS ALL THE TABLE SHOULD APPEAR WITHOUT A SELECTED
            # if the
            if( isset( $table_display_properties['testset_id'] ) && $table_display_properties['testset_id'] != 'all' ) {
                $q_testset_name = "SELECT $f_testset_name FROM $testset_tbl WHERE $f_testset_id = '$table_display_properties[testset_id]'";
                $testset_name = db_get_one( $db, $q_testset_name );
                print"<table align='left'>". NEWLINE;
                print"<tr>". NEWLINE;
                print"<td class='sub_menu' nowrap><b><a href='$page?release_id=$table_display_properties[release_id]&amp;build_id=$table_display_properties[build_id]&amp;testset_id=all'>" .lang_get('testset_name'). "</a></b></td>". NEWLINE;
                print"</tr>". NEWLINE;
                //print"<tr><td class='sub_menu'>$testset_name</td></tr>". NEWLINE;
                print"<tr>". NEWLINE;
				print"<td class='sub_menu'><a href='$page?release_id=$table_display_properties[release_id]&amp;build_id=$table_display_properties[build_id]&amp;testset_id=$table_display_properties[testset_id]&amp;test_id=none'>$testset_name</a></td>". NEWLINE;
				print"</tr>". NEWLINE;
                print"</table>". NEWLINE;
            }

            if( !empty($testset_id) && $testset_id != 'all' && !empty($test_id) && $test_id != 'none') {

            	$q_testname = "SELECT $f_test_name FROM $test_tbl WHERE $f_test_id = '$table_display_properties[test_id]'";
            	$test_name = db_get_one( $db, $q_testname );

				# Only display the link if not on the test run page
            	$current_page = basename($_SERVER["PHP_SELF"]);
            	if( $test_run_page!=$current_page ) {

            		$test_name = "<a href='$test_run_page?release_id=$table_display_properties[release_id]&amp;build_id=$table_display_properties[build_id]&amp;testset_id=$table_display_properties[testset_id]&amp;test_id=$table_display_properties[test_id]'>$test_name</a>";
            	}

            	print"<table>". NEWLINE;
				print"<tr>". NEWLINE;
				print"<td class='sub_menu' nowrap><b>" .lang_get('test_run'). "</b></td>". NEWLINE;
				print"</tr>". NEWLINE;
				print"<tr><td class='sub_menu'>$test_name</td></tr>". NEWLINE;
                print"</table>". NEWLINE;
            }
        }
    }

    print"</td>". NEWLINE;
    print"</tr>". NEWLINE;
    print"</table>". NEWLINE;

}

# -----------------------------------------------------------------------
# Display the menu when drilling down to view a testset
# The page, $table_display_properties and $filter parameters should allow
# us to reuse this function on different pages
# INPUT:
#   db, page and project_id
#   table_display_properties contains release, build and testset data
#   filter
# OUTPUT:
#   Corresponding testset information
# -----------------------------------------------------------------------
function html_testset_menu( $db, $page, $project_id, $table_display_properties=null ) {

    $release_tbl        = RELEASE_TBL;
    $f_release_id       = RELEASE_TBL .".". RELEASE_ID;
    $f_project_id		= RELEASE_TBL .".". PROJECT_ID;
    $f_release_name     = RELEASE_TBL .".". RELEASE_NAME;
    $f_release_archive  = RELEASE_TBL .".". RELEASE_ARCHIVE;
    $build_tbl          = BUILD_TBL;
    $f_build_id         = BUILD_TBL .".". BUILD_ID;
    $f_build_rel_id     = BUILD_TBL .".". BUILD_REL_ID;
    $f_build_name       = BUILD_TBL .".". BUILD_NAME;
    $f_build_archive    = BUILD_TBL .".". BUILD_ARCHIVE;
    $testset_tbl        = TS_TBL;
    $f_testset_id       = TS_TBL .".". TS_ID;
    $f_testset_name     = TS_TBL .".". TS_NAME;
    $test_tbl			= TEST_TBL;
    $f_test_id			= TEST_ID;
    $f_test_name		= TEST_NAME;

    if( isset($_GET['testset_menu_release_id']) ) {
		$release_id = $_GET['testset_menu_release_id'];
	}

	if( isset($_GET['testset_menu_build_id']) ) {
		$build_id = $_GET['testset_menu_build_id'];
	}

	if( isset($_GET['testset_menu_testset_id']) ) {
		$testset_id = $_GET['testset_menu_testset_id'];
	}

    if( empty( $filter['release_id'] ) ) {
        $show_all = true;
    } else {
        $show_all = false;
    }

    $q = "	SELECT DISTINCT	$f_release_name,
    						$f_release_id
    		FROM $release_tbl
    		WHERE $f_project_id = '$project_id'
    			AND  $f_release_archive = 'N'
    		ORDER BY $f_release_id";

    $rs = db_query($db, $q);

    //<!--Table for holding all other tables-->
    print"<table class='hide100'>". NEWLINE;
    print"<tr>". NEWLINE;
    print"<td>". NEWLINE;

	# Release Name
    print"<table align='left'>". NEWLINE;
    print"<tr>". NEWLINE;
    print"<td class='sub_menu' nowrap><b><a href='$page?testset_menu_release_id=all'>". lang_get( 'release_name' ) ."</a></b></td>". NEWLINE;
    print"</tr>". NEWLINE;

    # if the user has not selected a release show all releases
    if ( ( !isset( $release_id ) || $release_id == 'all') ) {

        while( $row = db_fetch_row( $db, $rs ) ) {

			$rel_id = $row[RELEASE_ID];
			$rel_name = $row[RELEASE_NAME];
            print"<tr>". NEWLINE;
            print"<td class='sub_menu'><a href='$page?testset_menu_release_id=$rel_id'>$rel_name</a></td>". NEWLINE;
            print"</tr>". NEWLINE;
        }

        print"</table>". NEWLINE;
    } else { # Show the selected release and the build information

        $q_rel_name = "	SELECT $f_release_name
        				FROM $release_tbl
        				WHERE $f_release_id = $release_id";

        $release_name = db_get_one( $db, $q_rel_name );

        print"<tr>". NEWLINE;
        print"<td class='sub_menu' nowrap>$release_name</td>". NEWLINE;
        print"</tr>". NEWLINE;
        print"</table>". NEWLINE;

		print"<table align='left'>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td class='sub_menu'>&nbsp;</td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"</table>". NEWLINE;

        $q_build = "SELECT DISTINCT $f_build_name,
        							$f_build_id
        			FROM $build_tbl
        			WHERE $f_build_archive = 'N'
        				AND $f_build_rel_id = $release_id
        			ORDER BY $f_build_name";

        $rs_build	= db_query($db, $q_build);
        $num_build	= db_num_rows($db, $rs_build);

		# Build Name
        print"<table align='left'>". NEWLINE;
        print"<tr>". NEWLINE;
        print"<td class='sub_menu' nowrap><b><a href='$page?testset_menu_release_id=$release_id&amp;testset_menu_build_id=all'>". lang_get('build_name') ."</a></b></td>". NEWLINE;
        print"</tr>". NEWLINE;

        # if the user has not selected a build, show all builds
        if ( ( !isset( $build_id ) || $build_id == 'all' ) && $show_all == true ) {
            if($num_build == 0) { # if there are no builds display a message
                print"<tr>". NEWLINE;
                print"<td class='sub_menu'>". lang_get('builds_none') ."	</td>". NEWLINE;
                print"</tr>". NEWLINE;
                print"</table>". NEWLINE;
            } else { # Show all builds associated to the selected release
                while($row_build = db_fetch_row( $db, $rs_build ) ) {

                    $b_name = $row_build[BUILD_NAME];
                    $b_id	= $row_build[BUILD_ID];
                    print"<tr>". NEWLINE;
                    print"<td class='sub_menu'><a href='$page?testset_menu_release_id=$release_id&amp;testset_menu_build_id=$b_id'>$b_name</a></td>". NEWLINE;
                    print"</tr>". NEWLINE;
                }
                print"</table>". NEWLINE;
            }
        } else { # show the selected build and testset information
            $q_build_name = "	SELECT $f_build_name
            					FROM $build_tbl
            					WHERE $f_build_id = $build_id";

            $build_name = db_get_one( $db, $q_build_name );

            print"<tr>". NEWLINE;
            print"<td class='sub_menu'>$build_name</td>". NEWLINE;
            print"</tr>". NEWLINE;
            print"</table>";

			print"<table align='left'>". NEWLINE;
			print"<tr>". NEWLINE;
			print"<td class='sub_menu'>&nbsp;</td>". NEWLINE;
			print"</tr>". NEWLINE;
			print"</table>". NEWLINE;

			# Testset Name
			print"<table align='left'>". NEWLINE;
			print"<tr>". NEWLINE;
			print"<td class='sub_menu' nowrap><b><a href='$page?testset_menu_release_id=$release_id&amp;testset_menu_build_id=$build_id&amp;testset_menu_testset_id=all'>" .lang_get('testset_name'). "</a></b></td>". NEWLINE;
			print"</tr>". NEWLINE;

            if( isset( $testset_id ) && $testset_id != 'all' ) {


				$q_testset_name = "	SELECT $f_testset_name
									FROM $testset_tbl
									WHERE $f_testset_id = $testset_id";

				$testset_name = db_get_one( $db, $q_testset_name );

                print"<tr>". NEWLINE;
				print"<td class='sub_menu'>$testset_name</td>". NEWLINE;
				print"</tr>". NEWLINE;
                print"</table>". NEWLINE;

            } else {

				$testset_tbl                = TS_TBL;
				$db_testset_id              = TS_TBL .".". TS_ID;
				$db_testset_name            = TS_TBL .".". TS_NAME;
				$db_testset_build_id        = TS_TBL .".". TS_BUILD_ID;

				$q = "	SELECT 	$db_testset_name,
								$db_testset_id
						FROM $testset_tbl
						WHERE $db_testset_build_id = $build_id
						ORDER BY $db_testset_name ASC";

				$rows 	= db_fetch_array($db, db_query($db, $q));

				if( $rows ) {

					foreach( $rows as $row ) {

						$testset_name = $row[TS_NAME];
						$testset_id = $row[TS_ID];

						print"<tr>". NEWLINE;
						print"<td class='sub_menu'><a href='$page?testset_menu_release_id=$release_id&amp;"
								. "testset_menu_build_id=$build_id&amp;"
								. "testset_menu_testset_id=$testset_id"
								. "'>$testset_name</a></td>". NEWLINE;
						print"</tr>". NEWLINE;
					}
					print"</table>". NEWLINE;

				} else {
					echo"<br>". NEWLINE;
					echo"<p class='error'>". lang_get( 'no_testsets' ) ."</p>". NEWLINE;
				}
            }
        }
    }

    print"</td>". NEWLINE;
    print"</tr>". NEWLINE;
    print"</table>". NEWLINE;
}


# -----------------------------------------------------------------------
# Display the menu when drilling down to view a testset
# The page, $table_display_properties and $filter parameters should allow
# us to reuse this function on different pages
# INPUT:
#   db, page and project_id
#   table_display_properties contains release, build and testset data
#   filter
# OUTPUT:
#   Corresponding testset information
# -----------------------------------------------------------------------
function html_browse_release_menu( 	$db,
									$page,
									$project_id,
									$display_property_group="",
									$show_build=true,
									$show_testset=true ) {

    $release_tbl        = RELEASE_TBL;
    $f_release_id       = RELEASE_TBL .".". RELEASE_ID;
    $f_project_id		= RELEASE_TBL .".". PROJECT_ID;
    $f_release_name     = RELEASE_TBL .".". RELEASE_NAME;
    $f_release_archive  = RELEASE_TBL .".". RELEASE_ARCHIVE;
    $f_release_date 	= RELEASE_TBL .".". RELEASE_DATE_RECEIVED;

    $build_tbl          = BUILD_TBL;
    $f_build_id         = BUILD_TBL .".". BUILD_ID;
    $f_build_rel_id     = BUILD_TBL .".". BUILD_REL_ID;
    $f_build_name       = BUILD_TBL .".". BUILD_NAME;
    $f_build_archive    = BUILD_TBL .".". BUILD_ARCHIVE;
    $f_build_date		= BUILD_TBL .".". BUILD_DATE_REC;

    $testset_tbl        = TS_TBL;
    $f_testset_id       = TS_TBL .".". TS_ID;
    $f_testset_name     = TS_TBL .".". TS_NAME;
    $f_testset_date     = TS_TBL .".". TS_DATE_CREATED;

    $test_tbl			= TEST_TBL;
    $f_test_id			= TEST_ID;
    $f_test_name		= TEST_NAME;

    if( isset($_GET["$display_property_group"."_release_id"]) ) {
		$release_id = $_GET["$display_property_group"."_release_id"];
	}

	if( isset($_GET["$display_property_group"."_build_id"]) ) {
		$build_id = $_GET["$display_property_group"."_build_id"];
	}

	if( isset($_GET["$display_property_group"."_testset_id"]) ) {
		$testset_id = $_GET["$display_property_group"."_testset_id"];
	}

    if( empty( $filter["release_id"] ) ) {
        $show_all = true;
    } else {
        $show_all = false;
    }

    //<!--Table for holding all other tables-->
    print"<table class='hide100'>". NEWLINE;
    print"<tr>". NEWLINE;
    print"<td>". NEWLINE;

	# Release Name
    print"<table align='left'>". NEWLINE;
    print"<tr>". NEWLINE;
    print"<td class='sub_menu' nowrap><b><a href='$page?$display_property_group"."_release_id=all'>". lang_get( 'release_name' ) ."</a></b></td>". NEWLINE;
    print"</tr>". NEWLINE;

    # if the user has not selected a release show all releases
    if ( ( !isset( $release_id ) || $release_id == 'all') ) {

		$q 		= "SELECT DISTINCT $f_release_name, $f_release_id FROM $release_tbl WHERE $f_project_id = '$project_id' AND  $f_release_archive = 'N' ORDER BY $f_release_id";
		$rs 	= db_query( $db, $q);
		$rows	= db_fetch_array( $db, $rs );

        if($rows) {

        	foreach($rows as $row_release) {
				$rel_id 	= $row_release[RELEASE_ID];
				$rel_name 	= $row_release[RELEASE_NAME];
				print"<tr>". NEWLINE;
				print"<td class='sub_menu'><a href='$page?_release_id=$rel_id'>$rel_name</a></td>". NEWLINE;
				print"</tr>". NEWLINE;
			}
        } else  {

			print"<tr>". NEWLINE;
			print"<td class='error'>".lang_get('no_releases_in_project')."</td>". NEWLINE;
			print"</tr>". NEWLINE;
        }

        print"</table>". NEWLINE;
    } else { # Show the selected release and the build information

        $q_rel_name = "	SELECT $f_release_name
        				FROM $release_tbl
        				WHERE $f_release_id = $release_id";

        $release_name = db_get_one( $db, $q_rel_name );

        print"<tr>". NEWLINE;
        print"<td class='sub_menu' nowrap>$release_name</td>". NEWLINE;
        print"</tr>". NEWLINE;
        print"</table>". NEWLINE;

		print"<table align='left'>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td class='sub_menu'>&nbsp;</td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"</table>". NEWLINE;

		if( $show_build ) {


			$q_build = "SELECT DISTINCT $f_build_name,
										$f_build_id
						FROM $build_tbl
						WHERE $f_build_archive = 'N'
							AND $f_build_rel_id = $release_id
						ORDER BY $f_build_date ASC";

			$rs_build	= db_query($db, $q_build);
			$num_build	= db_num_rows($db, $rs_build);

			# Build Name
			print"<table align='left'>". NEWLINE;
			print"<tr>". NEWLINE;
			print"<td class='sub_menu' nowrap><b><a href='$page?$display_property_group"."_release_id=$release_id&amp;$display_property_group"."_build_id=all'>". lang_get('build_name') ."</a></b></td>". NEWLINE;
			print"</tr>". NEWLINE;

			# if the user has not selected a build, show all builds
			if ( ( !isset( $build_id ) || $build_id == 'all' ) && $show_all == true ) {
				if($num_build == 0) { # if there are no builds display a message
					print"<tr>". NEWLINE;
					print"<td class='sub_menu'>". lang_get('builds_none') ."	</td>". NEWLINE;
					print"</tr>". NEWLINE;
					print"</table>". NEWLINE;
				} else { # Show all builds associated to the selected release
					while($row_build = db_fetch_row( $db, $rs_build ) ) {

						$b_name = $row_build[BUILD_NAME];
						$b_id	= $row_build[BUILD_ID];
						print"<tr>". NEWLINE;
						print"<td class='sub_menu'><a href='$page?$display_property_group"."_release_id=$release_id&amp;$display_property_group"."_build_id=$b_id'>$b_name</a></td>". NEWLINE;
						print"</tr>". NEWLINE;
					}
					print"</table>". NEWLINE;
				}
			} else { # show the selected build and testset information
				$q_build_name = "	SELECT $f_build_name
									FROM $build_tbl
									WHERE $f_build_id = $build_id";

				$build_name = db_get_one( $db, $q_build_name );

				print"<tr>". NEWLINE;
				print"<td class='sub_menu'>$build_name</td>". NEWLINE;
				print"</tr>". NEWLINE;
				print"</table>";

				print"<table align='left'>". NEWLINE;
				print"<tr>". NEWLINE;
				print"<td class='sub_menu'>&nbsp;</td>". NEWLINE;
				print"</tr>". NEWLINE;
				print"</table>". NEWLINE;

				if( $show_testset ) {

					# Testset Name
					print"<table align='left'>". NEWLINE;
					print"<tr>". NEWLINE;
					print"<td class='sub_menu' nowrap><b><a href='$page?$display_property_group"."_release_id=$release_id&amp;$display_property_group"."_build_id=$build_id&amp;$display_property_group"."_testset_id=all'>" .lang_get('testset_name'). "</a></b></td>". NEWLINE;
					print"</tr>". NEWLINE;

					if( isset( $testset_id ) && $testset_id != 'all' ) {


						$q_testset_name = "	SELECT $f_testset_name
											FROM $testset_tbl
											WHERE $f_testset_id = $testset_id";

						$testset_name = db_get_one( $db, $q_testset_name );

						print"<tr>". NEWLINE;
						print"<td class='sub_menu'>$testset_name</td>". NEWLINE;
						print"</tr>". NEWLINE;
						print"</table>". NEWLINE;

					} else {

						$testset_tbl                = TS_TBL;
						$db_testset_id              = TS_TBL .".". TS_ID;
						$db_testset_name            = TS_TBL .".". TS_NAME;
						$db_testset_build_id        = TS_TBL .".". TS_BUILD_ID;

						$q = "	SELECT 	$db_testset_name,
										$db_testset_id
								FROM $testset_tbl
								WHERE $db_testset_build_id = $build_id
								ORDER BY $f_testset_date ASC";

						$rows 	= db_fetch_array($db, db_query($db, $q));

						if( $rows ) {

							foreach( $rows as $row ) {

								$testset_name = $row[TS_NAME];
								$testset_id = $row[TS_ID];

								print"<tr>". NEWLINE;
								print"<td class='sub_menu'><a href='$page?$display_property_group"."_release_id=$release_id&amp;"
										. "$display_property_group"."_build_id=$build_id&amp;"
										. "$display_property_group"."_testset_id=$testset_id"
										. "'>$testset_name</a></td>". NEWLINE;
								print"</tr>". NEWLINE;
							}
							print"</table>". NEWLINE;

						} else {
							print"<tr>". NEWLINE;
							print"<td class='sub_menu'><br>";
							echo"<p class='error'>". lang_get( 'no_testsets' ) ."</p>". NEWLINE;
							print"</tr>". NEWLINE;
							print"</table>". NEWLINE;
						}
					}
				}
			}
		}
    }

    print"</td>". NEWLINE;
    print"</tr>". NEWLINE;
    print"</table>". NEWLINE;
}


function html_project_manage_menu() {

	$selected_project_properties 	= session_get_properties("project_manage");
	$selected_project_id 			= $selected_project_properties['project_id'];

	$manage_pages = array(	'project_manage_page.php'				=> lang_get('edit_project')." (".project_get_name($selected_project_id).")",
							'project_manage_reqareacovered_page.php'=> lang_get('reqs'),
							'project_archive_results_page.php'=> lang_get('tests'),
							'project_manage_bug_category_page.php' => lang_get('bug_link') );

	print"<div align='center'>". NEWLINE;
	print"<table class='sub-menu' rules=cols>". NEWLINE;
	print"<tr><td class='menu'>";

	$str = "";

	foreach($manage_pages as $key => $value) {

		if( stristr($_SERVER['PHP_SELF'], $key)===false ) {
			$str .= "&nbsp;<a href='$key'>$value</a>&nbsp;|". NEWLINE;
		} else {
			$str .= "&nbsp;$value&nbsp;|". NEWLINE;
		}
	}

	print substr($str, 0, -2);
	print"</td></tr>";

	print"</table>". NEWLINE;

	print"</div>". NEWLINE;

	print"<br>";
}

function html_project_manage_reqs_menu() {

	$selected_project_properties 	= session_get_properties("project_manage");
	$selected_project_id 			= $selected_project_properties['project_id'];

	$manage_pages = array(	'project_manage_reqareacovered_page.php'	=> lang_get('req_area_covered'),
							'project_manage_reqdoctype_page.php' 		=> lang_get('req_doc_type'),
							'project_manage_reqfunctionality_page.php'	=> lang_get('req_functionality') );

	print"<div align='center'>". NEWLINE;
	print"<table class='sub-menu' rules=cols>". NEWLINE;
	print"<tr><td class='menu'>";

	$str = "";

	foreach($manage_pages as $key => $value) {

		if( stristr($_SERVER['PHP_SELF'], $key)===false ) {
			$str .= "&nbsp;<a href='$key'>$value</a>&nbsp;|". NEWLINE;
		} else {
			$str .= "&nbsp;$value&nbsp;|". NEWLINE;
		}
	}

	print substr($str, 0, -2);
	print"</td></tr>";

	print"</table>". NEWLINE;
	print"</div>". NEWLINE;
	print"<br>". NEWLINE;
}

function html_project_manage_tests_menu() {

	$selected_project_properties 	= session_get_properties("project_manage");
	$selected_project_id 			= $selected_project_properties['project_id'];

	$manage_pages = array(
							'project_archive_results_page.php' 			=> lang_get('archive_results'),
							'project_archive_tests_page.php'	 		=> lang_get('archive_tests'),
							'project_manage_testareatested_page.php'	=> lang_get('area_tested'),
							'project_manage_testdoctype_page.php'		=> lang_get('test_doc_type'),
							'project_manage_testenvironment_page.php' 	=> lang_get('environment'),
							'project_manage_testmachines_page.php' 		=> lang_get('test_machine'),
							'project_manage_testtype_page.php' 			=> lang_get('testtype') );

	print"<div align='center'>". NEWLINE;
	print"<table class='sub-menu' rules=cols>". NEWLINE;
	print"<tr><td class='menu'>";

	$str = "";

	foreach($manage_pages as $key => $value) {

		if( stristr($_SERVER['PHP_SELF'], $key)===false ) {
			$str .= "&nbsp;<a href='$key'>$value</a>&nbsp;|". NEWLINE;
		} else {
			$str .= "&nbsp;$value&nbsp;|". NEWLINE;
		}
	}

	print substr($str, 0, -2);
	print"</td></tr>";

	print"</table>". NEWLINE;

	print"</div>". NEWLINE;
	print"<br>". NEWLINE;
}

function html_project_manage_bugs_menu() {

	$selected_project_properties 	= session_get_properties("project_manage");
	$selected_project_id 			= $selected_project_properties['project_id'];

	$manage_pages = array(
		'project_manage_bug_category_page.php' => lang_get('bug_category_link'),
		'project_manage_bug_component_page.php' => lang_get('bug_component_link') );

	print"<div align='center'>". NEWLINE;
	print"<table class='sub-menu' rules=cols>". NEWLINE;
	print"<tr><td class='menu'>";

	$str = "";

	foreach($manage_pages as $key => $value) {

		if( stristr($_SERVER['PHP_SELF'], $key)===false ) {
			$str .= "&nbsp;<a href='$key'>$value</a>&nbsp;|". NEWLINE;
		} else {
			$str .= "&nbsp;$value&nbsp;|". NEWLINE;
		}
	}

	print substr($str, 0, -2);
	print"</td></tr>";

	print"</table>". NEWLINE;
	print"</div>". NEWLINE;
	print"<br>". NEWLINE;
}


#--------------------------------------------------------------------------------------------------
# This function will place focus on a form field if you pass in the form name and obj name
# Its best to pass in variables if your using a form.  I think this will work well on most browsers
#--------------------------------------------------------------------------------------------------
function html_print_body( $form_name=null, $obj_name=null ) {

    if( empty($form_name) && empty($obj_name) ) {
        print"<body>". NEWLINE;
    }
    else {
        print"<body onload='document.$form_name.$obj_name.focus();'>". NEWLINE;
    }
}
/*
# -------------------------------------------------------
# This function is intended to make all table headers consistant.
# Pass in a page, field, order_by, and $order_dir and the header
# will be a hyperlink. Pass only the first parameter and the
# function will just list the table header without making it a link.
# If javascript param is included, then the javascript is included
# in the a tag.
# -------------------------------------------------------
function html_tbl_print_header(	$header,
								$page=null,
								$field=null,
								$order_by=null,
								$order_dir=null,
								$javascript=null ) {

	if($javascript) {
		$javascript = "onClick=\"sortPage('$field');return false\"";
	}

	switch( $order_dir ) {
		case( $field != $order_by ):
			$image="";
			break;
		case( $order_dir=="ASC" ):
			$image = "&nbsp;<img src='". IMG_SRC ."down.gif' border=0 alt='Ascending'>";
			break;
		case( $order_dir=="DESC" ):
			$image = "&nbsp;<img src='". IMG_SRC ."up.gif' border=0 alt='Descending'>";
			break;
		default:
			$image="";
	}

	if( $field !== null ) {

		print"<th nowrap><a $javascript href=\"$page?order_by=$field&amp;tbl_header=true\">$header$image</a></th>";
	} else {

		print"<th nowrap>$header</th>";
	}
}
*/
function html_tbl_print_header_not_sortable( $header,
											 $field=null,
											 $order_by=null,
											 $order_dir=null,
											 $page=null ) {
	switch( $order_dir ) {
		case( $field != $order_by ):
			$image="";
			break;
		case( $order_dir=="ASC" ):
			$image = "&nbsp;<img src='images/down.gif' border=0 alt='Ascending'>";
			$new_order_dir = "DESC";
			break;
		case( $order_dir=="DESC" ):
			$image = "&nbsp;<img src='images/up.gif' border=0 alt='Descending'>";
			$new_order_dir = "ASC";
			break;
		default:
			$image="";
	}


	if( $field !== null ) {

		print"<th class='unsortable' nowrap>". NEWLINE;

		if( !is_null($page) ) {
			print"<form method=post action='$page'>". NEWLINE;
			print"<input type=hidden name='table_header' value='true'>". NEWLINE;
		}
		print"<input type=hidden name='order_dir' value='$order_dir'>". NEWLINE;
		print"<input type=hidden name='$header' value='$field'>". NEWLINE;
		print"<input type=submit name='change_order_by'  value='$header' class='sort-header'>$image". NEWLINE;

		if( !is_null($page) ) {
			print"</form>". NEWLINE;
		}
		print"</th>". NEWLINE;
	} else {

		print"<th class='unsortable' nowrap>$header</th>". NEWLINE;
	}
}

function html_tbl_print_header(	$header,
								$field=null,
								$order_by=null,
								$order_dir=null,
								$page=null ) {

	switch( $order_dir ) {
		case( $field != $order_by ):
			$image="";
			break;
		case( $order_dir=="ASC" ):
			$image = "&nbsp;<img src='images/down.gif' border=0 alt='Ascending'>";
			$new_order_dir = "DESC";
			break;
		case( $order_dir=="DESC" ):
			$image = "&nbsp;<img src='images/up.gif' border=0 alt='Descending'>";
			$new_order_dir = "ASC";
			break;
		default:
			$image="";
	}


	if( $field !== null ) {

		print"<th nowrap>". NEWLINE;

		if( !is_null($page) ) {
			print"<form method=post action='$page'>". NEWLINE;
			print"<input type=hidden name='table_header' value='true'>". NEWLINE;
		}
		print"<input type=hidden name='order_dir' value='$order_dir'>". NEWLINE;
		print"<input type=hidden name='$header' value='$field'>". NEWLINE;
		print"<input type=submit name='change_order_by'  value='$header' class='sort-header'>$image". NEWLINE;

		if( !is_null($page) ) {
			print"</form>". NEWLINE;
		}
		print"</th>". NEWLINE;
	} else {

		print"<th nowrap>$header</th>". NEWLINE;
	}

}
function html_tbl_print_sortable_header($header,
								$tbl_column=null,
								$order_by=null,
								$order_dir=null,
								$page=null,
								$field=null ) {
	
	
	$new_order_dir="ASC";
	if( $tbl_column != $order_by ){
		$image= "&nbsp;<img src='images/arrow_none.gif' border=0 alt='no order'>";
	}
	else if( $order_dir=="ASC" ){
		$image = "&nbsp;<img src='images/arrow_up.gif' border=0 alt='Ascending'>";
		$new_order_dir = "DESC";			
	}
	else if( $order_dir=="DESC" ){
		$image = "&nbsp;<img src='images/arrow_down.gif' border=0 alt='Descending'>";
		$new_order_dir = "ASC";
	}
	else {
		$image= "&nbsp;<img src='images/arrow_none.gif' border=0 alt='no order'>";		
	}

	if($tbl_column!==null){
		print'<th><a href="javascript:setFieldsAndSubmit(\''.$tbl_column.'\',\''.$new_order_dir.'\')">'.$header.'</a>'. $image.'</th>'. NEWLINE;
	} else{
		print"<th nowrap>$header</th>". NEWLINE;
	}
	

}


function html_tbl_change_order_dir( $direction ) {

    if( $direction == 'ASC' ) {
        $direction = 'DESC';
    } elseif( $direction == 'DESC' ) {
        $direction = 'ASC';
    }
    print"direction in change_order_direction function = $direction<br>";
    return $direction;
}


# --------------------------------------------------------------------------
# Display alernating row colors for html tables.
# COMMENTS:
# This function will set the row style if it's not already set and then alternate the row style.
# The background colors are defined in properties_inc.php as constants
# USAGE:
#   $row_style = html_tbl_alternate_bgcolor( $row_style );
#   print"<tr class=$row_style>";
# ---------------------------------------------------------------------------
function html_tbl_alternate_bgcolor( $style ) {

    if( empty( $style ) ) {
        $style = ROW2_STYLE;
    }
    else if( $style == ROW2_STYLE ) {
        $style = ROW1_STYLE;
    }
    else {
        $style = ROW2_STYLE;
    }
    return $style;
}

# ------------------------------------------------------------------------------------
# DISPLAY THE PROPER ICON THAT IDENTIFIES A TEST AS MANUAL AUTOMATED OR BOTH
# ------------------------------------------------------------------------------------
function html_print_testtype_icon( $manual, $automated, $load_test="NO" ) {

    //print"<td align=center valign=top nowrap>";

		$return_str = "";

        if( $load_test=="YES" ) {

        	$return_str .= "<img src='".IMG_SRC."load_test.gif' title='". lang_get('test_performance') ."' alt=L>";
        }

		if( $manual == 'YES' && $automated == 'YES' ) {
			 $return_str .= "<img src='".IMG_SRC."auto_man.gif' title='". lang_get('man_auto_test') ."' alt='M/A'>";
		}
        elseif( $manual == 'YES')  {
            $return_str .= "<img src='".IMG_SRC."manual.gif' title='". lang_get('manual_test') ."' alt=M>";
        }
        elseif ($automated == 'YES') {
            $return_str .= "<img src='".IMG_SRC."auto.gif' title='". lang_get('automated_test') ."' alt=A>";
        }

    //print"</td>";

    return $return_str;
}

# ------------------------------------------------------------------------------------
# DISPLAY THE PROPER ICON THAT IDENTIFIES A TEST AS PASSED OR FAILED
# ------------------------------------------------------------------------------------
function html_teststatus_icon( $test_status ) {

	if( $test_status == 'Passed' ) {

		return"<img src='./images/pass.gif' title=Passed alt=Pass>";
	} elseif( $test_status == 'Failed')  {

		return"<img src='./images/fail.gif' title=Failed alt=Fail>";
	}
}

# ------------------------------------------------------------------------------------
# This function will display the html page footer
# COMMENTS:
# This will print out the time it takes to load an html page if you turn the DEBUG option
# in properties_inc.php = ON.
# ------------------------------------------------------------------------------------
function html_print_footer() {
    global $g_timer;
    $admin_email = ADMIN_EMAIL;

    print"<br>". NEWLINE;
    print"<br>". NEWLINE;
    print"<br>". NEWLINE;
    print"<hr>". NEWLINE;
    print"<table>". NEWLINE;
    print"<tr>". NEWLINE;
    print"<td class=footer-l>" . WINDOW_TITLE ."</td>". NEWLINE;
    print"</tr>". NEWLINE;
	print"<tr>". NEWLINE;
    print"<td class=footer-l>" . RTH_VERSION ."</td>". NEWLINE;
    print"</tr>". NEWLINE;
    print"<tr>". NEWLINE;
    print"<td class=footer-l>". lang_get('contact_admin') ." <a href='mailto:$admin_email'>". lang_get('rth_admin_email') ."</a></td>". NEWLINE;
    print"</tr>". NEWLINE;
    print"<tr>". NEWLINE;
    print"<td class=footer-l>". lang_get('help_sourceforge') ." <a href='http://sourceforge.net/projects/rth/' target='_blank'>sourceforge.net</a></td>". NEWLINE;
    print"</table>". NEWLINE;

    if ( ON == DEBUG ) {
        $g_timer->print_times();
    }

    print"</body>". NEWLINE;
    print"</html>". NEWLINE;
}

# -------------------------------------------------------------------------------------
# Redirect the user
# INPUT:
#   The url inluding any query strings
#   The delay ( default = 1 second )
# -------------------------------------------------------------------------------------
function html_redirect($url, $delay=null) {

    if( empty( $delay ) ) {
        $delay = '1';
    }

   print"<meta http-equiv='Refresh' content='$delay; URL=$url'>";

   exit;
}

/*
# --------------------------------------------------------------------------------------
# Populate list box with Manual/Auto values
# INPUT
#   the selected value (not required)
# COMMENTS:
# Print the manual automated list box.  I think we should call this list box test type.
# Make this test type so that we can account for Load Tests, Unit Tests, etc (Richard's idea)
# I've set it up so we can add a value to the language file and add that value here to update
# the values in the list box.  Maybe it should be an array of the values in the language file?
# Don't know what we'd call the current TestType field
# --------------------------------------------------------------------------------------
function html_manauto_list_box( $selected=null ) {

    $all        = lang_get('all');
    $manual     = lang_get('manual');
    $automated  = lang_get('automated');
    $man_auto   = lang_get('man_auto');

    # Might need to make the value = the db name

    if( $selected != '' ) {
        print"<option value=''></option>";
    }
    if( $selected != $manual ) {
        print"<option value='$manual'>$manual</option>";
    }
    if( $selected != $automated ) {
        print"<option value='$automated'>$automated</option>";
    }
    if( $selected != $man_auto ) {
        print"<option value='$man_auto'>$man_auto</option>";
    }

    print"<option selected value='$selected'>$selected</option>";
}
*/

/*
# ----------------------------------------------------------------------------
# Query a database and populate a list box with the query result
# INPUT:
#   database you want to query
#   table you want to query
#   field you want to query
#   the selected value of the html ( not required )
# COMMENTS:
# Same as html_print_list_box except that this function will not print out
# the value "all" in the list box
# -----------------------------------------------------------------------------
function html_list_box( $database, $table, $field, $selected=null ) {

    $q = "SELECT DISTINCT $field FROM $table WHERE $field != ''";


    //print"$q<br>";

    # ALTER WHERE CLAUSE TO ELIMINATE DUPLICATE ENTRIES IN THE LIST BOX
    if( $selected == '' ) {
        $q_clause =  " ORDER BY $field";
    }
    else {
        $q_clause =  " AND $field != '$selected' ORDER BY $field";
    }

    # RUN QUERY
    $q = $q . $q_clause;
    $rs = db_query( $database, $q );
    $num = db_num_rows( $database, $rs );
    //$row = array();

    if( $num > 0 ) {

        $row = db_fetch_array( $database, $num, $rs );

        for($i=0; $i < sizeof( $row ); $i++) {

            extract( $row[$i], EXTR_PREFIX_ALL, 'v' );

            $val = ${'v_' . $field};

            if( $val != '' ) {
                print"<option value='$val'>$val</option>";
            }
        }

        print"<option selected value='$selected'>$selected</option>";

    }
    else {  # DISPLAY NOTHING IF THERE IS NOTHING IN THE DB
        print"<td class=center>&nbsp</td>";
    }
}
*/

# ----------------------------------------------------------------------------
# Query a database and populate a list box with the query result (include "all" as a value)
# INPUT:
#   database you want to query
#   table you want to query
#   field you want to query
#   the selected value of the html ( not required )
# COMMENTS:
# The function will eliminate duplicate entries from the list box and ignore blanks
# We may want to add a second field input which is optional $field2=null so that
# we could query for an id and name field using id as the option value
# and name as the display option.  It is the function used to populate list boxes
# when filtering a table
# -----------------------------------------------------------------------------
function html_print_list_box( $database, $table, $field, $project_id, $selected=null ) {

	$db_project_id = PROJECT_ID;
    $q = "SELECT DISTINCT $field FROM $table WHERE $db_project_id = '$project_id' AND $field != ''";

    //print"$q<br>";

    # ALTER WHERE CLAUSE TO ELIMINATE DUPLICATE ENTRIES IN THE LIST BOX
    if( $selected == '' || $selected == 'all' ) {
        $q_clause =  " ORDER BY $field";
    }
    else {
        $q_clause =  " AND $field != '$selected' ORDER BY $field";
    }

    # RUN QUERY
    $q = $q . $q_clause;
    $rs = db_query( $database, $q );
	$num = db_num_rows( $database, $rs );
    //$row = array();

    if( $num > 0 ) {

        if( $selected != 'all' ) {
            print"<option value=all>". lang_get('all') ."</option>";
        }

        $row = db_fetch_array( $database, $rs );

        for($i=0; $i < sizeof( $row ); $i++) {

            extract( $row[$i], EXTR_PREFIX_ALL, 'v' );

            $val = ${'v_' . $field};

            if( $val != '' ) {
                print"<option value='$val'>$val</option>";
            }
        }

        print"<option selected value='$selected'>$selected</option>";

    }
    else {  # DISPLAY NOTHING IF THERE ARE NO BA_OWNERS
        print"<td class=center>&nbsp</td>";
    }
}


/*
# ----------------------------------------------------------------------------
# This function will query a db, add a where clause to the query and print the result set in a list box
# INPUT:
#   database you want to query
#   table you want to query
#   field you want to query
#   the where condition ( a field name ) such as WHERE table_id = ''
#   where_value - the value after the = in the where clause
#   the selected value of the html ( not required )
# COMMENTS:
# The function will eliminate duplicate entries from the list box and ignore blanks
# We may want to add a second field input which is optional $field2=null so that
# we could query for an id and name field using id as the option value
# and name as the display option.
# -----------------------------------------------------------------------------
function html_print_list_box_with_where( $database, $table, $field, $where_condition, $where_value, $selected=null ) {

    $q = "SELECT DISTINCT $field FROM $table WHERE $where_condition = $where_value AND $field != ''";
    print"$q<br>";

    # ALTER WHERE CLAUSE TO ELIMINATE DUPLICATE ENTRIES IN THE LIST BOX
    if( $selected == '' || $selected == 'all' ) {
        $q_clause =  " ORDER BY $field";
    }
    else {
        $q_clause =  " AND $field != '$selected' ORDER BY $field";
    }

    # RUN QUERY
    $q = $q . $q_clause;
    $rs = db_query( $database, $q );
    $num = db_num_rows( $database, $rs );
    //$row = array();

    if( $num > 0 ) {

        if( $selected != 'all' ) {
            print"<option value=all>". lang_get('all') ."</option>";
        }

        $row = db_fetch_array( $database, $num, $rs );

        for($i=0; $i < sizeof( $row ); $i++) {

            extract( $row[$i], EXTR_PREFIX_ALL, 'v' );

            $val = ${'v_' . $field};

            if( $val != '' ) {
                print"<option value='$val'>$val</option>";
            }
        }

        print"<option selected value='$selected'>$selected</option>";

    }
    else {  # DISPLAY NOTHING IF THERE ARE NO BA_OWNERS
        print"<td class=center>&nbsp</td>";
    }
}
*/
/*
# ----------------------------------------------------------------------
# This function will query a db, join two tables and print the result set in a list box
# INPUT:
#   $db - database you want to query
#   $table1 - table you want to select from (combined with field to fetch the values)
#   $table2 - table you want to join
#   $field  - value you want to return in the list box
#   $join_field - field to join in the ON portion of the query ( this field is combined with table1 and table2 to create ON clause)
#   $where_table -
#   $where_field - These two values are joined to create the where condition
#   $selected - The selected value
#   $all - when set to true, all is added to list box
# -------------------------------------------------------------------------
function html_print_list_box_with_join( $database, $table1, $table2, $field, $join_field, $where_table, $where_field, $where_value, $project_id, $selected=null, $all=null ) {


	$db_project_id	= PROJECT_ID;
    $f1 = $table1 .".". $field;
    $join1 = $table1 .".". $join_field;
    $join2 = $table2 .".". $join_field;
    $where_field = $where_table .".". $where_field;

    $q = "SELECT DISTINCT $f1 FROM $table1 INNER JOIN $table2 ON $join1 = $join2 WHERE $where_field = '$where_value' AND $f1 != ''";
    print"$q<br>";

    # ALTER WHERE CLAUSE TO ELIMINATE DUPLICATE ENTRIES IN THE LIST BOX
    if( $selected == '' || $selected == 'all' ) {
        $q_clause =  " ORDER BY $field";
    }
    else {
        $q_clause =  " AND $field != '$selected' ORDER BY $field";
    }

    # RUN QUERY
    $q = $q . $q_clause;
    $rs = db_query( $database, $q );
    $num = db_num_rows( $database, $rs );
    //$row = array();
    if( $num > 0 ) {

        if( $selected != 'all' && $all=='all' ) {
            print"<option value=all>". lang_get('all') ."</option>";
        }

        $row = db_fetch_array( $database, $num, $rs );

        for($i=0; $i < sizeof( $row ); $i++) {

            extract( $row[$i], EXTR_PREFIX_ALL, 'v' );

            $val = ${'v_' . $field};

            if( $val != '' ) {
                print"<option value='$val'>$val</option>";
            }
        }

        print"<option selected value='$selected'>$selected</option>";

    }
    else {  # DISPLAY NOTHING IF THERE ARE NO BA_OWNERS
        print"<td class=center>&nbsp</td>";
    }
}
*/

# ----------------------------------------------------------------------
# Print option items using an indexed array.
# INPUT:
#   $array_options - The <option> items
#		e.g. array("Iggy Pop", "Taylor Holbrook")
#   $selected - The selected value(s)
# 		e.g. 1) 'Iggy Pop'
#		e.g. 2) array('Iggy Pop', 'Taylor Holbrook')
#
# OUTPUT:
#	e.g. 1)
#	<option value='Albert Einstein' selected>Albert Einstein</option>
#	<option value='Albert Einstein'>Taylor Holbrook</option>
#
# NOTE: If this function does not do what you want it to, look at
# html_print_list_box_from_key_array
# ----------------------------------------------------------------------
function html_print_list_box_from_array( $array_options, $selected='' ) {

	foreach($array_options as $option) {

		# if selected is an array of values
	    if( is_array($selected) ) {
			# find out if this option is a selected value
			if ( util_array_value_search($option, $selected) ) {
				$selected_html = "selected";
			} else {
				$selected_html = "";
			}

		# if selected is a single value
	    } else {
	    	# find out if this option is a selected value
			if ($option == $selected) {
				$selected_html = "selected";
			} else {
				$selected_html = "";
			}
		}

		print"<option value='$option' $selected_html>$option</option>". NEWLINE;
    }
}

# ----------------------------------------------------------------------
# Print option items using an associative array.
# INPUT:
#   $array_options - The <option> items
#		e.g. array("aeinstein@liberty.com"=>"Albert Einstein", "tholbrook@liberty.com"=>"Taylor Holbrook")
#   $selected - The selected value(s)
# 		e.g. 1) 'aeinstein@liberty.com'
#		e.g. 2) array('aeinstein@liberty.com', 'tholbrook@liberty.com')
#
# OUTPUT:
#	e.g. 1)
#	<option value='aeinstein@liberty.com' selected>Albert Einstein</option>
#	<option value='tholbrook@liberty.com'>Taylor Holbrook</option>
#
# NOTE: If this function does not do what you want it to, look at
# html_print_list_box_from_array
# ----------------------------------------------------------------------
function html_print_list_box_from_key_array( $array_options, $selected=null ) {

	foreach($array_options as $option_value => $option_text) {

		# if selected is an array of values
		if( is_array($selected) ) {

			# find out if this option is a selected value
			if ( util_array_value_search($option_value, $selected) ) {
				$selected_html = "selected";
			}
			else {
				$selected_html = "";
			}

		# if selected is a single value
	    } else {
			# find out if this option is a selected value
			if( $option_value == $selected ) {
				$selected_html = "selected";
			} else {
				$selected_html = "";
			}
		}
		
		print"<option value='$option_value' $selected_html>$option_text</option>". NEWLINE;
		
    }
}

# ----------------------------------------------------------------------------
# Prints the user rights options for a form select.
# ----------------------------------------------------------------------------
function html_print_user_rights_list_box( $selected="" ) {

	$user_rights_list_box 		= array( 	USER=>lang_get('user'),
											DEVELOPER=>lang_get('developer'),
											MANAGER=>lang_get('manager') );

	html_print_list_box_from_key_array(	$user_rights_list_box,
										$selected );
}

# ----------------------------------------------------------------------------
# Prints the table offset details including "showing records x - y", csv_url
# and "first, prev, 1 2 ... next last"
# If use_javascript is set then, the function is added to the onClick event of
# the a tags.
# ----------------------------------------------------------------------------
function html_table_offset( $row_count, $per_page, $page_number, $order_by=null, $order_dir=null, $csv_url=null ) {

	# First page number
	$page_one = 1;

	# Make sure page count is at least 1
	$page_count = ceil($row_count / $per_page );
	if( $page_count < 1 ) {
		$page_count = 1;
	}

	# Set page_num = 1 in case the user hasn't yet chosen a page number
	if( empty($page_number) ) {
		$page_number = 1;
	}

	# Make sure page_number isn't past the last page.
	if( $page_number > $page_count ) {
		$page_number = $page_count;
	}

	# offset = (page number - 1) * records per page
	# if page number = 1, offset = 0, returning record 0 through 25 (page)
	# if page number = 2, offset = 25
	# if page number = 3, offset = 50
	$offset = ( ($page_number - 1) * $per_page );

	# Show the number of records displayed and the total number of records
	if( $row_count == '0' ) {
		return;

	} else {
		$lower_count = $offset + 1;
	}

	# upper count is the last record displayed on the page
	$upper_count = $offset + $per_page;
	$upper_count = ($upper_count > $row_count) ? $row_count : $upper_count;

	$page_numbers_array			= array();
	$number_page_links_shown	= 10;

	# Calculate first and last pages
	$first_page_link_shown	= max( $page_one, $page_number - $number_page_links_shown/2 );
	$first_page_link_shown	= min( $first_page_link_shown, $page_count - $number_page_links_shown );
	$first_page_link_shown	= max( $first_page_link_shown, $page_one );
	$last_page_link_shown	= $first_page_link_shown + $number_page_links_shown;
	$last_page_link_shown	= min( $last_page_link_shown, $page_count );

	# Page numbers array
	for ( $i = $first_page_link_shown ; $i <= $last_page_link_shown ; $i++ ) {
		if ( $i == $page_number ) {
			array_push( $page_numbers_array, "<input type=submit name=page_number value=$i disabled class='page-numbers-disabled'>" );
		} else {
			array_push( $page_numbers_array, "<input type=submit name=page_number value=$i class='page-numbers'>" );
		}
	}

	print"<input name='order_dir' id='order_dir' type=hidden value='$order_dir' >". NEWLINE;
	print"<input name='order_by' id='order_by' type=hidden value='$order_by' >". NEWLINE;

	print"<input type=hidden name=first_page_number    value=1>". NEWLINE;
	print"<input type=hidden name=previous_page_number value=".($page_number-1).">". NEWLINE;
	print"<input type=hidden name=next_page_number     value=".($page_number+1).">". NEWLINE;
	print"<input type=hidden name=last_page_number     value=$page_count>". NEWLINE;
	print"<input type=hidden name=page_number          value=$page_number>". NEWLINE;

	print"<table class=hide100>". NEWLINE;
	print"<tr>". NEWLINE;

	# Showing x - y of z
	print"<td class='left'>". NEWLINE;
	print"Showing $lower_count - $upper_count of $row_count". NEWLINE;
	print"</td>". NEWLINE;

	# CVS Export Link
	if( !is_null($csv_url) ) {
		echo"<td align='center'>". NEWLINE;
		echo"<a href='csv_export.php?table=$csv_url'>";
		if( IMPORT_EXPORT_TO_EXCEL ) {
			print lang_get('excel_export');
		} 
		else {
			print lang_get('csv_export');
		}
		print"</a>". NEWLINE;
		echo"</td>". NEWLINE;
	}

	print"<td class='right'>". NEWLINE;
	echo"[". NEWLINE;

	# First and previous links
	if ( 1 < $page_number ) {
		$disabled = "class='page-numbers'";
	} else {
		$disabled = "disabled class='page-numbers-disabled'";
	}
	echo"<input type=submit name=page_number value='".lang_get('first')."' $disabled >". NEWLINE;
	echo"<input type=submit name=page_number value='".lang_get('previous')."' $disabled >". NEWLINE;

	# ...
	if ( $first_page_link_shown > 1 ) {
		echo( " ... " );
	}

	# Print page numbers
	echo "".implode( "\n", $page_numbers_array )."". NEWLINE;

	# ...
	if ( $last_page_link_shown < $page_count ) {
		echo( " ... " );
	}

	# Next and last links
	if ( $page_number < $page_count ) {
		$disabled = "class='page-numbers'";
	} else {
		$disabled = "disabled class='page-numbers-disabled'";
	}
	echo"<input type=submit name=page_number value='".lang_get('next')."' $disabled>". NEWLINE;
	echo"<input type=submit name=page_number value='".lang_get('last')."' $disabled>". NEWLINE;

	echo"]". NEWLINE;

	echo"</td>". NEWLINE;
	echo"</tr>". NEWLINE;
	echo"</table>". NEWLINE;
}

# ------------------------------------------------------------------------------------
# Prints html for displaying file type icon
# ------------------------------------------------------------------------------------
function html_file_type( $file_name ) {

    $file_type = util_get_filetype( $file_name );

    switch($file_type)
    {
        case 'xls':
            return"<IMG SRC='". ICON_SRC . "/xls.jpg' alt='xls' title='microsoft excel document'>";
            break;
		case 'csv':
            return"<IMG SRC='". ICON_SRC . "/xls.jpg' alt='xls' title='microsoft excel document'>";
            break;
        case 'doc':
            return"<IMG SRC='". ICON_SRC . "/doc.jpg' alt='doc' title='microsoft word document'>";
            break;
        case 'txt':
            return"<IMG SRC='". ICON_SRC . "/file.gif' alt='txt' title='plain text file'>";
            break;
        case 'rtf':
            return"<IMG SRC='". ICON_SRC . "/doc.jpg' alt='rtf' title='rich text document'>";
            break;
        case 'pdf':
            return"<IMG SRC='". ICON_SRC . "/pdf.jpg' alt='pdf' title='pdf'>";
            break;
        case 'html':
            return"<IMG SRC='". ICON_SRC . "/html.jpg' alt='html' title='html document'>";
            break;
        case 'htm':
            return"<IMG SRC='". ICON_SRC . "/htm.jpg' alt='html' title='html document'>";
            break;
        case 'jpg':
            return"<IMG SRC='". ICON_SRC . "/jpg.jpg' alt='jpg' title='jpg image'>";
            break;
        case 'gif':
            return"<IMG SRC='". ICON_SRC . "/gif.jpg' alt='gif' title='gif image'>";
            break;
        default:
            return"&nbsp;";
    }
}

# ------------------------------------------------------------------------------------
# Returns html for displaying info icon
# ------------------------------------------------------------------------------------
function html_info_icon( $text ) {

	if( $text ) {
		return "<img src='images/info.gif' title=\"$text\" alt='Info'>";
	} else {
		return "&nbsp;". NEWLINE;
	}
}

# ------------------------------------------------------------------------------------
# Prints the html for the no records found message
# ------------------------------------------------------------------------------------
function  html_no_records_found_message( $message ) {

	print"<div class=center>". NEWLINE;
	print"<table class=hide100>". NEWLINE;
	print"<tr>". NEWLINE;
	print"<td>$message</td>". NEWLINE;
	print"</tr>". NEWLINE;
	print"</table>". NEWLINE;
	print"</div>". NEWLINE;

}

#------------------------------------------------------------------------------------------
# Prints all html including <html>, <head> and <body> tags, with an operation successful
# message in the body.
#
# INPUT:
# 	page title
#	redirect page
# OUTPUT:
# 	Entire html page with redirect
#------------------------------------------------------------------------------------------
function html_print_operation_successful( $page_title, $redirect_page ) {

	global $db;


	$s_project_properties = session_get_project_properties();
	$project_name = $s_project_properties['project_name'];

	html_window_title();
	html_print_body();
	html_page_title($project_name ." - ". lang_get($page_title) );
	html_page_header( $db, $project_name );
	html_print_menu();
	print"<div class=operation-successful>".lang_get('operation_successful')."</div>";
	html_print_footer();
	html_redirect($redirect_page);
	exit;
}

#------------------------------------------------------------------------------------------
# Prints a road map of where the user is in relation to other release pages.
# The function will loop through an array, format and print out the keys in the same order.
#
# A case can be provided in the switch statement to custom format certain keys, i.e. adding
# a hyperlink to a key.
#
# INPUT:
# 	Array of keys
# OUTPUT:
#	Formatted html roadmap map of where the current pageis in relation to other pages.
#------------------------------------------------------------------------------------------
function html_release_map( $map ) {
	$release_properties = session_get_properties("release");

	$html_top_row 		= "<tr>". NEWLINE;
	$html_bottom_row 	= "<tr>". NEWLINE;

	foreach($map as $key) {
		switch($key) {
		case "release_link":
			$release_name = admin_get_release_name($release_properties['release_id']);

			$html_top_row .= "<td class='sub_menu' nowrap>";
			$html_top_row .= "<b><a href=release_page.php>". lang_get( 'release_name' ) ."</a></b>";
			$html_top_row .= "</td>". NEWLINE;

			$html_bottom_row .= "<td class='sub_menu' nowrap>";
			$html_bottom_row .= "$release_name";
			$html_bottom_row .= "</td>". NEWLINE;
			break;
		case "build_link":
			$build_name = admin_get_build_name($release_properties['build_id']);

			$html_top_row .= "<td class='sub_menu' nowrap>";
			$html_top_row .= "<b><a href=build_page.php>". lang_get( 'build_name' ) ."</a></b>";
			$html_top_row .= "</td>". NEWLINE;

			$html_bottom_row .= "<td class='sub_menu' nowrap>";
			$html_bottom_row .= "$build_name";
			$html_bottom_row .= "</td>". NEWLINE;
			break;
		case "testset_link":
			$testset_name = admin_get_testset_name($release_properties['testset_id']);

			$html_top_row .= "<td class='sub_menu' nowrap>";
			$html_top_row .= "<b><a href=testset_page.php>". lang_get( 'testset_name' ) ."</a></b>";
			$html_top_row .= "</td>". NEWLINE;

			$html_bottom_row .= "<td class='sub_menu' nowrap>";
			$html_bottom_row .= "$testset_name";
			$html_bottom_row .= "</td>". NEWLINE;
			break;
		case "copy_testset_link":
			$testset_name = admin_get_testset_name($release_properties['testset_id']);

			$html_top_row .= "<td class='sub_menu' rowspan=2 nowrap>";
			$html_top_row .= "<b><a href=testset_page.php>". lang_get( 'copy_testset_to' ) ."</a></b>";
			$html_top_row .= "</td>". NEWLINE;

			$html_bottom_row .= "<td></td>". NEWLINE;

			break;
		default:

			$html_top_row .= "<td class='sub_menu' nowrap><b>$key</b></td>". NEWLINE;

			$html_bottom_row .= "<td></td>". NEWLINE;

		}

		$html_top_row .= "<td class='sub_menu' rowspan=2 nowrap>&nbsp;</td>". NEWLINE;
	}

	$html_top_row 		.= "</tr>". NEWLINE;
	$html_bottom_row 	.= "</tr>". NEWLINE;

	print"<table width=1 cellspacing=2>". NEWLINE;
	print$html_top_row;
	print$html_bottom_row;
	print"</table>". NEWLINE;
}

/*

function html_release_map( $map ) {
	$release_properties = session_get_properties("release");

	$html = "<table>". NEWLINE;
	$html .= "<tr>". NEWLINE;
	$html .= "<td>". NEWLINE;

	foreach($map as $key) {
		switch($key) {
		case "release_link":
			$release_name = admin_get_release_name($release_properties['release_id']);

			$html .= "<table align=left>". NEWLINE;
			$html .= "<tr>". NEWLINE;
			$html .= "<td class='sub_menu' nowrap>". NEWLINE;
			$html .= "<b><a href=release_page.php>". lang_get( 'release_name' ) ."</a></b>". NEWLINE;
			$html .= "</td>". NEWLINE;
			$html .= "</tr>". NEWLINE;
			$html .= "<tr>". NEWLINE;
			$html .= "<td class='sub_menu' nowrap>". NEWLINE;
			$html .= "$release_name". NEWLINE;
			$html .= "</td>". NEWLINE;
			$html .= "</tr>". NEWLINE;
			$html .= "</table>". NEWLINE;
			break;
		case "build_link":
			$build_name = admin_get_build_name($release_properties['build_id']);

			$html .= "<table align=left>". NEWLINE;
			$html .= "<tr>". NEWLINE;
			$html .= "<td class='sub_menu' nowrap>". NEWLINE;
			$html .= "<b><a href=build_page.php>". lang_get( 'build_name' ) ."</a></b>". NEWLINE;
			$html .= "</td>". NEWLINE;
			$html .= "</tr>". NEWLINE;
			$html .= "<tr>". NEWLINE;
			$html .= "<td class='sub_menu' nowrap>". NEWLINE;
			$html .= "$build_name". NEWLINE;
			$html .= "</td>". NEWLINE;
			$html .= "</tr>". NEWLINE;
			$html .= "</table>". NEWLINE;
			break;
		case "testset_link":
			$testset_name = admin_get_testset_name($release_properties['testset_id']);

			$html .= "<table align=left>". NEWLINE;
			$html .= "<tr>". NEWLINE;
			$html .= "<td class='sub_menu' nowrap>". NEWLINE;
			$html .= "<b><a href=testset_page.php>". lang_get( 'testset_name' ) ."</a></b>". NEWLINE;
			$html .= "</td>". NEWLINE;
			$html .= "</tr>". NEWLINE;
			$html .= "<tr>". NEWLINE;
			$html .= "<td class='sub_menu' nowrap>". NEWLINE;
			$html .= "$testset_name". NEWLINE;
			$html .= "</td>". NEWLINE;
			$html .= "</tr>". NEWLINE;
			$html .= "</table>". NEWLINE;
			break;
		case "copy_testset_link":
			$testset_name = admin_get_testset_name($release_properties['testset_id']);

			$html .= "<table align=left>". NEWLINE;
			$html .= "<tr>". NEWLINE;
			$html .= "<td class='sub_menu' rowspan=2 nowrap>". NEWLINE;
			$html .= "<b><a href=testset_page.php>". lang_get( 'copy_testset_to' ) ."</a></b>". NEWLINE;
			$html .= "</td>". NEWLINE;
			$html .= "</tr>". NEWLINE;
			$html .= "</table>". NEWLINE;
			break;
		default:
			$html .= "<table align=left>". NEWLINE;
			$html .= "<tr>". NEWLINE;
			$html .= "<td class='sub_menu' nowrap><b>$key</b></td>". NEWLINE;
			$html .= "</tr>". NEWLINE;
			$html .= "</table>". NEWLINE;
		}
	}

	$html .= "</td>". NEWLINE;
	$html .= "</tr>". NEWLINE;
	$html .= "</table>". NEWLINE;

	print $html;

*/

function html_project_manage_map( $map ) {

	$release_properties = session_get_properties("project_manage");
	$project_manage = session_get_properties('project_manage');

	$html = "<table>". NEWLINE;
	$html .= "<tr>". NEWLINE;
	$html .= "	<td>". NEWLINE;

	foreach($map as $key) {

		$hyperlink = "";

		switch($key) {
		case"project_manage_link":
			$hyperlink 	= "project_manage_page.php";
			$text 		= project_get_name($project_manage['project_id']);
			break;
		case"area_tested_link":
			$hyperlink 	= "project_manage_areatested_page.php";
			$text 		= lang_get('area_tested');
			break;
		case"environment_link":
			$hyperlink 	= "project_manage_environment_page.php";
			$text 		= lang_get('environment');
			break;
		case"machine_link":
			$hyperlink 	= "project_manage_machines_page.php";
			$text 		= lang_get('machine');
			break;
		case"reqareacovered_link":
			$hyperlink 	= "project_manage_reqareacovered_page.php";
			$text 		= lang_get('req_area_covered');
			break;
		case"reqdoctype_link":
			$hyperlink 	= "project_manage_reqdoctype_page.php";
			$text 		= lang_get('req_doc_type');
			break;
		case"reqfunctionality_link":
			$hyperlink 	= "project_manage_reqfunctionality_page.php";
			$text 		= lang_get('req_functionality');
			break;
		case"testdoctype_link":
			$hyperlink 	= "project_manage_testdoctype_page.php";
			$text 		= lang_get('test_doc_type');
			break;
		case"testtype_link":
			$hyperlink 	= "project_manage_testtype_page.php";
			$text 		= lang_get('testtype');
			break;
		case"bug_category_link":
			$hyperlink 	= "project_manage_bug_category_page.php";
			$text 		= lang_get('bug_category');
			break;
		case"bug_component_link":
			$hyperlink 	= "project_manage_bug_component_page.php";
			$text 		= lang_get('bug_component');
			break;
		}

		if($hyperlink) {

			$html .= "<table align=left>". NEWLINE;
			$html .= "<tr>". NEWLINE;
			$html .= "<td class='sub_menu' nowrap><b><a href='$hyperlink'>$text</a></b></td>". NEWLINE;
			$html .= "</tr>". NEWLINE;
			$html .= "</table>". NEWLINE;
		} else {

			$html .= "<table align=left>". NEWLINE;
			$html .= "<tr>". NEWLINE;
			$html .= "<td class='sub_menu' nowrap><b>$key</b></td>". NEWLINE;
			$html .= "</tr>". NEWLINE;
			$html .= "</table>". NEWLINE;
		}
	}

	$html .= "	</td>". NEWLINE;
	$html .= "</tr>". NEWLINE;
	$html .= "</table>". NEWLINE;

	print $html;
}

function html_print_tabs($tabs, $selected_tab) {

	$i = 1;

/*
	print"<table class=hide100>". NEWLINE;
	print"<tr>". NEWLINE;
	foreach($tabs as $key => $value) {
		if($i!=$selected_tab) {
			print"<td><a href='$value'>$key</a></td>". NEWLINE;
		} else {
			print"<td>$key</td>". NEWLINE;
		}
		$i++;
	}
	print"</tr>". NEWLINE;
	print"</table>". NEWLINE;
*/
	foreach($tabs as $key => $value) {
		if($i!=$selected_tab) {
			print"<a href='$value'>$key</a>";
		} else {
			print"$key";
		}

		if($i!=sizeof($tabs)) {
			print" | ";
		}
		$i++;
	}
}

# display's a nice indented tree
# $highlighted will BOLD any node with that id
function html_tree( $tree_array, $highlighted=null, $column_map=array() ) {

	global $db;

	# images html
	$img_tree_T 		= "<img align='middle' src='".IMG_SRC."tree_dots_T.gif' alt=''>";
	$img_tree_L 		= "<img align='middle' src='".IMG_SRC."tree_dots_L.gif' alt=''>";
	$img_tree_column 	= "<img align='middle' src='".IMG_SRC."tree_dots_column.gif' alt=''>";
	$img_tree_plus 		= "<img align='middle' src='".IMG_SRC."tree_dots_plus.gif' alt=''>";
	$img_tree_plus_b 	= "<img align='middle' src='".IMG_SRC."tree_dots_plus_b.gif' alt=''>";
	$img_tree_minus 	= "<img align='middle' src='".IMG_SRC."tree_dots_minus.gif' alt=''>";
	$img_tree_minus_b 	= "<img align='middle' src='".IMG_SRC."tree_dots_minus_b.gif' alt=''>";

	$img_spacer = "<img src='".IMG_SRC."1px_transparent.gif' width=15 height=1 alt=''>";

	$column = $img_tree_column.$img_spacer;

	# display each child
	for( $i=0; $i<sizeof($tree_array); $i++ ) {

		//echo"<tr><td valign=bottom>";
		echo$img_spacer;

		foreach($column_map as $column_type) {

			if($column_type=="|") {

				echo$column;
			} elseif($column_type==" ") {

				echo$img_spacer;
			}
		}

		# if the last child record print $table_L
		# else print $table_T
		if( $i == sizeof($tree_array)-1 ) {

			echo $img_tree_L;
			$column_type = array(" ");
		} else {

			echo $img_tree_T;
			$column_type = array("|");
		}

		$style="";

		if($tree_array[$i]['req_id']==$highlighted) {

			$style="style='font-weight: bold; font-size: 120%;'";
		}

		echo"<a $style href='requirement_detail_page.php?req_id=".$tree_array[$i]['req_id']."'>".$tree_array[$i]['req_name']."</a>";

		echo"<br>". NEWLINE;
		//echo"</td></tr>". NEWLINE;

		# display this node's children
		html_tree( $tree_array[$i]['children'], $highlighted, array_merge($column_map, $column_type) );

	}
}

# display's a dynamic tree with +/- boxes to expand/collapse the nodes
#
# $session_variable
#	name of the session variable used to store the expanded nodes
# $tree_array
#	the tree data structure
# $root_node
#	first node from which all others in $tree_array stem from
function html_dynamic_tree( $session_variable, $tree_array, $root_node, $highlighted=null, $column_map=array() ) {

	# Get the expanded tree array from the session
	$s_display_options = session_get_display_options($session_variable);
	$expanded = $s_display_options["filter"]["tree"];

	# Add expanded node (if there is one)
	if( isset($_GET['expand']) ) {

		$expanded[] = $_GET['expand'];
	}

	# Remove collapsed node (if there is one)
	if( isset($_GET['collapse']) ) {

		$expanded = array_diff( $expanded, array($_GET['collapse']) );
	}

	# Create variable to update the session
	$update_expanded = array("tree"=>$expanded);

	# Update the tree variable in the session and get the returned value
	$s_display_options = session_set_display_options($session_variable, $update_expanded);
	$expanded = $s_display_options["filter"]["tree"];

	global $db;

	# images html
	$img_tree_T			= "<img align='absmiddle' border=0 src='".IMG_SRC."tree_dots_T.gif' alt=''>";
	$img_tree_L			= "<img align='absmiddle' border=0 src='".IMG_SRC."tree_dots_L.gif' alt=''>";
	$img_tree_column	= "<img align='absmiddle' border=0 src='".IMG_SRC."tree_dots_column.gif' alt=''>";
	$img_tree_plus		= "<img align='absmiddle' border=0 src='".IMG_SRC."tree_dots_plus.gif' alt=''>";
	$img_tree_plus_b	= "<img align='absmiddle' border=0 src='".IMG_SRC."tree_dots_plus_b.gif' alt=''>";
	$img_tree_minus		= "<img align='absmiddle' border=0 src='".IMG_SRC."tree_dots_minus.gif' alt=''>";
	$img_tree_minus_b 	= "<img align='absmiddle' border=0 src='".IMG_SRC."tree_dots_minus_b.gif' alt=''>";
	$img_tree_folder 	= "<img align='absmiddle' border=0 src='".IMG_SRC."tree_folder.gif' alt=''>";
	$img_tree_folder_b 	= "<img align='absmiddle' border=0 src='".IMG_SRC."tree_folder_b.gif' alt=''>";

	$img_spacer 		= "<img align='absmiddle' border=0 src='".IMG_SRC."1px_transparent.gif' width=15 height=1 alt=''>";

	$column 			= $img_tree_column.$img_spacer;

	# display each child node
	for( $i=0; $i<sizeof($tree_array); $i++ ) {

		$req_id = $tree_array[$i]['uid'];
		$req_version_id = requirement_get_latest_version($req_id);

		echo$img_spacer;

		foreach($column_map as $column_type) {

			if($column_type=="|") {

				echo$column;
			} elseif($column_type==" ") {

				echo$img_spacer;
			}
		}

		# if the last node
		if( $i == sizeof($tree_array)-1 ) {

			# if the last node has no children
			if( empty($tree_array[$i]["children"]) ) {

				echo $img_tree_L;

			# if the last node has children
			} else {

				# if last node has children and the node is in the expanded array
				if( util_array_value_search($req_id, $expanded) ) {

					echo"<a href='?collapse=$req_id#$req_id'>".$img_tree_minus_b.$img_tree_folder_b."</a>";

				# if last node has children and the node is not in the expanded array
				} else {

					echo"<a href='?expand=$req_id#$req_id'>".$img_tree_plus_b.$img_tree_folder."</a>";
				}
			}

			$column_type = array(" ");

		# if not the last node
		} else {

			# if not the last node and the node has no children
			if( empty($tree_array[$i]["children"]) ) {

				echo $img_tree_T;
			} else {

				# if not the last node and the node is in the expanded array
				if( util_array_value_search($req_id, $expanded) ) {

					echo"<a href='?collapse=$req_id#$req_id'>".$img_tree_minus.$img_tree_folder_b."</a>";

				# if not the last node and the node is not in the expanded array
				} else {

					echo"<a href='?expand=$req_id#$req_id'>".$img_tree_plus.$img_tree_folder."</a>";
				}
			}

			$column_type = array("|");
		}

		# prints a closed folder if node has no children and $root_node is set to true
		if( empty($tree_array[$i]["children"]) && $root_node ) {

			echo$img_tree_folder;
		}

		# formatting for highlighted node
		$style="";

		if($req_id==$highlighted) {

			$style="style='font-weight: bold; font-size: 120%;'";
		}

		# print the node name
		echo" <a $style name=$req_id href='requirement_detail_page.php?req_id=$req_id&req_version_id=$req_version_id'>".$tree_array[$i]['name']."</a>";

		echo"<br>". NEWLINE;

		# display this nodes children
		if( util_array_value_search($req_id, $expanded) ) {

			html_dynamic_tree( $session_variable, $tree_array[$i]['children'], false, $highlighted, array_merge($column_map, $column_type) );
		}
	}
}

#------------------------------------------------------------------------------------------
# Prints the filter at the top of the requirements page.
# INPUT:
# 	all the possible values that a user can filter on
# OUTPUT:
#	The requirements filter form
#------------------------------------------------------------------------------------------
function html_print_requirements_filter(	$project_id,
											$filter_doc_type,
											$filter_status,
											$filter_area_covered,
											$filter_functionality,
											$filter_assign_release,
											$filter_per_page=null,
											$filter_show_versions=null,
											$filter_search,
											$filter_priority ) {
	print"<table class=width100>". NEWLINE;
	print"<tr>". NEWLINE;
	print"<td>". NEWLINE;
		print"<table class=inner rules=none border=0>". NEWLINE;

		# TITLES FOR FIRST ROW OF FORM
		print"<tr class=left>". NEWLINE;
		print"<td class=form-header-c>". lang_get('req_type') ."</td>". NEWLINE;
		print"<td class=form-header-c>". lang_get('status') ."</td>". NEWLINE;
		print"<td class=form-header-c>". lang_get('req_area') ."</td>". NEWLINE;

		# if show versions or per page is displayed
		if( !is_null($filter_show_versions) || !is_null($filter_per_page) ) {

			print"<td class=form-header-c>". lang_get('show') ."</td>". NEWLINE;
		}
		else {
			print"<td class='form-header-c'></td>". NEWLINE;
		}
		
		
		if( !is_null($filter_show_versions) ) {
				# SHOW VERSIONS
				print"<td class='left' rowspan=4>". NEWLINE;
				print"<input id=all_versions type='radio' name='show_versions' value='all' ".($filter_show_versions=="all"?"checked":"").">";
				print"<label for=all_versions>".lang_get("all_versions")."</label><br>". NEWLINE;
	
				print"<input id=latest_version type='radio' name='show_versions' value='latest' ".($filter_show_versions=="latest"?"checked":"").">";
				print"<label for=latest_version>".lang_get("latest_version")."</label>". NEWLINE;
				print"</td>". NEWLINE;
	
				//print"<td>&nbsp;</td>". NEWLINE;
	
		}

		print"<td align='center' rowspan=4><input type='submit' value='Filter'></td>". NEWLINE;
	
		print"</tr>". NEWLINE;

		# LIST BOXES FOR FIRST ROW
		print"<tr>". NEWLINE;

		# DOC TYPE
		print"<td align='center'>". NEWLINE;
		print"<select name='doc_type'>". NEWLINE;
		html_print_list_box_from_key_array( requirement_get_types($project_id, $blank=true),
											$selected=$filter_doc_type );
		print"</select>". NEWLINE;
		print"</td>". NEWLINE;

		# STATUS
		print"<td align='center'>". NEWLINE;
		print"<select name='status'>". NEWLINE;
		html_print_list_box_from_array( requirement_get_distinct_field($project_id, REQ_VERS_STATUS, $blank=true),
										$selected=$filter_status );
		print"</select>". NEWLINE;
		print"</td>". NEWLINE;

		# AREA COVERED
		print"<td align='center'>". NEWLINE;
		print"<select name='area_covered'>". NEWLINE;
		html_print_list_box_from_key_array( requirement_get_areas($project_id, $blank=true),
											$selected=$filter_area_covered );
		print"</select>". NEWLINE;
		print"</td>". NEWLINE;
		
		if( !is_null($filter_per_page) ) {
			# PER PAGE
			print"<td align='center'>". NEWLINE;
			print"<input type='text' size='3' maxlength='3' name='per_page' value='$filter_per_page'>". NEWLINE;
			print"</td>". NEWLINE;
		}
		
		print"</tr>";
		
		# TITLES FOR HEADER DIALOG - second row
		print"<tr>";
		print"<td class=form-header-c>". lang_get('functionality') ."</td>". NEWLINE;
		print"<td class=form-header-c>". lang_get('req_assign_release') ."</td>". NEWLINE;
		print"<td class=form-header-c>". lang_get('req_priority') ."</td>". NEWLINE;
		print"<td class=form-header-c>". lang_get('search') ."</td>". NEWLINE;
		
		/*
		if( !is_null($filter_show_versions) ) {
				# SHOW VERSIONS
				print"<td class='left' rowspan=4>". NEWLINE;
				print"<input id=all_versions type='radio' name='show_versions' value='all' ".($filter_show_versions=="all"?"checked":"").">";
				print"<label for=all_versions>".lang_get("all_versions")."</label><br>". NEWLINE;
	
				print"<input id=latest_version type='radio' name='show_versions' value='latest' ".($filter_show_versions=="latest"?"checked":"").">";
				print"<label for=latest_version>".lang_get("latest_version")."</label>". NEWLINE;
				print"</td>". NEWLINE;
	
				//print"<td>&nbsp;</td>". NEWLINE;
	
		}
		*/
		print"</tr>";
		
		# FUNCTIONALITY
		print"<tr>";
		$functions = requirement_get_functionality($project_id);
		$functions[""] = "";
		print"<td align='center'>". NEWLINE;
		print"<select name='functionality'>". NEWLINE;
		html_print_list_box_from_key_array( $functions, $selected=$filter_functionality );
		print"</select>". NEWLINE;
		print"</td>". NEWLINE;
		
		# ASSIGNED TO RELEASE
		print"<td align='center'>". NEWLINE;
		print"<select name='assign_release'>". NEWLINE;
		$rows_releases = requirement_get_all_assoc_releases($project_id, $blank=true);
		html_print_list_box_from_key_array( $rows_releases,$selected=$filter_assign_release );
		print"</select>". NEWLINE;
		print"</td>". NEWLINE;

		# PRIORITY
		print"<td align='center'>". NEWLINE;
		print"<select name='priority'>". NEWLINE;
		$rows_priority = requirement_get_priority();
		html_print_list_box_from_array( $rows_priority, $selected=$filter_priority );
		print"</select>". NEWLINE;
		print"</td>". NEWLINE;
		
		# SEARCH
		print"<td align='center'>". NEWLINE;
		print"<input type='text' size='15' maxlength='25' name='requirement_search' value='" . $filter_search . "'>". NEWLINE;
		print"</td>". NEWLINE;
		
		print"</tr>";
		print"</table>". NEWLINE;
	print"</td>". NEWLINE;
	print"</tr>". NEWLINE;
	print"</table>". NEWLINE;
	
}

#------------------------------------------------------------------------------------------
# Prints the filter at the top of the test page.
# INPUT:
# 	all the possible values that a user can filter on
# OUTPUT:
#	The test filter form
#------------------------------------------------------------------------------------------
function html_print_tests_filter(	$project_id,
									$filter_manual_auto,
									$filter_test_type,
									$filter_ba_owner,
									$filter_qa_owner,
									$filter_tester,
									$filter_area_tested,
									$filter_test_status=null,
									$filter_priority,
									$filter_per_page,
									$filter_search) {


	print"<table class=width100>". NEWLINE;
	print"<tr>". NEWLINE;
	print"<td>". NEWLINE;
		print"<table class=inner rules=none border=0>". NEWLINE;

		# TITLES FOR HEADER DIALOG
		print"<tr class=left>". NEWLINE;
		print"<td class=form-header-c>". lang_get('man_auto') ."</td>". NEWLINE;
		print"<td class=form-header-c>". lang_get('ba_owner') ."</td>". NEWLINE;
		print"<td class=form-header-c>". lang_get('qa_owner') ."</td>". NEWLINE;
		print"<td class=form-header-c>". lang_get('tester') ."</td>". NEWLINE;
		print"<td class=form-header-c>". lang_get('show') ."</td>". NEWLINE;
		print"<td>&nbsp;</td>". NEWLINE;
		print"</tr>". NEWLINE;

		# MANUAL/AUTOMATED
		print"<tr>". NEWLINE;
		print"<td align='center'>". NEWLINE;
		print"<select name='manual_auto'>". NEWLINE;
		$man_auto = test_get_man_auto_values();
		html_print_list_box_from_array( $man_auto, $filter_manual_auto );
		print"</select>". NEWLINE;
		print"</td>". NEWLINE;

		# BA OWNER
		print"<td align='center'>". NEWLINE;
		print"<select name='ba_owner'>". NEWLINE;
		$ba_owners = test_get_test_value($project_id, TEST_BA_OWNER, $blank=true);
		html_print_list_box_from_array( $ba_owners, $filter_ba_owner );
		print"</select>". NEWLINE;
		print"</td>". NEWLINE;

		# QA OWNER
		print"<td align='center'>". NEWLINE;
		print"<select name='qa_owner'>". NEWLINE;
		$qa_owners = test_get_test_value($project_id, TEST_QA_OWNER, $blank=true);
		html_print_list_box_from_array( $qa_owners, $filter_qa_owner );
		print"</select>". NEWLINE;
		print"</td>". NEWLINE;

		# TESTER
		print"<td align='center'>". NEWLINE;
		print"<select name='tester'>". NEWLINE;
		$testers = test_get_test_value($project_id, TEST_TESTER, $blank=true);
		html_print_list_box_from_array( $testers, $filter_tester );
		print"</select>". NEWLINE;
		print"</td>". NEWLINE;

		# PER PAGE
				print"<td align='center'>". NEWLINE;
				print"<input type='text' size='3' maxlength='3' name='per_page' value='" . $filter_per_page . "'>". NEWLINE;
		print"</td>". NEWLINE;
		print"<td align='center'><input type='submit' value='Filter'></td>". NEWLINE;
		print"</tr>". NEWLINE;
		

		print"<tr>";
		print"<td class=form-header-c>". lang_get('testtype') ."</td>". NEWLINE;
		print"<td class=form-header-c>". lang_get('area_tested') ."</td>". NEWLINE;
		if( !is_null($filter_test_status) ) {
			print"<td class=form-header-c>". lang_get('test_status') ."</td>". NEWLINE;
		}
		print"<td class=form-header-c>". lang_get('priority') ."</td>". NEWLINE;
		print"<td class=form-header-c>". lang_get('search') ."</td>". NEWLINE;
		
		print"</tr>";
		
		print"<tr>";
		
		# TEST TYPE
		print"<td align='center'>". NEWLINE;
		print"<select name='test_type'>". NEWLINE;
		$test_type = test_get_test_value($project_id, TEST_TESTTYPE, $blank=true);
		html_print_list_box_from_array( $test_type, $filter_test_type );
		print"</select>". NEWLINE;
		print"</td>". NEWLINE;
		
		# AREA TESTED
		print"<td align='center'>". NEWLINE;
		print"<select name='area_tested'>". NEWLINE;
		$area_tested = test_get_test_value($project_id, TEST_AREA_TESTED, $blank=true);
		html_print_list_box_from_array( $area_tested, $filter_area_tested );
		print"</select>". NEWLINE;
		print"</td>". NEWLINE;
		
		if( !is_null($filter_test_status) ) {
			# TEST STATUS
			print"<td align='center'>". NEWLINE;
			print"<select name='test_status'>". NEWLINE;
			$test_status = test_get_test_value($project_id, TEST_STATUS, $blank=true);
			html_print_list_box_from_array( $test_status, $filter_test_status );
			print"</select>". NEWLINE;
			print"</td>". NEWLINE;
		}

		# PRIORITY
		print"<td align='center'>". NEWLINE;
		print"<select name='priority'>". NEWLINE;
		$priority = test_get_priorities();
		html_print_list_box_from_array( $priority, $filter_priority );
		print"</select>". NEWLINE;
		print"</td>". NEWLINE;

		
		# SEARCH
		print"<td align='center'>". NEWLINE;
		print"<input type='text' size='15' maxlength='25' name='test_search' value='" . $filter_search . "'>". NEWLINE;
		print"</td>". NEWLINE;
		
		
		print"</tr>". NEWLINE;

		print"</table>";
		print"<input type=hidden name=test_form_filter_value value=true>". NEWLINE;
	print"</td>". NEWLINE;
	print"</tr>". NEWLINE;
	print"</table>". NEWLINE;

}

#------------------------------------------------------------------------------------------
# Prints the filter at the top of the test page.
# INPUT:
# 	all the possible values that a user can filter on
# OUTPUT:
#	The test filter form
#------------------------------------------------------------------------------------------
function html_print_testsets_filter($project_id, $filter_build_name, $filter_release_name, $filter_per_page) {


	print"<table class=width100>". NEWLINE;
	print"<tr>". NEWLINE;
	print"<td>". NEWLINE;
		print"<table class=inner rules=none border=0>". NEWLINE;

		# TITLES FOR HEADER DIALOG
		print"<tr class=left>". NEWLINE;
		print"<td class=form-header-c>". lang_get('build_name') ."</td>". NEWLINE;
		print"<td class=form-header-c>". lang_get('release_name') ."</td>". NEWLINE;
		print"<td class=form-header-c>". lang_get('show') ."</td>". NEWLINE;	
		print"<td>&nbsp;</td>". NEWLINE;
		print"</tr>". NEWLINE;

		# BUILD NAME
		print"<tr>". NEWLINE;
		print"<td align='center'>". NEWLINE;
		print"<select name='build_name'>". NEWLINE;
		$build_names = build_get_buildnames($project_id);
		html_print_list_box_from_array( $build_names, $filter_build_name );
		print"</select>". NEWLINE;
		print"</td>". NEWLINE;

		# RELEASE NAME
		print"<td align='center'>". NEWLINE;
		print"<select name='release_name'>". NEWLINE;
		$release_names = release_get_releasenames($project_id);
		html_print_list_box_from_array( $release_names, $filter_release_name );
		print"</select>". NEWLINE;
		print"</td>". NEWLINE;
		
		# PER PAGE
				print"<td align='center'>". NEWLINE;
				print"<input type='text' size='3' maxlength='3' name='per_page' value='" . $filter_per_page . "'>". NEWLINE;
		print"</td>". NEWLINE;

		print"<td align='center'><input type='submit' value='Filter'></td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"<tr><td></td></tr>". NEWLINE;
	print"</table>". NEWLINE;
	print"</table>". NEWLINE;

}

#------------------------------------------------------------------------------------------
# Prints the filter at the top of the bug page.
# INPUT:
# 	all the possible values that a user can filter on
# OUTPUT:
#	The bug filter form
#------------------------------------------------------------------------------------------
function html_print_bug_filter(	$project_id, $filter_bug_status, $filter_bug_category,
								$filter_bug_component, $filter_reported_by, $filter_assigned_to, $filter_assigned_to_developer, $filter_found_in_rel, $filter_assigned_to_rel, $filter_per_page, $filter_view_closed, $filter_search, $filter_jump  ) {

	
	print"<table class=width100>". NEWLINE;
	print"<tr>". NEWLINE;
	print"<td>". NEWLINE;
		print"<table class=inner rules=none border=0>". NEWLINE;

		# TITLES FOR HEADER DIALOG
		print"<tr class=left>". NEWLINE;
		print"<td class=form-header-c>". lang_get('reported_by') ."</td>". NEWLINE;
		print"<td class=form-header-c>". lang_get('assigned_to') ."</td>". NEWLINE;
		print"<td class=form-header-c>". lang_get('assigned_to_developer') ."</td>". NEWLINE;
		print"<td class=form-header-c>". lang_get('found_in_release') ."</td>". NEWLINE;
		print"<td class=form-header-c>". lang_get('assigned_to_release') ."</td>". NEWLINE;
		print"<td class=form-header-c>". lang_get('show') ."</td>". NEWLINE;
		print"<td>&nbsp;</td>". NEWLINE;
		print"</tr>". NEWLINE;

		# REPORTED BY - all users
		print"<td align='center'>". NEWLINE;
		print"<select name='reported_by'>". NEWLINE;
		$reported_by = user_get_usernames_by_project($project_id, $blank=true);
		html_print_list_box_from_array( $reported_by, $filter_reported_by );
		print"</select>". NEWLINE;
		print"</td>". NEWLINE;

		# ASSIGNED TO - all users?? or all users with bug_assign_status
		print"<td align='center'>". NEWLINE;
		print"<select name='assigned_to'>". NEWLINE;
		$assigned_to = user_get_usernames_by_project($project_id, $blank=true);
		html_print_list_box_from_array( $assigned_to, $filter_assigned_to );
		print"</select>". NEWLINE;
		print"</td>". NEWLINE;

		# ASSIGNED TO DEVELOPER
		print"<td align='center'>". NEWLINE;
		print"<select name='assigned_to_developer'>". NEWLINE;
		$assigned_to_developer = user_get_usernames_by_project($project_id, $blank=true);
		html_print_list_box_from_array( $assigned_to_developer, $filter_assigned_to_developer );
		print"</select>". NEWLINE;
		print"</td>". NEWLINE;

		# FOUND IN RELEASE
		print"<td align='center'>". NEWLINE;
		print"<select name='found_in_release'>". NEWLINE;
		$found_in_release = admin_get_all_release_names( $project_id, $blank=true );
		html_print_list_box_from_array( $found_in_release, $filter_found_in_rel );
		print"</select>". NEWLINE;
		print"</td>". NEWLINE;

		# ASSIGNED TO RELEASE
		print"<td align='center'>". NEWLINE;
		print"<select name='assigned_to_release'>". NEWLINE;
		$assigned_to_release = admin_get_all_release_names( $project_id, $blank=true );
		html_print_list_box_from_array( $assigned_to_release, $filter_assigned_to_rel );
		print"</select>". NEWLINE;
		print"</td>". NEWLINE;

		# PER PAGE
		print"<td align='center'>". NEWLINE;
		print"<input type='text' size='3' maxlength='3' name='per_page' value='" . $filter_per_page . "'>". NEWLINE;
		print"</td>". NEWLINE;

		print"<td align='center' rowspan=4 valign=center><input type='submit' value='Filter'></td>". NEWLINE;
		print"</tr>". NEWLINE;


		# SECOND ROW OF FILTERS
		print"<tr class='left'>". NEWLINE;
		print"<td class=form-header-c>". lang_get('bug_status') ."</td>". NEWLINE;
		print"<td class=form-header-c>". lang_get('bug_category') ."</td>". NEWLINE;
		print"<td class=form-header-c>". lang_get('bug_component') ."</td>". NEWLINE;
		print"<td class=form-header-c>". lang_get('view_closed') ."</td>". NEWLINE;
		print"<td class=form-header-c>". lang_get('search') ."</td>". NEWLINE;
		print"<td class=form-header-c>". lang_get('jump') ."</td>". NEWLINE;
		print"<td>&nbsp;</td>". NEWLINE;
		print"</tr>". NEWLINE;

		print"<tr>". NEWLINE;

		# STATUS
		print"<td align='center'>". NEWLINE;
		print"<select name='status'>". NEWLINE;
		$bug_status = bug_get_status( true );
		html_print_list_box_from_array( $bug_status, $filter_bug_status );
		print"</select>". NEWLINE;
		print"</td>". NEWLINE;

		# CATEGORY
		print"<td align='center'>". NEWLINE;
		print"<select name='category'>". NEWLINE;
		html_print_list_box_from_key_array( bug_get_categories( $project_id, $blank=true ), $selected=$filter_bug_category);
		print"</select>". NEWLINE;
		print"</td>". NEWLINE;

		# COMPONENT
		print"<td align='center'>". NEWLINE;
		print"<select name='component'>". NEWLINE;
		html_print_list_box_from_key_array( bug_get_components( $project_id, $blank=true ), $selected=$filter_bug_component);
		print"</select>". NEWLINE;
		print"</td>". NEWLINE;

		# VIEW CLOSED
		print"<td align='center'>". NEWLINE;
		print"<select name='view_closed'>". NEWLINE;
		$view_closed_options = array( lang_get('yes'), lang_get('no') );
		html_print_list_box_from_array( $view_closed_options, $filter_view_closed );
		print"</select>". NEWLINE;
		print"</td>". NEWLINE;

		# SEARCH
		print"<td align='center'>". NEWLINE;
		print"<input type='text' size='15' maxlength='25' name='bug_search' value='" . $filter_search . "'>". NEWLINE;
		print"</td>". NEWLINE;

		# JUMP
		print"<td align='center'>". NEWLINE;
		print"<input type='text' size='6' maxlength='6' name='filter_jump' value='" . $filter_jump . "'>". NEWLINE;
		print"</td>". NEWLINE;

		print"</tr>". NEWLINE;

		print"</table>". NEWLINE;
		print"<input type=hidden name=bug_form_filter_value value=true>". NEWLINE;
	print"</td>". NEWLINE;
	print"</tr>". NEWLINE;
	print"</table>". NEWLINE;

}

#------------------------------------------------------------------------------------------
# Prints the filter at the top of the test page.
# INPUT:
# 	all the possible values that a user can filter on
# OUTPUT:
#	The test filter form
#------------------------------------------------------------------------------------------
function html_print_field_filter( $filter_screen, $filter_search) {

	$s_project_properties	= session_get_project_properties();
	$project_id				= $s_project_properties['project_id'];

	print"<table class=width60>". NEWLINE;
	print"<tr>". NEWLINE;
	print"<td>". NEWLINE;
		print"<table class=inner rules=none border=0>". NEWLINE;

		# TITLES FOR HEADER DIALOG
		print"<tr class=left>". NEWLINE;
		print"<td class=form-header-c>". lang_get('screen_name') ."</td>". NEWLINE;
		print"<td class=form-header-c>". lang_get('search') ."</td>". NEWLINE;
		print"<td>&nbsp;</td>". NEWLINE;
		print"</tr>". NEWLINE;

		print"<tr>". NEWLINE;

		# SCREEN_NAMES
		$screens = test_get_screens( $project_id, SCREEN_NAME, "ASC" );
		$screen_array = array();
		foreach( $screens as $screen ) {
			$screen_array[$screen[SCREEN_ID]] = $screen[SCREEN_NAME];
		}
		print"<td align=center>". NEWLINE;
		print"<select name='filter_screen'>". NEWLINE;
		print"<option value=''></option>". NEWLINE;
			html_print_list_box_from_key_array( $screen_array,  $filter_screen );
		print"</select>". NEWLINE;
		print"</td>". NEWLINE;

		# SEARCH
		print"<td align='center'>". NEWLINE;
		print"<input type='text' size='15' maxlength='25' name='filter_search' value='" . $filter_search . "'>". NEWLINE;
		print"</td>". NEWLINE;
		
		# FILTER BUTTON
		print"<td align='center' rowspan=4 valign=center><input type='submit' value='Filter'></td>". NEWLINE;
		
		print"</tr>". NEWLINE;

		print"</table>";
		print"<input type=hidden name=field_form_filter_value value=true>". NEWLINE;
	print"</td>". NEWLINE;
	print"</tr>". NEWLINE;
	print"</table>". NEWLINE;

}

#------------------------------------------------------------------------------------------
# Prints the FCKeditor code if session_use_FCKeditor() returns true,
# otherwise prints out a normal <textarea>.
# INPUT:
# 	name of field
#	width in pixels
#	height in pixels
#	default value of field
#------------------------------------------------------------------------------------------
function html_FCKeditor($name, $width, $height, $value="") {

	# Set minimum height and width of the FCKeditor
	if( $width < 510 ) {
		$width = 510;
	}
	if( $height < 120 ) {
		$height = 120;
	}

	# if using FCKeditor
	if( session_use_FCKeditor() ) {

		$FCKeditor = new FCKeditor($name);
		$FCKeditor->BasePath			= FCK_EDITOR_BASEPATH;
		$FCKeditor->Config['SkinPath']	= $FCKeditor->BasePath.'editor/skins/office2003/';
		$FCKeditor->ToolbarSet			= "RTH";
		$FCKeditor->Width				= $width."px";
		$FCKeditor->Height				= $height."px";
		$FCKeditor->Value				= $value;
		$FCKeditor->Create();

	# if not using FCKeditor
	} else {

		# work out number of rols and cols for the text area.
		# this is ~ 8 pixels per character and 20 pixels per row
		$cols = round($width/8);
		$rows = round($height/20);

		$value = util_strip_html_tags( $value );

		print"<textarea rows=$rows cols=$cols name='$name'>$value</textarea>";
	}
}

#------------------------------------------------------------------------------------------
# Prints one teststep row and parses the action, input and results column.
# If any of these columns includes an hyperlink according an fixed patter
# the link will made clickable
# INPUT:
# 	action of teststep
#	input of teststep
#	resutls of teststep
#------------------------------------------------------------------------------------------
function html_print_teststep_with_hyperlinks($info_step_class, $action, $input, $result)
{
	$step_action = replace_uri($action);
	$step_test_inputs = replace_uri($input);
	$step_expected = replace_uri($result);
	print"<td align=left><div $info_step_class>$step_action</div></td>". NEWLINE;
	print"<td align=left><div $info_step_class>$step_test_inputs</div></td>". NEWLINE;
	print"<td align=left><div $info_step_class>$step_expected</div></td>". NEWLINE;
}

#------------------------------------------------------------------------------------------
# replace URIs with appropriate HTML code to be clickable.
#------------------------------------------------------------------------------------------
function replace_uri($str) {
  $pattern = '#(^|[^\"=]{1})(http://|ftp://|mailto:|news:)([^\s<>]+)([\s\n<>]|$)#sm';
  return preg_replace($pattern,"\\1<a href=\"\\2\\3\" target=\"_blank\"><u>\\2\\3</u></a>\\4",$str);
}

# ------------------------------------
# $Log: html_api.php,v $
# Revision 1.23  2009/02/03 12:44:34  sca_gs
# removed copyright notice
#
# Revision 1.22  2008/08/08 09:30:25  peter_thal
# added direct navigate to testid function above project switch select box
#
# Revision 1.21  2008/08/04 06:55:01  peter_thal
# added sorting function to several tables
#
# Revision 1.20  2008/07/25 09:50:07  peter_thal
# added lock testset feature
# disabled detail column in test result, because functionality is not implemented yet
#
# Revision 1.19  2008/07/17 13:54:12  peter_thal
# added new feature: test sets status (overview)
# +fixed some bugs with project_id parameter in testdetail_page references
#
# Revision 1.18  2008/01/22 08:28:14  cryobean
# added function for not sortable headers
#
# Revision 1.17  2007/11/19 08:59:01  cryobean
# bugfixes
#
# Revision 1.16  2007/11/15 12:58:48  cryobean
# bugfixes
#
# Revision 1.15  2007/03/14 17:45:52  gth2
# removing code that passes varables by reference - gth
#
# Revision 1.14  2007/02/25 23:17:41  gth2
# fixing bugs for release 1.6.1 - gth
#
# Revision 1.13  2007/02/06 03:28:12  gth2
# correct email problem when updating test results - gth
#
# Revision 1.12  2007/02/03 11:58:37  gth2
# no message
#
# Revision 1.11  2007/02/03 10:25:04  gth2
# no message
#
# Revision 1.10  2007/02/02 04:26:27  gth2
# adding version information to the footer of each page - gth
#
# Revision 1.9  2006/10/05 02:42:18  gth2
# adding file upload to the bug page - gth
#
# Revision 1.8  2006/09/25 12:46:37  gth2
# Working on linking rth and other bugtrackers - gth
#
# Revision 1.7  2006/08/05 22:31:46  gth2
# adding NEWLINE constant to support mulitple OS - gth
#
# Revision 1.6  2006/05/08 15:38:10  gth2
# Changing formatting - gth
#
# Revision 1.5  2006/05/03 21:52:43  gth2
# adding screen and field to menu - gth
#
# Revision 1.4  2006/01/20 02:36:03  gth2
# enable export to excel functionaltiy - gth
#
# Revision 1.3  2006/01/16 13:27:48  gth2
# adding excel integration - gth
#
# Revision 1.2  2005/12/13 13:59:53  gth2
# Completed the addition of requirement priority - gth
#
# Revision 1.1.1.1  2005/11/30 23:01:12  gth2
# importing initial version - gth
#
# ------------------------------------
?>