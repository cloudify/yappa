<?

/*
 * Project:     YAPPA : Yet Another PHP Photo Album
 * Author:      Federico Feroldi <pix@pixzone.com>
 * Copyright:   2001 Federico Feroldi
 * $Header: /cvsroot/yappa/yappa/image-magick.class.php,v 1.2 2002/01/28 11:01:44 pixzone Exp $
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
 	
class ImageUtil
{
	var $_convert_path;	
	
	function ImageUtil(&$config)
	{
		$this->_convert_path = $config["convert_path"];
        if(!file_exists($this->_convert_path)) {
            exit("file not found '$this->_convert_path'");
        }
	}

    function resize($src, $dst, $params)
    {
       	$params_line = "-geometry " . $params["resize_width"] . "x" . $params["resize_height"] . " -quality " . $params["quality"];

        if($dst) {
            exec("$this->_convert_path $params_line \"$src\" \"" . escapeshellcmd($dst) . "\"");
        } else {
            passthru("$this->_convert_path $params_line \"$src\" -");
		}

    }
}


?>
