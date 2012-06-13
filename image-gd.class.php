<?

/*
 * Project:     YAPPA : Yet Another PHP Photo Album
 * Author:      Federico Feroldi <pix@pixzone.com>
 * Copyright:   2001 Federico Feroldi
 * $Header: /cvsroot/yappa/yappa/image-gd.class.php,v 1.2 2002/01/28 11:01:44 pixzone Exp $
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

include_once("common.php");
 	
class ImageUtil
{
	var $_can_truecolor;	
	
	function ImageUtil(&$config)
	{
        $this->_can_truecolor = (function_exists("ImageCreateTruecolor") && function_exists("ImageCopyResampled")) ? 1 : 0;
	}

    function resize($src, $dst, $params)
    {
        $im_mime = get_image_mime($src);

        switch($im_mime) {
            case "image/jpeg":
                ImageJPEG($this->_resizeimage(ImageCreateFromJPEG($src), $params["resize_width"], $params["resize_height"]), $dst, $params["quality"]);
                break;
            case "image/gif":
                ImageGIF($this->_resizeimage(ImageCreateFromGIF($src), $params["resize_width"], $params["resize_height"]), $dst); 
                break;
            case "image/png":
                ImagePNG($this->_resizeimage(ImageCreateFromPNG($src), $params["resize_width"], $params["resize_height"]), $dst); 
                break;
            default:
                return;
        }
    }

    function _resizeimage($im_src, $dst_width, $dst_height)
    {
        $src_width = ImageSX($im_src);
        $src_height = ImageSY($im_src);
        
        list($res_width, $res_height) = calc_resized_geometry($src_width, $src_height, $dst_width, $dst_height);

        $im_dst = $this->_can_truecolor ? 
            ImageCreateTruecolor($res_width, $res_height) : 
            ImageCreate($res_width, $res_height);
            
        $this->_can_truecolor ? 
            ImageCopyResampled($im_dst, $im_src, 0, 0, 0, 0, $res_width, $res_height, $src_width, $src_height) :
            ImageCopyResized($im_dst, $im_src, 0, 0, 0, 0, $res_width, $res_height, $src_width, $src_height);

        return $im_dst;
    }
    
}

?>
