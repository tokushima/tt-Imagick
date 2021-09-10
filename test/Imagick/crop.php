<?php
$filename = \testman\Resource::path('test.jpg');
$out = \ebi\WorkingStorage::path(base64_encode(__FILE__).'.jpg');

$image = new \tt\image\Imagick($filename);
eq(\tt\image\Imagick::ORIENTATION_SQUARE,$image->get_orientation());
$image->crop(100,50)->write($out);
eq(\tt\image\Imagick::ORIENTATION_LANDSCAPE,$image->get_orientation());

eq(file_get_contents(\testman\Resource::path('croped.jpg')) == file_get_contents($out));





