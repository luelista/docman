<?php // Code within app\Helpers\Helper.php
//included from composer.json

/**
* Displays file sizes in a human readable format.
* --------------------------------------------------
* Note: Because PHP's integer type is signed and 
* many platforms use 32bit integers, some filesystem 
* functions may return unexpected results for files 
* which are larger than 2GB.
*
* http://www.php.net/manual/en/function.filesize.php
*/
function readable_size($tmp) {
  if ($tmp >= 0 && $tmp < 1024) {
    $file = $tmp . " bytes";
  } elseif ($tmp >=1024 && $tmp < 1048576) { // less than 1 MB
      $tmp = $tmp / 1024;
      $file = round($tmp) . " KB";
  } elseif ($tmp >=1048576 && $tmp < 10485760) { // more than 1 MB, but less than 10
      $tmp = $tmp / 1048576;
      $file = round($tmp, 1) . " MB";
  } else  { // more than 10 MB, but less that 1 GB
      $tmp = $tmp / 1048576;
      $file = round($tmp) . " MB";
  }
  return $file;
}

