<?
/********* 
Example 1
filename: browse.php
Platform: Win2k
Webserver: IIS5
PHP Version: PHP4.02
*********/

$url = "/tms/userimages/";

$base = "c:\\inetpub\\wwwroot\\tms\\userimages\\";

$path = $base.$dir."/";



if (!empty($dir)) 
{

$html_img_lst = "Directory: <b>$dir</b><br><a href=\"javascript:history.go(-1)\"><--Back</a><br>\n";

if ($list = opendir($path)) 
{
while (false !== ($file = readdir($list)))
{
if (is_file($path.$file)) 
{
if ($file != "." && $file != "..") 
{

$file = $dir."/".$file; 
$html_img_lst .= "<a href=\"javascript:getImage('$file');\">$file</a><br>\n";
}
}
}
}

} else {

$html_img_lst = get_dir($base);

}

function get_dir($base)
{
if ($dir = opendir($base)) 
{
while (false !== ($file = readdir($dir)))
{
if (is_dir($base.$file)) 
{
if ($file != "." && $file != "..") 
{
$dir_list .= "[<a href=\"?dir=$file\">$file</a>]<br>\n";
}
} else {
$dir_list .= "<a href=\"javascript:getImage('$file');\">$file</a><br>\n";
}
}
}
return $dir_list;
}




?>

<HTML>
<HEAD>
<TITLE>Image Browser</TITLE>
<LINK rel="stylesheet" type="text/css" href="../../css/fck_dialog.css">


<SCRIPT language="javascript">
var sImagesPath = "<?php echo $url; ?>";
var sActiveImage = "" ;

function getImage(imageName)
{
sActiveImage = sImagesPath + imageName ;
imgPreview.src = sActiveImage ;
}

function ok()
{
window.setImage(sActiveImage) ;
window.close() ;
}
</SCRIPT>

</HEAD>
<BODY bottommargin="5" leftmargin="5" topmargin="5" rightmargin="5">

<table width="100%" border="0" height="100%">
<tr>
<td width="25%" height="90%"><DIV class="ImagePreviewLinks"><?php echo $html_img_lst ?></DIV></td>
<td width="75%" height="90%"><DIV class="ImagePreviewArea"><IMG id="imgPreview" border=1 width=200 height=200></DIV></td>
</tr>
<tr>
<td width="25%" height="10%"><INPUT style="WIDTH: 80px" type="button" value="OK" onclick="ok();"></td>
<td width="75%" height="10%"><INPUT style="WIDTH: 80px" type="button" value="Cancel" onclick="window.close();"></td>
</tr>
</table>
<BR>
</BODY>
</HTML> 