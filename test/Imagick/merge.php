<?php
$path_png = tempnam(sys_get_temp_dir(), '');
$path_txt = tempnam(sys_get_temp_dir(), '');


\tt\image\Imagick::set_font('/System/Library/Fonts/ヒラギノ明朝 ProN.ttc','HIRAMIN');

$img_jpg = new \tt\image\Imagick(\testman\Resource::path('wani.jpg'));
$img_png = new \tt\image\Imagick(\testman\Resource::path('mm.png'));

$img_jpg->merge(10, 10,$img_png);
$img_jpg->write($path_png);




$img_jpg = new \tt\image\Imagick(\testman\Resource::path('wani.jpg'));
$img_text = \tt\image\Imagick::create(300,100);
$img_text->text(16, 40, '#FF0000',16,'HIRAMIN', 'This is a sample.');


$img_jpg->merge(10, 10,$img_text);
$img_jpg->write($path_txt);
