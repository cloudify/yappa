<?

/* 
 * YAPPA, main configuration
 */

$config = array(

/* title of your collection */
"title" => "pix's photo album",

/* directory where album dirs are stored */
"photo_root" => "/home/feroldi/public_html/photos",

/* image module: magick, gd */
"image_module" => 'magick',

/* full path to ImageMagick's convert program */
"convert_path" => '/usr/local/bin/convert',

/* thumbnails width and height */
"thumb_width" => 100,
"thumb_height" => 100,

/* thumbnails per row in thumbnails view */
"thumbs_per_row" => 3,

/* total thumbnails rows per page */
"thumbs_rows" => 2,

/* albums per row in homepage */
"albums_per_row" => 4,

/* available sizes */
"resize_options" => array( "640x640", "800x800", "original" ),

/* cache resized images? */
"resize_cache" => true,
"cache_root" => '/tmp/yappa',

/* jpeg quality for resized images */
"resize_quality" => 60,

/* default size when viewing an image */
"resize_default" => "640x640",

/* news filename */
"news_filename" => "news.html",

/* album info filename */
"info_filename" => "info.txt",

/* image captions filename */
"captions_filename" => "captions.txt",

/* hits counter log filename */
"log_filename" => "hits.log",

/* sort by none, name, mtime */
"sort_by" => "name",
"album_sort_by" => "name",

/* sort order (a)scending or (d)escending, not used when 'sort_by' is 'none' */
"sort_order" => "a",

/* date format used when printing dates, refer to PHP's date format documentation */
"date_format" => "Y/m/d H:i:s",

/* size of the pages navbar in thumbnail mode (must be multiple of 2) */
"navlink_pages" => 10,

);

?>
