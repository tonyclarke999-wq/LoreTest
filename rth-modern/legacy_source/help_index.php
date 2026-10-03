<?php
include_once"./api/include_api.php";
auth_authenticate_user();

html_window_title();
html_print_body();

print"<p class='help-page-title'>". lang_get('help_index') ."</p>". NEWLINE;

print"<ul>". NEWLINE;
print"<li class='help-link'><a href='help_requirements.php'>Requirements</a>". NEWLINE;
print"<li class='help-link'><a href='help_tests.php'>Tests</a>". NEWLINE;
print"<li class='help-link'><a href='help_testset.php'>Adding/Editing a Release, Build, or Test Set</a>". NEWLINE;
print"<li class='help-link'><a href='help_view_results.php'>Test Results</a>". NEWLINE;
print"<li class='help-link'><a href='help_run_mantest.php'>Running a Test</a>". NEWLINE;
#print"<li class='help-link'><a href='help_edit_entry.php'>Adding/Editing Calendar Entries</a>". NEWLINE;
print"<li class='help-link'><a href='help_reports.php'>Reports</a>". NEWLINE;
print"<li class='help-link'><a href='help_user_add.php'>Adding a User</a>". NEWLINE;
print"<li class='help-link'><a href='help_preferences.php'>Preferences</a>". NEWLINE;
print"<li class='help-link'><a href='help_docs.php'>Documents Uploading/Downloading</a>". NEWLINE;
print"<li class='help-link'><a href='help_project.php'>Managing a Project</a>". NEWLINE;
print"<li class='help-link'><a href='help_faq.php'>FAQ</a>". NEWLINE;
print"</ul>". NEWLINE;

print"<br><br><br><br><br>". NEWLINE;

html_print_footer();
?>