<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# FCKeditor Page
#
# $RCSfile: FCKeditor.php,v $    $Revision: 1.1.1.1 $
# ------------------------------------
include("FCKeditor/fckeditor.php") ;
?>
<html>
  <head>
    <title>FCKeditor - Sample</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  </head>
  <body>
    <form action="savedata.php" method="post">
<?php
$oFCKeditor = new FCKeditor('FCKeditor1') ;
$oFCKeditor->BasePath = '/FCKeditor/';
$oFCKeditor->Value = 'Default test in editor';
$oFCKeditor->Create() ;

print_r($_SERVER);
?>
      <br>
      <input type="submit" value="Submit">
    </form>
  </body>
</html>

<?php

# ------------------------------------
# $Log: FCKeditor.php,v $
# Revision 1.1.1.1  2005/11/30 23:00:56  gth2
# importing initial version - gth
#
# ------------------------------------
?>