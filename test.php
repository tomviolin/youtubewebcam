<?php
require_once 'info.php';

$myVideo = new Zend_Gdata_YouTube();

$x = $myVideo->getVideoEntry($argv[1]);
$y = $x->getVideoThumbnails();
print_r($y)
?>
