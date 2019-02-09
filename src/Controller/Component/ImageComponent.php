<?php
namespace App\Controller\Component;

use Cake\Controller\Component;

class ImageComponent extends Component
{

////////////////////////////////////////////////////////////////////////////////

    public $allowed_ext = array('jpg', 'jpeg', 'png');
    public $min_filesize = 10000;
    public $max_filesize = 50000000;

////////////////////////////////////////////////////////////////////////////////

    public function upload($source, $targetdir, $targetfile) {

        $filesize = filesize($source);

        // echo $source;
        // echo $filesize;
        // die;

        if(($filesize < $this->min_filesize) || ($filesize > $this->max_filesize)) {
            return 'filesize must be between ' . $this->human_filesize($this->min_filesize) . ' and ' . $this->human_filesize($this->max_filesize);
        }

        $data = getimagesize($source);

//      if (empty($data) || !is_array($data)) {
//          return 'invalid picture';
//      }

        list($srcWidth, $srcHeight, $type) = $data;

        if($type != IMAGETYPE_JPEG) {
            return 'file is not allowed.';
        }

        if (!$srcWidth || !$srcHeight) {
            return 'invalid picture size';
        }

        if ($srcWidth < 640 || $srcHeight < 480) {
            return 'picture size must be larger tahnk 639x479';
        }

        $this->mkdir($targetdir);

        if(move_uploaded_file($source, $targetdir . '/' . $targetfile)) {
            @chmod($targetdir . '/' . $targetfile, 0777);
            return 'Success';
        }

        return 'invalid upload';
    }

////////////////////////////////////////////////////////////////////////////////

    public function human_filesize($bytes, $decimals = 0) {
        $sz = 'BKMGTP';
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1000, $factor)) . @$sz[$factor] . 'B';
    }

////////////////////////////////////////////////////////////////////////////////

    public function ext($file) {
        return mb_strtolower(trim(mb_strrchr($file, '.'), '.'));
    }

////////////////////////////////////////////////////////////////////////////////

    public function cropimage($src, $dst, $width, $height, $start_width, $start_height, $scale){
        $newImageWidth = ceil($width * $scale);
        $newImageHeight = ceil($height * $scale);
        $newImage = imagecreatetruecolor($newImageWidth, $newImageHeight);
        $source = imagecreatefromjpeg($src);
        imagecopyresampled($newImage, $source, 0, 0, $start_width, $start_height, $newImageWidth, $newImageHeight, $width, $height);
        imagejpeg($newImage, $dst, 95);
        @chmod($dst, 0777);
        //return $thumb_image_name;
    }

////////////////////////////////////////////////////////////////////////////////

    public function mkdir($targetdir) {
        if(!is_dir($targetdir)) {
            App::uses('Folder', 'Utility');
            $dir = new Folder($targetdir, true, 0777);
            if(!$dir) {
                return false;
            }
        }
        return true;
    }

////////////////////////////////////////////////////////////////////////////////

    public function resample($srcPath, $targetdir, $image, $dst_w = null, $dst_h = null, $fixedSize = false, $centerCrop = false) {

        if(!is_file($srcPath)) {
            // return;
        }

        $dstPath = $targetdir . $image;

        if(is_file($dstPath)) {
            // return;
        }

        $this->mkdir($targetdir);

        list($src_w, $src_h, $type) = getimagesize($srcPath);

        $scale = min($dst_w / $src_w, $dst_h / $src_h);
        $new_w = (int) ($src_w * $scale);
        $new_h = (int) ($src_h * $scale);

        if($fixedSize) {
            $dst_x = (int) (($dst_w - $new_w) / 2);
            $dst_y = (int) (($dst_h - $new_h) / 2);
        }

        if(!$fixedSize && !$centerCrop) {
            $dst_w = $new_w;
            $dst_h = $new_h;
            $dst_x = 0;
            $dst_y = 0;
        }

        if($centerCrop) {

            $original_aspect = $src_w / $src_h;
            $thumb_aspect = $dst_w / $dst_h;

            if ( $original_aspect >= $thumb_aspect ) {
                $new_h = $dst_h;
                $new_w = (int) $src_w / ($src_h / $dst_h);
            } else {
                $new_w = $dst_w;
                $new_h = (int) $src_h / ($src_w / $dst_w);
            }
            $dst_x = (int) 0 - ($new_w - $dst_w) / 2;
            $dst_y = (int) 0 - ($new_h - $dst_h) / 2;

        }

        $dst = ImageCreateTrueColor($dst_w, $dst_h);
        $backColor = ImageColorAllocate($dst, 0, 0, 0);
        ImageFilledRectangle($dst, 0, 0, $dst_w, $dst_h, $backColor);

        $exploded = explode('.', $image);
        $ext = $exploded[count($exploded) - 1];

        if (preg_match('/jpg|jpeg/i', $ext)) {
            $src = imagecreatefromjpeg($srcPath);
        }else if (preg_match('/png/i', $ext)) {
            $src = imagecreatefrompng($srcPath);
        }else if (preg_match('/gif/i', $ext)) {
            $src = imagecreatefromgif($srcPath);
        }else if (preg_match('/bmp/i', $ext)) {
            $src = imagecreatefrombmp($srcPath);
        }else {
            $src = imagecreatefromjpeg($srcPath);
        }

        ImageCopyResampled($dst, $src, $dst_x, $dst_y, 0, 0, $new_w, $new_h, $src_w, $src_h);
        imagejpeg($dst, $dstPath, 100);

        @chmod($dst, 0777);
        @chmod($srcPath, 0777);
        @chmod($dstPath, 0777);

        return true;
    }

////////////////////////////////////////////////////////////////////////////////

}