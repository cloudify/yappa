<?

/*
 * YAPPA : Yet Another PHP Photo Album
 * Federico Feroldi <pix@pixzone.com>
 * Copyright 2001, 2002 Federico Feroldi
 * $Header: /cvsroot/yappa/yappa/main.php,v 1.25 2002/04/23 16:09:36 pixzone Exp $
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

include_once("config.inc.php");
include_once("album.class.php");
include_once("common.php");

// some initialization
$page = array();
$date_format = $config["date_format"];

// gather some data
$page["php_self"] = ($PHP_SELF) ? $PHP_SELF : $HTTP_SERVER_VARS["PHP_SELF"];
if($page["php_self"] == "") {
	die("error: neither \$PHP_SELF or \$HTTP_SERVER_VARS defined, please check your PHP config.");
}

// get referer
$page["referer"] = ($HTTP_REFERER) ? $HTTP_REFERER : $HTTP_SERVER_VARS["HTTP_REFERER"];

$album_group = new AlbumGroup($config["photo_root"]);

// get selected album and check if selected album exists
$selected_album = rawurldecode($HTTP_GET_VARS["album"]);
if(!$selected_album || !$album_group->exists($selected_album)) {
	$selected_album = "";
}

$page["mode"] = "album";

$page["album_name"] = basename($selected_album);
$page["album_link"] = makeselflink(array("album" => $selected_album));

$album_path_curr = '';
$album_path = array(
    array(
        "link" => makeselflink(array()),
        "name" => 'home',
    )
);
foreach(explode('/', $selected_album) as $album_path_item) {
    if($item = basename($album_path_item)) {
        $album_path_curr .= $item . '/';
        $album_path[] = array(
            "link" => makeselflink(array('album' => $album_path_curr)),
            "name" => $item,
        );
    }
}
$page["album_path"] = $album_path;

if(count($album_path) > 1) {
    $page["album_link_up"] = $album_path[count($album_path) - 2]["link"];
}


// create a new album object, read image list and check for thumbnails
$album = $album_group->get_album($selected_album);
$album->read_dir();
$album->sort();
$album->read_album_info();
$album->read_images_info();

// get some album info
$page["album_title"] = $album->_album_title;
$page["album_comment"] = $album->_album_comment;

$thumbs_per_page = $config["thumbs_per_row"] * $config["thumbs_rows"];

// get selected image
$selected_image = basename(rawurldecode($HTTP_GET_VARS["image"]));
if(!$album->image_exists($selected_image)) {
	$selected_image = "";
}

if(!$selected_image) {
	// no image selected, or invalid selected image
	// so we create a thumbnail page

	// increment album hits counter if user is not coming from the same album
	$hitcounter = new HitCounter($config["log_filename"]);
	if($page["referer"] && $selected_album && !strstr($page["referer"], rawurlencode($selected_album))) {
		$page["album_hits"] = $hitcounter->add_hit($selected_album);
	} else {
		$page["album_hits"] = $hitcounter->read_hit($selected_album);
	}

    $page["album_images"] = $album->count_images();

    if(count($album->_subalbums) > 0) {
        foreach($album->_subalbums as $subalbum) {
            $page["subalbums"][] = array(
                "name" => $subalbum["name"],
                "path" => $subalbum["path"], // "$selected_album/$subalbum",
                "url" => makeselflink(array("album" => $subalbum["path"])),
                "images_count" => $subalbum["images"],
                "subalbums_count" => $subalbum["subalbums"],
                "title" => $subalbum["title"],
            );

        }
    }
    
	$thumb_page = floor($HTTP_GET_VARS["page"]);
	$thumb_max_page = ceil($page["album_images"] / $thumbs_per_page);
	if(($thumb_page < 1)) {
		$thumb_page = 1;
	} elseif($thumb_page > $thumb_max_page) {
		$thumb_page = $thumb_max_page;
	}

	$page["thumb_page"] = $thumb_page;
	$page["thumb_max_page"] = $thumb_max_page;
    $page["thumb_table_width"] = ($config["thumb_width"] + 55) * $config["thumbs_per_row"];

	// generate thumbnails array
	$thumb_start = (($thumb_page - 1) * $thumbs_per_page);
	$thumb_end = min(($thumb_page * $thumbs_per_page), $page["album_images"]);
	for($i = $thumb_start; $i < $thumb_end; $i++) {
		$image_name = $album->get_imagename($i);
		$thumb = $album->get_thumbnail($image_name);

        $caption_cut = 30;
        if(strlen($thumb["caption"]) > $caption_cut) {
            $cut = strpos($thumb["caption"], " ", $caption_cut);
            if($cut) {
               $thumb["caption"] = substr($thumb["caption"], 0, $cut) . "..."; 
            }
        }

		$page["thumbnails"][] = array(
			"link" => makeselflink(array("album" => $selected_album, "image" => $image_name)),
            "src" => makeshowlink($image_name, $selected_album, 'thumbnail'),
			"size_html" => $thumb["html"],
			"alt" => htmlspecialchars($image_name, ENT_QUOTES),
			"mtime" => $thumb["mtime"],
            "caption" => $thumb["caption"],
		);
	}
    $page["thumbnails_tot"] = $thumb_end - $thumb_start;

    $page["thumbnail_cell_percentwidth"] = floor(100 / $config["thumbs_per_row"]);

    /* calculate page navigation links */

    $page_links = floor($config["navlink_pages"] / 2);

    if($thumb_page <= $page_links) {
        $start_page = 1;
        $end_page = $start_page + $page_links * 2;
    } elseif($thumb_page > ($thumb_max_page - $page_links)) {
        $end_page = $thumb_max_page;
        $start_page = $end_page - $page_links * 2;
    } else {
        $start_page = $thumb_page - $page_links;
        $end_page = $thumb_page + $page_links;
    }

    if($start_page < 1) {
        $start_page = 1;
    }

    if($end_page > $thumb_max_page) {
        $end_page = $thumb_max_page;
    }

    $back_page = $start_page - 1;
    $forw_page = $end_page + 1;

    $page["navlinks"] = array();
    for($i = $start_page; $i <= $end_page; $i++) {
        $page["navlinks"][] = array(
            "page" => $i,
            "link" => makeselflink(array("album" => $selected_album, "page" => $i)),
            "selected" => ($i == $thumb_page) ? 1 : 0,
        );
	}

    if($start_page > 1) { 
        $page["navlink_back"] = makeselflink(array("album" => $selected_album, "page" => $back_page));
    }

    if($end_page < $thumb_max_page) { 
        $page["navlink_forw"] = makeselflink(array("album" => $selected_album, "page" => $forw_page));
    }

    if($thumb_page > 1) {
        $page["navlink_previous"] = makeselflink(array("album" => $selected_album, "page" => ($thumb_page - 1)));
    }

    if($thumb_page < $thumb_max_page) {
        $page["navlink_next"] = makeselflink(array("album" => $selected_album, "page" => ($thumb_page + 1)));
    }

} else {
	// valid image selected, so we create an image view page
	$page["mode"] = "image";
	$page["image_name"] = $selected_image;

	// increment image hits counter
	if($config["log_filename"]) {
        $hitcounter = new HitCounter($config["log_filename"]);
    	$page["image_hits"] = $hitcounter->add_hit("$selected_album/$selected_image");
    }

	// get image info from the album
	$image = $album->get_image($selected_image);
	$page["image_info"] = $image;

	// check if user wants to resize the image
	$resize_opt = $HTTP_GET_VARS["resize"];

	// check if user specified resize
	if(!in_array($resize_opt, $config["resize_options"])) {
		if(in_array($HTTP_COOKIE_VARS["resize"], $config["resize_options"])) {
			$resize_opt = $HTTP_COOKIE_VARS["resize"];
		} else {
			$resize_opt = $config["resize_default"];
		}
	}
	$page["image_resize"] = $resize_opt;
	$page["image_src"] = makeshowlink($selected_image, $selected_album, $resize_opt);
	setcookie("resize", $resize_opt);

	// get info for prev/next buttons
	list($image_prev, $image_next) = $album->get_prevnext($selected_image);

	if($image_prev) {
		$thumb = $album->get_thumbnail($image_prev);
		$page["prev"] = array(
			"link" => makeselflink(array("album" => $selected_album, "image" => $image_prev)),
			"src" => $thumb["uri"],
			"size_html" => $thumb["html"],
			"alt" => $image_prev
		);	
	}

	if($image_next) {
		$thumb = $album->get_thumbnail($image_next);
		$page["next"] = array(
			"link" => makeselflink(array("album" => $selected_album, "image" => $image_next)),
			"src" => $thumb["uri"],
			"size_html" => $thumb["html"],
			"alt" => $image_next
		);	
	}

    $image_index = $album->get_imageindex($selected_image);
    $page["page_index"] = ($image_index >= 0) ? (floor($image_index / $thumbs_per_page) + 1) : 1;
    $page["page_link"] = makeselflink(array("album" => $selected_album, "page" => $page["page_index"])); 

    /* create resize options links */
    foreach($config["resize_options"] as $resize_opt) {
        $page["resize_options"][] = array(
            "geometry" => $resize_opt,
            "selected" => ($resize_opt == $page["image_resize"]) ? 1 : 0,
            "link" => makeselflink(array("album" => $selected_album, "image" => $selected_image, "resize" => $resize_opt)),
        );
    }

    /* info line data */

    $page["info_line"][] = array(
        "key" => "Hits",
        "value" => ($page["image_hits"]) ? $page["image_hits"] : 0
    );

    $page["info_line"][] = array(
        "key" => "Size",
        "value" => ($page["image_info"]["width"] && $page["image_info"]["height"]) ? 
            ($page["image_info"]["width"] . " x " . $page["image_info"]["height"]) : 
            "unknown"
    );

    if($image["exif"]["DateTime"] && ($image["exif"]["DateTime"] != "0000:00:00 00:00:00") ) {
        $page["info_line"][] = array(
            "key" => "Date",
            "value" => $image["exif"]["DateTime"]
        );
    }

    $photo_info = array();

    if($image["exif"]["FocalLength"]) {
        $photo_info[] = $image["exif"]["FocalLength"];
    }

    if($image["exif"]["ExposureTime"]) {
        if(preg_match("/(\d+\/\d+)/", $image["exif"]["ExposureTime"], $matches)) {
            $photo_info[] = $matches[1];
        } else {
            $photo_info[] = $image["exif"]["ExposureTime"];
        }
    }

    if($image["exif"]["ApertureFNumber"]) {
        $photo_info[] = $image["exif"]["ApertureFNumber"];
    }

    if($image["exif"]["ISOspeed"]) {
        $photo_info[] = $image["exif"]["ISOspeed"] . " ISO";
    }

    if(count($photo_info) > 0) {
        $page["info_line"][] = array(
            "key" => "Photo",
            "value" => join(", ", $photo_info),
        );
    }

}

?>
