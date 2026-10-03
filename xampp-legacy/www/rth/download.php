<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Download Page
#
# $RCSfile: download.php,v $  $Revision: 1.3 $
# ------------------------------------
 /*******************************************************************
  * download.php
  *
  * This PHP script sends a file in such a way that most web clients
  * will offer to download the file to the client computer. It uses
  * the Content-Disposition headeer extension to RFC2616
  * (see http://www.w3.org/Protocols/rfc2616/rfc2616.html)
  * to suggest the web client should download the file. This is
  * implemented on most (but not all) web clients. I have tested it
  * on Mozilla, Netscape 4.78 and 6.21, Internet Explorer 5.5, lynx,
  * Konqueror and Opera. It works fully on all.
  *
  * Usage: download.php?upload_filename=name_of_file.extension
  *
  * Examples: to download the SPSS file data.sav from index.html
  * where download.php, index.html and data.sav are all in the 
  * same directory, put a link in index.html of the form
  * <a href="download.php?data.sav">Download SPSS data file</a>.
  * You can use paths in the filename, as in
  * <a href="download.php?../include/data.sav">Download data</a>.
  *
  * You can specialise the code by putting a line of the form
  * $filename="data.sav";
  * immediately after this comment. This will allow you to send
  * exactly one file for download, viz data.sav.
  * 
  * Only one variable, $filename, is not defined by default. In
  * principle, you can send a the name of the file to download
  * through a POST request (e.g. on a form button). I haven't
  * tested this.
  *
  * Restrictions: by default you can't download files with the
  * extensions html, phtml, htm, phtm, inc, php or php3. This is to
  * avoid potential security problems. For example, it is possible
  * to use a PHP file to hide sensitive data such as the password
  * to connect to an SQL server. If we allowed this script to offer
  * php scripts for download, then a client request of the form
  * http://../download.php?sensitive.php could show the raw php file.
  *
  * Security issues: see the comments under Restrictions above. If
  * in doubt, define $filename immediately after this comment and
  * use a separate script for each downloadable file. I've tried
  * using header( "Location: ... " ) to retrieve the file. It doesn't
  * work on a solaris server, but does work on gnu/linux.
  *******************************************************************/
require_once('./api/properties_inc.php');

$filename  = $_GET['upload_filename'];
$shortname = basename( $filename );

if(substr($shortname, 0, 1) == 'R'){
    $shortname = substr( $shortname, 14);
}
else{
    $shortname = substr( $shortname, 17);
}

if( file_exists( $filename )
	&& realpath($filename)!='' // the file has a real path
   	&& realpath(FILE_UPLOAD_PATH)!='' // the file upload directory has a real path
   	&& (strpos(realpath($filename),realpath(FILE_UPLOAD_PATH)) !== false) // the file is _in_ the file upload directory
){
	// sanity check
    //&& !eregi( "p?html?", $filename ) // security check
    //&& !eregi( "inc", $filename )
    //&& !eregi( "php3?", $filename ) )
    //&& {
  $realpath = realpath(FILE_UPLOAD_PATH);
  $path = FILE_UPLOAD_PATH;
  $size = filesize( $filename ); 
  header("Content-Type: application/save"); 
  header("Content-Length: $size");
  header("Content-Disposition: attachment; filename=$shortname"); 
  header("Content-Transfer-Encoding: binary"); 
  $fh = fopen("$filename", "r"); 
  fpassthru($fh); 
  exit; 
} else {
?> 
<!DOCTYPE HTML PUBLIC "-//W3C//DTD 4.01 Transitional//EN"
   "http://www.w3.org/TR/1999/REC-html401-19991224/loose.dtd">
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Download error</title>
 <style type="text/css">
   <!--
   body {background-image:url(include/background.gif);
         font-family:helvetica,arial,sans-serif}
   a:hover {text-decoration:none; border-width:thin; border-style:dotted;
            background-color:#f2f2ff; color:#000000}
   a:focus {text-decoration:none; background-color:#dadae6; color:#000000}
   a:active {text-decoration:none; background-color:#ffffff; color:#000000}
   -->
 </style>
</head>
<body>
<h1>File <?php print( $basename ) ?> not available</h1>
<p>
  Either the file you requested does not exist or you are not permitted to
  download it using this page.
</p>
</body>
</html>
<?php
}

# ------------------------------------
# $Log: download.php,v $
# Revision 1.3  2008/07/10 08:39:19  peter_thal
# fixed security leak, now files can only be downloaded from upload directory
#
# Revision 1.2  2006/01/09 02:02:12  gth2
# fixing some defects found while writing help file
#
# Revision 1.1.1.1  2005/11/30 23:00:56  gth2
# importing initial version - gth
#
# ------------------------------------
?>

