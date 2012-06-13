<?
	
/*
 * Project:     YAPPA : Yet Another PHP Photo Album
 * Author:      Federico Feroldi <pix@pixzone.com>
 * Copyright:   2001 Federico Feroldi
 * $Header: /cvsroot/yappa/yappa/album.class.php,v 1.20 2002/04/23 15:48:05 pixzone Exp $
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
include_once("image.class.php");
include_once("common.php");

/*
 * class AlbumGroup
 * This class manages an album collection (album group), that is the
 * albums list on the homepage.
 *
 */
 
class AlbumGroup 
{
    var $_group_root, $_albums;
	
	function AlbumGroup($group_root)
	{
		$this->_group_root = $group_root;
	}
	
	function exists($album_name)
	{
        return (!(preg_match('/\.\./', $album_name)) && @is_dir($this->_group_root . '/' . $album_name));
	}
	
    function get_album($album_name)
    {
        return new Album($this->_group_root, $album_name);
    }
    
}

/*
 * class Album
 * manages an image collection (album)
 *
 */
 
class Album
{
    var $_album_root, $_album_name, $_images, $_album_title, $_album_comment;
	var $_mtime, $_caption;
    var $_subalbums;
		
	function Album($group_root, $album_name)
	{
		$this->_album_root = "$group_root/$album_name";
        $this->_album_name = $album_name;
	}

    /* reads the album directory and information files */
	function read_dir() 
	{
		global $config;
        
        $this->_images = array();
        
		/* read images */
		$d = dir($this->_album_root);
		while($entry = $d->read()) {
            $entry_path = $this->_album_root . '/' . $entry;
			if(is_file($entry_path) && get_image_mime($entry_path)) {
				$this->_images[] = $entry;
				$this->_mtime[$entry] = filemtime($this->_album_root . '/' . $entry);
			} elseif(is_dir($entry_path) && (substr($entry, 0, 1) != '.') && (substr($entry, 0, 1) != '_')) {
                $subalbum = new Album($this->_album_root, $entry);
                $subalbum->read_dir();
                $subalbum->read_album_info();
                $this->_subalbums[$entry] = array(
                    name => $entry,
                    path => $this->_album_name . "/$entry",
                    images => $subalbum->count_images(),
                    subalbums => $subalbum->count_subalbums(),
                    title => $subalbum->_album_title,
                );
			}
		}
        
        @sort($this->_subalbums);
        
        return count($this->_images);
    }
    
    function sort()
    {
        global $config;
        
		if($config["sort_by"] == "name") {
			if($config["sort_order"] == "a") {
				sort($this->_images);
			} else {
				rsort($this->_images);
			}
		} elseif($config["sort_by"] == "mtime") {
			if($config["sort_order"] == "a") {
				asort($this->_mtime);
			} else {
				arsort($this->_mtime);
			}
			$this->_images = array_keys($this->_mtime);
		}
    }		
		
    function read_album_info()
    {
        global $config;
        
		/* check for info file */
		$info_filename = $this->_album_root . '/' . $config["info_filename"];
		if(file_exists($info_filename) && is_file($info_filename) && is_readable($info_filename)) {
			if($fd = fopen($info_filename, "rt")) {
			    $this->_album_title = fgets($fd, 1024);
			    while(!feof($fd)) {
				    $this->_album_comment .= fgets($fd, 1024);
			    }
                fclose($fd);
            }
		}
    }

    function read_images_info()
    {
        global $config;
        
        /* check for captions file */
        $captions_filename = $this->_album_root . '/' . $config["captions_filename"];
		if(file_exists($captions_filename) && is_file($captions_filename) && is_readable($captions_filename)) {
			if($fd = fopen($captions_filename, "rt")) {
			    while(!feof($fd)) {
				    $data = fgets($fd, 1024);
                    if(preg_match("/([^\|]+)\|(.+)/", $data, $matches)) {
                        $this->_caption[$matches[1]] = $matches[2];
                    }
			    }
                fclose($fd);
            }
		}
	}
	
