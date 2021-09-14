<?php
$filename = \testman\Resource::path('test.jpg');

$path = tempnam(sys_get_temp_dir(), '');

$image = new \tt\image\Imagick($filename);
eq(\tt\image\Imagick::ORIENTATION_SQUARE,$image->get_orientation());
$image->crop(100,50)->write($path);
eq(\tt\image\Imagick::ORIENTATION_LANDSCAPE,$image->get_orientation());

eq(file_get_contents(\testman\Resource::path('croped.jpg')) == file_get_contents($path));





