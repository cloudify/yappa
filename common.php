<?
	
/*
 * Project:     YAPPA : Yet Another PHP Photo Album
 * Author:      Federico Feroldi <pix@pixzone.com>
 * Copyright:   2001 Federico Feroldi
 * $Header: /cvsroot/yappa/yappa/common.php,v 1.8 2002/04/15 15:39:31 pixzone Exp $
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

include_once("defines.inc.php");

/*
 * makeselflink($params)
 * makes a link to ourself passing the parameters from $params array
 *
 */
function makeselflink($params)
{
    $url = ($PHP_SELF) ? $PHP_SELF : $HTTP_SERVER_VARS["PHP_SELF"];

    if(is_array($params)) {
        $url .= "?";
        while(list($key, $value) = each($params)) {
            $url .= $key . "=" . rawurlencode($value) . "&";
        }
    }

    return $url;
}

/*
 * makeshowlink($image_name, $album_name, $size)
 * makes a link to the show script
 *
 */
function makeshowlink($image_name, $album_name, $size)
{
    global $define;
    return $define["resize_script"] . "/$size/" . $album_name . '/' . htmlentities(rawurlencode($image_name));
}

/*
 * get_image_mime($filename)
 * returns the mime type of the filename
 * returns null if the extension is unknown
 *
 */
function get_image_mime($filename)
{
    global $define;
    
    if(preg_match('/\.([^\.]+)$/', $filename, $matches)) {
        if($mime = $define["image_mime"][strtolower($matches[1])]) {
            return $mime;
        }
    }
    return;
}

/*
 * calc_resized_geometry($src_width, $src_height, $dst_width, $dst_height)
 * compute the resized image geometry with the same aspect ratio of the
 * source image
 * returns an array with withd and height
 */
function calc_resized_geometry($src_width, $src_height, $dst_width, $dst_height)
{
    $ratio_width = $dst_width / $src_width;
    $ratio_height = $dst_height / $src_height;

    $ratio = ($ratio_width < $ratio_height) ? $ratio_width : $ratio_height;

    return array($src_width * $ratio, $src_height * $ratio);
}
 
?>
