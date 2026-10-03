<?php

include"./api/include_api.php";
auth_authenticate_user();

$project_properties     = session_get_project_properties();
$project_name           = $project_properties['project_name'];
$project_id				= $project_properties['project_id'];

html_window_title();
html_print_body();
html_page_title($project_name ." - ". lang_get('testset_time_remaining_page') );
//html_page_header( $db, $project_name );

$testset_id			= $_GET['testset_id'];
$testset_name		= testset_get_name( $testset_id );
$duration			= results_calculate_total_duration( $testset_id );
$remaining_duration = results_calculate_remaining_duration( $testset_id, $duration );

print"<br><br>". NEWLINE;

print"<table class=width100>". NEWLINE;

print"<tr>". NEWLINE;
print"<td class='form-lbl-l' width='50%'>". lang_get('est_time_for_testset') ." $testset_name</td>". NEWLINE;
print"<td width='50%'>&nbsp;</td>". NEWLINE;
print"</tr>". NEWLINE;

util_add_spacer();

print"<tr>". NEWLINE;
print"<td class='form-lbl-r'>". lang_get('est_total_time') .": </td>". NEWLINE;
print"<td class='form-data-l'>$duration</td>". NEWLINE;
print"</tr>". NEWLINE;


print"<tr>". NEWLINE;
print"<td class='form-lbl-r'>". lang_get('est_time_remaining') .": </td>". NEWLINE;
print"<td class='form-data-l'>$remaining_duration</td>". NEWLINE;
print"</tr>". NEWLINE;

print"</table>". NEWLINE;

print"<br><br><br><br><br><br>". NEWLINE;

html_print_footer();


?>