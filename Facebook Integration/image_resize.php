<?php 
 
 class Image {      
  
  
  public function img_resize($ini_path, $dest_path, $params = array()) {
       
    $width = !empty($params['width']) ? $params['width'] : null;
    $height = !empty($params['height']) ? $params['height'] : null;
    $constraint = !empty($params['constraint']) ? $params['constraint'] : false;
    $rgb = !empty($params['rgb']) ?  $params['rgb'] : 0xFFFFFF;
    $quality = !empty($params['quality']) ?  $params['quality'] : 100;
    $aspect_ratio = isset($params['aspect_ratio']) ?  $params['aspect_ratio'] : true;
    $crop = isset($params['crop']) ?  $params['crop'] : true;
 
    if (!file_exists($ini_path)) return false;
 
 
    if (!is_dir($dir=dirname($dest_path))) mkdir($dir);
 
    $img_info = getimagesize($ini_path);
    if ($img_info === false) return false;
 
    $ini_p = $img_info[0]/$img_info[1];
    if ( $constraint ) {
        $con_p = $constraint['width']/$constraint['height'];
        $calc_p = $constraint['width']/$img_info[0];
 
        if ( $ini_p < $con_p ) {
            $height = $constraint['height'];
            $width = $height*$ini_p;
        } else {
            $width = $constraint['width'];
            $height = $img_info[1]*$calc_p;
        }
    } else {
        if ( !$width && $height ) {
            $width = ($height*$img_info[0])/$img_info[1];
        } else if ( !$height && $width ) {
            $height = ($width*$img_info[1])/$img_info[0];
        } else if ( !$height && !$width ) {
            $width = $img_info[0];
            $height = $img_info[1];
        }
    }
 
    preg_match('/\.([^\.]+)$/i',basename($dest_path), $match);
    $ext = $match[1];
    $output_format = ($ext == 'jpg') ? 'jpeg' : $ext;
 
    $format = strtolower(substr($img_info['mime'], strpos($img_info['mime'], '/')+1));
    $icfunc = "imagecreatefrom" . $format;
 
    $iresfunc = "image" . $output_format;
 
    if (!function_exists($icfunc)) return false;
 
    $dst_x = $dst_y = 0;
    $src_x = $src_y = 0;
    $res_p = $width/$height;
    if ( $crop && !$constraint ) {
        $dst_w  = $width;
        $dst_h = $height;
        if ( $ini_p > $res_p ) {
            $src_h = $img_info[1];
            $src_w = $img_info[1]*$res_p;
            $src_x = ($img_info[0] >= $src_w) ? floor(($img_info[0] - $src_w) / 2) : $src_w;
        } else {
            $src_w = $img_info[0];
            $src_h = $img_info[0]/$res_p;
            $src_y    = ($img_info[1] >= $src_h) ? floor(($img_info[1] - $src_h) / 2) : $src_h;
        }
    } else {
        if ( $ini_p > $res_p ) {
            $dst_w = $width;
            $dst_h = $aspect_ratio ? floor($dst_w/$img_info[0]*$img_info[1]) : $height;
            $dst_y = $aspect_ratio ? floor(($height-$dst_h)/2) : 0;
        } else {
            $dst_h = $height;
            $dst_w = $aspect_ratio ? floor($dst_h/$img_info[1]*$img_info[0]) : $width;
            $dst_x = $aspect_ratio ? floor(($width-$dst_w)/2) : 0;
        }
        $src_w = $img_info[0];
        $src_h = $img_info[1];
    }
 
    $isrc = $icfunc($ini_path);
    $idest = imagecreatetruecolor($width, $height);
    if ( ($format == 'png' || $format == 'gif') && $output_format == $format ) {
        imagealphablending($idest, false);
        imagesavealpha($idest,true);
        imagefill($idest, 0, 0, IMG_COLOR_TRANSPARENT);
        imagealphablending($isrc, true);
        $quality = 0;
    } else {
        imagefill($idest, 0, 0, $rgb);
    }
    imagecopyresampled($idest, $isrc, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
    $res = $iresfunc($idest, $dest_path, $quality);
 
    imagedestroy($isrc);
    imagedestroy($idest);
 
    return $res;
  }
     
 }

?>