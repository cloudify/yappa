<?

/*
 * Project:     YAPPA : Yet Another PHP Photo Album
 * Author:      Federico Feroldi <pix@pixzone.com>
 * Copyright:   2001 Federico Feroldi
 * $Header: /cvsroot/yappa/yappa/show.php,v 1.20 2002/04/15 15:39:32 pixzone Exp $
 *              
 * This software is released under the GPL.  Please
 * see the included LICENSE file.
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
include_once("config.inc.php");
include_once("image.class.php");
include_once("common.php");

$path_info = $PATH_INFO ? $PATH_INFO : $HTTP_SERVER_VARS["PATH_INFO"];
if($path_info == "") {
	die("error: neither \$PATH?INFO or \$HTTP_SERVER_VARS defined, please check your PHP config.");
}

preg_match("/^\/([^\/]+)\/(.+)\/(.+)$/", $path_info, $matches);

list($dummy, $resize, $album_name, $obj_name) = $matches;

$album_name = rawurldecode($album_name);
$obj_name = stripslashes(rawurldecode($obj_name));

if(!is_dir($album_root = "$config[photo_root]/$album_name")) {
    die("album root doesn't exists [$album_root]");
}

$obj_path = "$album_root/$obj_name";
$mime_type = get_image_mime($obj_path);

header("Content-type: $mime_type");

if($resize == "thumbnail") {
    $thumbnail_path = update_cache($album_name, $obj_name, $resize);
   	readfile($thumbnail_path);
} elseif($resize == "original") {
	readfile($obj_path);
} elseif($resize == "download") {
    header("Content-Disposition: attachment; filename=$obj_name");
	readfile($obj_path);
} elseif(in_array($resize, $config["resize_options"])) {
    list($resize_width, $resize_height) = split('x', $resize);

    if($config["resize_cache"] && ($image_cached = update_cache($album_name, $obj_name, $resize))) {
   	    readfile($image_cached);
    } else {
        $cimage = new ImageResize($obj_path);
    	$cimage->set_params(
	    	array(	
                "resize_width" => $resize_width,
                "resize_height" => $resize_height, 
                "quality" => $config["resize_quality"]
	    	)
    	);

	    $cimage->convert();
    }

} else {
	print "error: invalid resize option";
}

exit;

function update_cache($album_name, $obj_name, $resize)
{
    global $config, $define;

    $album_root = $config["photo_root"] . "/$album_name";
    if(!is_dir($album_root)) {
        die("album root doesn't exists [$album_root]");
    }

    $obj_path = "$album_root/$obj_name";

    if($config["cache_root"]) {
        $cache_root = $config["cache_root"];
        if(!@file_exists($cache_root)) {
            if(!@mkdir($cache_root, $define["mkdir_mode"])) {
                $cache_root = $config["photo_root"];
            }
        }
    } else {
        $cache_root = $config["photo_root"];
    }

    if($resize == 'thumbnail') {
        $cache_dir = $define[thumbnails_dirname];
        $resize_width = $config["thumb_width"];
        $resize_height = $config["thumb_height"];
    } else {
        $cache_dir = $resize;
        list($resize_width, $resize_height) = split('x', $resize);
    }

    $cache_dir = "$album_name/_$cache_dir";
	$image_cached = "$cache_root/$cache_dir/$obj_name";

	if(!file_exists($image_cached) || (filemtime($obj_path) > filemtime($image_cached))) {
		if(!@file_exists($resize_path = "$cache_root/$cache_dir")) {
			if(!makepath($cache_root, $cache_dir)) {
                die("cannot create dir [$resize_path]");
            }
		}

        $cimage = new ImageResize($obj_path);
        $cimage->set_params(
	        array(	
                "resize_width" => $resize_width,
                "resize_height" => $resize_height, 
                "quality" => $config["resize_quality"]
    	    )
        );
        $cimage->convert($image_cached);
	}
    return $image_cached;
}

function makepath($root, $subpath, $mode = '')
{
    global $define;
    
    if($mode == '') {
        $mode = $define["mkdir_mode"];
    }
    
    $dir_array = split('/', $subpath);
    $path = $root;
    foreach($dir_array as $dir) {
        $path .= "/$dir";
        if(!file_exists($path)) {
    	    if(!@mkdir($path, $mode)) {
                return;
            }
        }
    }
    return $path;
}


?>
