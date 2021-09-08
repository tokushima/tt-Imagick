<?php

$image = \tt\image\Barcode::CustomerBarcode('160005','２丁目３４−５')->image();
$path = \ebi\WorkingStorage::tmpfile('', '.png');
$image->write($path);
eq(is_file($path));



$image = \tt\image\Barcode::NW7('a1234567890123a')->image();
$path = \ebi\WorkingStorage::tmpfile('', '.png');
$image->write($path);
eq(is_file($path));


$image = \tt\image\Barcode::EAN13('4549995186550')->image();
$path = \ebi\WorkingStorage::tmpfile('', '.png');
$image->write($path);
eq(is_file($path));


$image = \tt\image\Barcode::CODE39('a1234567890123a')->image();
$path = \ebi\WorkingStorage::tmpfile('', '.png');
$image->write($path);
eq(is_file($path));



$image = \tt\image\Barcode::QRCode('a1234567890123a', 10)->image();
$path = \ebi\WorkingStorage::tmpfile('', '.png');
$image->write($path);
eq(is_file($path));

