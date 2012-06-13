<?
	
/*
 * Project:     YAPPA : Yet Another PHP Photo Album
 * Author:      Federico Feroldi <pix@pixzone.com>
 * Copyright:   2001 Federico Feroldi
 * $Header: /cvsroot/yappa/yappa/index.php,v 1.17 2002/04/23 16:09:56 pixzone Exp $
 *              
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * You may contact the author of YAPPA by e-mail at:
 * pix@pixzone.com
 *
 * The latest version of YAPPA can be obtained from:
 * http://www.pixzone.com/dev/
 *
 */

include_once("main.php");

?>

<!-- #### HTML starts here #### -->
<html>

<head>
<title><? print $config["title"]; ?></title>
<link rel=stylesheet type="text/css" href="default.css">
</head>

<body topmargin="0" leftmargin="0" bgcolor="#FFFFFF">

<table width="100%" cellspacing="0" cellpadding="5" border="0">
<tr class="menubar_cell" valign="bottom"><td>
<font class="text_title"><? print $config["title"]; ?></font>
<font class="text_albumpath">
 : 
<? 
foreach($page["album_path"] as $album_path_item) {
?><a href="<? print $album_path_item["link"] ?>"><? print $album_path_item["name"] ?></a> / <? 
}
?>
</font>
</td>
</tr>
</table>

<table cellspacing="0" cellpadding="5" border="0">
<tr valign="top">
<? 
if($page["mode"] == "album") {
?>
<td class="menubar_cell">
<table width="100%" border="0" cellspacing="0" cellpadding="3" class="subalbums_bar">
<?
if($page["album_link_up"]) { ?>
<tr>
<td><a href="<? print $page["album_link_up"] ?>"><img src="icons/up.png" border="0" width="24" height="24"></a></td>
<td><a href="<? print $page["album_link_up"] ?>"><b>Back</b></a></td>
</tr>
<? } 
    if(count($page["subalbums"]) > 0) {
        $album_count = 0;
        foreach($page["subalbums"] as $album) { 
?>
<tr class="album_cell_<? print ($album_count & 1) == 0 ? 'a' : 'b' ?>">
<td><a href="<? print $album["url"] ?>"><img src="icons/album.png" border="0" width="24" height="24"></a></td>
<td><a href="<? print $album["url"] ?>"><i><? print
$album["title"] ? $album["title"] : $album["name"] ?></i></a><br>
<font class="text_album_nav_info"><? print $album["images_count"] ?> images
<? if($album["subalbums_count"] > 0) { ?>, <? print $album["subalbums_count"] ?> albums</font><? } ?></td>
</tr>
<?
        $album_count++;
    }
}
?>
</table>
</td><td>
<?
	if($page["thumbnails_tot"] > 0) {
?>
<!-- #### thumbnails/ #### -->
<table cellspacing="3" cellpadding="10" width="<? print $page["thumb_table_width"]; ?>">
<tr><td class="thumbnail_cell" colspan="<? print $config["thumbs_per_row"]; ?>">
<font class="text_album_title"><? print $page["album_title"] ? $page["album_title"] : $page["album_name"]; ?></font>
<font class="text_album_info">
[
<? print $page["album_images"]; ?> images,
<? print $page["thumb_max_page"]; ?> pages
<? if($page["album_hits"] > 1) { ?>, <? print $page["album_hits"]; ?> hits<? } ?>
]
</font>
<br>
<? if($page["album_comment"]) { ?><font class="text_album_comment"><? print $page["album_comment"] ?></font><br><? } ?>
<!-- next/prev page links - start -->
<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr>
<td width="30%" align="left"><? 
    if($page["navlink_previous"]) {
?><font class="text_navlink"><a href="<? print $page["navlink_previous"]; ?>">&lt; previous page</a></font><?    
    }
?></td>
<td width="40%" align="center">
<table border="0" cellspacing="1" cellpadding="1"><tr>
<td><? if($page["navlink_back"]) { ?>
<font class="text_navlink"><a href="<? print $page["navlink_back"]; ?>">&lt;&lt;</a>&nbsp;</font>
<? } ?></td>
<?        
	foreach($page["navlinks"] as $navlink) {
?><td><font class="text_navlink"><?
		if($navlink["selected"]) {
            print "<b>" . $navlink["page"] . "</b>&nbsp;";
		} else {
?><a href="<? print $navlink["link"]; ?>"><? print $navlink["page"]; ?></a>&nbsp;<?
		}
?></font></td><?
	}
?>
<td><? if($page["navlink_forw"]) { ?>
<font class="text_navlink"><a href="<? print $page["navlink_forw"]; ?>">&gt;&gt;</a></font>
<? } ?></td>
</tr></table>

</td>
<td width="30%" align="right"><? 
    if($page["navlink_next"]) {
?><font class="text_navlink"><a href="<? print $page["navlink_next"]; ?>">next page &gt;</a></font><?
    }
?></td>
</tr></table>
<!-- next/prev page links - end -->

</td></tr>
<?
    $thumb_count = 0;        
	foreach($page["thumbnails"] as $thumb) {
        if(($thumb_count % $config["thumbs_per_row"]) == 0) {
            print "<tr>";
        }
?>
<td align="center" class="thumbnail_cell" width="<? print $page["thumbnail_cell_percentwidth"]; ?>%">
<table border="0" cellspacing="0" cellpadding="3" bgcolor="#BBBBBB"><tr>
<td align="center"><a href="<? print $thumb["link"]; ?>"><img src="<? print $thumb["src"]; ?>" <? print $thumb["size_html"]; ?>" alt="<? print
$thumb["alt"]; ?>" border="0"></a><br><a class="link_image" href="<? print $thumb["link"]; ?>"><? print $thumb["alt"]; ?></a></td>
</tr></table>
<? if($thumb["caption"]) { ?>
<table width="<? print $config["thumb_width"]; ?>"><tr>
<td><font class="text_image_caption"><? print $thumb["caption"]; ?></font></td>
</tr></table>
<? } ?>

</td>
<?
        $thumb_count++;    
	}
?>

</table>

<!-- #### /thumbnails #### -->
<?
	}
    if(count($page["album_path"]) == 1) {
        include($config["news_filename"]);
    }
}
?>

