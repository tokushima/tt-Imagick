<?php
$outpath_png = tempnam(sys_get_temp_dir(), '');
$outpath_jpg = tempnam(sys_get_temp_dir(), '');
$outpath_mix_jpg = tempnam(sys_get_temp_dir(), '');
$outpath_small_png = tempnam(sys_get_temp_dir(), '');

\tt\image\Imagick::set_font('/System/Library/Fonts/ヒラギノ明朝 ProN.ttc','HIRAMIN');


$img_text = \tt\image\Imagick::create(300,100);
$img_text->text(16, 40, '#FF0000',16,'HIRAMIN', 'This is a sample.');
$img_text->text(16, 60, '#0000FF',16,'HIRAMIN', 'This is a sample.');

$img_text->rectangle(10, 80, 80, 10, '#0000FF',true,120);
$img_text->write($outpath_png, 'png');
$img_text->write($outpath_jpg, 'jpeg');

$img_jpg = new \tt\image\Imagick(\testman\Resource::path('wani.jpg'));
$img_jpg->merge(10, 10,$img_text);
$img_jpg->write($outpath_mix_jpg, 'jpeg');

$img_text->write($outpath_jpg, 'jpeg');


$img_text->resize(150,50);
$img_text->write($outpath_small_png, 'png');
