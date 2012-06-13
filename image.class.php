<?

/*
 * Project:     YAPPA : Yet Another PHP Photo Album
 * Author:      Federico Feroldi <pix@pixzone.com>
 * Copyright:   2001 Federico Feroldi
 * $Header: /cvsroot/yappa/yappa/image.class.php,v 1.8 2002/01/28 11:01:44 pixzone Exp $
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
include_once("image-$config[image_module].class.php");  // quite bad hack :P

/*
 * ImageResize class
 */
 
class ImageResize
{
	var $_src, $_params = array();
    var $_image_util;
	
    function ImageResize($src)
    {
        global $config;
        
        $this->_src = $src;
        $this->_image_util = new ImageUtil($config);
    }
	
	function set_params($params) 
	{
		$this->_params = $params;
	}
	
	function convert($dst = '') 
	{
        $this->_image_util->resize($this->_src, $dst, $this->_params);
	}
}


?>
