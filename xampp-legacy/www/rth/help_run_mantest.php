<?php
include_once"./api/include_api.php";
auth_authenticate_user();

html_window_title();
html_print_body();


print"<br>". NEWLINE;
print"<p class='help-page-title'>". lang_get('help_run_test') ."</p>". NEWLINE;

print"<base target='_self'>". NEWLINE;


print"<ul>". NEWLINE;
print"<li class='help-link'><a href='#results_overview'>". lang_get('help_results_overview') ."</a>". NEWLINE;
print"<li class='help-link'><a href='#results_view_tests'>". lang_get('help_results_view_tests') ."</a>". NEWLINE;
print"<li class='help-link'><a href='#results_test_run'>". lang_get('help_results_view_testrun') ."</a>". NEWLINE;
print"<li class='help-link'><a href='#results_verifications'>". lang_get('help_results_view_verifications') ."</a>". NEWLINE;
print"</ul>". NEWLINE;

print"<table>";

print"<tr>". NEWLINE;
print"<td class='help-title'><a href='help_index.php'>". lang_get('help_return_to_index') ."</a></td>". NEWLINE;
print"</tr>". NEWLINE;

util_add_spacer();

# RESULTS OVERVIEW
print"<tr>";
print"<td class='help-title'><a name='results_overview'>". lang_get('help_results_overview') ."</a></td>";
print"</tr>";
print"<tr>";
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp; </td>".NEWLINE;
print"</tr>";


print"<td><a href='#Top'>Back To Top</a></td>". NEWLINE;
util_add_spacer();


# RESULTS - VIEW TESTS
print"<tr>";
print"<td class='help-title'><a name='results_view_tests'>". lang_get('help_results_view_tests') ."</a></td>";
print"</tr>". NEWLINE;
print"<tr>";
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp; </td>".NEWLINE;
print"</tr>";
print"<tr>";


print"<td><a href='#Top'>Back To Top</a></td>". NEWLINE;
util_add_spacer();

# RESULTS - TEST RUN
print"<tr>".NEWLINE;
print"<td class='help-title'><a name='results_test_run'>". lang_get('help_results_view_testrun') ."</a></td>";
print"</tr>". NEWLINE;
print"<tr>".NEWLINE;
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp; </td>".NEWLINE;
print"</tr>";


print"<td><a href='#Top'>Back To Top</a></td>". NEWLINE;
util_add_spacer();

# RESULTS - VERIFICATIONS
print"<tr>";
print"<td class='help-title'><a name='results_verifications'>". lang_get('help_results_view_verifications') ."</a></td>";
print"</tr>". NEWLINE;
print"<tr>".NEWLINE;
print"<td class='help-text'>&nbsp;&nbsp;&nbsp;&nbsp; </td>".NEWLINE;
print"</tr>".NEWLINE;

print"<td><a href='#Top'>Back To Top</a></td>". NEWLINE;
util_add_spacer();


print"<tr>". NEWLINE;
print"<td class='help-title'><a href='help_index.php'>". lang_get('help_return_to_index') ."</a></td>". NEWLINE;
print"</tr>". NEWLINE;
util_add_spacer();

print"</table>";

?>