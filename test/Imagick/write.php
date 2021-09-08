<?php



$image = \tt\image\Imagick::create(100, 100, '#FF0000');

$path = \ebi\WorkingStorage::tmpfile('', '.jpg');
$image->write($path);

eq(is_file($path));