<?
if($page["mode"] == "image") {
?>
<!-- #### image/ #### -->

<table border="0" cellspacing="0" cellpadding="0" class="image_cell"><tr><td>
<table border="0" cellspacing="3" cellpadding="5">
<tr>
<td class="imagemenu_cell">
<font size="3">
<? if($page["prev"]) { ?>
<a class="bold_link" href="<? print $page["prev"]["link"]; ?>">&lt; previous image</a>
<? } ?>
&nbsp;
<? if($page["next"]) { ?>
<a class="bold_link" href="<? print $page["next"]["link"]; ?> ">next image &gt;</a>
<? } ?>
</font>
</td>
<td class="imagemenu_cell">
<font size="1">
image size:
<?
foreach($page["resize_options"] as $resize_option) { 
    if($resize_option["selected"]) { 
?>
<b>[<? print $resize_option["geometry"] ?>]</b>
<? 
    } else { 
?>
<a class="bold_link" href="<? print $resize_option["link"]; ?>"><? print $resize_option["geometry"]; ?></a>
<? 
    } 
} 
?>
</font>
</td>
<td class="imagemenu_cell">
<font size="2"><a class="bold_link" href="<? print $page["page_link"]; ?>">show thumbnails</a></font>
</td>
</tr></table>
</td></tr></table>

<p>
<table cellspacing="0" cellpadding="4" class="image_cell" border="0">
<tr><td>
<table width="100%" border="0" cellspacing="0" cellpadding="1">
<tr><td bgcolor="#CCCCCC">
<font size="4"><? print $page["image_name"]; ?></font><br>
<font size="2"><? print $page["image_info"]["caption"]; ?></font>
</td></tr>
<tr><td bgcolor="#AAAAAA"><font size="1">
<?
    for($i = 0; $i < count($page["info_line"]); $i++) {
        print $page["info_line"][$i]["key"] . ": " . $page["info_line"][$i]["value"];
        if($i < (count($page["info_line"]) - 1)) {
            print " | ";
        }
    }
?>
</font>
</td></tr>
</table>
</td></tr>
<tr><td align="center"><img src="<? print $page["image_src"]; ?>"></td>
</tr></table>

<br>
<?
if(is_array($exif = $page["image_info"]["exif"])) {
?>
<table border="0" cellspacing="0" cellpadding="2">
<tr bgcolor="#CCCCCC"><td colspan="2"><font size="3">EXIF data</font></td></tr>
<?
    $row = 0;
    foreach(array( "CameraModel", "DateTime", "Width", "Height", "FlashUsed", "IsColor", "FocalLength", "ExposureTime",
    "ApertureFNumber", "ISOspeed") as $k) {
        echo "<tr bgcolor=\"" . ($row ? "#DDDDDD" : "#EEEEEE") . "\"><td><font size=\"2\"><b>$k</b></font></td><td><font size=\"2\">" . $exif[$k] . "</font></td></tr>\n";
        $row = 1 - $row;
    }
?>
</table>
<?
} 
?>
<!-- #### /image #### -->
<?
}
?>

</td></tr>
</table>

<hr noshade>
<font size="1">Made with YAPPA by Federico 'pix' Feroldi - <a href="http://www.pixzone.com/">http://www.pixzone.com/</a></font>
</body>
</html>
<!-- $Header: /cvsroot/yappa/yappa/index.php,v 1.17 2002/04/23 16:09:56 pixzone Exp $ -->