	/* returns an array with all the images */
	function get_image_array() 
	{
		return $this->_images;
	}
	
	/* returns an array with some informations about this album */
	function get_info() {
		return array(
			"mtime" => filemtime($this->_album_root),
			"images_count" => count($this->_images),
            "title" => $this->_album_title,
            "comment" => $this->_album_comment,
		);
	}

    /* returns thumbnail information for an image */
	function get_thumbnail($image_name)
	{
        global $define;
        
        $thumbnail_path = $this->_album_root . '/' . $define["thumbnails_dirname"] . '/' . $image_name;
        
		if(file_exists($thumbnail_path)) {
            $image_size = getimagesize($thumbnail_path);
        }
        
		return array(
			"width" => $image_size[0],
			"height" => $image_size[1],
			"type" => $image_size[2],
			"html" => $image_size[3],
			"mtime" => filemtime($this->_album_root . '/' . $image_name),
            "caption" => $this->_caption[$image_name],
		);
	}
	
	/* returns information for an image */
	function get_image($image_name)
	{
		$image_path = $this->_album_root . '/' . $image_name;
		$image_size = getimagesize($image_path);
        $mime = get_image_mime($image_path);
		return array(
			"width" => $image_size[0],
			"height" => $image_size[1],
			"type" => $image_size[2],
			"html" => $image_size[3],
			"mtime" => filemtime($image_path),
			"path" => $image_path,
            "caption" => $this->_caption[$image_name],
			"exif" => (($mime == "jpeg") && function_exists("read_exif_data")) ? read_exif_data($image_path) : '',
            "mime" => $mime,
		);
	}
	
	/* returns previous and next images from an image */
	function get_prevnext($image_name) 
	{
		$prev = "";
		reset($this->_images);
 		while (list ($key, $curr) = each ($this->_images)) {
			if($curr == $image_name) {
				break;
			}
			$prev = $curr;
		}
		return array(
			$prev,		
			current($this->_images)
		);
	}
    
    /* returns the index for an image */
    function get_imageindex($image_name)
    {
        for($count = 0; $count < count($this->_images); $count++) { 
            if($this->_images[$count] == $image_name) {
                return $count;
            }
        }
        return -1;
    }
    
    function get_imagename($index)
    {
        return $this->_images[$index];
    }
    
    function image_exists($image_name)
    {
        return in_array($image_name, $this->_images);
    }
    
    function count_images()
    {
        return count($this->_images);
    }
    
    function count_subalbums()
    {
        return count($this->_subalbums);
    }

}

/*
 * HitCounter class, store a hits log, an entry is automatically created when
 * passed to add_hit()
 *
 */
class HitCounter 
{
	var $_log_filename, $_log_array = array();
		
	function HitCounter($log_filename) 
	{
		$this->_log_filename = $log_filename;
		if(!file_exists($this->_log_filename)) {
			$this->_write_log();
		}
	}

	function _read_log() 
	{
		if(!file_exists($this->_log_filename) || !is_readable($this->_log_filename) || !$fd = fopen($this->_log_filename, "r")) {
			return;
		}
		flock($fd, 1); // get a shared lock
		$data = fread($fd, filesize($this->_log_filename));
		flock($fd, 3); // release the lock
		fclose($fd);
		$this->_log_array = unserialize($data);
	}
	
	function _write_log() 
	{
		if(!file_exists($this->_log_filename) || !is_writeable($this->_log_filename) || !$fd = fopen($this->_log_filename, "w")) {
 			return;
		}
		flock($fd, 2); // get an exclusive lock
		fwrite($fd, serialize($this->_log_array));
		flock($fd, 3); // release the lock
		fclose($fd);
	}
	
	function add_hit($keyword) 
	{
		$this->_read_log();
		$this->_log_array[$keyword] = floor($this->_log_array[$keyword] + 1);
		$count = $this->_log_array[$keyword];
		$this->_write_log();
		return $count;
	}
	
	function read_hit($keyword) 
	{
		$this->_read_log();
		return $this->_log_array[$keyword];
	}
}


?>
