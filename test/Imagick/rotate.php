<?php
$filename = \testman\Resource::path('test.jpg');

$path = tempnam(sys_get_temp_dir(), '');

$image = new \tt\image\Imagick($filename);
$image->rotate(90);
$image->write(sprintf($path,90));


$image = new \tt\image\Imagick($filename);
$image->rotate(180);
$image->write(sprintf($path,180));


$image = new \tt\image\Imagick($filename);
$image->rotate(270);
$image->write(sprintf($path,270));


$image = new \tt\image\Imagick($filename);
$image->rotate(-90);
$image->write(sprintf($path,-90));

$image = new \tt\image\Imagick($filename);
$image->rotate(-180);
$image->write(sprintf($path,-180));


$image = new \tt\image\Imagick($filename);
$image->rotate(-270);
$image->write(sprintf($path,-270));










